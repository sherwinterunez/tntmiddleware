#!/usr/bin/perl -w
#
# USB Power
# Krellan
#
# This script will attempt to recover a misbehaving USB serial tty device,
# by unbinding/rebinding the USB hub(s) that are upstream from it.
# This should force the device to reset itself.
#
# Usage: usbpower.pl ttyUSB0
#
# If this script helps you, perhaps it helps make you money
# by keeping your Bitcoin miners alive, donations appreciated:
# 1KCABpu4gqmPqTW2yCi9o6oqoTKR11YKma

use warnings;
use strict;

my $argc = @ARGV;
if ($argc != 1)
{
	print "Usage: $0 ttyUSB0\n";
	exit 1;
}

my $devname = $ARGV[0];

# Remove optional /dev prefix
if ($devname =~ /^[\/]dev[\/](.+)$/)
{
	$devname = $1;
}

# Disallow any special characters (be paranoid, this script runs as root!)
if (!($devname =~ /^[A-Za-z0-9]+$/))
{
	print "Argument must be a USB serial tty device: example ttyUSB0\n";
	exit 1;
}

# Device must exist in ttyUSB list
my $rootpath = "/sys/bus/usb-serial/devices/";
my $devpath = $rootpath . $devname;

my $linkpath = readlink $devpath;
if (!(defined $linkpath))
{
	print "Unable to find USB serial tty device $devname\n";
	exit 1;
}

print "Attempting to reset $devname\n";

my $fullpath = $rootpath . $linkpath;

my $treepath = $fullpath;
my $usbdevice;

my $midpath   = "";
my $underpath = "";

my $lowestdriver = "";

# Control files
my $fileoff = "/sys/bus/usb/drivers/usb/unbind";
my $fileon  = "/sys/bus/usb/drivers/usb/bind";

for(;;)
{
	# Chop off path components as we work our way up the USB device tree
	if ($treepath =~ /^(.+)\/([^\/]+)$/)
	{
		# Save two levels of previous path
		$underpath = $midpath;
		$midpath = $treepath;
		
		# Split into two parts, at the rightmost slash character
		$treepath = $1;
		$usbdevice = $2;
	}
	else
	{
		print "Fell out of device tree!\n";
		exit 1;
	}
	
	# Stop search at the root of the device tree
	if ($usbdevice eq "devices")
	{
		print "Searched entire device tree but came up empty!\n";
		exit 1;
	}
	
	print "Searching $usbdevice\n";
	
	my $driverfile = $midpath . "/driver";
	
	# Device must have a driver
	$linkpath = readlink $driverfile;
	if (!(defined $linkpath))
	{
		print "Skipping, device does not have a driver\n";
		next;
	}
	
	# Driver path must end in bus/usb/drivers/usb
	if (!($linkpath =~ /bus[\/]usb[\/]drivers[\/]usb$/))
	{
		print "Skipping, device is not a USB hub\n";
		next;
	}
	
	# At this point, we should have a saved path
	if ($underpath eq "")
	{
		print "Lost track of device tree branches!\n";
		exit 1;
	}
	
	my $underdriver = $underpath . "/driver";
	
	# Device immediately underneath this device must have a driver, so we can detect its restoration
	$linkpath = readlink $underdriver;
	if (!(defined $linkpath))
	{
		print "Skipping, device below this hub does not have a driver\n";
		next;
	}
	
	# Remember this path, so we can validate, even if we have to reset hubs further upstream
	if ($lowestdriver eq "")
	{
		$lowestdriver = $underdriver;
	}
	
	print "Commanding $usbdevice OFF\n";
	
	# Tell kernel to unbind this device from the driver (should power it off if this hub is capable)
	if (!(open(FILEOFF, ">", $fileoff)))
	{
		print "Unable to open $fileoff: $!\nDo you have root permissions?\n";
		exit 1;
	}
	print FILEOFF $usbdevice;
	close FILEOFF;
	
	select(undef, undef, undef, 0.5);
	
	# Wait for the device underneath to disappear, it should if we turned it off OK
	my $isgone = 0;
	for (my $i = 1; $i <= 5; $i ++)
	{
		if (!(stat $underpath))
		{
			$isgone = 1;
			last;
		}
		
		print "Waiting $i\n";
		select(undef, undef, undef, 0.25);
	}
	
	# Failed to turn the device off, advance to the next hub, maybe that hub is built better
	if ($isgone != 1)
	{
		print "It did not turn off, trying next hub upstream\n";
		next;
	}
	
	print "Commanding $usbdevice ON\n";
	
	if (!(open(FILEON,  ">", $fileon)))
	{
		print "Unable to open $fileon: $!\n";
		exit 1;
	}
	print FILEON $usbdevice;
	close FILEON;
	
	select(undef, undef, undef, 0.5);

	# Wait for the device to reappear and its driver to reregister, allow extra time
	# Can't just check for the ttyUSB entry to reappear, because its number might have changed
	# Can't just check for the device path to reappear, because its driver might still be wedged and fail to initialize
	my $isback = 0;
	for (my $i = 1; $i <= 10; $i ++)
	{
		$linkpath = readlink $underdriver;
		if (defined $linkpath)
		{
			$isback = 1;
			last;
		}
		
		print "Waiting $i\n";
		select(undef, undef, undef, 0.25);
	}
	
	# Failed to turn the device back on, maybe it is really stuck, also try next hub upstream
	if ($isback != 1)
	{
		print "It did not turn back on, trying next hub upstream\n";
		next;
	}
	
	# If we made it this far, we're nearly done
	last;
}

if ($lowestdriver eq "")
{
	print "Something went wrong while trying to search for driver!\n";
	exit 1;
}

print "Validating\n";

# Allow time to wait for entire device tree to repopulate to what it was before
my $isrestored = 0;
for (my $i = 1; $i <= 20; $i ++)
{
	$linkpath = readlink $lowestdriver;
	if (defined $linkpath)
	{
		$isrestored = 1;
		last;
	}
	
	print "Waiting $i\n";
	select(undef, undef, undef, 0.25);
}

if ($isrestored != 1)
{
	print "The driver was not restored!\n";
	exit 1;
}

# The ttyUSB device should now be once again ready to use, although its number might have changed
print "Done!\n";
exit 0;
