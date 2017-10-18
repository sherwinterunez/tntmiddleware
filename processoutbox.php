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

function sendSMS($device=false,$number=false,$message=false) {

	$retval = false;

	$sms = new APP_SMS;

	if(!empty($device)&&!empty($number)&&!empty($message)) {
	} else return false;

	if(!$sms->deviceInit($device,115200)) {
		die('Error initializing device!');
	}

	echo "\nstarted balance.\n";

	$msg = array();

	$msg['message'] = $message;	
	$msg['number'] = $number;
	//$msg['smsc'] = '+639180000101';
	$msg['class'] = -1;
	$msg['alphabetSize'] = 7;
	$msg['pdu'] = true;
	$msg['receiverFormat'] = '81';

	if(!empty($msg['pdu'])) {

		$sms->sendMessageOk("AT+CMGF=0\r\n");

		$pdu = new PduFactory();

		$x=1;

		$max=10; 

		if(strlen($msg['message'])>160) {
			$dta=str_split($msg['message'],152); 
			
			$ref=mt_rand(100,250); 
			
			//$ref=62;

			$sms->udh['msg_count']=$sms->dechex_str(count($dta)); 

			if(count($dta)>$max) {
				$sms->udh['msg_count']=$sms->dechex_str($max); 

			}

			$sms->udh['reference']=$sms->dechex_str($ref); 

			$ctr=1;

			$break = false;

			foreach($dta as $part) { 
				$sms->udh['msg_part']=$sms->dechex_str($x); 
				$msg['message'] = $part . ' ';
				$msg['udh'] = implode('', $sms->udh);
				$chop[] = $msg;
				$x++; 

				$stra = $pdu->encode($msg,true);

				print_r(array('$msg'=>$msg,'$stra'=>$stra));

				print_r(array('$pdu->decode'=>$pdu->decode($stra['message'])));

				$cntr = 0;

				while(!$sms->sendMessageOk("AT+CMGS=".$stra['byte_size']."\n".$stra['message'].chr(26))) {
					sleep(5);
					$cntr++;
					if($cntr>5) {
						$break = true;
						break;
					}
				};

				$ctr++;

				if($ctr>$max) break;

				if($break) {
					break;
				}
			} 

			if($break) {
				$retval = false;
			} else {
				$retval = true;			
			}

			//print_r(array('$chop'=>$chop));

		} else {

			$stra = $pdu->encode($msg);

			print_r(array('$stra'=>$stra));

			print_r(array('$pdu->decode'=>$pdu->decode($stra['message'])));

			//$sms->sendMessageOk("AT+CMGS=".$stra['byte_size']."\n".$stra['message'].chr(26));

			$cntr = 0;

			$break = false;

			while(!$sms->sendMessageOk("AT+CMGS=".$stra['byte_size']."\n".$stra['message'].chr(26))) {
				sleep(5);
				$cntr++;
				if($cntr>5) {
					$break = true;
					break;
				}
			};

			if($break) {
				$retval = false;
			} else {
				$retval = true;			
			}

		}
	} else {

		$sms->sendMessageOk("AT+CMGF=1\r\n");

		while(!$sms->sendMessageOk("AT+CMGS=".$msg['number']."\n".$msg['message'].chr(26))) {
			sleep(5);
		};

		$sms->sendMessageOk("AT+CMGF=0\r\n");

		$retval = true;
	}

	echo "\ndone balance.\n";

	$sms->deviceClose();

	print_r(array('$sms->history'=>$sms->history ));

	return $retval;

}

function processOutbox($dev=false) {
	global $appdb;

	if(!empty($dev)) {
	} else return false;

	echo "\nprocessOutbox started: $dev.\n";

	$sms = new APP_SMS;

	if(!$sms->deviceInit($dev,115200)) {
		die('Error initializing device!');
	}

	if($sms->sendMessageOk("AT\r\n")) {
		//print_r($sms->history);
		$sms->clearHistory();
	} else {
		//print_r($sms->history);
		//die('1An error has occured!');
		echo "\nprocessOutbox failed (AT).\n";
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
				//$oMobileNo = $res[2].$res[3];
				//$mobileNetwork = getNetworkName($mobileNo);
			}
		}

		if(empty($mobileNo)) {
			echo "\nprocessOutbox failed (invalid \$mobileNo).\n";
			$sms->deviceClose();
			return false;
		}
	} else {
		//print_r($sms->history);
		echo "\nprocessOutbox failed (AT+CNUM).\n";
		$sms->deviceClose();
		return false;
		//die('An error has occured!');
	}

	print_r(array('$mobileNo'=>$mobileNo));

	$sms->deviceClose();

	$sendsms = false;

	if(!($result = $appdb->query("select * from tbl_smsoutbox where smsoutbox_simnumber='$mobileNo' and smsoutbox_deleted=0 and smsoutbox_status=1 limit 5"))) {
		echo "\n0 message. processOutbox done.\n";
		return false;
	}

	if(!empty($result['rows'][0]['smsoutbox_id'])) {

		print_r(array('$result'=>$result['rows']));

		$sendsms = $result['rows'];

		echo "\nstarted sending.\n";

		if(!empty($sendsms)&&is_array($sendsms)) {
			foreach($sendsms as $k=>$v) {
				//if($v['smsoutbox_total']==1) {

					//if(sendSMS($v['smsoutbox_portdevice'],$v['smsoutbox_contactnumber'],$v['smsoutbox_message'])) {

					$appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>3,'smsoutbox_sentstamp'=>'now()'),'smsoutbox_status=1 and smsoutbox_id='.$v['smsoutbox_id']);

					if(sendSMS($dev,$v['smsoutbox_contactnumber'],$v['smsoutbox_message'])) {
						$appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>4,'smsoutbox_sentstamp'=>'now()'),'smsoutbox_id='.$v['smsoutbox_id']);
					} else {
						$appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>5),'smsoutbox_id='.$v['smsoutbox_id']);						
					}
				//} else
				//if($v['smsoutbox_total']>1) {
				//}
			}
		}

		echo "\ndone sending.\n";

	}

	echo "\nprocessOutbox done.\n";

	return true;
}

if(getOption('$MAINTENANCE',false)) {
	die("\nprocessOutbox: Server under maintenance.\n");
}

if(!empty($_GET['dev'])) {
	//pre(array('$_GET'=>$_GET));

	setSetting('STATUS_PROCESSOUTBOX_'.$_GET['dev'],'1');

	if(processOutbox($_GET['dev'])) {
		setSetting('STATUS_PROCESSOUTBOX_'.$_GET['dev'],'0');
	}

}

//processOutbox();

//sendSMS('/dev/ttyUSB1','214','?1515');


