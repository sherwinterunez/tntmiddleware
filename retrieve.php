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

function retrieveSMS($dev=false) {
	global $appdb;

	if(!empty($dev)) {
	} else return false;

	echo "\nretrieve starting.\n";

	$sms = new APP_SMS;

	//$portdevice = "/dev/cu.usbserial";

	if(!$sms->deviceInit($dev,115200)) {
		die('Error initializing device!');
	}

	$portdevice = $dev;

	echo "\nretrieve started.\n";

	if($sms->sendMessageOk("AT\r\n")) {
		//print_r($sms->history);
		$sms->clearHistory();
	} else {
		//print_r($sms->history);
		//die('1An error has occured!');
		echo "\nretrieve failed (AT).\n";
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
			echo "\nretrieve failed (invalid \$mobileNo).\n";
			$sms->deviceClose();
			return false;
		}
	} else {
		//print_r($sms->history);
		echo "\nretrieve failed (AT+CNUM).\n";
		$sms->deviceClose();
		return false;
		//die('An error has occured!');
	}

	print_r(array('$mobileNo'=>$mobileNo));

	$messages = array();

	if($sms->sendMessageReadPort("AT+CMGL=4\r\n", "(.+?)\r\nOK\r\n")) {
		$result = $sms->getResult();
		$result['flat'] = $sms->tocrlf($result[0]);
		//print_r(array('current'=>$sms->getCurrent(),'result'=>$result));
		
		//die;

		if(preg_match_all('/\+CMGL\:\s+(\d+)\,\d+\,\,\d+\r\n(.+?)\r\n/s', $result[0], $matches)) {
			//print_r(array('matches'=>$matches));

			//die;

			$pdu = new PduFactory();

			foreach($matches[2] as $key=>$pdustr) {

				$msg = $pdu->decode($pdustr);

				$msgid = $matches[1][$key];

				//print_r(array('$msgid'=>$msgid,'$msg'=>$msg));

				if(!empty($msg['number'])) {

					$number = strrev($msg['number']);

					if(strlen($number)>=10) {
						$number = strrev(substr($number, 0, 10));
					} else {
						$number = strrev($number);
					}

					if(!empty($msg['userDataHeader'])) {

						$udf = explode(' ', $msg['userDataHeader']);

						$total = $sms->HexToNum($udf[4]);
						$part = $sms->HexToNum($udf[5]);
						$ref = $udf[3];

						$cid = getContactIDByNumber($number);

						if($cid) {
							$messages[$number]['id'] = getContactIDByNumber($number);
							$messages[$number]['number'] = getContactNumber($messages[$number]['id']);
						} else {
							$messages[$number]['id'] = 0;
							$messages[$number]['number'] = $msg['number'];
						}

						$messages[$number]['ref'] = $udf[3];

						$messages[$number]['long'][$ref]['total'] = $total;
						$messages[$number]['long'][$ref]['parts'][$part] = $msg['message']; //$sms->history[0][$i+1];
						$messages[$number]['long'][$ref]['ids'][] = $msgid;
					} else {

						$cid = getContactIDByNumber($number);

						if($cid) {
							$messages[$number]['id'] = getContactIDByNumber($number);
							$messages[$number]['number'] = getContactNumber($messages[$number]['id']);
						} else {
							$messages[$number]['id'] = 0;
							$messages[$number]['number'] = $msg['number'];
						}

						$messages[$number]['short']['messages'][$msgid] = $msg['message']; //$sms->history[0][$i+1];
						//$messages[$number]['short']['ids'][] = $msgid; //$sms->history[0][$i+1];
					}
				}

			}
		}
	}

	if(!empty($messages)&&is_array($messages)) {

		//print_r(array('$messages'=>$messages));

		//die;

		foreach($messages as $num=>$data) {
			if(!empty($data['ref'])&&!empty($data['long'][$data['ref']]['total'])&&!empty($data['long'][$data['ref']]['parts'])&&is_array($data['long'][$data['ref']]['parts'])) {
				if($data['long'][$data['ref']]['total']==count($data['long'][$data['ref']]['parts'])) {
					$messages[$num]['long'][$data['ref']]['message'] = '';
					foreach($data['long'][$data['ref']]['parts'] as $part) {
						$messages[$num]['long'][$data['ref']]['message'] .= $part;
					}
				}	
			}
		}

		foreach($messages as $num=>$data) {
			if(!empty($data['ref'])&&!empty($data['long'][$data['ref']]['message'])) {

				$content = array();
				$content['smsinbox_contactsid'] = $data['id'];
				$content['smsinbox_contactnumber'] = $data['number'];
				$content['smsinbox_portdevice'] = $portdevice;
				$content['smsinbox_simnumber'] = $mobileNo;
				$content['smsinbox_message'] = $data['long'][$data['ref']]['message'];
				$content['smsinbox_unread'] = 1;

				$result = $appdb->insert('tbl_smsinbox',$content,'smsinbox_id');

				if(!empty($result['returning'][0]['smsinbox_id'])) {
					//return $result['returning'][0]['smsinbox_id'];
					foreach($data['long'][$data['ref']]['ids'] as $j) {
						$sms->sendMessageOk("AT+CMGD=".$j."\r\n");
					}
				}
			} else
			if(!empty($data['short']['messages'])&&is_array($data['short']['messages'])) {

				foreach($data['short']['messages'] as $msgid=>$vmsg) {

					$content = array();
					$content['smsinbox_contactsid'] = $data['id'];
					$content['smsinbox_contactnumber'] = $data['number'];
					$content['smsinbox_portdevice'] = $portdevice;
					$content['smsinbox_simnumber'] = $mobileNo;
					$content['smsinbox_message'] = $vmsg;
					$content['smsinbox_unread'] = 1;

					$result = $appdb->insert('tbl_smsinbox',$content,'smsinbox_id');

					if(!empty($result['returning'][0]['smsinbox_id'])) {
						//return $result['returning'][0]['smsinbox_id'];
						$sms->sendMessageOk("AT+CMGD=".$msgid."\r\n");
					}

				}

			}
		}		

		//print_r(array('$messages'=>$messages));
	}

	//print_r(array('history'=>$sms->getHistory()));

	$sms->deviceClose();

	$tstop = timer_stop();

	echo "\nretrieve done (".$tstop." secs).\n";

	return true;
}

if(getOption('$MAINTENANCE',false)) {
	die("\nretrieve: Server under maintenance.\n");
}

if(!empty($_GET['dev'])) {
	//pre(array('$_GET'=>$_GET));

	setSetting('STATUS_RETRIEVESMS_'.$_GET['dev'],'1');

	if(retrieveSMS($_GET['dev'])) {
		setSetting('STATUS_RETRIEVESMS_'.$_GET['dev'],'0');
	}
}

//retrieveSMS('/dev/ttyUSB1');


