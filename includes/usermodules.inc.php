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

function _SendSMS($vars=array()) {
	global $appdb;

	if(!empty($vars)) {
	} else return false;

	print_r(array('$vars'=>$vars));

	if(preg_match('/'.$vars['regx'].'/si',$vars['smsinbox']['smsinbox_message'],$match)) {

		print_r(array('$match'=>$match));

		//$nickname = trim($match[1]);

		for($i=0;$i<10;$i++) {

			//print_r(array('$i'=>$i));

			if(!empty($vars['smscommands']['smscommands_sendsms'.$i])) {

				$msg = $vars['smscommands']['smscommands_sendsms'.$i];

				//$msg = str_replace('%nickname%',$nickname,$msg);

				//print_r(array('$msg'=>$msg));

				sendToOutBox($vars['smsinbox']['smsinbox_contactnumber'],$vars['smsinbox']['smsinbox_simnumber'],$msg);

			}
		}
	}
}

function _SendSMStoMobileNumber($vars=array()) {
	global $appdb;

	if(!empty($vars)) {
	} else return false;

	//print_r(array('$vars'=>$vars));

	if(preg_match('/'.$vars['regx'].'/si',$vars['smsinbox']['smsinbox_message'],$match)) {

		//print_r(array('$match'=>$match));

		$mobileNo = trim($match[1]);

		for($i=0;$i<10;$i++) {

			//print_r(array('$i'=>$i));

			if(!empty($vars['smscommands']['smscommands_sendsms'.$i])) {

				$msg = $vars['smscommands']['smscommands_sendsms'.$i];

				//$msg = str_replace('%nickname%',$nickname,$msg);

				//print_r(array('$msg'=>$msg));

				sendToOutBox($mobileNo,$vars['smsinbox']['smsinbox_simnumber'],$msg);

			}
		}
	}
}

function _ReferSMSCommand($vars=array()) {
	global $appdb;

	if(!empty($vars)) {
	} else return false;

	print_r(array('$vars'=>$vars));

	if(preg_match('/'.$vars['regx'].'/si',$vars['smsinbox']['smsinbox_message'],$match)) {

		print_r(array('$match'=>$match));

		if($mno = parseMobileNo($match[1])) {

			$mobileNo = '0'.$mno[2].$mno[3];

			$network = getNetworkName($mobileNo);

			$registered = false;

			if(getContactIDByNumber($mobileNo)) {
				$registered = true;

				sendToOutBox($vars['smsinbox']['smsinbox_contactnumber'],$vars['smsinbox']['smsinbox_simnumber'],getOption('$REFERRAL_ALREADY_REGISTERED'));

				return false;
			}

			print_r(array('$mno'=>$mno,'$network'=>$network,'$registered'=>$registered));

			if(!$registered&&$network!='Unknown') {

				if(!($result = $appdb->query("select * from tbl_referral where referral_template>0 order by referral_id asc limit 1"))) {
					return false;
				}

				if(!empty($result['rows'][0]['referral_id'])) {
					$row = $result['rows'][0];
				}

				print_r(array('$row'=>$row));

				if(!empty($row)) {
/////
					$referralsent_title = $row['referral_title'];
					$referralsent_desc = $row['referral_desc'];
					$referralsent_sms = $row['referral_sms'];
					$referralsent_referredby = $vars['smsinbox']['smsinbox_contactnumber'];

					$contactnumber = $mobileNo;

					$simnumber = $vars['smsinbox']['smsinbox_simnumber'];

					$textmsg = getOption('$REFERRAL_MESSAGE') . "\n" . $row['referral_sms'];

					$referralcode_referralcode = generateReferralCode();

					$textmsg = str_replace('%referralcode%', $referralcode_referralcode, $textmsg);
					$textmsg = str_replace('%mobilenumber%', $referralsent_referredby, $textmsg);

					if(strlen($smscontent)>160) {

						// long sms

						$smsparts = str_split($textmsg,152);

						$smsoutbox_udhref = dechex_str(mt_rand(100,250));

						$smsoutbox_total = count($smsparts);

						$content = array();
						//$content['referralsent_contactid'] = getContactIDByNumber($contactnumber);
						$content['referralsent_contactnumber'] = $contactnumber;
						$content['referralsent_title'] = $referralsent_title;
						$content['referralsent_desc'] = $referralsent_desc;
						$content['referralsent_sms'] = $referralsent_sms;
						$content['referralsent_referralcode'] = $referralcode_referralcode;
						$content['referralsent_referredby'] = $referralsent_referredby;

						if(!($result = $appdb->insert("tbl_referralsent",$content,"referralsent_id"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						if(!empty($result['returning'][0]['referralsent_id'])) {

							$smsoutbox_referralsentid = $result['returning'][0]['referralsent_id'];

							$content = array();
							$content['smsoutbox_contactnumber'] = $contactnumber;
							$content['smsoutbox_message'] = $textmsg;
							$content['smsoutbox_udhref'] = $smsoutbox_udhref;
							$content['smsoutbox_part'] = $smsoutbox_total;
							$content['smsoutbox_total'] = $smsoutbox_total;
							$content['smsoutbox_simnumber'] = $simnumber;
							$content['smsoutbox_type'] = 1;
							$content['smsoutbox_referralsentid'] = $smsoutbox_referralsentid;
							$content['smsoutbox_status'] = 1;

							if(!($result = $appdb->insert("tbl_smsoutbox",$content,"smsoutbox_id"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;
							}

							$content = array();
							//$content['referralcode_contactid'] = getContactIDByNumber($contactnumber);
							$content['referralcode_contactnumber'] = $contactnumber;

							if(!($result = $appdb->update("tbl_referralcode",$content,"referralcode_referralcode='$referralcode_referralcode'"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;
							}

						}

					} else {

						// short sms

						$content = array();
						//$content['referralsent_contactid'] = getContactIDByNumber($contactnumber);
						$content['referralsent_contactnumber'] = $contactnumber;
						$content['referralsent_title'] = $referralsent_title;
						$content['referralsent_desc'] = $referralsent_desc;
						$content['referralsent_sms'] = $referralsent_sms;
						$content['referralsent_referralcode'] = $referralcode_referralcode;
						$content['referralsent_referredby'] = $referralsent_referredby;

						if(!($result = $appdb->insert("tbl_referralsent",$content,"referralsent_id"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						if(!empty($result['returning'][0]['referralsent_id'])) {

							$smsoutbox_referralsentid = $result['returning'][0]['referralsent_id'];

							$content = array();
							$content['smsoutbox_contactnumber'] = $contactnumber;
							$content['smsoutbox_message'] = $textmsg;
							$content['smsoutbox_simnumber'] = $simnumber;
							$content['smsoutbox_part'] = 1;
							$content['smsoutbox_total'] = 1;
							$content['smsoutbox_referralsentid'] = $smsoutbox_referralsentid;
							$content['smsoutbox_status'] = 1;

							if(!($result = $appdb->insert("tbl_smsoutbox",$content,"smsoutbox_id"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;
							}

							$content = array();
							//$content['referralcode_contactid'] = getContactIDByNumber($contactnumber);
							$content['referralcode_contactnumber'] = $contactnumber;

							if(!($result = $appdb->update("tbl_referralcode",$content,"referralcode_referralcode='$referralcode_referralcode'"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;
							}
						}
					}
/////
				}

			} else {
				sendToOutBox($vars['smsinbox']['smsinbox_contactnumber'],$vars['smsinbox']['smsinbox_simnumber'],'Invalid mobile number!');
			}

		}

		/*for($i=0;$i<10;$i++) {

			//print_r(array('$i'=>$i));

			if(!empty($vars['smscommands']['smscommands_sendsms'.$i])) {

				$msg = $vars['smscommands']['smscommands_sendsms'.$i];

				//$msg = str_replace('%nickname%',$nickname,$msg);

				//print_r(array('$msg'=>$msg));

				//sendToOutBox($mobileNo,$vars['smsinbox']['smsinbox_simnumber'],$msg);

			}
		}*/
	}
}

function _doProcessSMSCommands($vars=array()) {
	global $appdb;

	if(!empty($vars)) {
	} else return false;

	if(preg_match('/'.$vars['regx'].'/si',$vars['smsinbox']['smsinbox_message'],$match)) {

		print_r(array('$match'=>$match));

		$loadtransaction_keyword = $match[0];

		$simhotline = $vars['smsinbox']['smsinbox_simnumber'];

		if(!($result = $appdb->query('select * from tbl_smsactions where smsactions_smscommandsid='.$vars['smscommands']['smscommands_id']))) {
			return false;
		}

		if(!empty($result['rows'][0]['smsactions_id'])) {

			foreach($result['rows'] as $row) {

				$content = array();
				$content['loadtransaction_contactnumber'] = $vars['smsinbox']['smsinbox_contactnumber'];
				$content['loadtransaction_keyword'] = $loadtransaction_keyword;
				$content['loadtransaction_simhotline'] = $simhotline;
				$content['loadtransaction_simnumber'] = $row['smsactions_simnumber'];
				$content['loadtransaction_smsaction'] = $row['smsactions_action'];

				if(!($result = $appdb->insert("tbl_loadtransaction",$content,"loadtransaction_id"))) {
					return false;
				}

				if(!empty($result['returning'][0]['loadtransaction_id'])) {
					return $result['returning'][0]['loadtransaction_id'];
				}

			}
		}
	}
}

function _eLoadProcessSMS($vars=array()) {
	global $appdb;

	if(!empty($vars)) {
	} else return false;

	if(!($result=$appdb->query("select * from tbl_sim where sim_disabled=0 and sim_deleted=0 and sim_online=1 and sim_hotline=1 and sim_number='".$vars['smsinbox']['smsinbox_simnumber']."'"))) {
		return false;
	}

	if(!empty($result['rows'][0]['sim_id'])) {
	} else {
		return false;
	}

	$simhotline = $vars['smsinbox']['smsinbox_simnumber'];

	print_r(array('$vars'=>$vars));

	if(preg_match('/'.$vars['regx'].'/si',$vars['smsinbox']['smsinbox_message'],$match)) {

		print_r(array('$match'=>$match));

		if(!empty($match[0])&&!empty($match[1])&&!empty($match[2])) {
		} else {

			$errmsg = "Invalid product code. Send PRODUCT for the list of all product codes. Send HELP to get help.";

			$content = array();
			$content['loadtransaction_contactnumber'] = $loadtransaction_contactnumber = $vars['smsinbox']['smsinbox_contactnumber'];
			//$content['loadtransaction_recipientnumber'] = $loadtransaction_recipientnumber;
			$content['loadtransaction_keyword'] = !empty($match[0]) ? $match[0] : '';
			//$content['loadtransaction_amount'] = $loadtransaction_amount;
			$content['loadtransaction_simhotline'] = $simhotline;
			//$content['loadtransaction_productcode'] = $loadtransaction_productcode;
			$content['loadtransaction_invalid'] = 1;
			$content['loadtransaction_completed'] = 2;

			if(!($result = $appdb->insert("tbl_loadtransaction",$content,"loadtransaction_id"))) {
				return false;
			}

			sendToOutBox($loadtransaction_contactnumber,$simhotline,$errmsg);

			if(!empty($result['returning'][0]['loadtransaction_id'])) {
				return $result['returning'][0]['loadtransaction_id'];
			}

			return false;
		}

		$exp = explode(' ', $vars['smsinbox']['smsinbox_message']);

		$loadtransaction_productcode = strtoupper($exp[1]);

		$loadtransaction_keyword = $match[0];

		$loadtransaction_amount = $match[1];

		$mno = parseMobileNo($match[2]);

		print_r(array('$mno'=>$mno));

		$loadtransaction_recipientnumber = '0'.$mno[2].$mno[3];

		if($loadtransaction_amount>=5&&$loadtransaction_amount<=1000) {
		} else {
			$errmsg = smsdt()." ".getOption('$INVALID_AMOUNT');

			$content = array();
			$content['loadtransaction_contactnumber'] = $loadtransaction_contactnumber = $vars['smsinbox']['smsinbox_contactnumber'];
			$content['loadtransaction_recipientnumber'] = $loadtransaction_recipientnumber;
			$content['loadtransaction_keyword'] = $loadtransaction_keyword;
			$content['loadtransaction_amount'] = $loadtransaction_amount;
			$content['loadtransaction_simhotline'] = $simhotline;
			$content['loadtransaction_productcode'] = $loadtransaction_productcode;
			$content['loadtransaction_invalid'] = 1;
			$content['loadtransaction_completed'] = 2;

			if(!($result = $appdb->insert("tbl_loadtransaction",$content,"loadtransaction_id"))) {
				return false;
			}

			sendToOutBox($loadtransaction_contactnumber,$simhotline,$errmsg);

			if(!empty($result['returning'][0]['loadtransaction_id'])) {
				return $result['returning'][0]['loadtransaction_id'];
			}

			return false;
		}

		if(!($result = $appdb->query('select * from tbl_smsactions where smsactions_smscommandsid='.$vars['smscommands']['smscommands_id']))) {
			return false;
		}

		if(!empty($result['rows'][0]['smsactions_id'])) {

			$validNetwork = false;
			$smsactions_action = false;
			$smsactions_simnumber = false;
			$validModemCommands = false;

			foreach($result['rows'] as $row) {
				if(getNetworkName($loadtransaction_recipientnumber)==getNetworkName($row['smsactions_simnumber'])) {
					$validNetwork = true;
					$smsactions_action = $row['smsactions_action'];
					$smsactions_simnumber = $row['smsactions_simnumber'];
					break;
				}
			}

			if(!$validNetwork) {
				//$errmsg = "Cannot process command due to Destination is Invalid Network.";

				//$errmsg = smsdt()." Product $loadtransaction_productcode is unavailable and cannot be loaded to $loadtransaction_recipientnumber.";

				$errmsg = smsdt()." ".getOption('$INVALID_PRODUCTUNAVAILABLECANNOTBELOADED');

				$errmsg = str_replace('%productcode%',$loadtransaction_productcode,$errmsg);
				$errmsg = str_replace('%recipientnumber%',$loadtransaction_recipientnumber,$errmsg);

				$content = array();
				$content['loadtransaction_contactnumber'] = $loadtransaction_contactnumber = $vars['smsinbox']['smsinbox_contactnumber'];
				$content['loadtransaction_recipientnumber'] = $loadtransaction_recipientnumber;
				$content['loadtransaction_keyword'] = $loadtransaction_keyword;
				$content['loadtransaction_amount'] = $loadtransaction_amount;
				$content['loadtransaction_simhotline'] = $simhotline;
				$content['loadtransaction_productcode'] = $loadtransaction_productcode;
				$content['loadtransaction_invalid'] = 1;
				$content['loadtransaction_completed'] = 2;

				if(!($result = $appdb->insert("tbl_loadtransaction",$content,"loadtransaction_id"))) {
					return false;
				}

				sendToOutBox($loadtransaction_contactnumber,$simhotline,$errmsg);

				if(!empty($result['returning'][0]['loadtransaction_id'])) {
					return $result['returning'][0]['loadtransaction_id'];
				}

				return false;
			}
		}

		$content = array();
		$content['loadtransaction_contactnumber'] = $vars['smsinbox']['smsinbox_contactnumber'];
		$content['loadtransaction_recipientnumber'] = $loadtransaction_recipientnumber;
		$content['loadtransaction_keyword'] = $loadtransaction_keyword;
		$content['loadtransaction_amount'] = $loadtransaction_amount;
		$content['loadtransaction_simhotline'] = $simhotline;
		$content['loadtransaction_simnumber'] = $smsactions_simnumber;
		$content['loadtransaction_smsaction'] = $smsactions_action;
		$content['loadtransaction_productcode'] = $loadtransaction_productcode;

		if(!($result = $appdb->insert("tbl_loadtransaction",$content,"loadtransaction_id"))) {
			return false;
		}

		if(!empty($result['returning'][0]['loadtransaction_id'])) {
			return $result['returning'][0]['loadtransaction_id'];
		}

	}

	return false;
}

/*
.+?(?<loadtransaction_simnumber>\d+\d{3}\d{7}).+?loaded(?<loadtransaction_product>.+?)to.+?(?<loadtransaction_recipientnumber>\d+\d{3}\d{7}).+?balance.+?(?<loadtransaction_balance>\d+\.\d+).+?ref.+?(?<loadtransaction_ref>\d+.+)
*/

function _LoadWalletProcessSMS($vars=array()) {
	global $appdb;

	if(empty($vars)) {
		return false;
	}

	print_r(array('$vars'=>$vars));

	if(preg_match('/'.$vars['regx'].'/si',$vars['smsinbox']['smsinbox_message'],$match)) {

		print_r(array('$match'=>$match));

		$where = '1=1';

		if(!empty($match['loadtransaction_simnumber'])) {
			$mno = parseMobileNo($match['loadtransaction_simnumber']);

			$loadtransaction_simnumber = '0'.$mno[2].$mno[3];

			$where .= " and loadtransaction_simnumber='$loadtransaction_simnumber'";
		}

		if(!empty($match['loadtransaction_recipientnumber'])) {
			$mno = parseMobileNo($match['loadtransaction_recipientnumber']);

			$loadtransaction_recipientnumber = '0'.$mno[2].$mno[3];
			$where .= " and loadtransaction_recipientnumber='$loadtransaction_recipientnumber'";
		}

		$sql = "select * from tbl_loadtransaction where $where and loadtransaction_completed=1 and loadtransaction_invalid=0 order by loadtransaction_id asc limit 1";

		print_r(array('$sql'=>$sql));

		if(!($result = $appdb->query($sql))) {
			return false;
		}

		print_r(array('$result'=>$result));

		if(!empty($result['rows'][0]['loadtransaction_id'])) {

			//$content = array();
			//$content['loadtransaction_simnumber'] = $simnumber;

			$content = $result['rows'][0];

			print_r(array('$result'=>$content));

			unset($content['loadtransaction_id']);
			unset($content['loadtransaction_createstamp']);
			unset($content['loadtransaction_execstamp']);

			if(trim($content['loadtransaction_confirmation'])=='') {
				$content['loadtransaction_confirmation'] = $match[0];
			} else {
				$content['loadtransaction_confirmation'] = $content['loadtransaction_confirmation'] . ' ' . $match[0];
			}

			if(!empty($match['loadtransaction_ref'])) {
				$content['loadtransaction_ref'] = $match['loadtransaction_ref'];
			}

			if(!empty($match['loadtransaction_product'])) {
				$content['loadtransaction_product'] = $match['loadtransaction_product'];
			}

			if(!empty($match['loadtransaction_balance'])) {
				$content['loadtransaction_balance'] = $match['loadtransaction_balance'];
			}

			if(!empty($content['loadtransaction_confirmation'])&&!empty($content['loadtransaction_ref'])&&!empty($content['loadtransaction_product'])&&!empty($content['loadtransaction_balance'])) {
				$content['loadtransaction_completed'] = 2;

			}

			$content['loadtransaction_updatestamp'] = 'now()';

			print_r(array('$content'=>$content));

			if(!($result = $appdb->update("tbl_loadtransaction",$content,"loadtransaction_id=".$result['rows'][0]['loadtransaction_id']))) {
				return false;
			}

			if($content['loadtransaction_completed']==2) {

				//$errmsg = smsdt().' Product '.$content['loadtransaction_productcode'].' has been successfully loaded to '.$content['loadtransaction_recipientnumber'].' Ref:'.$content['loadtransaction_ref'].'.';

				$errmsg = smsdt(). ' '.getOption('$SUCCESSFULLY_LOADED');

				$errmsg = str_replace('%productcode%',$content['loadtransaction_productcode'],$errmsg);
				$errmsg = str_replace('%recipientnumber%',$content['loadtransaction_recipientnumber'],$errmsg);
				$errmsg = str_replace('%ref%',$content['loadtransaction_ref'],$errmsg);

				sendToOutBox($content['loadtransaction_contactnumber'],$content['loadtransaction_simhotline'],$errmsg);
			}

		}

	}

	return true;
}

/*
Array
(
    [$match] => Array
        (
            [0] => 21Jun 2101:15(14.7) successfully loaded to 09493621618. bal: 85.30. Ref#005854568113
            [productcode] => 15
            [1] => 15
            [cost] => 14.7
            [2] => 14.7
            [mobileno] => 09493621618
            [3] => 09493621618
            [balance] => 85.30
            [4] => 85.30
            [reference] => 005854568113
            [5] => 005854568113
        )

)
*/

function _OverLoadProcessSMS2($vars=array()) {
	global $appdb;

	if(empty($vars)) {
		return false;
	}

	print_r(array('$vars'=>$vars));

	if(preg_match('/'.$vars['regx'].'/si',$vars['smsinbox']['smsinbox_message'],$match)) {

		print_r(array('$match'=>$match));

		$where = '1=1';

		if(!empty($match['mobileno'])) {
			$mno = parseMobileNo($match['mobileno']);

			$eloadtransaction_mobileno = '0'.$mno[2].$mno[3];
			$where .= " and eloadtransaction_mobileno='$eloadtransaction_mobileno'";
		}

		$sql = "select * from tbl_eloadtransaction where $where and eloadtransaction_completed=0 order by eloadtransaction_id asc limit 1";

		print_r(array('$sql'=>$sql));

		if(!($result = $appdb->query($sql))) {
			return false;
		}

		print_r(array('$result'=>$result));

		if(!empty($result['rows'][0]['eloadtransaction_id'])) {

			//$content = array();
			//$content['loadtransaction_simnumber'] = $simnumber;

			$content = $result['rows'][0];

			print_r(array('$result'=>$content));

			unset($content['eloadtransaction_id']);
			unset($content['eloadtransaction_createstamp']);

			if(trim($content['eloadtransaction_confirmation'])=='') {
				$content['eloadtransaction_confirmation'] = $match[0];
			} else {
				$content['eloadtransaction_confirmation'] = $content['eloadtransaction_confirmation'] . ' ' . $match[0];
			}

			if(!empty($match['cost'])) {
				$content['eloadtransaction_actualcost'] = $match['cost'];
			}

			if(!empty($match['balance'])) {
				$content['eloadtransaction_balance'] = $match['balance'];
			}

			if(!empty($match['reference'])) {
				$content['eloadtransaction_reference'] = $match['reference'];
			}

			$content['eloadtransaction_updatestamp'] = 'now()';

			$content['eloadtransaction_completed'] = 1;

			print_r(array('$content'=>$content));

			if(!($result = $appdb->update("tbl_eloadtransaction",$content,"eloadtransaction_id=".$result['rows'][0]['eloadtransaction_id']))) {
				return false;
			}

		}
	}
}

function _OverLoadProcessSMS($vars=array()) {
	global $appdb;

	if(empty($vars)) {
		return false;
	}

	print_r(array('$vars'=>$vars));

	if(preg_match('/'.$vars['regx'].'/si',$vars['smsinbox']['smsinbox_message'],$match)) {

		/*print_r(array('$match'=>$match));

		$where = '1=1';

		if(!empty($match['mobileno'])) {
			$mno = parseMobileNo($match['mobileno']);

			$eloadtransaction_mobileno = '0'.$mno[2].$mno[3];
			$where .= " and eloadtransaction_mobileno='$eloadtransaction_mobileno'";
		}

		$sql = "select * from tbl_eloadtransaction where $where and eloadtransaction_completed=0 order by eloadtransaction_id asc limit 1";

		print_r(array('$sql'=>$sql));

		if(!($result = $appdb->query($sql))) {
			return false;
		}

		print_r(array('$result'=>$result));

		if(!empty($result['rows'][0]['eloadtransaction_id'])) {*/

			$content = array();
			//$content['loadtransaction_simnumber'] = $simnumber;

			//$content = $result['rows'][0];

			//print_r(array('$result'=>$content));

			//unset($content['eloadtransaction_id']);
			//unset($content['eloadtransaction_createstamp']);

			if(!empty($match['mobileno'])&&($mno = parseMobileNo($match['mobileno']))) {
				$content['eloadtransaction_mobileno'] = '0'.$mno[2].$mno[3];
			}

			$content['eloadtransaction_confirmation'] = $match[0];

			if(!empty($match['cost'])) {
				$content['eloadtransaction_actualcost'] = $match['cost'];
			}

			if(!empty($match['balance'])) {
				$content['eloadtransaction_balance'] = $match['balance'];
			}

			if(!empty($match['reference'])) {
				$content['eloadtransaction_reference'] = $match['reference'];
			}

			if(!empty($match['productcode'])) {
				$content['eloadtransaction_productcode'] = $match['productcode'];
			}

			//$content['eloadtransaction_updatestamp'] = 'now()';

			//$content['eloadtransaction_completed'] = 1;

			//print_r(array('$content'=>$content));

			//if(!($result = $appdb->update("tbl_eloadtransaction",$content,"eloadtransaction_id=".$result['rows'][0]['eloadtransaction_id']))) {
			//	return false;
			//}

			if(!($result = $appdb->insert("tbl_eloadtransaction",$content,"eloadtransaction_id"))) {
				return false;
			}

		//}
	}
}

function _OverLoadBalanceProcessSMS($vars=array()) {
	global $appdb;

	if(empty($vars)) {
		return false;
	}

	print_r(array('$vars'=>$vars));

	if(preg_match('/'.$vars['regx'].'/si',$vars['smsinbox']['smsinbox_message'],$match)) {

		/*print_r(array('$match'=>$match));

		$where = '1=1';

		if(!empty($match['mobileno'])) {
			$mno = parseMobileNo($match['mobileno']);

			$eloadtransaction_mobileno = '0'.$mno[2].$mno[3];
			$where .= " and eloadtransaction_mobileno='$eloadtransaction_mobileno'";
		}

		$sql = "select * from tbl_eloadtransaction where $where and eloadtransaction_completed=0 order by eloadtransaction_id asc limit 1";

		print_r(array('$sql'=>$sql));

		if(!($result = $appdb->query($sql))) {
			return false;
		}

		print_r(array('$result'=>$result));

		if(!empty($result['rows'][0]['eloadtransaction_id'])) {*/

			$content = array();
			//$content['loadtransaction_simnumber'] = $simnumber;

			//$content = $result['rows'][0];

			//print_r(array('$result'=>$content));

			//unset($content['eloadtransaction_id']);
			//unset($content['eloadtransaction_createstamp']);

			if(!empty($match['mobileno'])&&($mno = parseMobileNo($match['mobileno']))) {
				$content['eloadtransaction_mobileno'] = '0'.$mno[2].$mno[3];
			}

			$content['eloadtransaction_confirmation'] = $match[0];

			if(!empty($match['cost'])) {
				$content['eloadtransaction_actualcost'] = $match['cost'];
			}

			if(!empty($match['balance'])) {
				$content['eloadtransaction_balance'] = $match['balance'];
			}

			if(!empty($match['reference'])) {
				$content['eloadtransaction_reference'] = $match['reference'];
			}

			if(!empty($match['productcode'])) {
				$content['eloadtransaction_productcode'] = $match['productcode'];
			}

			//$content['eloadtransaction_updatestamp'] = 'now()';

			//$content['eloadtransaction_completed'] = 1;

			//print_r(array('$content'=>$content));

			//if(!($result = $appdb->update("tbl_eloadtransaction",$content,"eloadtransaction_id=".$result['rows'][0]['eloadtransaction_id']))) {
			//	return false;
			//}

			if(!($result = $appdb->insert("tbl_eloadtransaction",$content,"eloadtransaction_id"))) {
				return false;
			}

		//}
	}
}

/* INCLUDES_END */


#eof ./includes/functions/index.php
