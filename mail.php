<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: February 23, 2011
*
* Description:
*
* Application entry point.
*
*/

//define('ANNOUNCE', true);

error_reporting(E_ALL);

ini_set("max_execution_time", 300);

define('APPLICATION_RUNNING', true);

define('ABS_PATH', dirname(__FILE__) . '/');

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

//define('INCLUDE_PATH', ABS_PATH . 'includes/');

require_once(ABS_PATH.'includes/index.php');
//require_once(ABS_PATH.'modules/index.php');

/*require_once(INCLUDE_PATH.'config.inc.php');
require_once(INCLUDE_PATH.'miscfunctions.inc.php');
require_once(INCLUDE_PATH.'functions.inc.php');
require_once(INCLUDE_PATH.'errors.inc.php');
require_once(INCLUDE_PATH.'error.inc.php');
require_once(INCLUDE_PATH.'db.inc.php');
require_once(INCLUDE_PATH.'pdu.inc.php');
require_once(INCLUDE_PATH.'pdufactory.inc.php');
require_once(INCLUDE_PATH.'utf8.inc.php');
require_once(INCLUDE_PATH.'sms.inc.php');
require_once(INCLUDE_PATH.'userfuncs.inc.php');*/

$timezone = getOption('TIMEZONE','Asia/Manila');

date_default_timezone_set($timezone);

 /*----------------------------------------------------------------------------*\
|*  Email settings for sending all emails from your website forms.              *|
 \*============================================================================*/

// Choose here whether to use php mail() function or your SMTP server (recommended) to send the email.
// Use 'smtp' for better reliability, or use 'phpmail' for quick + easy setup with lower reliability.
$emailMethod                = 'smtp'; // REQUIRED value. Options: 'smtp' , 'phpmail'

// Outgoing Server Settings - replace values on the right of the = sign with your own.
// These 3 settings are only required if you choose 'smtp' for emailMethod above.
//$outgoingServerAddress      = 'smtpout.secureserver.net'; // Consult your hosting provider.
//$outgoingServerPort         = '80';                  // Options: '587' , '25' - Consult your hosting provider.

$outgoingServerAddress      = 'smtpout.secureserver.net'; // Consult your hosting provider.
$outgoingServerPort         = '465';                  // Options: '587' , '25' - Consult your hosting provider.
$outgoingServerSecurity     = 'ssl';                 // Options: 'ssl' , 'tls' , null - Consult your hosting provider.

//$outgoingServerAddress      = 'mail.obisph.com'; // Consult your hosting provider.
//$outgoingServerPort         = '587';                  // Options: '587' , '25' - Consult your hosting provider.
//$outgoingServerSecurity     = 'tls';                 // Options: 'ssl' , 'tls' , null - Consult your hosting provider.

// Sending Account Settings - replace these details with an email account held on the SMTP server entered above.
// These 2 settings are only required if you choose 'smtp' for emailMethod above.
$sendingAccountUsername     = 'support@mep.technology';
$sendingAccountPassword     = 'meptech123!';

//$sendingAccountUsername     = 'sherwin@obisph.com';
//$sendingAccountPassword     = 'Joshua0412';

// Recipient (To:) Details  - Change this to the email details of who will receive all the emails from the website.
//$recipientEmail             = 'support@mep.technology'; // REQUIRED value.
//$recipientName              = 'Support';             // REQUIRED value.

$recipientEmail             = 'sherwinterunez@yahoo.com'; //'sherwin@obisph.com'; // REQUIRED value.
$recipientName              = 'Support';             // REQUIRED value.

// Email details            - Change these to suit your website needs
$websiteName                = 'AscendSMS';                // REQUIRED value. This is used when a name or email is not collected from the website form
$emailSubject               = 'A message from '.$websiteName; // REQUIRED value. Subject of the email that the recipient will see

//$timeZone					= 'Asia/Manila';         // OPTIONAL, but some servers require this to be set.
                                                             // See a list of all supported timezones at: http://php.net/manual/en/timezones.php
 /*----------------------------------------------------------------------------*\
|*  You do not need to edit anything below this line, the rest is automatic.    *|
 \*============================================================================*/

pre(array('$_SERVER'=>$_SERVER,'$_POST'=>$_POST,'$_GET'=>$_GET));

include('includes/maillib/mail_sender.php');

?>
