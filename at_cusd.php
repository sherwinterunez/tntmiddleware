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

define ("DEVICE_NOTSET", 0);
define ("DEVICE_SET", 1);
define ("DEVICE_OPENED", 2);

$map = array();

class APP_SMS extends SMS {

	public function deviceInit($device=false,$baudrate=115200) {

		if(!($this->deviceSet($device)&&$this->deviceOpen('w+')&&$this->setBaudRate($baudrate))) {
			return false;
		}

		return true;
	}

}

function check($sms=false) {

	if(empty($sms)) die('Error!');

	$repeat = 100;

	do {

		$repeat--;

		if($sms->sendMessageReadPort("AT+CUSD=1,0\r\n", "\+CUSD\:.+?\r\n")) {

			$result = $sms->getResult();
			$result['flat'] = $sms->tocrlf($result[0]);

			print_r(array('$result'=>$result));

			if(preg_match("/\+CUSD\:\s+(\d+)\r\n/s",$result[0],$match)) {

				print_r(array('$match'=>$match));

				if(intval($match[1])===4) break;

			}

		}

	} while($repeat>0);

}

function doMap($sms=false,$cusdId=0) {

	$map = array();

	if(empty($sms)) die('Error!');

	if($cusdId==0) {

		check($sms);

		$COMMAND = "AT+CUSD=1,*343#\r\n";

		print_r(array('$COMMAND'=>$COMMAND));

		if($sms->sendMessageReadPort($COMMAND, "\+CUSD\:\s+\d+\,\"(.+?)\".+?\r\n")) {

			$result = $sms->getResult();
			$result['flat'] = $sms->tocrlf($result[0]);

			//$result[1] = str_replace('0:BACK 9:MORE', "0:BACK\n9:MORE", $result[1]);

			print_r(array('$result'=>$result));

			if(preg_match_all("/(\d+)\:(.+)/", $result[1], $matches)) {
				print_r(array('$matches'=>$matches));
				foreach($matches[1] as $k=>$v) {
					if($v!=0) {
						$map[$cusdId][$v]['menu_id'] = $v;
						$map[$cusdId][$v]['menu_name'] = $matches[2][$k];
						$map[$cusdId][$v]['menu_menus'] = doMap($sms,$v);
					} else {
						$COMMAND = "AT+CUSD=1,0\r\n";

						print_r(array('$COMMAND0'=>$COMMAND));

						if($sms->sendMessageReadPort($COMMAND, "\+CUSD\:.+?\r\n")) {

							$result = $sms->getResult();
							$result['flat'] = $sms->tocrlf($result[0]);

							print_r(array('$result'=>$result));

							if(preg_match("/\+CUSD\:\s+(\d+)\r\n/s",$result[0],$match)) {

								print_r(array('$match'=>$match));

							}

						}
					}
				}
			}

		}

		return $map;
	} else {

		$COMMAND = "AT+CUSD=1,".$cusdId."\r\n";

		print_r(array('$COMMAND'=>$COMMAND));

		if($sms->sendMessageReadPort($COMMAND, "\+CUSD\:\s+\d+\,\"(.+?)\".+?\r\n")) {

			$result = $sms->getResult();
			$result['flat'] = $sms->tocrlf($result[0]);

			//$result[1] = str_replace('0:BACK 9:MORE', "0:BACK\n9:MORE", $result[1]);

			print_r(array('$result'=>$result));

			if(preg_match("/Enter\s+number/", $result[0])) {
				$map = doMap($sms,'09493621618');

				$COMMAND = "AT+CUSD=1,0\r\n";

				print_r(array('$COMMAND1'=>$COMMAND));

				if($sms->sendMessageReadPort($COMMAND, "\+CUSD\:.+?\r\n")) {

					$result = $sms->getResult();
					$result['flat'] = $sms->tocrlf($result[0]);

					print_r(array('$result'=>$result));

					if(preg_match("/\+CUSD\:\s+(\d+)\r\n/s",$result[0],$match)) {

						print_r(array('$match'=>$match));

						//if(intval($match[1])===4) break;

					}

				}

			} else
			if(preg_match("/Enter\s+Amount/", $result[0])) {
				$map = doMap($sms,1);

				$COMMAND = "AT+CUSD=1,0\r\n";

				print_r(array('$COMMAND2'=>$COMMAND));

				if($sms->sendMessageReadPort($COMMAND, "\+CUSD\:.+?\r\n")) {

					$result = $sms->getResult();
					$result['flat'] = $sms->tocrlf($result[0]);

					print_r(array('$result'=>$result));

					if(preg_match("/\+CUSD\:\s+(\d+)\r\n/s",$result[0],$match)) {

						print_r(array('$match'=>$match));

						//if(intval($match[1])===4) break;

					}

				}

			} else
			if(preg_match("/1\:Load\n0\:BACK/", $result[0])) {
				///$map = doMap($sms,1);

				$COMMAND = "AT+CUSD=1,0\r\n";

				print_r(array('$COMMAND3'=>$COMMAND));

				if($sms->sendMessageReadPort($COMMAND, "\+CUSD\:.+?\r\n")) {

					$result = $sms->getResult();
					$result['flat'] = $sms->tocrlf($result[0]);

					print_r(array('$result'=>$result));

					if(preg_match("/\+CUSD\:\s+(\d+)\r\n/s",$result[0],$match)) {

						print_r(array('$match'=>$match));

						//if(intval($match[1])===4) break;

					}

				}

			} else
			if(preg_match("/1\:Register\n0\:BACK/", $result[0])) {
				///$map = doMap($sms,1);

				$COMMAND = "AT+CUSD=1,0\r\n";

				print_r(array('$COMMAND4'=>$COMMAND));

				if($sms->sendMessageReadPort($COMMAND, "\+CUSD\:.+?\r\n")) {

					$result = $sms->getResult();
					$result['flat'] = $sms->tocrlf($result[0]);

					print_r(array('$result'=>$result));

					if(preg_match("/\+CUSD\:\s+(\d+)\r\n/s",$result[0],$match)) {

						print_r(array('$match'=>$match));

						//if(intval($match[1])===4) break;

					}

				}

			} else			
			if(preg_match("/1\:Get\s+All\s+Text\s+12\n2\:Continue\s+to\s+buy\s+All\s+Text\s+10\s+Plus\n0\:BACK/", $result[0])) {
				///$map = doMap($sms,1);

				$COMMAND = "AT+CUSD=1,0\r\n";

				print_r(array('$COMMAND5'=>$COMMAND));

				if($sms->sendMessageReadPort($COMMAND, "\+CUSD\:.+?\r\n")) {

					$result = $sms->getResult();
					$result['flat'] = $sms->tocrlf($result[0]);

					print_r(array('$result'=>$result));

					if(preg_match("/\+CUSD\:\s+(\d+)\r\n/s",$result[0],$match)) {

						print_r(array('$match'=>$match));

						//if(intval($match[1])===4) break;

					}

				}

			} else		
			if(preg_match_all("/(\d+)\:(.+)/", $result[1], $matches)) {
				print_r(array('$matches'=>$matches));
				foreach($matches[1] as $k=>$v) {
					if($v!=0) {
						$map[$cusdId][$v]['menu_id'] = $v;
						$map[$cusdId][$v]['menu_name'] = $matches[2][$k];
						$map[$cusdId][$v]['menu_menus'] = doMap($sms,$v);
					} else {

						$COMMAND = "AT+CUSD=1,0\r\n";

						print_r(array('$COMMAND6'=>$COMMAND));

						if($sms->sendMessageReadPort($COMMAND, "\+CUSD\:.+?\r\n")) {

							$result = $sms->getResult();
							$result['flat'] = $sms->tocrlf($result[0]);

							print_r(array('$result'=>$result));

							if(preg_match("/\+CUSD\:\s+(\d+)\r\n/s",$result[0],$match)) {

								print_r(array('$match'=>$match));

							}

						}

					}
				}

			} else {

				/*$COMMAND = "AT+CUSD=1,0\r\n";

				print_r(array('$COMMAND5'=>$COMMAND));

				if($sms->sendMessageReadPort($COMMAND, "\+CUSD\:.+?\r\n")) {

					$result = $sms->getResult();
					$result['flat'] = $sms->tocrlf($result[0]);

					print_r(array('$result'=>$result));

					if(preg_match("/\+CUSD\:\s+(\d+)\r\n/s",$result[0],$match)) {

						print_r(array('$match'=>$match));

						//if(intval($match[1])===4) break;

					}

				}*/

			}

			/*if(preg_match("/Enter\s+number/", $result[0])) {

				if($sms->sendMessageReadPort("AT+CUSD=1,09493621618\r\n", "\+CUSD\:\s+\d+\,\"(.+?)\".+?\r\n")) {

					$result = $sms->getResult();
					$result['flat'] = $sms->tocrlf($result[0]);

					print_r(array('$result'=>$result));


				}

			}*/

		}

		return $map;

	}

}

function cusdMap($dev=false) {
	global $map;

	if(!empty($dev)) {
	} else {
		return false;
	}

	echo "\nexec starting.\n";
 
	$sms = new APP_SMS;

	if(!$sms->deviceInit($dev,115200)) {
		die('Error initializing device!');
	}

	echo "\nexec started.\n";

	$map = doMap($sms,0);

	print_r(array('$map'=>$map));

	print_r(array('history'=>$sms->getHistory()));

	$sms->deviceClose();

	$tstop = timer_stop();

	echo "\nexec done (".$tstop." secs).\n";

}

cusdMap("/dev/ttyUSB0");








