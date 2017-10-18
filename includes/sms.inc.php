<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Misc functions include file
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

/* INCLUDES_START */

if(!class_exists('SMS')) {

	class SMS {

		public $baud = false;
		public $os = false;
		public $state = DEVICE_NOTSET;
		public $handle = NULL;
		public $device = false;
		public $winDevice = false;
		public $buffer = '';
		public $buf = '';
		public $lastsentmessage = '';
		public $lastcmeerror = 0;
		public $lastresult = '';

		public $udh = array();

		public $adata = array();

		public $history = array();

		public $current = array();

		public $showbuf = false;

		public $cmeerror = false;

		public $cmserror = false;

		public $result = false;

		public $dev = '';
		public $mobileNo = '';
		public $ip = '';

		function __construct() {
			$this->_init();
		}

		function __destruct() {
		}

		public function _init() {

			$this->udh = array(
		        'udh_length'=>'05', //sms udh lenth 05 for 8bit udh, 06 for 16 bit udh
		        'identifier'=>'00', //use 00 for 8bit udh, use 08 for 16bit udh
		        'header_length'=>'03', //length of header including udh_length & identifier
		        'reference'=>'00', //use 2bit 00-ff if 8bit udh, use 4bit 0000-ffff if 16bit udh
		        'msg_count'=>1, //sms count
		        'msg_part'=>1 //sms part number
	        );

	        setlocale(LC_ALL, "en_US");

	        $sysName = php_uname();

	        //print_r($sysName);

	        if (substr($sysName, 0, 5) === "Linux") {
	            $this->os = "linux";

	            //if ($this->exec("stty") === 0) {
	                register_shutdown_function(array($this, "deviceClose"));
	            //} else {
	            //    trigger_error(
	            //        "No stty availible, unable to run.",
	            //        E_USER_ERROR
	            //    );
	            //}
	        } elseif (substr($sysName, 0, 6) === "Darwin") {
	            $this->os = "osx";
	            register_shutdown_function(array($this, "deviceClose"));
	        } elseif (substr($sysName, 0, 7) === "Windows") {
	            $this->os = "windows";
	            register_shutdown_function(array($this, "deviceClose"));
	        } else {
	            trigger_error("Host OS is neither osx, linux nor windows, unable " .
	                          "to run.", E_USER_ERROR);
	            exit();
	        }

	        //print_r(array('$this->os'=>$this->os));
		}

		public function init() {

		}

		public function str2hex($str) {
			$hex = '';

			for($j=0;$j<strlen($str);$j++) {
			    $hex .= str_pad(dechex(ord($str[$j])), 2, '0', STR_PAD_LEFT) . ' ';
			}
			return $hex;
		}

		public function str2hex2($str) {
			$hex = '';

			for($j=0;$j<strlen($str);$j++) {
			    $hex .= str_pad(dechex(ord($str[$j])), 2, '0', STR_PAD_LEFT);
			}
			return $hex;
		}

	    public function sendMessage($str, $waitForReply = 0) {

	        if ($this->state !== DEVICE_OPENED) {
	            trigger_error("Device must be opened", E_USER_WARNING);

	            return false;
	        }

	        //$this->buffer .= $str;

	        $this->lastsentmessage = $str;

	        if (fwrite($this->handle, $str) !== false) {
	            return true;
	        } else {
	            trigger_error("Error while sending message", E_USER_WARNING);

	            return false;
	        }

	        if($waitForReply) {
		        usleep((int) ($waitForReply * 1000000));
	        }
	    }

	    public function sendMessageOk($str, $timeout=60) {

	    	if($this->sendMessage($str)) {
				if($this->readPort("OK\r\n", $timeout, false, $str)) {
					return true;
				}
	    	}

			return false;
	    }

	    public function sendMessageReadPort($str, $expected_result, $timeout=60) {

	    	if($this->sendMessage($str)) {

	    		//readPort($expected_result=false, $timeout=0, $showbuffer=false, $commandstr='')

				if($this->readPort($expected_result, $timeout, false, $str)) {
					return true;
				}

				$history = $this->getHistory();

				foreach($history as $a=>$b) {
					foreach($b as $k=>$v) {
						if($k=='timestamp') continue;
						//$dt = logdt($b['timestamp']);
						trigger_error($this->dev." ".$this->mobileNo." ".$this->ip." $v",E_USER_NOTICE);
						//atLog($v,'retrievesms',$dev,$mobileNo,$ip,logdt($b['timestamp']));
					}
				}

	    	}

			return false;
	    }

	    public function tocrlf($str) {
	    	$str = str_replace("\r", '\r', $str);
	    	$str = str_replace("\n", '\n', $str);
	    	return $str;
	    }

	    public function clearHistory() {
	    	$this->history = '';
	    	return true;
	    }

	    public function getHistory() {
	    	return $this->history;
	    }

	    public function clearCurrent() {
	    	$this->current = '';
	    	return true;
	    }

	    public function getCurrent() {
	    	return $this->current;
	    }

	    public function showBuffer() {
	    	$this->showbuf = true;
	    }

	    public function hideBuffer() {
	    	$this->showbuf = false;
	    }

	    public function isCMEError() {
	    	return !empty($this->cmeerror);
	    }

	    public function isCMSError() {
	    	return !empty($this->cmserror);
	    }

	    public function getCMEError() {
	    	return $this->cmeerror;
	    }

	    public function getCMSError() {
	    	return $this->cmserror;
	    }

	    public function resetCMEError() {
	    	$this->cmeerror = false;
	    }

	    public function resetCMSError() {
	    	$this->cmserror = false;
	    }

	    public function getResult() {
	    	return $this->result;
	    }

	    public function getLastResult($index=false) {

	    	if(is_numeric($index)) {
	    		$index = intval($index);

	    		return $this->lastresult[$index];
	    	}

	    	return $this->lastresult;
	    }

	    public function getLastMessage() {
	    	return $this->lastsentmessage;
	    }

	    public function modemFunction($simfunctions=false,$debug=false) {
	    	return modemFunction($this, $simfunctions, $debug);
	    }

	    public function at() {
	    	return at_at($this);
	    }

			public function atgt() {
	    	return at_atgt($this);
	    }

	    public function cnum() {
	    	return at_cnum($this);
	    }

	    public function cmgl_4() {
	    	return at_cmgl_4($this);
	    }

	    public function cmgs($bytesize=false,$msg=false) {
	    	return at_cmgs($this,$bytesize,$msg);
	    }

		public function readPort2($expected_result=false, $timeout=0, $showbuffer=false) {

			if(!empty($expected_result)&&is_numeric($expected_result)) {
				$timeout = $expected_result;
				$expected_result = false;
			}

			//$this->buffer = '';

			$timeoutat = time() + $timeout;

			$cmt = array();

			do {

				$buffer = fread($this->handle, 1024);

				$this->buf .= $buffer;

				if(strlen($this->buf)<1) continue;

				if(isset($this->buf[1])&&ord($this->buf[0])==13&&ord($this->buf[1])==10) {
					$this->buf = substr($this->buf, 2);
				}

				if(preg_match("/^\+CMT\:(.+)$/s",$this->buf)&&!preg_match("/\+CMT\:(.+)\r\n(.+)\r\n$/s",$this->buf)) {
					continue;
				}

				for($i=0;$i<strlen($this->buf);$i++) {
					if(isset($this->buf[$i+1])&&ord($this->buf[$i])==13&&ord($this->buf[$i+1])==10) {

						$str = substr($this->buf, 0, ($i+2));

						$this->buf = str_replace($str, '', $this->buf);

						$this->buffer .= $str;
					}
				}

				if ($expected_result&&preg_match('/'.preg_quote($expected_result, '/').'$/', $this->buffer, $match)) {
					//$this->buffer = str_replace($match[0],'',$this->buffer);
					$this->buffer = '';
					//print_r(array('readReply'=>'Found match1', '$expected_result'=>$expected_result, '$this->buffer'=>$this->buffer));
					return true;
				} else
				if ($expected_result&&preg_match('/'.preg_quote($expected_result, '/').'/', $this->buffer, $match)) {
					$this->buffer = str_replace($match[0],'',$this->buffer);
					//print_r(array('readReply'=>'Found match2', '$expected_result'=>$expected_result, '$this->buffer'=>$this->buffer));
					return true;
				} else // +CSCA: "+639180000101",145
				if (preg_match('/\+CME ERROR\:\ (\d{1,3})\r\n$/', $this->buffer, $match)) {
					$this->buffer = str_replace($match[0],'',$this->buffer);
					//print_r(array('readReply'=>$this->buffer,'match'=>$match));
					return false;
				} else
				if (preg_match('/\+CMS ERROR\:\ (\d{1,3})\r\n$/', $this->buffer, $match)) {
					$this->buffer = str_replace($match[0],'',$this->buffer);
					//print_r(array('readReply'=>$this->buffer,'match'=>$match));
					return false;
				} else
				if(preg_match("/\+CMT\:(.+)\r\n(.+)\r\n$/s",$this->buffer, $match)) {

					print_r(array('$match2'=>$match));

					$pdu = new PduFactory();

					$apd = $pdu->decode($match[2]);

					if(!empty($apd['userDataHeader'])) {
						if(!empty($apd['userDataHeader'])) {
							$udh = explode(' ', trim($apd['userDataHeader']));
							$idx = intval($udh[5]);
							$max = intval($udh[4]);
							unset($udh[5]);
							$udhi = implode('', $udh);
							//$cmt[$udhi]['max'] = $max;
							$cmt[$udhi][$idx] = $apd;

							//print_r(array('$udh'=>$udh,'$udhi'=>$udhi));

							if(!empty($cmt[$udhi])&&sizeof($cmt[$udhi])==$max) {
								$msg = '';

								for($i=1;$i<=$max;$i++) {
									$msg .= $cmt[$udhi][$i]['message'];
								}

								$npdu = $cmt[$udhi][1];
								//$npdu['message'] = str_replace('\n',chr(10),$msg);
								$npdu['length'] = strlen($msg);
								$npdu['message'] = $msg;
								//unset($npdu['userDataHeader']);

								//print_r(array('$cmt'=>$cmt, '$cmt[$udhi]'=>$cmt[$udhi], '$msg'=>$msg, '$npdu'=>$npdu));
								print_r(array('$npdu'=>$npdu));
							}
						}
					} else {
						print_r($apd);
					}

					//$this->buffer = '';

					$this->buffer = str_replace($match[0],'',$this->buffer);

					//print_r(array('$this->buffer'=>'['.$this->buffer.']','strlen($this->buffer)'=>strlen($this->buffer)));

					$this->sendMessageOk("AT+CNMA\r\n");

					/*$msg = array();
					$msg['message'] = 'hello';
					$msg['number'] = '+639493621618';
					//$msg['smsc'] = '+639180000101';
					$msg['alphabetSize'] = 7;

					$pdu = new PduFactory();

					$stra = $pdu->encode($msg);

					print_r(array('$stra'=>$stra));

					$this->sendMessageOk("AT+CMGS=".$stra['byte_size']."\n".$stra['message'].chr(26));*/

				} else
				if(preg_match("/^\+([^\:]+)\:\s+(\d+)\r\n/s",$this->buffer, $match)) {
					print_r(array('$match3.1'=>$match));
					$this->buffer = str_replace($match[0],'',$this->buffer);
					//print_r(array('$this->buffer'=>'['.$this->buffer.']','strlen($this->buffer)'=>strlen($this->buffer)));
					//$this->buffer = '';
				} else
				if(preg_match("/^\+(.+)\:\s+(\d+)\r\n/s",$this->buffer, $match)) {
					print_r(array('$match3.2'=>$match));
					$this->buffer = str_replace($match[0],'',$this->buffer);
					//print_r(array('$this->buffer'=>'['.$this->buffer.']','strlen($this->buffer)'=>strlen($this->buffer)));
					//$this->buffer = '';
				} else
				if(preg_match("/^\+(.+)\r\n/s",$this->buffer, $match)) {
					print_r(array('$match4'=>$match));
					$this->buffer = str_replace($match[0],'',$this->buffer);
					//print_r(array('$this->buffer'=>'['.$this->buffer.']','strlen($this->buffer)'=>strlen($this->buffer)));
					//$this->buffer = '';
				} else
				if(preg_match("/^[^\r]+\r\n/s", $this->buffer, $match)) {
					print_r(array('$match5'=>$match));
					$this->buffer = str_replace($match[0],'',$this->buffer);
					//print_r(array('readPort'=>'Found match10', '$this->buffer'=>'{'.$this->buffer.'}','$match'=>$match));
				}
				/*if(strlen($this->buffer)>2) {
					$t = strrev($this->buffer);
			    	if(ord($t[0])==10&&ord($t[1])==13) {
			    		//print_r(array('$this->buffer'=>$this->buffer));
			    		$this->buffer = '';
			    	}
				} */


				/*else
				if(preg_match("/\+CMT\:(.+)\r\n$/",$this->buffer, $match)) {
					print_r(array('$match4'=>$match));
					$this->buffer = '';
				} else
				if(preg_match("/\+CMT\:(.+)\r\n/",$this->buffer, $match)) {
					print_r(array('$match5'=>$match));
					$this->buffer = '';
				}*/

				usleep(10000);//0.2 sec

			} while ($timeoutat > time());

			return false;
		}

		public function readPort3($expected_result=false, $timeout=0, $showbuffer=false) {

			if(!empty($expected_result)&&is_numeric($expected_result)) {
				$timeout = $expected_result;
				$expected_result = false;
			}

			//$this->buffer = '';

			$timeoutat = time() + $timeout;

			$cmt = array();

			do {

				$buffer = fread($this->handle, 1024);

				$this->buf .= $buffer;

				if(strlen($this->buf)<1) continue;

				while(1) {
					if(isset($this->buf[1])&&ord($this->buf[0])==13&&ord($this->buf[1])==10) {
						$this->buf = substr($this->buf, 2);
					} else break;
				}

				if(preg_match("/^\+CMT\:(.+)$/s",$this->buf)&&!preg_match("/\+CMT\:(.+)\r\n(.+)\r\n$/s",$this->buf)) {
					continue;
				}

				for($i=0;$i<strlen($this->buf);$i++) {
					if(isset($this->buf[$i+1])&&ord($this->buf[$i])==13&&ord($this->buf[$i+1])==10) {

						$str = substr($this->buf, 0, ($i+2));

						$this->buf = str_replace($str, '', $this->buf);

						$this->buffer .= $str;
					}
				}

				if ($expected_result&&preg_match('/'.preg_quote($expected_result, '/').'$/', $this->buffer, $match)) {
					$tbuf = $this->buffer;
					$this->buffer = str_replace($match[0],'',$this->buffer);
					print_r(array('$tbuf'=>'{'.$this->tocrlf($tbuf).'}','$match1'=>$match,'$this->buffer'=>'{'.$this->tocrlf($this->buffer).'}'));
					return true;
				} else
				if ($expected_result&&preg_match('/'.preg_quote($expected_result, '/').'/', $this->buffer, $match)) {
					$tbuf = $this->buffer;
					$this->buffer = str_replace($match[0],'',$this->buffer);
					print_r(array('$tbuf'=>'{'.$this->tocrlf($tbuf).'}','$match2'=>$match,'$this->buffer'=>'{'.$this->tocrlf($this->buffer).'}'));
					return true;
				} else
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
					print_r(array('$match7'=>$match));
					$this->buffer = str_replace($match[0],'',$this->buffer);
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

				usleep(10000);//0.2 sec

			} while ($timeoutat > time());

			return false;
		}

		public function readPort($expected_result=false, $timeout=0, $showbuffer=false, $commandstr='') {

			if(!empty($expected_result)&&is_numeric($expected_result)) {
				$timeout = $expected_result;
				$expected_result = false;
			}

			//$this->buffer = '';

			$timeoutat = time() + $timeout;

			$cmt = array();

			if(empty($this->handle)) {
				trigger_error("Invalid handle", E_USER_WARNING);
				return false;
			}

			if(!is_resource($this->handle)) {
				trigger_error("Invalid handle", E_USER_WARNING);
				return false;
			}

			do {

				$buffer = fread($this->handle, 1024);

				if($buffer===false) {
					trigger_error("An error has occured (".$this->getLastMessage().")", E_USER_WARNING);
					return false;
				}

				if($this->showbuf&&$buffer) {
					//echo ".\r\n";
					$echo = $this->tocrlf('$buf => '. $buffer)."\r\n";
					$echo = str_replace(chr(26),'(0x26)',$echo);
					$tohex = $this->str2hex($buffer)."\r\n";
					echo $echo;
					echo strtoupper($tohex);
				}

				$this->buf .= $buffer;

				if(strlen($this->buf)<1) {
					usleep(10000); //0.02 sec
					continue;
				}

				if(preg_match("/\r\n\>\s/si",$this->buf)) {
					$this->buf = str_replace("\r\n> ",">\r\n",$this->buf);
				}

				while(1) {
					if(isset($this->buf[1])&&ord($this->buf[0])==13&&ord($this->buf[1])==10) {
						//echo $this->tocrlf('$this->buf => '. $this->buf)."\r\n";
						$this->buf = substr($this->buf, 2);
						//echo $this->tocrlf('$this->buf => ['. $this->buf.']')."\r\n";

						//if(isset($this->buf[1])&&$this->buf[0]=='>'&&$this->buf[1]==' ') {
						//	echo "\n\n>>>>>>>>>>>>\n\n";
						//	$this->buf = ">\r\n";
						//}

					} else break;
				}

				if(preg_match("/^\+CMT\:(.+)$/s",$this->buf)&&!preg_match("/\+CMT\:(.+)\r\n(.+)\r\n$/s",$this->buf)) {
					usleep(10000);//0.2 sec
					continue;
				}

				for($i=0;$i<strlen($this->buf);$i++) {
					if(isset($this->buf[$i+1])&&ord($this->buf[$i])==13&&ord($this->buf[$i+1])==10) {

						$str = substr($this->buf, 0, ($i+2));

						$this->buf = str_replace($str, '', $this->buf);

						$this->buffer .= $str;
					}
				}

				if($this->showbuf) {
					echo $this->tocrlf('$buffer => '. $buffer) . ' / ' . $this->tocrlf('$this->buffer => '. $this->buffer)."\r\n";
				}

				$ret = false;

				if ($expected_result&&preg_match('/'.preg_quote($expected_result, '/').'$/s', $this->buffer, $match)) {
					$tbuf = $this->buffer;
					//$this->adata = $exp = explode("\r\n", $tbuf);
					$this->adata = $exp = explode("\r", $tbuf);

					$history = array();

					$history['regx'] = $this->tocrlf($expected_result);
					$history['flat'] = $this->tocrlf($tbuf);
					$history['timestamp'] = time();

					foreach($exp as $v) {
						$v = trim($v);
						if(!empty($v)) {
							$history[] = $this->tocrlf($v);
						}
					}

					if(!empty($history)) {
						$this->history[] = $this->current = $history;
					}

					$this->result = $match;

					$this->buffer = str_replace($match[0],'',$this->buffer);
					//print_r(array('$exp'=>$exp,'$tbuf'=>'{'.$this->tocrlf($tbuf).'}','$match1'=>$match,'$this->buffer'=>'{'.$this->tocrlf($this->buffer).'}'));
					$ret = true;
				} else
				if ($expected_result&&preg_match('/'.preg_quote($expected_result, '/').'/s', $this->buffer, $match)) {
					$tbuf = $this->buffer;
					//$this->adata = $exp = explode("\r\n", $tbuf);
					$this->adata = $exp = explode("\r", $tbuf);

					$history = array();

					$history['regx'] = $this->tocrlf($expected_result);
					$history['flat'] = $this->tocrlf($tbuf);
					$history['timestamp'] = time();

					foreach($exp as $v) {
						$v = trim($v);
						if(!empty($v)) {
							$history[] = $this->tocrlf($v);
						}
					}

					if(!empty($history)) {
						$this->history[] = $this->current = $history;
					}

					$this->result = $match;

					$this->buffer = str_replace($match[0],'',$this->buffer);
					//print_r(array('$exp'=>$exp,'$tbuf'=>'{'.$this->tocrlf($tbuf).'}','$match2'=>$match,'$this->buffer'=>'{'.$this->tocrlf($this->buffer).'}'));
					$ret = true;
				} else
				if ($expected_result&&preg_match('/'.$expected_result.'/s', $this->buffer, $match)) {
					$tbuf = $this->buffer;
					//$this->adata = $exp = explode("\r\n", $tbuf);
					$this->adata = $exp = explode("\r", $tbuf);

					$history = array();

					$history['regx'] = $this->tocrlf($expected_result);
					$history['flat'] = $this->tocrlf($tbuf);
					$history['timestamp'] = time();

					foreach($exp as $v) {
						$v = trim($v);
						if(!empty($v)) {
							$history[] = $this->tocrlf($v);
						}
					}

					if(!empty($history)) {
						$this->history[] = $this->current = $history;
					}

					$this->result = $match;

					$this->buffer = str_replace($match[0],'',$this->buffer);
					//print_r(array('$exp'=>$exp,'$tbuf'=>'{'.$this->tocrlf($tbuf).'}','$match2'=>$match,'$this->buffer'=>'{'.$this->tocrlf($this->buffer).'}'));
					$ret = true;
				}


				if($expected_result&&$ret) {
					//$this->buffer = $this->buf = '';
					$this->buffer = '';
					return true;
				}

				if($expected_result&&!$ret) {
				} else {
					$this->process();
				}

				if (preg_match('/\+CME ERROR\:\ (\d{1,3})\r\n$/', $this->buffer, $match)) {
					$tbuf = $this->buffer;
					//$this->adata = $exp = explode("\r\n", $tbuf);
					$this->adata = $exp = explode("\r", $tbuf);

					$history = array();

					if(!empty($expected_result)) {
						$history['regx'] = $this->tocrlf($expected_result);
						$history['flat'] = $this->tocrlf($tbuf);
						$history['timestamp'] = time();
					}

					foreach($exp as $v) {
						$v = trim($v);
						if(!empty($v)) {
							$history[] = $this->tocrlf($v);
						}
					}

					if(!empty($history)) {
						$this->history[] = $this->current = $history;
					}

					$this->buffer = str_replace($match[0],'',$this->buffer);

					//print_r(array('$match'=>$match));

					$this->cmeerror = $match[1];
					$this->buffer = '';

					return false;
				} else

				if (preg_match('/\+CMS ERROR\:\ (\d{1,3})\r\n$/', $this->buffer, $match)) {
					$tbuf = $this->buffer;
					//$this->adata = $exp = explode("\r\n", $tbuf);
					$this->adata = $exp = explode("\r", $tbuf);

					$history = array();

					if(!empty($expected_result)) {
						$history['regx'] = $this->tocrlf($expected_result);
						$history['flat'] = $this->tocrlf($tbuf);
						$history['timestamp'] = time();
					}

					foreach($exp as $v) {
						$v = trim($v);
						if(!empty($v)) {
							$history[] = $this->tocrlf($v);
						}
					}

					if(!empty($history)) {
						$this->history[] = $this->current = $history;
					}

					$this->buffer = str_replace($match[0],'',$this->buffer);

					//print_r(array('$match'=>$match));

					$this->cmserror = $match[1];
					$this->buffer = '';

					return false;
				}


				if (preg_match('/ERROR\r\n$/', $this->buffer, $match)) {
					$tbuf = $this->buffer;
					//$this->adata = $exp = explode("\r\n", $tbuf);
					$this->adata = $exp = explode("\r", $tbuf);

					$history = array();

					if(!empty($expected_result)) {
						$history['regx'] = $this->tocrlf($expected_result);
						$history['flat'] = $this->tocrlf($tbuf);
						$history['timestamp'] = time();
					}

					foreach($exp as $v) {
						$v = trim($v);
						if(!empty($v)) {
							$history[] = $this->tocrlf($v);
						}
					}

					if(!empty($history)) {
						$this->history[] = $this->current = $history;
					}

					$this->buffer = str_replace($match[0],'',$this->buffer);

					//print_r(array('$match'=>$match));

					//$this->cmserror = $match[1];
					$this->buffer = '';

					return false;
				}

				usleep(200000);//0.2 sec

			} while ($timeoutat > time());

			$tbuf = $this->buffer;

			$this->adata = $exp = explode("\r", $tbuf);

			$history = array();

			if(!empty($expected_result)) {
				$history['regx'] = $this->tocrlf($expected_result);
				$history['flat'] = $this->tocrlf($tbuf);
				$history['timestamp'] = time();
			}

			foreach($exp as $v) {
				$v = trim($v);
				if(!empty($v)) {
					$history[] = $this->tocrlf($v);
				}
			}

			$history[] = 'Timed Out ('.$timeout.') ('.$commandstr.')! ('.$this->tocrlf($expected_result).')';

			/*ob_start();

			debug_print_backtrace();

			$history[] = ob_get_contents();

			ob_end_clean();*/

			if(!empty($history)) {
				$this->history[] = $this->current = $history;
			}

			return false;
		}

		public function readRFIDPort($expected_result=false, $timeout=0, $showbuffer=false, $commandstr='') {
			global $appdb;

			if(!empty($expected_result)&&is_numeric($expected_result)) {
				$timeout = $expected_result;
				$expected_result = false;
			}

			//$this->buffer = '';

			$timeoutat = time() + $timeout;

			$cmt = array();

			if(empty($this->handle)) {
				trigger_error("Invalid handle", E_USER_WARNING);
				return false;
			}

			if(!is_resource($this->handle)) {
				trigger_error("Invalid handle", E_USER_WARNING);
				return false;
			}

			$rfid = array();

			$curl = new MyCurl;

			do {

				$buffer = fread($this->handle, 1024);

				if($buffer===false) {
					trigger_error("An error has occured (".$this->getLastMessage().")", E_USER_WARNING);
					return false;
				}

				//if($this->showbuf&&$buffer) {
					//echo ".\r\n";
					//$echo = $this->tocrlf('$buf => '. $buffer)."\r\n";
					//$echo = str_replace(chr(26),'(0x26)',$echo);
					//$tohex = $this->str2hex2($buffer)."\r\n";
					//echo $echo;
					//echo strtoupper($tohex);
				//}

				$this->buf .= $buffer;

				if(strlen($this->buf)<1) {
					usleep(10000); //0.02 sec
					continue;
				}

				if((strlen($this->buf) % 8)==0) {
					$len = strlen($this->buf);
					//echo "\n(strlen = $len )\n";
					log_notice("\n(strlen = $len )\n");
					$tohex = strtoupper($this->str2hex2($buffer));
					//echo '['.$tohex."]\r\n";
					log_notice('['.$tohex."]\r\n");
					$this->buf = '';

					while(1) {
						if(strlen($tohex)>16) {
							$t = substr($tohex,0,16);
							if(substr($tohex,0,8)=='0700EE00') {
								if(!empty($rfid[$t])) {
								} else {
									$rfid[$t] = $t;

									$content = array();
									$content['rfidqueue_rfid'] = $t;

									if(!($result = $appdb->insert("tbl_rfidqueue",$content,"rfidqueue_id"))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;
									}

									//$curl->get('http://127.0.0.1:8080/rfidreader/'.$t.'/');
								}
							}
							$tohex = str_replace($t,'',$tohex);
							log_notice('['.$tohex."]\r\n");
						} else {
							if(strlen($tohex)==16) {
								if(substr($tohex,0,8)=='0700EE00') {
									if(!empty($rfid[$tohex])) {
									} else {
										$rfid[$tohex] = $tohex;
										//$curl->get('http://127.0.0.1:8080/rfidreader/'.$tohex.'/');

										$content = array();
										$content['rfidqueue_rfid'] = $tohex;

										if(!($result = $appdb->insert("tbl_rfidqueue",$content,"rfidqueue_id"))) {
											json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
											die;
										}

									}
								}
							}
							break;
						}
					}

					//print_r(array('$rfid'=>$rfid));
					log_notice(array('$rfid'=>$rfid));

				}

				usleep(200000);//0.2 sec

			} while ($timeoutat > time());

			$tbuf = $this->buffer;

			$this->adata = $exp = explode("\r", $tbuf);

			$history = array();

			if(!empty($expected_result)) {
				$history['regx'] = $this->tocrlf($expected_result);
				$history['flat'] = $this->tocrlf($tbuf);
				$history['timestamp'] = time();
			}

			foreach($exp as $v) {
				$v = trim($v);
				if(!empty($v)) {
					$history[] = $this->tocrlf($v);
				}
			}

			$history[] = 'Timed Out ('.$timeout.') ('.$commandstr.')! ('.$this->tocrlf($expected_result).')';

			/*ob_start();

			debug_print_backtrace();

			$history[] = ob_get_contents();

			ob_end_clean();*/

			if(!empty($history)) {
				$this->history[] = $this->current = $history;
			}

			return false;
		}

		public function process() {

		}

		public function process2() {

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
				print_r(array('$match7'=>$match));
				$this->buffer = str_replace($match[0],'',$this->buffer);
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

		public function deviceSet($device) {
	        if ($this->state !== DEVICE_OPENED) {
	            if ($this->os === "linux") {
	                if (preg_match("@^COM(\\d+):?$@i", $device, $matches)) {
	                    $device = "/dev/ttyS" . ($matches[1] - 1);
	                }

	                if ($this->exec("stty -F " . $device) === 0) {
	                    $this->device = $device;
	                    $this->state = DEVICE_SET;

	                    return true;
	                }
	            } elseif ($this->os === "osx") {
	                if ($this->exec("stty -f " . $device) === 0) {
	                    $this->device = $device;
	                    $this->state = DEVICE_SET;

	                    return true;
	                }
	            } elseif ($this->_os === "windows") {
	                if (preg_match("@^COM(\\d+):?$@i", $device, $matches)
	                        and $this->_exec(
	                            exec("mode " . $device . " xon=on BAUD=9600")
	                        ) === 0
	                ) {
	                    $this->winDevice = "COM" . $matches[1];
	                    $this->device = "\\.com" . $matches[1];
	                    $this->state = DEVICE_SET;

	                    return true;
	                }
	            }

	            trigger_error("Specified serial port is not valid", E_USER_WARNING);

	            return false;
	        } else {
	            trigger_error("You must close your device before to set an other " .
	                          "one", E_USER_WARNING);

	            return false;
	        }
		}

	    public function deviceOpen($mode = "r+b") {

				//echo "\ndeviceOpen\n";

	        if ($this->state === DEVICE_OPENED) {
	            trigger_error("The device is already opened", E_USER_NOTICE);

	            return true;
	        }

	        if ($this->state === DEVICE_NOTSET) {
	            trigger_error(
	                "The device must be set before to be open",
	                E_USER_WARNING
	            );

	            return false;
	        }

	        if (!preg_match("@^[raw]\\+?b?$@", $mode)) {
	            trigger_error(
	                "Invalid opening mode : ".$mode.". Use fopen() modes.",
	                E_USER_WARNING
	            );

	            return false;
	        }

	        //echo "\nopening device: ".$this->device."\n";

	        $this->handle = fopen($this->device, $mode);

	        //echo "\nopening done.\n";

	        if ($this->handle !== false) {
	            //var_dump($this->handle);
	            stream_set_blocking($this->handle, 0);
	            $this->state = DEVICE_OPENED;

	            return true;
	        }

	        $this->handle = null;
	        trigger_error("Unable to open the device", E_USER_WARNING);

	        return false;
	    }

		public function deviceClose() {
	        if ($this->state !== DEVICE_OPENED) {
	            return true;
	        }

	        if (fclose($this->handle)) {
	            $this->handle = NULL;
	            $this->state = DEVICE_SET;

	            return true;
	        }

	        trigger_error("Unable to close the device", E_USER_ERROR);

	        return false;
		}

	    public function setBaudRate($rate) {

				//echo "\nsetBaudRate\n";

	        if ($this->state !== DEVICE_OPENED) {
	            trigger_error("Unable to set the baud rate : the device is " .
	                          "either not set or opened", E_USER_WARNING);

	            return false;
	        }

	        //$validParams = 'icanon isig iexten -echo -icrnl ixon -ixany imaxbel brkint opost -onlcr';

	        //$validParams = '-icanon -isig -iexten -echo -icrnl ixon -ixany -imaxbel -brkint -opost -onlcr -igncr -inlcr';

	        $validParams = '-icanon -isig -iexten -echo -icrnl ixon -ixany -imaxbel -brkint -opost -onlcr -igncr -inlcr';

	        $validBauds = array (
	            110    => 11,
	            150    => 15,
	            300    => 30,
	            600    => 60,
	            1200   => 12,
	            2400   => 24,
	            4800   => 48,
	            9600   => 96,
	            19200  => 19,
	            38400  => 38400,
	            57600  => 57600,
	            115200 => 115200
	        );

	        if (isset($validBauds[$rate])) {
	            if ($this->os === "linux") {
	                $ret = $this->exec(
	                    "stty -F " . $this->device . " " . (int) $rate . " " . $validParams,
	                    $out
	                );
	            } elseif ($this->os === "osx") {
	                $ret = $this->exec(
	                    "stty -f " . $this->device . " " . (int) $rate,
	                    $out
	                );
	            } elseif ($this->os === "windows") {
	                $ret = $this->exec(
	                    "mode " . $this->winDevice . " BAUD=" . $validBauds[$rate],
	                    $out
	                );
	            } else {
	                return false;
	            }

	            if ($ret !== 0) {
	                trigger_error(
	                    "Unable to set baud rate: " . $out[1],
	                    E_USER_WARNING
	                );

	                return false;
	            }

							//echo 'hello!';

            	$this->sendMessageOk("ATE1\r\n",1);

            	$this->sendMessageOk("AT\r\n",1);

	            return true;
	        } else {
	            return false;
	        }
	    }

		public function exec($cmd, &$out = null) {
	        $desc = array(
	            0 => array("pipe", "r"),
	            1 => array("pipe", "w"),
	            2 => array("pipe", "w")
	        );

	        $proc = proc_open($cmd, $desc, $pipes);

	        $ret = stream_get_contents($pipes[1]);
	        $err = stream_get_contents($pipes[2]);

	        //print_r(array('$ret'=>$ret,'$err'=>$err));

	        fclose($pipes[1]);
	        fclose($pipes[2]);

	        $retVal = proc_close($proc);

	        if (func_num_args() == 2) $out = array($ret, $err);
	        return $retVal;
		}

		public function dechex_str($ref) {
			$hex = ($ref <= 15 )?'0'.dechex($ref):dechex($ref);
			return strtoupper($hex);
		}

	    public function strlen($string, $encoding = 'UTF-8') {
	        //@check
	        return  mb_strlen($string, $encoding);
	    }

	    private function substring($string, $start = null, $end = null ,$encoding = 'UTF-8') {
	        if(isset($start) && isset($end)){
	            if($start > $end){
	                list($start,$end) = array($end,$start);
	            }else if($start < 0){
	                $start = 0;
	            }
	            return  mb_substr( $string, $start, $end - $start,$encoding);
	        }else if(isset($start) && !isset($end)){
	            if($start < 0){
	                $start = 0;
	            }
	            return  mb_substr( $string, $start);
	        }
	        return false;
	    }

	    public function HexToNum($numberS){

	        $tens = $this->MakeNum($this->substring($numberS,0,1));
	        $ones = 0;

	        if($this->strlen($numberS) > 1){
	            $ones = $this->MakeNum($this->substring($numberS,1,2));
	        }

	        if($ones == 'X'){
	            return '00';
	        }

	        return  ($tens * 16) + ($ones * 1);
	    }

	    private function MakeNum($string){
	        //@check
	        if(($string >= '0') && ($string <= '9')){
	            return $string;
	        }

	        switch(strtoupper($string))
	        {
	            case 'A': return 10;
	            case 'B': return 11;
	            case 'C': return 12;
	            case 'D': return 13;
	            case 'E': return 14;
	            case 'F': return 15;
	            default:
	                return 16;
	        }
	    }
	}
}

/* INCLUDES_END */
