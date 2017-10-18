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
		setSetting('STATUS_SIMERROR','1');
		return false;
	}

	//atLog('retrieve started','retrievesms',$dev,$mobileNo,$ip,logdt());

	if(!$sms->at()) {

		$em = 'Retrieve failed (AT)';
		atLog($em,'retrievesms',$dev,$mobileNo,$ip,logdt());
		trigger_error("$dev $mobileNo $ip $em",E_USER_NOTICE);

		if(!$sms->atgt()) {
			$em = 'Retrieve failed (ATGT)';
			atLog($em,'retrievesms',$dev,$mobileNo,$ip,logdt());
			trigger_error("$dev $mobileNo $ip $em",E_USER_NOTICE);

			$sms->deviceClose();
			return false;
		} else {
			$em = 'Retrieve (ATGT) success!';
			atLog($em,'retrievesms',$dev,$mobileNo,$ip,logdt());
			trigger_error("$dev $mobileNo $ip $em",E_USER_NOTICE);
		}
	}

	setSetting('STATUS_SIMERROR','0');

	$sms->clearHistory();

	//print_r(array('at_cnum'=>$sms->cnum()));

	$messages = array();
	$eloadnumbers = array();

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

			$optionx = getOption('$ELOAD_NUMBERS',array());

			if(!empty($optionx)) {
				$eloadnumbers = explode(',', $optionx);
			}

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

						$msg['number'] = $number;
					}

					//print_r(array('$msg[number]'=>$msg['number'],'$number'=>$number));

					if(!empty($msg['userDataHeader'])) {

						$udf = explode(' ', trim($msg['userDataHeader']));

						//if(isset($udf[3])) {

						$udflen = sizeof($udf) - 1;

						//print_r(array('$udflen'=>$udflen,'sizeof'=>sizeof($udf),'$udf'=>$udf));

						//if(isset($udf[3])) {

						if(isset($udf[$udflen-2])) {

							$total = $sms->HexToNum($udf[$udflen-1]);
							$part = $sms->HexToNum($udf[$udflen]);
							$ref = $udf[$udflen-2];

							$cid = getContactIDByNumber($number);

							if($cid) {
								$smsinboxtemp_contactsid = getContactIDByNumber($number);
								$smsinboxtemp_contactnumber = getContactNumber($smsinboxtemp_contactsid);
							} else {
								$smsinboxtemp_contactsid = 0;
								$smsinboxtemp_contactnumber = $number; //$msg['number'];
							}

							$smsinboxtemp_udhref = $ref;
							$smsinboxtemp_udhtotal = $total;
							$smsinboxtemp_udhpart = $part;
							$smsinboxtemp_message = $msg['message']; //$sms->history[0][$i+1];

							$content = array();
							$content['smsinboxtemp_contactsid'] = $smsinboxtemp_contactsid;
							$content['smsinboxtemp_contactnumber'] = $smsinboxtemp_contactnumber;
							$content['smsinboxtemp_simnumber'] = $mobileNo;
							$content['smsinboxtemp_message'] = $smsinboxtemp_message;
							$content['smsinboxtemp_udh'] = trim($msg['userDataHeader']);
							$content['smsinboxtemp_udhref'] = $smsinboxtemp_udhref;
							$content['smsinboxtemp_udhtotal'] = $smsinboxtemp_udhtotal;
							$content['smsinboxtemp_udhpart'] = $smsinboxtemp_udhpart;

							if(in_array($smsinboxtemp_contactnumber,$eloadnumbers)) {
								$content['smsinboxtemp_eload'] = 1;
							}

							if(!($result = $appdb->insert('tbl_smsinboxtemp',$content,'smsinboxtemp_id'))) {
								atLog('$appdb->lasterror ('.$appdb->lasterror.')','retrievesms',$dev,$mobileNo,$ip,logdt());
							}

							$sms->sendMessageOk("AT+CMGD=".$msgid."\r\n");

						} else {

							$cid = getContactIDByNumber($number);

							if($cid) {
								$messages[$number]['id'] = getContactIDByNumber($number);
								$messages[$number]['number'] = getContactNumber($messages[$number]['id']);
							} else {
								$messages[$number]['id'] = 0;
								$messages[$number]['number'] = $number; //$msg['number'];
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
							$messages[$number]['number'] = $number; //$msg['number'];
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

					if(!($result = $appdb->insert('tbl_smsinbox',$content,'smsinbox_id'))) {
						atLog('$appdb->lasterror ('.$appdb->lasterror.')','retrievesms',$dev,$mobileNo,$ip,logdt());
					}

					$sms->sendMessageOk("AT+CMGD=".$msgid."\r\n");
				}

			}
		}

		//print_r(array('$messages'=>$messages));
	}

	$sql = "select *,(extract(epoch from now()) - extract(epoch from smsinboxtemp_timestamp))::integer as elapsedtime from tbl_smsinboxtemp where smsinboxtemp_simnumber='$mobileNo' order by smsinboxtemp_contactsid,smsinboxtemp_udhpart::integer asc";

	if(!($result = $appdb->query($sql))) {
		return false;
	}

	//print_r(array('$sql'=>$sql));

	if(!empty($result['rows'][0]['smsinboxtemp_id'])) {

		$messages = array();

		$longsms = $result['rows'];

		foreach($longsms as $k=>$v) {
			$smsinboxtemp_id = $v['smsinboxtemp_id'];
			$smsinboxtemp_contactnumber = $v['smsinboxtemp_contactnumber'];

			$cid = getContactIDByNumber($smsinboxtemp_contactnumber);

			if($cid) {
				$smsinboxtemp_contactsid = $cid;
			} else {
				$smsinboxtemp_contactsid = 0;
			}

			$smsinboxtemp_simnumber = $v['smsinboxtemp_simnumber'];
			$smsinboxtemp_message = $v['smsinboxtemp_message'];
			$smsinboxtemp_udhref = $v['smsinboxtemp_udhref'];
			$smsinboxtemp_udhtotal = $v['smsinboxtemp_udhtotal'];
			$smsinboxtemp_udhpart = $v['smsinboxtemp_udhpart'];
			$elapsedtime = $v['elapsedtime'];

			$messages[$smsinboxtemp_contactnumber][$smsinboxtemp_udhref]['ids'][$smsinboxtemp_udhpart] = $smsinboxtemp_id;
			$messages[$smsinboxtemp_contactnumber][$smsinboxtemp_udhref]['udhtotal'] = intval($smsinboxtemp_udhtotal);
			$messages[$smsinboxtemp_contactnumber][$smsinboxtemp_udhref]['contactsid'] = $smsinboxtemp_contactsid;
			$messages[$smsinboxtemp_contactnumber][$smsinboxtemp_udhref]['simnumber'] = $smsinboxtemp_simnumber;
			$messages[$smsinboxtemp_contactnumber][$smsinboxtemp_udhref]['contactnumber'] = $smsinboxtemp_contactnumber;
			$messages[$smsinboxtemp_contactnumber][$smsinboxtemp_udhref]['contactsid'] = $smsinboxtemp_contactsid;
			$messages[$smsinboxtemp_contactnumber][$smsinboxtemp_udhref]['udhref'] = $smsinboxtemp_udhref;

			if(!empty($messages[$smsinboxtemp_contactnumber][$smsinboxtemp_udhref]['elapsedtime'])) {
				if($elapsedtime>$messages[$smsinboxtemp_contactnumber][$smsinboxtemp_udhref]['elapsedtime']) {
					$messages[$smsinboxtemp_contactnumber][$smsinboxtemp_udhref]['elapsedtime'] = $elapsedtime;
				}
			} else {
				$messages[$smsinboxtemp_contactnumber][$smsinboxtemp_udhref]['elapsedtime'] = $elapsedtime;
			}

			$messages[$smsinboxtemp_contactnumber][$smsinboxtemp_udhref]['parts'][$smsinboxtemp_udhpart] = $smsinboxtemp_message;
		}

		//print_r(array('$messages'=>$messages));

		if(!empty($messages)) {
			foreach($messages as $num=>$b) {
				foreach($b as $ref=>$v) {
					//print_r(array('$v'=>$v));
					if(($v['udhtotal']==count($v['parts']))||($v['elapsedtime']>3600)) {
						//print_r(array('$v'=>$v));

						$longmsg = '';

						for($i=0;$i<20;$i++) {
							if(!empty($v['parts'][$i])) {
								$longmsg .= $v['parts'][$i];
							}
						}

						$messages[$num][$ref]['message'] = $longmsg;

						$content = array();
						$content['smsinbox_contactsid'] = $v['contactsid'];
						$content['smsinbox_contactnumber'] = $v['contactnumber'];
						$content['smsinbox_simnumber'] = $v['simnumber'];
						$content['smsinbox_message'] = $longmsg;
						$content['smsinbox_unread'] = 1;

						processSMS($content);

						if(in_array($v['contactnumber'],$eloadnumbers)) {
							$content['smsinbox_eload'] = 1;
						}

						if(!($result = $appdb->insert('tbl_smsinbox',$content,'smsinbox_id'))) {
							atLog('$appdb->lasterror ('.$appdb->lasterror.')','retrievesms',$dev,$mobileNo,$ip,logdt());
						}

						$smsinboxtemp_ids = implode(',', $v['ids']);

						if(!($result = $appdb->query("delete from tbl_smsinboxtemp where smsinboxtemp_id in ($smsinboxtemp_ids)"))) {
							atLog('$appdb->lasterror ('.$appdb->lasterror.')','retrievesms',$dev,$mobileNo,$ip,logdt());
						}

					}
				}
			}
		}

		print_r(array('$messages'=>$messages));

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
