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

function sendLoad($sms=false) {
	global $appdb;

	if(!empty($sms)) {
	} else return false;

	$hotlines = getAllHotline();

	if(!empty($hotlines[0]['sim_number'])) {
		$sim_number = $hotlines[0]['sim_number'];
	}

	print_r(array('$hotlines'=>$hotlines));

	$content = array();
	$content['smsinbox_simnumber'] = $sim_number;
	$content['smsinbox_message'] = $sms;

	$matched=smsCommandMatched($content);

	if(!empty($matched['matched']['$MOBILENUMBER'])) {
		$contactnumber = $matched['matched']['$MOBILENUMBER'];
		$matched['smsinbox']['smsinbox_contactnumber'] = $contactnumber;
	}

	if($matched===false) {
		return false;
	} 

	print_r(array('$matched'=>$matched));

	if(!empty($contactnumber)) {
		if($matched&&is_array($matched)&&!empty($matched['error'])) {
			$errmsg = smsdt()." ".getOption($matched['errmsg']);
			sendToOutBox($contactnumber,$content['smsinbox_simnumber'],$errmsg);
			return false;
		} else
		if($matched&&is_array($matched)) {
			if(!empty($matched['smscommands']['smscommands_action0'])&&is_callable($matched['smscommands']['smscommands_action0'],false,$callable_name)) {
				return $callable_name($matched);
			}
		}
	}

}

//$sms = 'qload at10 09493621618';

//$sms = 'qload at10 09493265223';

//$sms = 'qload at10 09165347754';

//sendLoad($sms);

function date2timestamp2($date, $format='m/d/Y', $timezone='Asia/Manila') {
	$old_timezone = date_default_timezone_get();
	date_default_timezone_set($timezone);
	$date = date_parse_from_format($format, $date);

	$hour = !empty($date['hour']) ? $date['hour'] : 0;
	$minute = !empty($date['minute']) ? $date['minute'] : 0;
	$second = !empty($date['second']) ? $date['second'] : 0;
	$month = !empty($date['month']) ? $date['month'] : 0;
	$day = !empty($date['day']) ? $date['day'] : 0;
	$year = !empty($date['year']) ? $date['year'] : 0;

	//pre($date);

	$day_start=mktime($hour,$minute,$second,$month,$day,$year);
	//$day_end=$day_start+(60*60*24);
	date_default_timezone_set($old_timezone);
	//return array('day_start'=>$day_start, 'day_end'=>$day_end);
	
	return $day_start;
}

echo date2timestamp2('01/12/2017 23:59:59','m/d/Y H:i:s');
echo "\n";














