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

function at_cpas($sms) {
	$sms->sendMessage("AT+CPAS\r\n");

	//$sms->readPort(5000);

	//print_r(array(preg_quote("+CPAS: 0\r")));

	if($sms->readPort("+CPAS: 0\r\n",60)) {
		return true;
	}
	return false;
}

function wind4($sms) {
	//print_r(array('wind4'=>'wind4'));
	if($sms->readPort("+WIND: 4\r\n", 120)) {
		return true;
	}
	return false;
}


class APP_SMS extends SMS {

	public function deviceInit($device=false,$baudrate=115200) {

		if(!($this->deviceSet($device)&&$this->deviceOpen('w+')&&$this->setBaudRate($baudrate))) {
			return false;
		}

		return true;
	}

}

function processSMS() {
	global $appdb;

	echo "\nprocess starting.\n";

	// QLOAD PASALOAD 09493621255 10 

	$clientkey = 'QLOAD';

	$productcodes = array('PASALOAD5','PASALOAD10','TALK100','SMART100','SMART200','SMART300');

	$regxproductcode = implode('|', $productcodes);

	if(!($result = $appdb->query("select * from tbl_smsinbox where smsinbox_deleted=0 and smsinbox_processed=0 order by smsinbox_id asc"))) {
		return false;
	}

	if(!empty($result['rows'][0]['smsinbox_id'])) {

		$inboxrows = $result['rows'];

		//print_r(array('$result'=>$result['rows']));

		foreach($inboxrows as $irow) {

			if(!empty($irow['smsinbox_contactsid'])) {
			} else {
				continue;
			}

			$str = trim($irow['smsinbox_message']);

			do {
				$str = str_replace('  ', ' ', trim($str));
			} while(preg_match('#\s\s#si', $str));

			$keys = explode(' ', $str);

			$invalid = false;

			if(!empty($keys[0])&&preg_match('/'.$clientkey.'/si',$keys[0],$match)) {

			} else {
				print_r(array('INVALID_KEYWORD','$str'=>$str));	
				$invalid = true;
			}

			if(!$invalid) {
				if(!empty($keys[1])&&preg_match('/'.$regxproductcode.'/si',$keys[1],$match)) {
					//print_r(array('$match'=>$match));
					$productcode = $match[0];
				} else {
					print_r(array('INVALID_PRODUCTCODE','$str'=>$str));	
					$invalid = true;
				}

			}

			if(!$invalid) {
				if(!empty($keys[2])&&preg_match('/(0)(\d{3})(\d{7})$/si',$keys[2],$match)) {
					$mobilenumber = $match[0];
					$mobilenetwork = $match[2];
				} else {
					print_r(array('INVALID_MOBILENUMBER','$str'=>$str));	
					$invalid = true;
				}
			}

			if(!$invalid) {
				if(!empty($keys[3])&&preg_match('/F(\d+)/si',$keys[3],$match)) {
					$flag = $match[1];
				}
			}

			if(!$invalid) {
				$req = array('VALID_REQUESTCODE','$clientkey'=>$clientkey,'$productcode'=>$productcode,'$mobilenumber'=>$mobilenumber,'$mobilenetwork'=>$mobilenetwork);

				if(!empty($flag)) {
					$req['$flag'] = $flag;
				}

				print_r($req);
			}

		}

	}

	$tstop = timer_stop();

	echo "\nprocess done (".$tstop." secs).\n";

}

print_r(array(getOption('$KEY_QLOAD'),getOption('$PRODUCT_SMART300'),getOption('$MOBILENUMBER')));

print_r(array(getOptionsWithType('KEYCODE'),getOptionValuesWithType('KEYCODE'),getOptionNamesWithType('KEYCODE')));

print_r(array(getOptionsWithType('PRODUCTCODE'),getOptionValuesWithType('PRODUCTCODE'),getOptionNamesWithType('PRODUCTCODE')));

//processSMS();




