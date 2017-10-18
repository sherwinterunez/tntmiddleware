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

error_reporting(E_ALL);

ini_set("max_execution_time", 300);

class APP_SMS extends SMS {

	public function process() {

		if (preg_match('/\+CME ERROR\:\ (\d{1,3})\r\n$/', $this->buffer, $match)) {
			print_r(array('$match3'=>$match));
			$this->buffer = str_replace($match[0],'',$this->buffer);
			return false;
		} else
		if (preg_match('/\+CMS ERROR\:\ (\d{1,3})\r\n$/', $this->buffer, $match)) {
			print_r(array('$match4'=>$match));
			$this->buffer = str_replace($match[0],'',$this->buffer);
			return false;
		} else
		if(preg_match("/^\+([^\:]+)\:\s+(\d+)\r\n/s",$this->buffer, $match)) {
			$tbuf = $this->buffer;
			//print_r(array('$match5'=>$match));
			$this->buffer = str_replace($match[0],'',$this->buffer);
			print_r(array('$tbuf'=>'{'.$this->tocrlf($tbuf).'}','$match5'=>$match,'$this->buffer'=>'{'.$this->tocrlf($this->buffer).'}'));
		} else
		if(preg_match("/^\+(.+)\:\s+(\d+)\r\n/s",$this->buffer, $match)) {
			print_r(array('$match6'=>$match));
			$this->buffer = str_replace($match[0],'',$this->buffer);
		} else
		if(preg_match("/^\+(.+)\r\n/s",$this->buffer, $match)) {
			$tbuf = $this->buffer;
			$exp = explode("\r\n", $tbuf);
			//print_r(array('$match7'=>$match));
			$this->buffer = str_replace($match[0],'',$this->buffer);
			print_r(array('$exp'=>$exp,'$this->buf'=>'{'.$this->tocrlf($this->buf).'}','$tbuf'=>'{'.$this->tocrlf($tbuf).'}','$match7'=>$match,'$this->buffer'=>'{'.$this->tocrlf($this->buffer).'}'));
		} else
		if(preg_match("/^[^\r]+\r\n\r\n/s", $this->buffer, $match)) {
			$tbuf = $this->buffer;
			//print_r(array('$match8'=>$match));
			$this->buffer = str_replace($match[0],'',$this->buffer);
			print_r(array('$tbuf'=>'{'.$this->tocrlf($tbuf).'}','$match8'=>$match,'$this->buffer'=>'{'.$this->tocrlf($this->buffer).'}'));
		} else
		if(preg_match("/^\+([^\:]+)\:\s+(\d+)\r\n/",$this->buffer, $match)) {
			print_r(array('$match9'=>$match));
			$this->buffer = str_replace($match[0],'',$this->buffer);
		} else
		if(preg_match("/AT\+[^\r]+\r\n/", $this->buffer, $match)) {
			$tbuf = $this->buffer;
			//print_r(array('$match10'=>$match));
			$this->buffer = str_replace($match[0],'',$this->buffer);
			print_r(array('$tbuf'=>'{'.$this->tocrlf($tbuf).'}','$match10'=>$match,'$this->buffer'=>'{'.$this->tocrlf($this->buffer).'}'));
		} else
		if(preg_match("/\+[^\r]+\r\n/", $this->buffer, $match)) {
			$tbuf = $this->buffer;
			//print_r(array('$match11'=>$match));
			$this->buffer = str_replace($match[0],'',$this->buffer);
			print_r(array('$tbuf'=>'{'.$this->tocrlf($tbuf).'}','$match11'=>$match,'$this->buffer'=>'{'.$this->tocrlf($this->buffer).'}'));
		} else {
			if(!empty($this->buffer)) {
				//if($this->buffer=="\r\n"||$this->buffer=="\r\n\r\n") $this->buffer = '';


				while(1) {
					if(isset($this->buffer[1])&&ord($this->buffer[0])==13&&ord($this->buffer[1])==10) {
						$this->buffer = substr($this->buffer, 2);
					} else break;
				}

				print_r(array('$match255'=>'{'.$this->buffer.'}','strlen'=>strlen($this->buffer),'str2hex'=>$this->str2hex($this->buffer)));					
			}
		}

	}

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

function modemInit() {

	echo "\ninit starting.\n";
 
	$sms = new APP_SMS;

	if(!$sms->deviceInit("/dev/cu.usbserial",115200)) {
		die('Error initializing device!');
	}

	echo "\ninit started.\n";

	$sms->showBuffer();

	if($sms->sendMessageOk("AT\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	$sms->hideBuffer();

	if($sms->sendMessageOk("AT+STSF?\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+CCLK?\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+CPAS\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+CGMI\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+CGMM\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+CGMR\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+CCID\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+CSMS=1\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+CMGF=0\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+CNMI=2,1,0,0,0\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+CNMI?\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+COPS?\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+CREG?\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+CSCA?\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+CNUM\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+CSQ\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+CMGL=4\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	//$sms->showBuffer();

	//if($sms->sendMessageOk("AT+STGI=0\r\n")) {
	if($sms->sendMessageReadPort("AT+STGI=0\r\n", "(\+STGI:\s+.+)\r\nOK\r\n")) {
		//print_r($sms->history);
		$result = $sms->getResult();
		$result[1] = $sms->tocrlf($result[1]);
		print_r(array('history'=>$sms->history,'result'=>$result));
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		$sms->clearHistory();
		//die('1An error has occured!');

		if($sms->isCMEError()) {
			print_r(array('cmeerror'=>$sms->getCMEError()));

			$cmeerror = $sms->getCMEError();

			if($cmeerror==518) {
				if($sms->sendMessageOk("AT+STGR=0,1,0\r\n")) {
					print_r($sms->history);
					$sms->clearHistory();
					if($sms->sendMessageReadPort("AT+STIN?\r\n", "\+STIN:\s+(\d+)\r\n")) {
						$result = $sms->getResult();
						print_r(array('history'=>$sms->history,'result'=>$result));
						$sms->clearHistory();
					}
				} else {
					print_r($sms->history);
					$sms->clearHistory();
					//die('1An error has occured!');
				}
			} else
			if($cmeerror==3) {
				if($sms->sendMessageReadPort("AT+STIN?\r\n", "\+STIN:\s+(\d+)(.+?)OK\r\n")) {
					$result = $sms->getResult();
					$stin = trim(intval($result[1]));
					print_r(array('history'=>$sms->history,'result'=>$result,'stin'=>$stin));
					$sms->clearHistory();

					if(!empty($stin)) {

						if($sms->sendMessageOk("AT+STGI=".$stin."\r\n")) {
							print_r($sms->history);
							$sms->clearHistory();

							if($sms->sendMessageOk("AT+STGR=99\r\n")) {
								print_r($sms->history);
								$sms->clearHistory();
							}
						}

					}

				} else {
					print_r($sms->history);
					$sms->clearHistory();
					//die('1An error has occured!');
				}
			}

		}

	}


	/*if($sms->sendMessageOk("AT+STGR=0,1,0\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		$sms->clearHistory();
		//die('1An error has occured!');
	}*/

	//$sms->showBuffer();

	//$sms->readPort('\+STIN\:\ (\d{1,3})\r\n',60);

	//print_r(array('preg_quote(str)'=>preg_quote("\+STIN\:\s+(\d)\r\n",'/')));

	/*if($sms->readPort("\+STIN\:\s+(\d)\r\n",60)) {
		print_r(array('$result'=>$sms->getResult()));	
	}*/

	//sleep(5);

	/*if($sms->sendMessageOk("AT+STIN?\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		$sms->clearHistory();
		//die('1An error has occured!');
	}*/

	//$sms->sendMessageOk("AT+CMGF=1\r\n");

	//$sms->sendMessageOk("AT+CMGL=\"ALL\"\n",120);

	//$sms->readPort(5000);

	print_r($sms->history);

	$sms->deviceClose();

	$tstop = timer_stop();

	echo "\ninit done (".$tstop." secs).\n";

}

//////////


//die;


/////////

//print_r(array('$_SERVER'=>$_SERVER,'$_GET'=>$_GET));
//die;

if(!empty($_GET['q'])&&$_GET['q']=='init') {

	modemInit();

} else

if(!empty($_GET['q'])&&$_GET['q']=='balance') {

	$sms = new APP_SMS;

	if(!$sms->deviceInit("/dev/cu.usbserial",115200)) {
		die('Error initializing device!');
	}

	echo "\nstarted balance.\n";

	$msg = array();

	$msg['message'] = '?1515';	
	$msg['number'] = '214';
	//$msg['smsc'] = '+639180000101';
	$msg['class'] = -1;
	$msg['alphabetSize'] = 7;
	$msg['pdu'] = true;
	$msg['receiverFormat'] = '81';

	if(!empty($msg['pdu'])) {

		$sms->sendMessageOk("AT+CMGF=0\r\n");

		$pdu = new PduFactory();

		$x=1;

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

			foreach($dta as $part) { 
				$sms->udh['msg_part']=$sms->dechex_str($x); 
				$msg['message'] = $part . ' ';
				$msg['udh'] = implode('', $sms->udh);
				$chop[] = $msg;
				$x++; 

				$stra = $pdu->encode($msg,true);

				print_r(array('$msg'=>$msg,'$stra'=>$stra));

				print_r(array('$pdu->decode'=>$pdu->decode($stra['message'])));

				while(!$sms->sendMessageOk("AT+CMGS=".$stra['byte_size']."\n".$stra['message'].chr(26))) {
					sleep(5);
				};

				$ctr++;

				if($ctr>$max) break;
			} 

			//print_r(array('$chop'=>$chop));

		} else {

			$stra = $pdu->encode($msg);

			print_r(array('$stra'=>$stra));

			print_r(array('$pdu->decode'=>$pdu->decode($stra['message'])));

			//$sms->sendMessageOk("AT+CMGS=".$stra['byte_size']."\n".$stra['message'].chr(26));

			while(!$sms->sendMessageOk("AT+CMGS=".$stra['byte_size']."\n".$stra['message'].chr(26))) {
				sleep(5);
			};

		}
	} else {

		$sms->sendMessageOk("AT+CMGF=1\r\n");

		while(!$sms->sendMessageOk("AT+CMGS=".$msg['number']."\n".$msg['message'].chr(26))) {
			sleep(5);
		};

		$sms->sendMessageOk("AT+CMGF=0\r\n");
	}

	echo "\ndone balance.\n";

	$sms->deviceClose();

} else 

if(!empty($_GET['q'])&&$_GET['q']=='check') {

	$sendsms = false;

	if(!($result = $appdb->query("select * from tbl_smsoutbox where smsoutbox_status=1"))) {
		return false;
	}

	if(!empty($result['rows'][0]['smsoutbox_id'])) {

		print_r(array('$result'=>$result['rows']));

		$sendsms = $result['rows'];

		echo "\nstarted sending.\n";

		if(!empty($sendsms)&&is_array($sendsms)) {
			foreach($sendsms as $k=>$v) {
				//if($v['smsoutbox_total']==1) {
					if(sendSMS($v['smsoutbox_portdevice'],$v['smsoutbox_contactnumber'],$v['smsoutbox_message'])) {
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

	$sms = new APP_SMS;

	$portdevice = "/dev/cu.usbserial";

	if(!$sms->deviceInit($portdevice,115200)) {
		die('Error initializing device!');
	}

	echo "\nstarted checking.\n";

	$sms->sendMessageOk("AT+CMGL=4\r\n");

	//$sms->readPort(70);

	$pdu = new PduFactory();

	print_r(array('$sms->history'=>$sms->history));

	$messages = array();

	if(!empty($sms->history[0])&&count($sms->history[0])>0) {

		for($i=0;$i<count($sms->history[0]);$i++) {
			if(preg_match("#\+CMGL\:\s+(\d+)\,.+#",$sms->history[0][$i],$match)) {
				$msg = $pdu->decode($sms->history[0][$i+1]);
				print_r(array('$msg'=>$msg,'$match'=>$match,'$pdu'=>$sms->history[0][$i+1]));
				//$sms->sendMessageOk("AT+CMGD=".$match[1]."\r\n");

				$msgid = $match[1];

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

						//print_r(array('$udf'=>$udf));

						//$partno = getContactIDByNumber

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
				$content['smsinbox_message'] = $data['long'][$data['ref']]['message'];

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
					$content['smsinbox_message'] = $vmsg;

					$result = $appdb->insert('tbl_smsinbox',$content,'smsinbox_id');

					if(!empty($result['returning'][0]['smsinbox_id'])) {
						//return $result['returning'][0]['smsinbox_id'];
						$sms->sendMessageOk("AT+CMGD=".$msgid."\r\n");
					}

				}

			}
		}		
	}

	print_r(array('$messages'=>$messages));

	echo "\ndone checking.\n";

	$sms->deviceClose();

} else {
	modemInit();
}


