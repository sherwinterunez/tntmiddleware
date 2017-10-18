<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: September 23, 2017
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

date_default_timezone_set('Asia/Manila');

define ("DEVICE_NOTSET", 0);
define ("DEVICE_SET", 1);
define ("DEVICE_OPENED", 2);

class APP_SMS extends SMS {

	public function deviceInit($device=false,$baudrate=57600) {

		if(!($this->deviceSet($device)&&$this->deviceOpen('w+')&&$this->setBaudRate($baudrate))) {
			return false;
		}

		return true;
	}

}

function RFIDRead($dev=false,$mobileNo=false,$ip='') {
	global $appdb;

	if(!empty($dev)&&!empty($mobileNo)&&!empty($ip)) {
	} else return false;

	//atLog('retrieve starting','retrievesms',$dev,$mobileNo,$ip,logdt());

	$sms = new APP_SMS;

	$sms->dev = $dev;
	$sms->mobileNo = $mobileNo;
	$sms->ip = $ip;

	if(!$sms->deviceInit($dev)) {
		$em = 'Error initializing device!';
		atLog($em,'retrievesms',$dev,$mobileNo,$ip,logdt());
		trigger_error("$dev $mobileNo $ip $em",E_USER_NOTICE);
		setSetting('STATUS_SIMERROR','1');
		return false;
	}

	echo 'RFIDRead!';

	//atLog('retrieve started','retrievesms',$dev,$mobileNo,$ip,logdt());

	//print_r(array('history'=>$sms->getHistory()));

	$settings_uhfrfidreadinterval = getOption('$SETTINGS_UHFRFIDREADINTERVAL',60);

	$sms->showbuf = true;
	$sms->readRFIDPort('hello',$settings_uhfrfidreadinterval,true);

	$history = $sms->getHistory();

	if(!empty($history)) {
		foreach($history as $a=>$b) {
			foreach($b as $k=>$v) {
				if($k=='timestamp') continue;
				$dt = logdt($b['timestamp']);
				trigger_error("$dev $mobileNo $ip $v",E_USER_NOTICE);
				doLog("$dt $dev $mobileNo $ip $v",$mobileNo);
				//atLog($v,'retrievesms',$dev,$mobileNo,$ip,logdt($b['timestamp']));
			}
		}
	}

	$sms->deviceClose();

	$tstop = timer_stop();

	//echo "\nretrieve done (".$tstop." secs) for $dev.\n";

	atLog('RFIDRead ('.$tstop.' secs)','retrievesms',$dev,$mobileNo,$ip,logdt());

	return true;
}

if(getOption('$MAINTENANCE',false)) {
	die("\nretrieve: Server under maintenance.\n");
}

$settings_useuhfrfidreader = getOption('$SETTINGS_USEUHFRFIDREADER',false);

//$_GET['dev'] = '/dev/ttyUSB1';
//$_GET['dev'] = '/dev/ttyUSB0';

//$_GET['dev'] = '/dev/ttyUSB1';
//$_GET['dev'] = '/dev/ttyUSB8';

//$_GET['dev'] = '/dev/tty.usbserial';

//$_GET['dev'] = '/dev/ttyUSB0';
//$_GET['ip'] = '192.168.1.200';
$_GET['sim'] = '09493621618';


//if(!empty($_GET['dev'])&&!empty($_GET['sim'])&&!empty($_GET['ip'])&&isSimEnabled($_GET['sim'])) {
	//setSetting('STATUS_RETRIEVESMS_'.$_GET['sim'],'1');

	if($settings_useuhfrfidreader&&RFIDRead($_GET['dev'],$_GET['sim'],$_GET['ip'])) {
		//setSetting('STATUS_RETRIEVESMS_'.$_GET['sim'],'0');
	}
//}
