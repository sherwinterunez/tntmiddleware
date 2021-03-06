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

date_default_timezone_set('Asia/Manila');

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

function checkSignal($dev=false,$mobileNo=false,$ip='') {
	global $appdb;

	if(!empty($dev)&&!empty($mobileNo)&&!empty($ip)) {
	} else return false;

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

	//atLog('retrieve started','retrievesms',$dev,$mobileNo,$ip,logdt());

	if(!$sms->at()) {
		$em = 'Checksignal failed (AT)';
		atLog($em,'retrievesms',$dev,$mobileNo,$ip,logdt());
		trigger_error("$dev $mobileNo $ip $em",E_USER_NOTICE);

		$ctr = getOption('STATUS_AT_ERROR_'.$mobileNo,0);

		$ctr++;

		setSetting('STATUS_AT_ERROR_'.$mobileNo,$ctr);

		$sms->deviceClose();
		return false;
	}

	setSetting('STATUS_AT_ERROR_'.$mobileNo,'0');

	$sms->clearHistory();

	//if($sms->sendMessageReadPort("AT+CSQ\r\n", "\+CSQ\:\s+(.+?)\r\nOK\r\n",10)) {
	if($sms->sendMessageReadPort("AT+CSQ\r\n", "\+CSQ\:\s+(.+?)\r\n",10)) {
		$result = $sms->getResult();

		if(preg_match('#(\d+).+?(\d+)#si',$result[1],$match)) {
			//print_r(array('$match'=>$match));
			setSetting('SIGNAL_'.$mobileNo,$match[1].','.$match[2]);
		}
		//print_r(array('AT+CSQ'=>$result));
		//$flag=true;
		//break;
	} //else {
			//$sms->clearHistory();
		//}


	//if(getOption('STATUS_SIMERROR',false)) {
		//pre(array('$dev'=>$dev,'$mobileNo'=>$mobileNo,'$ip'=>$ip,'STATUS_SIMERROR'=>getOption('STATUS_SIMERROR',false)));
		//echo 'STATUS_SIMERROR';
	//}

	$sms->deviceClose();

	return true;
}

if(getOption('$MAINTENANCE',false)) {
	die("\nchecksignal: Server under maintenance.\n");
}


//$_GET['dev'] = '/dev/ttyUSB1';
//$_GET['dev'] = '/dev/ttyUSB8';


if(!empty($_GET['dev'])&&!empty($_GET['sim'])&&!empty($_GET['ip'])&&isSimEnabled($_GET['sim'])) {
	setSetting('STATUS_CHECKSIGNAL_'.$_GET['sim'],'1');

	//setSetting('STATUS_SIMERROR','1');
	//setSetting('STATUS_SIMERROR','0');

	if(checkSignal($_GET['dev'],$_GET['sim'],$_GET['ip'])) {
		setSetting('STATUS_CHECKSIGNAL_'.$_GET['sim'],'0');
	}
}
