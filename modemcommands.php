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

define('INCLUDE_PATH', ABS_PATH . 'includes/');

//require_once(ABS_PATH.'includes/index.php');
//require_once(ABS_PATH.'modules/index.php');

require_once(INCLUDE_PATH.'config.inc.php');
require_once(INCLUDE_PATH.'miscfunctions.inc.php');
require_once(INCLUDE_PATH.'functions.inc.php');
require_once(INCLUDE_PATH.'errors.inc.php');
require_once(INCLUDE_PATH.'error.inc.php');
require_once(INCLUDE_PATH.'db.inc.php');
require_once(INCLUDE_PATH.'pdu.inc.php');
require_once(INCLUDE_PATH.'pdufactory.inc.php');
require_once(INCLUDE_PATH.'utf8.inc.php');
require_once(INCLUDE_PATH.'sms.inc.php');
require_once(INCLUDE_PATH.'userfuncs.inc.php');

date_default_timezone_set('Asia/Manila');

//require_once('gsmsms.class.inc.php');
//require_once('Pdu/Pdu.php');
//require_once('Pdu/PduFactory.php');
//require_once('Utf8/Utf8.php');

define ("DEVICE_NOTSET", 0);
define ("DEVICE_SET", 1);
define ("DEVICE_OPENED", 2);

class APP_SMS extends SMS {

	public function deviceInit($device=false,$baudrate=115200) {

		if(!($this->deviceSet($device)&&$this->deviceOpen('w+')&&$this->setBaudRate($baudrate))) {
			return false;
		}

		return true;
	}

}

function modemCommands($dev=false) {
	global $appdb;

	if(!empty($dev)) {
	} else {
		return false;
	}

	echo "\nmodemCommands starting ($dev).\n";
 
	$sms = new APP_SMS;

	if(!$sms->deviceInit($dev,115200)) {
		die('Error initializing device!');
	}

	echo "\nmodemCommands started ($dev).\n";

	if($sms->sendMessageOk("AT\r\n")) {
		//print_r($sms->history);
		$sms->clearHistory();
	} else {
		//print_r($sms->history);
		//die('1An error has occured!');
		echo "\nmodemCommands failed (AT).\n";
		$sms->deviceClose();
		return false;
	}

	if($sms->sendMessageReadPort("AT+CNUM\r\n", "\+CNUM\:\s+(.+?)\r\nOK\r\n")) {
		$result = $sms->getResult();
		print_r(array('$result'=>$result));
		$sms->clearHistory();

		$cnum = explode(',', $result[1]);

		foreach($cnum as $v) {
			$v = str_replace('"', '', $v);
			if(($res=parseMobileNo($v))) {
				print_r(array('$res'=>$res));
				$mobileNo = '0'.$res[2].$res[3];
				//$mobileNetwork = getNetworkName($mobileNo);
			}
		}

		if(empty($mobileNo)) {
			echo "\nmodemCommands failed (invalid \$mobileNo).\n";
			$sms->deviceClose();
			return false;
		}
	} else {
		//print_r($sms->history);
		echo "\nmodemCommands failed (AT+CNUM) ($dev).\n";
		$sms->deviceClose();
		return false;
		//die('An error has occured!');
	}

	doModemCommands($sms,$mobileNo);

	$sms->deviceClose();

	$tstop = timer_stop();

	print_r(array('$mobileNo'=>$mobileNo));

	echo "\nmodemCommands (".$tstop." secs).\n";

	return true;
}

/*
print_r(array(getOption('$KEY_QLOAD'),getOption('$PRODUCT_SMART300'),getOption('$MOBILENUMBER')));

print_r(array(getOptionsWithType('KEYCODE'),getOptionValuesWithType('KEYCODE'),getOptionNamesWithType('KEYCODE')));

print_r(array(getOptionsWithType('PRODUCTCODE'),getOptionValuesWithType('PRODUCTCODE'),getOptionNamesWithType('PRODUCTCODE')));
*/

$_GET['dev'] = '/dev/ttyUSB7';

if(!empty($_GET['dev'])) {

	modemCommands($_GET['dev']);

}

//processSMSCommands('/dev/ttyUSB0');




