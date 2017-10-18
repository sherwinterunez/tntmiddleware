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

function processSMSCommands($dev=false,$mobileNo=false,$ip='') {
	global $appdb;


/////

	if(!empty($dev)&&!empty($mobileNo)&&!empty($ip)) {
	} else return false;

	//echo "\nprocessOutbox started: ($dev) ($mobileNo).\n";

	//atLog('processSMSCommands starting','smscommands',$dev,$mobileNo,$ip,logdt());

	$sms = new APP_SMS;

	$sms->dev = $dev;
	$sms->mobileNo = $mobileNo;
	$sms->ip = $ip;

	if(!$sms->deviceInit($dev)) {
		$em = 'Error initializing device!';
		atLog($em,'smscommands',$dev,$mobileNo,$ip,logdt());
		trigger_error("$dev $mobileNo $ip $em",E_USER_NOTICE);
		setSetting('STATUS_SIMERROR','1');
		return false;
	}

	if(!$sms->at()) {

		$em = 'processSMSCommands failed (AT)';
		atLog($em,'smscommands',$dev,$mobileNo,$ip,logdt());
		trigger_error("$dev $mobileNo $ip $em",E_USER_NOTICE);

		if(!$sms->atgt()) {
			$em = 'processSMSCommands failed (AT)';
			atLog($em,'smscommands',$dev,$mobileNo,$ip,logdt());
			trigger_error("$dev $mobileNo $ip $em",E_USER_NOTICE);

			$sms->deviceClose();
			return false;
		} else {
			$em = 'processSMSCommands (ATGT) success!';
			atLog($em,'smscommands',$dev,$mobileNo,$ip,logdt());
			trigger_error("$dev $mobileNo $ip $em",E_USER_NOTICE);
		}
	}

	$sms->clearHistory();

	doSMSCommands($sms,$mobileNo,$ip);

	$history = $sms->getHistory();

	if(!empty($history)) {
		foreach($history as $a=>$b) {
			foreach($b as $k=>$v) {
				if($k=='timestamp') continue;
				$dt = logdt($b['timestamp']);
				trigger_error("$dev $mobileNo $ip $v",E_USER_NOTICE);
				doLog("$dt $dev $mobileNo $ip $v",$mobileNo);
				//atLog($v,'smscommands',$dev,$mobileNo,$ip,$dt);
			}
		}
	}

	$sms->deviceClose();

	$tstop = timer_stop();

	atLog('processSMSCommands done ('.$tstop.' secs)','smscommands',$dev,$mobileNo,$ip,logdt());

	return true;
}

if(getOption('$MAINTENANCE',false)) {
	die("\processSMSCommands: Server under maintenance.\n");
}

//$_GET['dev'] = '/dev/ttyUSB0';

//$_GET['dev'] = '/dev/ttyUSB9';

if(!empty($_GET['dev'])&&!empty($_GET['sim'])&&!empty($_GET['ip'])&&isSimEnabled($_GET['sim'])) {
	//pre(array('$_GET'=>$_GET));

	setSetting('STATUS_PROCESSSMSCOMMANDS_'.$_GET['sim'],'1');

	if(processSMSCommands($_GET['dev'],$_GET['sim'],$_GET['ip'])) {
		setSetting('STATUS_PROCESSSMSCOMMANDS_'.$_GET['sim'],'0');
	}

}
