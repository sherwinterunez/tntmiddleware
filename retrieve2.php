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

function retrieveSMS($dev=false,$mobileNo=false,$ip='') {
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
		return false;
	}

	//atLog('retrieve started','retrievesms',$dev,$mobileNo,$ip,logdt());

	if(!$sms->at()) {
		$em = 'Retrieve failed (AT)';
		atLog($em,'retrievesms',$dev,$mobileNo,$ip,logdt());
		trigger_error("$dev $mobileNo $ip $em",E_USER_NOTICE);

		$sms->deviceClose();
		return false;
	}

	$sms->clearHistory();

	//print_r(array('at_cnum'=>$sms->cnum()));

	$messages = array();

	if(!$sms->cmgl_4()) {

		$sms->clearHistory();

	} else {

		$lastResult = $sms->getLastResult(0);

		//print_r(array('$lastResult'=>$lastResult));

		if(!preg_match_all('/\+CMGL\:\s+(\d+)\,\d+\,\,\d+\r\n(.+?)\r\n/s', $lastResult, $matches)) {

			$sms->clearHistory();

		} else {

			//print_r(array('$matches'=>$matches));

			$pdu = new PduFactory();

			foreach($matches[2] as $key=>$pdustr) {

				$msg = $pdu->decode($pdustr);

				//print_r(array('$msg'=>$msg));

				$msgid = $matches[1][$key];

				if(!empty($msg['number'])) {

					if($res=parseMobileNo($msg['number'])) {

						$number = '0' . $res[2] . $res[3];

						$msg['number'] = $number;

					} else {

						$number = strrev($msg['number']);

						if(strlen($number)>=10) {
							$number = strrev(substr($number, 0, 10));
						} else {
							$number = strrev($number);
						}
					}

					//print_r(array('$msg[number]'=>$msg['number'],'$number'=>$number));

					if(!empty($msg['userDataHeader'])) {

						$udf = explode(' ', trim($msg['userDataHeader']));

						//if(isset($udf[3])) {

						$udflen = sizeof($udf) - 1;

						//print_r(array('$udflen'=>$udflen,'sizeof'=>sizeof($udf),'$udf'=>$udf));

						//if(isset($udf[3])) {

						if(isset($udf[$udflen-2])) {

							//$total = $sms->HexToNum($udf[4]);
							//$part = $sms->HexToNum($udf[5]);
							//$ref = $udf[3];

							$total = $sms->HexToNum($udf[$udflen-1]);
							$part = $sms->HexToNum($udf[$udflen]);
							$ref = $udf[$udflen-2];

							$cid = getContactIDByNumber($number);

							if($cid) {
								$messages[$number]['id'] = getContactIDByNumber($number);
								$messages[$number]['number'] = getContactNumber($messages[$number]['id']);
							} else {
								$messages[$number]['id'] = 0;
								$messages[$number]['number'] = $msg['number'];
							}

							$messages[$number]['ref'] = $ref;

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

						}

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

					} // if(!empty($msg['userDataHeader'])) {

				} // if(!empty($msg['number'])) {

			} // foreach($matches[2] as $key=>$pdustr) {

		} // if(!preg_match_all('/\+CMGL\:\s+(\d+)\,\d+\,\,\d+\r\n(.+?)\r\n/s', $lastResult, $matches)) {

	} // if(!$sms->cmgl_4()) {

	if(!empty($messages)&&is_array($messages)) {

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

		print_r(array('$messages'=>$messages));

		$eloadnumbers = explode(',', getOption('$ELOAD_NUMBERS'));

		foreach($messages as $num=>$data) {
			if(!empty($data['ref'])&&!empty($data['long'][$data['ref']]['message'])) {

				$content = array();
				$content['smsinbox_contactsid'] = $data['id'];
				$content['smsinbox_contactnumber'] = $data['number'];
				$content['smsinbox_simnumber'] = $mobileNo;
				$content['smsinbox_message'] = $data['long'][$data['ref']]['message'];
				$content['smsinbox_unread'] = 1;

				processSMS($content);

				if(in_array($data['number'],$eloadnumbers)) {
					$content['smsinbox_eload'] = 1;
				}

				//$result = $appdb->insert('tbl_smsinbox',$content,'smsinbox_id');

				if(!($result = $appdb->insert('tbl_smsinbox',$content,'smsinbox_id'))) {
					//print_r(array('$appdb->lasterror'=>$appdb->lasterror));
					atLog('$appdb->lasterror ('.$appdb->lasterror.')','retrievesms',$dev,$mobileNo,$ip,logdt());
				}

				//if(!empty($result['returning'][0]['smsinbox_id'])) {
					//return $result['returning'][0]['smsinbox_id'];
					foreach($data['long'][$data['ref']]['ids'] as $j) {
						$sms->sendMessageOk("AT+CMGD=".$j."\r\n");
					}
				//}
			} else
			if(!empty($data['short']['messages'])&&is_array($data['short']['messages'])) {

				foreach($data['short']['messages'] as $msgid=>$vmsg) {

					$content = array();
					$content['smsinbox_contactsid'] = $data['id'];
					$content['smsinbox_contactnumber'] = $data['number'];
					$content['smsinbox_simnumber'] = $mobileNo;
					$content['smsinbox_message'] = $vmsg;
					$content['smsinbox_unread'] = 1;

					processSMS($content);

					if(in_array($data['number'],$eloadnumbers)) {
						$content['smsinbox_eload'] = 1;
					}

					//print_r(array('$content'=>$content));

					if(!($result = $appdb->insert('tbl_smsinbox',$content,'smsinbox_id'))) {
						//print_r(array('$appdb->lasterror'=>$appdb->lasterror,'$appdb->lastquery'=>json_encode($appdb->lastquery)));
						atLog('$appdb->lasterror ('.$appdb->lasterror.')','retrievesms',$dev,$mobileNo,$ip,logdt());
					}

					//print_r(array('$result'=>$result));

					//if(!empty($result['returning'][0]['smsinbox_id'])) {
						//return $result['returning'][0]['smsinbox_id'];
						$sms->sendMessageOk("AT+CMGD=".$msgid."\r\n");
					//}

				}

			}
		}		

		//print_r(array('$messages'=>$messages));
	}

	//print_r(array('history'=>$sms->getHistory()));

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

	atLog('retrieve done ('.$tstop.' secs)','retrievesms',$dev,$mobileNo,$ip,logdt());

	return true;
}

if(getOption('$MAINTENANCE',false)) {
	die("\nretrieve: Server under maintenance.\n");
}

//$_GET['dev'] = '/dev/ttyUSB1';
//$_GET['dev'] = '/dev/ttyUSB0';

//$_GET['dev'] = '/dev/ttyUSB1';
//$_GET['dev'] = '/dev/ttyUSB8';


if(!empty($_GET['dev'])&&!empty($_GET['sim'])&&!empty($_GET['ip'])&&isSimEnabled($_GET['sim'])) {
	setSetting('STATUS_RETRIEVESMS_'.$_GET['sim'],'1');

	if(retrieveSMS($_GET['dev'],$_GET['sim'],$_GET['ip'])) {
		setSetting('STATUS_RETRIEVESMS_'.$_GET['sim'],'0');
	}
}



