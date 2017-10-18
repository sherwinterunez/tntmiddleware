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

	/*public function process() {

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

	}*/

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

	echo "\nexec starting.\n";
 
	$sms = new APP_SMS;

	if(!$sms->deviceInit("/dev/cu.usbserial",115200)) {
		die('Error initializing device!');
	}

	echo "\nexec started.\n";

	//$sms->showBuffer();

	if($sms->sendMessageOk("AT\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	$simfunctions = array();

	//$simfunctions[] = array('stin'=>0,'param'=>'Mobile\s+Banking','regx'=>"\+(STGI):\s+(\d+)\,(\d+)\,\"(<param>)\".+?\r\nOK\r\n");
	//$simfunctions[] = array('stin'=>6,'param'=>'BPI','regx'=>"\+(STGI):\s+(\d+)\,(\d+)\,\"(<param>)\".+?\r\nOK\r\n");
	//$simfunctions[] = array('stin'=>6,'param'=>'Balance','regx'=>"\+(STGI):\s+(\d+)\,(\d+)\,\"(<param>)\".+?\r\nOK\r\n");
	//$simfunctions[] = array('stin'=>6,'param'=>'Account','regx'=>"\+(STGI):\s+(\d+)\,(\d+)\,\"(<param>)\".+?\r\nOK\r\n");
	//$simfunctions[] = array('stin'=>6,'param'=>'SA2073','regx'=>"\+(STGI):\s+(\d+)\,(\d+)\,\"(<param>)\".+?\r\nOK\r\n");
	//$simfunctions[] = array('stin'=>3,'param'=>'123456');

	//$simfunctions[] = array('stin'=>0,'param'=>'SMART\s+Money','regx'=>"\+(STGI):\s+(\d+)\,(\d+)\,\"(<param>)\".+?\r\nOK\r\n");	
	//$simfunctions[] = array('stin'=>6,'param'=>'Balance','regx'=>"\+(STGI):\s+(\d+)\,(\d+)\,\"(<param>)\".+?\r\nOK\r\n");	
	//$simfunctions[] = array('stin'=>6,'param'=>'MySmartMoney','regx'=>"\+(STGI):\s+(\d+)\,(\d+)\,\"(<param>)\".+?\r\nOK\r\n");	
	//$simfunctions[] = array('stin'=>3);	

	$simfunctions[] = array('command'=>'AT','regx'=>"OK\r\n",'resultindex'=>0);

	$simfunctions[] = array('command'=>'AT+CSQ','regx'=>"\+CSQ\:\s+(\d+\,\d+)\r\nOK\r\n",'resultindex'=>1);

	$simfunctions[] = array('command'=>'AT+CNUM','regx'=>"\+CNUM\:\s+\".+?\"\,\"(.+?)\"\,.+?\r\nOK\r\n",'resultindex'=>1);

	$simfunctions[] = array(
		'command'=>'AT+STGI=0',
		'regx'=>"\+(STGI):\s+(\d+)\,(\d+)\,\"(<param>)\".+?\r\nOK\r\n",
		'param'=>'SMART\s+Money',
		'resultindex'=>2,
	);

	$simfunctions[] = array(
		'command'=>"AT+STGR=0,1,<result>",
		'regx'=>"\+STIN:\s+(\d+)\r\n",
		'resultindex'=>1,
		'expectedresult'=>6,
	);

	$simfunctions[] = array(
		'command'=>'AT+STGI=<result>',
		'regx'=>"\+(STGI):\s+(\d+)\,(\d+)\,\"(<param>)\".+?\r\nOK\r\n",
		'param'=>'Balance',
		'resultindex'=>2,
	);

	$simfunctions[] = array(
		'command'=>"AT+STGR=6,1,<result>",
		'regx'=>"\+STIN:\s+(\d+)\r\n",
		'resultindex'=>1,
		'expectedresult'=>6,
	);

	$simfunctions[] = array(
		'command'=>'AT+STGI=<result>',
		'regx'=>"\+(STGI):\s+(\d+)\,(\d+)\,\"(<param>)\".+?\r\nOK\r\n",
		'param'=>'MySmartMoney',
		'resultindex'=>2,
	);

	$simfunctions[] = array(
		'command'=>"AT+STGR=6,1,<result>",
		'regx'=>"\+STIN:\s+(\d+)\r\n",
		'resultindex'=>1,
		'expectedresult'=>3,
	);

	$simfunctions[] = array(
		'command'=>'AT+STGI=<result>',
		'regx'=>"\+STGI:.+?\r\nOK\r\n",
	);

	$simfunctions[] = array(
		'command'=>"AT+STGR=3,1",
		'regx'=>"(\>)\r\n",
		'resultindex'=>1,
		'expectedresult'=>'>',
	);

	//$smsfunctions[] = array(
	//	'command'=>"123456".chr(26),
	//	'regx'=>"\r\nOK\r\n.+?\+STIN:\s+(\d+)\r\n",
	//);

	$simfunctions[] = array(
		'command'=>"123456".chr(26),
		'regx'=>"\+STIN:\s+98|99\r\n",
	);

	//$smsfunctions[] = array(
	//	'command'=>'AT+STGI=<result>',
	//	'regx'=>"\+STGI:.+?\r\nOK\r\n",
	//);

	$REGX_STGI_6 = "\+(STGI):\s+(\d+)\,(\d+)\,\"(<param>)\".+?\r\nOK\r\n";

	$REGX_STIN = "\+STIN:\s+(\d+)\r\n";

	unset($gotresult);

	if(!empty($simfunctions)) {
		foreach($simfunctions as $func) {

			if(!empty($func['command'])) {

				$REGX = '';

				if(!empty($func['regx'])) {
					$REGX = $func['regx'];

					if(!empty($func['param'])) {
						if(is_array($func['param'])) {

						} else {
							$REGX = str_replace('<param>',$func['param'],$REGX);
						}
					}
				}

				$FUNC = trim($func['command']);

				if(isset($gotresult)) {
					$FUNC = str_replace('<result>', $gotresult, $FUNC);
				}

				if(!empty($REGX)) {

					if($sms->sendMessageReadPort($FUNC."\r\n", $REGX)) {
						$result = $sms->getResult();
						$result['flat'] = $sms->tocrlf($result[0]);
						if(isset($func['resultindex'])&&is_numeric($func['resultindex'])) {
							$index = intval(trim($func['resultindex']));
							if(isset($result[$index])) {
								$gotresult = $result[$index];

								if(isset($func['expectedresult'])) {
									if(is_numeric($func['expectedresult'])) {
										$expectedresult = intval(trim($func['expectedresult']));
									} else {
										$expectedresult = $func['expectedresult'];
									}

									if($gotresult==$expectedresult) {

									} else {
										print_r(array('history'=>$sms->history,'result'=>$result,'gotresult'=>$gotresult));
										die('2An error has occured!');										
									}
								}
							}
						}
						print_r(array('history'=>$sms->history,'result'=>$result,'gotresult'=>$gotresult));
						$sms->clearHistory();
					} else {
						print_r(array('history'=>$sms->history));
						//die('2An error has occured!');
					}

				} else {

				}


			} else

			if(isset($func['stin'])&&!empty($func['param'])) {

				if(!empty($func['stin'])&&!isset($stin)) {
					die('(ERRCODE:101) An error has occured while processing your request. System engineers has been notified. Please try again later.');					
				} else
				if(!empty($func['stin'])&&isset($stin)) {
					if($stin==$func['stin']) {
						print_r(array('$stin'=>$stin));
					} else {
						die('(ERRCODE:102) An error has occured while processing your request. System engineers has been notified. Please try again later.');					
					}
				}

				if($func['stin']===0||$func['stin']===6) {

					if(!empty($func['regx'])) {
						$REGX = str_replace('<param>',$func['param'],$func['regx']);
					} else {
						$REGX = str_replace('<param>',$func['param'],$REGX_STGI_6);				
					}

					if($sms->sendMessageReadPort("AT+STGI=".$func['stin']."\r\n", $REGX)) {
						//print_r($sms->history);
						$result = $sms->getResult();
						$menuid = intval(trim($result[2]));
						$result[0] = $sms->tocrlf($result[0]);
						print_r(array('history'=>$sms->history,'result'=>$result,'menuid'=>$menuid));
						$sms->clearHistory();
					} else {
						print_r($sms->history);
						die('1An error has occured while processing your request. System engineers has been notified. Please try again later.');
					}

					if(!empty($menuid)&&$sms->sendMessageReadPort("AT+STGR=".$func['stin'].",1,".$menuid."\r\n", $REGX_STIN)) {
						$result = $sms->getResult();
						$result[1] = $stin = intval(trim($result[1]));

						print_r(array('history'=>$sms->history,'result'=>$result,'stin'=>$stin));
						$sms->clearHistory();
					} else {
						print_r($sms->history);
						die('2An error has occured while processing your request. System engineers has been notified. Please try again later.');
					}

				} else

				if($func['stin']===3) {

					if($sms->sendMessageReadPort("AT+STGI=".$func['stin']."\r\n", "(\+STGI:\s+.+)\r\nOK\r\n")) {
						$result = $sms->getResult();
						$result[1] = $sms->tocrlf($result[1]);
						print_r(array('history'=>$sms->history,'result'=>$result,'stin'=>$stin));
						$sms->clearHistory();
					} else {
						print_r($sms->history);
						die('2An error has occured!');
					}

					$sendData = false;

					if($sms->sendMessageReadPort("AT+STGR=".$func['stin'].",1\r\n", "\>\r\n")) {
						$result = $sms->getResult();
						$result[0] = $sms->tocrlf($result[0]);

						$sendData = true;

						print_r(array('history'=>$sms->history,'result'=>$result));
						$sms->clearHistory();
					} else {
						print_r($sms->history);
						die('3An error has occured!');
					}

					if(!empty($sendData)&&$sms->sendMessageReadPort($func['param'].chr(26), "\r\nOK\r\n.+?".$REGX_STIN)) {
						$result = $sms->getResult();
						$result[0] = $sms->tocrlf($result[0]);
						print_r(array('history'=>$sms->history,'result'=>$result));
						$sms->clearHistory();
					} else {
						$sms->sendMessageOk("AT+STGR=99\r\n");
						print_r($sms->history);
						//die('4An error has occured!');
					}


				}

			} else

			if(isset($func['stin'])&&empty($func['param'])) {

					if($sms->sendMessageReadPort("AT+STGI=".$func['stin']."\r\n", "(\+STGI:\s+.+?)\r\nOK\r\n")) {
						$result = $sms->getResult();
						$result[1] = $sms->tocrlf($result[1]);
						print_r(array('history'=>$sms->history,'result'=>$result,'stin'=>$stin));
						$sms->clearHistory();
					} else {
						print_r($sms->history);
						die('2An error has occured!');
					}

			}

		}
	}

	print_r($sms->history);

	$sms->deviceClose();

	$tstop = timer_stop();

	echo "\nexec done (".$tstop." secs).\n";

}

modemInit();

