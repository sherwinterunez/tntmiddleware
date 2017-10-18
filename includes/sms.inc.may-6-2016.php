<?php

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

		public $udh = array();

		public function SMS() {

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

	        print_r($sysName);

	        if (substr($sysName, 0, 5) === "Linux") {
	            $this->os = "linux";

	            if ($this->exec("stty") === 0) {
	                register_shutdown_function(array($this, "deviceClose"));
	            } else {
	                trigger_error(
	                    "No stty availible, unable to run.",
	                    E_USER_ERROR
	                );
	            }
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

	        print_r(array('$this->os'=>$this->os));
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

	    public function sendMessageOk($str, $timeout=5) {
	    	$this->sendMessage($str);

			//if($this->readReply("OK\r\n", $timeout)) {
			if($this->readPort("OK\r\n", $timeout)) {
				return true;
			}
			return false;
	    }

		public function readReply($expected_result=false, $timeout=0, $showbuffer=false) {

			$this->buffer = '';

			if(empty($expected_result)) return false;

			$timeoutat = time() + $timeout;

			$timestart = (time() + microtime());

			//Loop until timeout reached (or expected result found)
			do {

				$buffer = fread($this->handle, 1024);

				if($showbuffer) {
					echo $buffer;
				}

				$this->buffer .= $buffer;

				usleep(10000);//0.2 sec

				if (preg_match('/'.preg_quote($expected_result, '/').'$/', $this->buffer, $match)) {
					print_r(array('readReply'=>'Found match1', '$expected_result'=>$expected_result, '$this->buffer'=>$this->buffer));
					$this->buffer = str_replace($match[0],'',$this->buffer);
					return true;
				} else 
				if (preg_match('/'.preg_quote($expected_result, '/').'/', $this->buffer, $match)) {
					print_r(array('readReply'=>'Found match2', '$expected_result'=>$expected_result, '$this->buffer'=>$this->buffer));
					$this->buffer = str_replace($match[0],'',$this->buffer);
					return true;
				} else 
				if (preg_match('/\+CME ERROR\:\ (\d{1,3})\r\n$/', $this->buffer, $match)) {
					print_r(array('readReply'=>$this->buffer,'match'=>$match));
					$this->buffer = str_replace($match[0],'',$this->buffer);
					return false;
				} else
				if (preg_match('/\+CMS ERROR\:\ (\d{1,3})\r\n$/', $this->buffer, $match)) {
					print_r(array('readReply'=>$this->buffer,'match'=>$match));
					$this->buffer = str_replace($match[0],'',$this->buffer);
					return false;
				} else {
					//print_r(array('readReply'=>$this->buffer));					
				}

			} while ($timeoutat > time());

			return false;

		}

		public function readPort($expected_result=false, $timeout=0, $showbuffer=false) {

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

				echo $buffer;

				// 0x82 0x40 0x6F 0xE4 0x2C 0x20 0x40

				// 0x82 0x40 0x6F 0xE4 0x2C 0xAD 0x40


				// 0x82 0x40 0x6F 0x20 0x2C 0x20 0x40

				// 0x82 0x40 0x6F 0x20 0x2C 0xAD 0x40


				// 0x82 0x40 0x6F 0x28 0x2C 0x20 0x40

				// 0x82 0x40 0x6F 0x28 0x2C 0xAD 0x40


				/*if(preg_match("/\+CMT\:[^\"]+\"([^\"]+)\"\,[^\,]+\,\"([^\"]+)\"[^\@]+\@[^\@]+\@(.+)\r\n$/s",$this->buffer, $match)) {
					print_r(array('$match1'=>$match));
					$this->buffer = '';
				} else
				if(preg_match("/\+CMT\:[^\"]+\"([^\"]+)\"\,\,\"([^\"]+)\"[^\@]+\@[^\@]+\@(.+)\r\n$/s",$this->buffer, $match)) {
					print_r(array('$match1'=>$match));
					$this->buffer = '';
				} else*/

				/*$i=0;

				do {
					if(ord($this->buf[$i])==13&&ord($this->buf[$i+1])==10) {
						print_r(array('\r\n'=>substr($this->buf, $i)));
					}
					$i++;
				} while(isset($this->buf[$i+1]));*/

				if(strlen($this->buf)<1) continue;

				//print_r(array('sherwin'=>'sherwin','$this->buf'=>'['.$this->buf.']','strlen($this->buf)'=>strlen($this->buf),'hex'=>strtoupper($this->str2hex($this->buf))));

				//if(strlen($this->buf)<2) continue;

				//print_r(array('$this->buf[1]'=>$this->buf[1]));

				if(isset($this->buf[1])&&ord($this->buf[0])==13&&ord($this->buf[1])==10) {
				//if(isset($this->buf[1])) {

					$this->buf = substr($this->buf, 2);

					//print_r(array('hello'=>'hello','$this->buf'=>'['.$this->buf.']','strlen($this->buf)'=>strlen($this->buf),'hex'=>strtoupper($this->str2hex($this->buf))));
				}

				if(preg_match("/^\+CMT\:(.+)$/s",$this->buf)&&!preg_match("/\+CMT\:(.+)\r\n(.+)\r\n$/s",$this->buf)) {
					continue;
				}

				for($i=0;$i<strlen($this->buf);$i++) {
					if(isset($this->buf[$i+1])&&ord($this->buf[$i])==13&&ord($this->buf[$i+1])==10) {

						$str = substr($this->buf, 0, ($i+2));

						//print_r(array('before \r\n'=>'{'.$str.'}','$this->buf'=>'['.$this->buf.']','strlen($this->buf)'=>strlen($this->buf),'$i'=>$i,'hex'=>strtoupper($this->str2hex($this->buf))));

						$this->buf = str_replace($str, '', $this->buf);

						//print_r(array('after \r\n'=>'{'.$str.'}','$this->buf'=>'['.$this->buf.']','strlen($this->buf)'=>strlen($this->buf),'$i'=>$i,'hex'=>strtoupper($this->str2hex($this->buf))));

						$this->buffer .= $str;
						//die;
					}
				}

				//if(!preg_match("/\r\n/s", $this->buf)) continue;
				
				/*if(preg_match("/^[^\r]+\r\n/s", $this->buf, $match)) {
					$this->buffer .= $match[0];
					$this->buf = str_replace($match[0],'',$this->buf);
					print_r(array('readPort'=>'Found match0', '$expected_result'=>$expected_result, '$this->buf'=>'{'.$this->buf.'}', '$this->buffer'=>'{'.$this->buffer.'}','$match'=>$match));
				}*/

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

	        $this->handle = fopen($this->device, $mode);

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

	        if ($this->state !== DEVICE_OPENED) {
	            trigger_error("Unable to set the baud rate : the device is " .
	                          "either not set or opened", E_USER_WARNING);

	            return false;
	        }

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
	                    "stty -F " . $this->device . " " . (int) $rate,
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

	            return true;
	        } else {
	            return false;
	        }
	    }

		public function exec($cmd, &$out = null) {
	        $desc = array(
	            1 => array("pipe", "w"),
	            2 => array("pipe", "w")
	        );

	        $proc = proc_open($cmd, $desc, $pipes);

	        $ret = stream_get_contents($pipes[1]);
	        $err = stream_get_contents($pipes[2]);

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

	    private function HexToNum($numberS){

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
