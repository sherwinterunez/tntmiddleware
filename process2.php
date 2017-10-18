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

function modemFunction($sms=false,$simfunctions=false) {

	if(!empty($sms)&&!empty($simfunctions)) {
	} else {
		return false;
	}

	if(!empty($simfunctions)) {
		foreach($simfunctions as $func) {

			if(!empty($func['command'])) {

				$REGX = '';

				if(!empty($func['regx'])) {

					if(!empty($func['regx'][0])&&is_array($func['regx'])) {
						$REGX = $func['regx'][0];						
					} else {
						$REGX = $func['regx'];						
					}

					/*if(!empty($func['param'])) {
						if(is_array($func['param'])) {

						} else {
							$REGX = str_replace('<param>',$func['param'],$REGX);
						}
					}*/
				}

				$FUNC = trim($func['command']);

				//if(isset($gotresult)) {
				//	$FUNC = str_replace('%result%', $gotresult, $FUNC);
				//}

				if(isset($lastresult)&&is_array($lastresult)) {

					//print_r(array('$lastresult'=>$lastresult));

					foreach($lastresult as $k=>$v) {
						$FUNC = str_replace('$'.$k, $v, $FUNC);
					}
				}

				if(!empty($REGX)) {

					print_r(array('$FUNC'=>$FUNC));

					$flag = true;

					$repeatCtr = false;

					if(!empty($func['repeat'])) {
						$repeatCtr = intval($func['repeat']);
					}

					$break = false;

					do {

						if($repeatCtr) {
							$repeatCtr--;							
						}

						if($sms->sendMessageReadPort($FUNC."\r\n", $REGX)) {
							$result = $sms->getResult();
							$result['flat'] = $sms->tocrlf($result[0]);

							print_r(array('$result'=>$result));

							if(!empty($func['regx'][1])&&is_array($func['regx'])) {
								for($i=1;$i<count($func['regx']);$i++) {
									print_r(array('regx'=>$func['regx'][$i]));
									if(preg_match('/'.$func['regx'][$i].'/s',$result[0],$result)) {
										print_r(array('regx'=>$func['regx'][$i],'$result'=>$result));
									} else {
										$flag = false;
										break;
									}
								}
							}

							if(!empty($flag)) {
								//print_r(array('$result'=>$result));
							} else {
								print_r(array('$repeatCtr'=>$repeatCtr));
								if(!$repeatCtr) {
									$break = true;
									break;									
								}
							}

							$lastresult = $result;

							if(isset($func['resultindex'])&&is_numeric($func['resultindex'])) {
								$index = intval(trim($func['resultindex']));
								if(isset($result[$index])) {
									$gotresult = $result[$index];

									if(isset($func['expectedresult'])) {
										if(preg_match('/'.$func['expectedresult'].'/s',$gotresult,$match)) {
											print_r(array('$repeatCtr'=>$repeatCtr,'$match'=>$match));
											$repeatCtr = 0;
										} else {
											if(!$repeatCtr) {
												$break = true;
												break;
											}
										}
									}
								}
							}

							//print_r(array('current'=>$sms->getCurrent(),'result'=>$result,'gotresult'=>$gotresult));
						} else {
							//print_r(array('current'=>$sms->getCurrent()));
							$break = true;
							break;
						}

					} while($repeatCtr);

					if($break) break; 

				} else {

				} // if(!empty($REGX)) {

			} // if(!empty($func['command'])) {

		} // foreach($simfunctions as $func) {
			
	} // if(!empty($simfunctions)) {

	print_r(array('history'=>$sms->getHistory()));

	return true;
}

function processSMSCommands($dev=false) {
	global $appdb;

	if(!empty($dev)) {
	} else {
		return false;
	}

	echo "\nprocessSMSCommands starting ($dev).\n";
 
	$sms = new APP_SMS;

	if(!$sms->deviceInit($dev,115200)) {
		die('Error initializing device!');
	}

	echo "\nprocessSMSCommands started ($dev).\n";

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
		echo "\nprocessSMSCommands failed (AT+CNUM) ($dev).\n";
		$sms->deviceClose();
		return false;
		//die('An error has occured!');
	}

	if(!($result=$appdb->query("select * from tbl_sim where sim_disabled=0 and sim_deleted=0 and sim_online=1 and sim_hotline=0 and sim_number='$mobileNo'"))) {
		return false;
	}

	if(!empty($result['rows'][0]['sim_id'])) {
	} else {
		return true;
	}

	if(!($result=$appdb->query('select * from tbl_smscommands where smscommands_active=1 order by smscommands_priority'))) {
		return false;
	}

	if(!empty($result['rows'][0]['smscommands_id'])) {
		//print_r(array('$result'=>$result['rows']));
		$smscommands = $result['rows'];
	}

	if(!empty($smscommands)) {

		if(!($result = $appdb->query("select * from tbl_smsinbox where smsinbox_deleted=0 and smsinbox_processed=0 order by smsinbox_id asc"))) {
			return false;
		}

		if(!empty($result['rows'][0]['smsinbox_id'])) {

			$inboxrows = $result['rows'];

			//print_r(array('$result'=>$result['rows']));

			foreach($inboxrows as $irow) {

				/*if(!empty($irow['smsinbox_contactsid'])) {
				} else {
					continue;
				}*/

				$str = trim($irow['smsinbox_message']);

				$smsinbox_id = $irow['smsinbox_id'];
				$smsinbox_contactnumber = $irow['smsinbox_contactnumber'];

				//print_r(array('$str'=>$str));

				do {
					$str = str_replace('  ', ' ', trim($str));
					$str = str_replace("\n",' ', trim($str));
					$str = str_replace("\r",' ', trim($str));
					//echo '.';
				} while(preg_match('#\s\s#si', $str));

				//$keys = explode(' ', $str);

				//pre(array('$keys'=>$keys));

				foreach($smscommands as $smsc) {

					$allmatched = array();

					$smscommands_key0 = getOption($smsc['smscommands_key0']);

					$regstr = $smscommands_key0;

					$regx = '/'.$regstr.'/si';

					//print_r(array('regx'=>$regx));

					$matched = false;

					if(preg_match($regx,$str,$match)) {

						$matched = true;
						//print_r(array('$smscommands_key0'=>$smscommands_key0,'$match'=>$match,'$smsc'=>$smsc));

						if(isset($match[1])) {
							$allmatched[$smsc['smscommands_key0']] = $match[1];
						} else {
							$allmatched[$smsc['smscommands_key0']] = $match[0];							
						}

					} else {
						$matched = false;						
					}

					if($matched&&!empty($smsc['smscommands_key1'])) {

						$smscommands_key1 = getOption($smsc['smscommands_key1']);

						$regstr .= '\s+'.$smscommands_key1;

						$regx = '/'.$regstr.'/si';

						//print_r(array('regx'=>$regx));

						if(preg_match($regx,$str,$match)) {

							$matched = true;

							if(preg_match('/'.$smscommands_key1.'/si',$str,$match)) {
								if(isset($match[1])) {
									$allmatched[$smsc['smscommands_key1']] = $match[1];
								} else {
									$allmatched[$smsc['smscommands_key1']] = $match[0];									
								}
							}

							//print_r(array('$smscommands_key1'=>$smscommands_key1,'$match'=>$match,'$smsc'=>$smsc));

						} else {
							$matched = false;
						}
					}

					if($matched&&!empty($smsc['smscommands_key2'])) {

						$smscommands_key2 = getOption($smsc['smscommands_key2']);

						$regstr .= '\s+'.$smscommands_key2;

						$regx = '/'.$regstr.'/si';

						//print_r(array('regx'=>$regx));

						if(preg_match($regx,$str,$match)) {

							$matched = true;

							//if(preg_match('/'.$smscommands_key2.'/si',$str,$match)) {
							//	$allmatched[$smsc['smscommands_key2']] = $match[1];
							//}

							if(preg_match('/'.$smscommands_key2.'/si',$str,$match)) {
								if(isset($match[1])) {
									$allmatched[$smsc['smscommands_key2']] = $match[1];
								} else {
									$allmatched[$smsc['smscommands_key2']] = $match[0];									
								}
							}

							//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match,'$smsc'=>$smsc));

						} else {
							$matched = false;
						}
					}

					if($matched&&!empty($smsc['smscommands_key3'])) {

						$smscommands_key3 = getOption($smsc['smscommands_key3']);

						$regstr .= '\s+'.$smscommands_key3;

						$regx = '/'.$regstr.'/si';

						//print_r(array('regx'=>$regx));

						if(preg_match($regx,$str,$match)) {

							$matched = true;

							//if(preg_match('/'.$smscommands_key3.'/si',$str,$match)) {
							//	$allmatched[$smsc['smscommands_key3']] = $match[0];
							//}

							if(preg_match('/'.$smscommands_key3.'/si',$str,$match)) {
								if(isset($match[1])) {
									$allmatched[$smsc['smscommands_key3']] = $match[1];
								} else {
									$allmatched[$smsc['smscommands_key3']] = $match[0];									
								}
							}

							//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match,'$smsc'=>$smsc));

						} else {
							$matched = false;
						}
					}

					if($matched&&!empty($smsc['smscommands_key4'])) {

						$smscommands_key4 = getOption($smsc['smscommands_key4']);

						$regstr .= '\s+'.$smscommands_key4;

						$regx = '/'.$regstr.'/si';

						//print_r(array('regx'=>$regx));

						if(preg_match($regx,$str,$match)) {

							$matched = true;

							//if(preg_match('/'.$smscommands_key4.'/si',$str,$match)) {
							//	$allmatched[$smsc['smscommands_key4']] = $match[0];
							//}

							if(preg_match('/'.$smscommands_key4.'/si',$str,$match)) {
								if(isset($match[1])) {
									$allmatched[$smsc['smscommands_key4']] = $match[1];
								} else {
									$allmatched[$smsc['smscommands_key4']] = $match[0];									
								}
							}

							//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match,'$smsc'=>$smsc));

						} else {
							$matched = false;
						}
					}

					if($matched&&!empty($smsc['smscommands_key5'])) {

						$smscommands_key5 = getOption($smsc['smscommands_key5']);

						$regstr .= '\s+'.$smscommands_key5;

						$regx = '/'.$regstr.'/si';

						//print_r(array('regx'=>$regx));

						if(preg_match($regx,$str,$match)) {

							$matched = true;

							//if(preg_match('/'.$smscommands_key5.'/si',$str,$match)) {
							//	$allmatched[$smsc['smscommands_key5']] = $match[0];
							//}

							if(preg_match('/'.$smscommands_key5.'/si',$str,$match)) {
								if(isset($match[1])) {
									$allmatched[$smsc['smscommands_key5']] = $match[1];
								} else {
									$allmatched[$smsc['smscommands_key5']] = $match[0];									
								}
							}

							//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match,'$smsc'=>$smsc));

						} else {
							$matched = false;
						}
					}

					if($matched&&!empty($smsc['smscommands_key6'])) {

						$smscommands_key6 = getOption($smsc['smscommands_key6']);

						$regstr .= '\s+'.$smscommands_key6;

						$regx = '/'.$regstr.'/si';

						//print_r(array('regx'=>$regx));

						if(preg_match($regx,$str,$match)) {

							$matched = true;

							//if(preg_match('/'.$smscommands_key6.'/si',$str,$match)) {
							//	$allmatched[$smsc['smscommands_key6']] = $match[0];
							//}

							if(preg_match('/'.$smscommands_key6.'/si',$str,$match)) {
								if(isset($match[1])) {
									$allmatched[$smsc['smscommands_key6']] = $match[1];
								} else {
									$allmatched[$smsc['smscommands_key6']] = $match[0];									
								}
							}

							//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match,'$smsc'=>$smsc));

						} else {
							$matched = false;
						}
					}

					if($matched&&!empty($smsc['smscommands_key7'])) {

						$smscommands_key7 = getOption($smsc['smscommands_key7']);

						$regstr .= '\s+'.$smscommands_key7;

						$regx = '/'.$regstr.'/si';

						//print_r(array('regx'=>$regx));

						if(preg_match($regx,$str,$match)) {

							$matched = true;

							//if(preg_match('/'.$smscommands_key7.'/si',$str,$match)) {
							//	$allmatched[$smsc['smscommands_key7']] = $match[0];
							//}

							if(preg_match('/'.$smscommands_key7.'/si',$str,$match)) {
								if(isset($match[1])) {
									$allmatched[$smsc['smscommands_key7']] = $match[1];
								} else {
									$allmatched[$smsc['smscommands_key7']] = $match[0];									
								}
							}

							//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match,'$smsc'=>$smsc));

						} else {
							$matched = false;
						}
					}

					if($matched&&!empty($smsc['smscommands_key8'])) {

						$smscommands_key8 = getOption($smsc['smscommands_key8']);

						$regstr .= '\s+'.$smscommands_key8;

						$regx = '/'.$regstr.'/si';

						//print_r(array('regx'=>$regx));

						if(preg_match($regx,$str,$match)) {

							$matched = true;

							//if(preg_match('/'.$smscommands_key8.'/si',$str,$match)) {
							//	$allmatched[$smsc['smscommands_key8']] = $match[0];
							//}

							if(preg_match('/'.$smscommands_key8.'/si',$str,$match)) {
								if(isset($match[1])) {
									$allmatched[$smsc['smscommands_key8']] = $match[1];
								} else {
									$allmatched[$smsc['smscommands_key8']] = $match[0];									
								}
							}

							//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match,'$smsc'=>$smsc));

						} else {
							$matched = false;
						}
					}

					if($matched&&!empty($smsc['smscommands_key9'])) {

						$smscommands_key9 = getOption($smsc['smscommands_key9']);

						$regstr .= '\s+'.$smscommands_key9;

						$regx = '/'.$regstr.'/si';

						//print_r(array('regx'=>$regx));

						if(preg_match($regx,$str,$match)) {

							$matched = true;

							//if(preg_match('/'.$smscommands_key9.'/si',$str,$match)) {
							//	$allmatched[$smsc['smscommands_key9']] = $match[0];
							//}

							if(preg_match('/'.$smscommands_key9.'/si',$str,$match)) {
								if(isset($match[1])) {
									$allmatched[$smsc['smscommands_key9']] = $match[1];
								} else {
									$allmatched[$smsc['smscommands_key9']] = $match[0];									
								}
							}

							//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match,'$smsc'=>$smsc));

						} else {
							$matched = false;
						}
					}

					if($matched) {
						$match['smsinbox_id'] = $smsinbox_id;
						$match['smsinbox_contactnumber'] = $smsinbox_contactnumber;
						$match['network'] = getNetworkName($smsinbox_contactnumber);
						$match['allmatched'] = $allmatched;

						//$MOBILENUMBER = $match[];

						//print_r(array('regx'=>$regx,'$str'=>$str,'$match'=>$match));

						break;

					} else {
			
						//print_r(array('nomatched'=>$str));

					}

				} // foreach($smscommands as $smsc) {

				if($matched) {
					print_r(array('regx'=>$regx,'$str'=>$str,'$match'=>$match));

					//if(getNetworkName($smsinbox_contactnumber)) {

					///} /// 

					if(!empty($smsc['smscommands_action0'])&&is_callable($smsc['smscommands_action0'],false,$callable_name)) {
						$callable_name(array('mobileNo'=>$mobileNo,'regx'=>$regstr,'smscommands'=>$smsc,'smsinbox'=>$irow));
					}

					//return true;


					if(!($result = $appdb->query('select * from tbl_smsactions where smsactions_smscommandsid='.$smsc['smscommands_id']." and smsactions_simnumber='".$mobileNo."'"))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						die;				
					}

					if(!empty($result['rows'][0]['smsactions_id'])) {

						$validNetwork = false;
						$smsactions_action = false;
						$validModemCommands = false;

						foreach($result['rows'] as $row) {
							if(getNetworkName($smsinbox_contactnumber)==getNetworkName($row['smsactions_simnumber'])) {
								$validNetwork = true;
								$smsactions_action = $row['smsactions_action'];
								break;
							}
						}

						if(!$validNetwork) {
							echo "\nCannot process command due to Destination Invalid Network.\n";
						}

						if($validNetwork) {

							if(!($result = $appdb->query("select * from tbl_modemcommands where modemcommands_name='$smsactions_action'"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['modemcommands_id'])) {
								//print_r(array('$result'=>$result['rows']));
								$validModemCommands = $result['rows'][0]['modemcommands_id'];
							}

						}

						if($validModemCommands) {

							if(!($result = $appdb->query("select * from tbl_atcommands where atcommands_modemcommandsid='$validModemCommands' order by atcommands_id asc"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['atcommands_id'])) {
								print_r(array('$result'=>$result['rows']));

/*

	$simfunctions[] = array('command'=>'AT','regx'=>array("OK\r\n"),'resultindex'=>0);

	$simfunctions[] = array('command'=>'AT+CUSD=1,0','regx'=>array("\+CUSD\:.+?\r\n","\+CUSD\:\s+(\d+)\r\n"),'resultindex'=>1,'expectedresult'=>4,'repeat'=>100);

	$simfunctions[] = array('command'=>'AT+CUSD=1,*343#','regx'=>array("\+CUSD\:.+?\r\n","(\d+)\:Regular\s+Load"),'resultindex'=>1);

	$simfunctions[] = array('command'=>'AT+CUSD=1,$1','regx'=>array("\+CUSD\:.+?\r\n","(Enter\s+number)"),'resultindex'=>0);

	$simfunctions[] = array('command'=>'AT+CUSD=1,09493621618','regx'=>array("\+CUSD\:.+?\r\n","(Enter\s+Amount)"),'resultindex'=>0);

	$simfunctions[] = array('command'=>'AT+CUSD=1,5','regx'=>array("\+CUSD\:.+?\r\n","(\d+)\:Load"),'resultindex'=>1,'expectedresult'=>1);

	$simfunctions[] = array('command'=>'AT+CUSD=1,$1','regx'=>array("\+CUSD\:.+?\r\n"),'resultindex'=>0);



*/

								$atsc = array();

								foreach($result['rows'] as $row) {
									$t = array();

									$at = $row['atcommands_at'];

									foreach($allmatched as $ak=>$am) {
										$at = str_replace($ak,$am,$at);
									}

									$t['command'] = $at;
									$t['resultindex'] = $row['atcommands_resultindex'];
									$t['expectedresult'] = !empty($row['atcommands_expectedresult']) ? $row['atcommands_expectedresult'] : false;
									$t['repeat'] = !empty($row['atcommands_repeat']) ? $row['atcommands_repeat'] : false;
									$t['regx'] = array();

									for($i=0;$i<10;$i++) {
										if(!empty($row['atcommands_regx'.$i])) {
											$o = getOption($row['atcommands_regx'.$i]);
											if(!empty($row['atcommands_param'.$i])) {
												$o = str_replace('%param%',$row['atcommands_param'.$i],$o);
											}
											$t['regx'][] = $o;
										}
									}

									$atsc[] = $t;
								}

								pre(array('$atsc'=>$atsc));

								if(modemFunction($sms,$atsc)) {

									$content = array();
									$content['smsinbox_processed'] = 1;
									$content['smsinbox_processedby'] = $mobileNo;

									if(!($result=$appdb->update('tbl_smsinbox',$content,'smsinbox_id='.$smsinbox_id))) {

									}

									break;

								}

							}

						}

					}

					//break;

				} else {
					//print_r(array('$smsinbox_id'=>$smsinbox_id,'nomatched'=>$str));

					$content = array();
					$content['smsinbox_processed'] = 2;

					if(!($result=$appdb->update('tbl_smsinbox',$content,'smsinbox_id='.$smsinbox_id))) {

					}

				}

			} // foreach($inboxrows as $irow) {


		} // if(!empty($result['rows'][0]['smsinbox_id'])) {

	}

	$sms->deviceClose();

	$tstop = timer_stop();

	print_r(array('$mobileNo'=>$mobileNo));

	echo "\nprocessSMSCommands (".$tstop." secs).\n";

	return true;
}

/*
print_r(array(getOption('$KEY_QLOAD'),getOption('$PRODUCT_SMART300'),getOption('$MOBILENUMBER')));

print_r(array(getOptionsWithType('KEYCODE'),getOptionValuesWithType('KEYCODE'),getOptionNamesWithType('KEYCODE')));

print_r(array(getOptionsWithType('PRODUCTCODE'),getOptionValuesWithType('PRODUCTCODE'),getOptionNamesWithType('PRODUCTCODE')));
*/

if(getOption('$MAINTENANCE',false)) {
	die("\processSMSCommands: Server under maintenance.\n");
}

//$_GET['dev'] = '/dev/ttyUSB0';

if(!empty($_GET['dev'])) {
	//pre(array('$_GET'=>$_GET));

	setSetting('STATUS_PROCESSSMSCOMMANDS_'.$_GET['dev'],'1');

	if(processSMSCommands($_GET['dev'])) {
		setSetting('STATUS_PROCESSSMSCOMMANDS_'.$_GET['dev'],'0');
	}

}

//processSMSCommands('/dev/ttyUSB0');




