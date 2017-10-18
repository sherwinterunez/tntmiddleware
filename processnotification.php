<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: January 13, 2017
*
* Description:
*
* Application entry point.
*
*/

//define('ANNOUNCE', true);

error_reporting(E_ALL);

ini_set("max_execution_time", 300);

ini_set('precision',30);

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

/*
http://obis101.terunez.com/fcmsendtotopic.php?topic=tapntxt09493621618&msg=Hello&title=Tap%20N%20Txt
*/

//define('REMOTE_FCMSENDTOTOPIC_URL','http://obis101.terunez.com/fcmsendtotopic.php');
define('REMOTE_FCMSENDTOTOPIC_URL','https://tntserver.obisph.com/fcmsendtotopic.php');

date_default_timezone_set('Asia/Manila');

//if(!getOption('$SETTINGS_SENDPUSHNOTIFICATION',false)) {
//	die;
//}

if(!empty(($license=checkLicense()))) {
} else {
	print_r(array('ERROR'=>'Invalid or expired license!'));
	sleep(10);
	return false;
}

global $appdb;

$settings_sendtimeinnotification  = getOption('$SETTINGS_SENDTIMEINNOTIFICATION',true);
$settings_sendtimeoutnotification  = getOption('$SETTINGS_SENDTIMEOUTNOTIFICATION',true);
$settings_sendlatenotification  = getOption('$SETTINGS_SENDLATENOTIFICATION',false);
$settings_sendabsentnotification  = getOption('$SETTINGS_SENDABSENTNOTIFICATION',false);
$settings_sendpushnotification  = getOption('$SETTINGS_SENDPUSHNOTIFICATION',false);
$settings_sendsmsnotification  = getOption('$SETTINGS_SENDSMSNOTIFICATION',true);

$settings_timeinnotification = getOption('$SETTINGS_TIMEINNOTIFICATION');
$settings_timeoutnotification = getOption('$SETTINGS_TIMEOUTNOTIFICATION');

$settings_latenotification = getOption('$SETTINGS_LATENOTIFICATION',false);
$settings_absentnotification = getOption('$SETTINGS_ABSENTNOTIFICATION',false);

if(!($result = $appdb->query("select * from tbl_studentdtr where studentdtr_notified=0 order by studentdtr_id asc limit 5"))) {
	json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
	die;
}

if(!empty($result['rows'][0]['studentdtr_id'])) {
	$notifications = $result['rows'];
}

if(!empty($notifications)) {

	//print_r(array('studentdtr notifications'=>$notifications));

	foreach($notifications as $k=>$v) {

		$ch = new MyCURL;

		curl_setopt($ch->ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch->ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch->ch, CURLOPT_CAINFO, ABS_PATH . "cacert/cacert.pem");

		//$msgin = getOption('$SETTINGS_TIMEINNOTIFICATION');
		//$msgout = getOption('$SETTINGS_TIMEOUTNOTIFICATION');

		$msgin = $settings_timeinnotification;
		$msgout = $settings_timeoutnotification;

		$studentdtr_type = $v['studentdtr_type'];

		$fullname = getStudentFullName($v['studentdtr_studentid']);

		$profile = getStudentProfile($v['studentdtr_studentid']);

		$studentprofile_id = $v['studentdtr_studentid'];

		print_r(array('$profile'=>$profile));

		if(!empty($fullname)) {
			$msgin = str_replace('%STUDENTFULLNAME%',strtoupper($fullname),$msgin);
			$msgout = str_replace('%STUDENTFULLNAME%',strtoupper($fullname),$msgout);
		}

		$msgin = str_replace('%FIRSTNAME%',strtoupper($profile['studentprofile_firstname']),$msgin);
		$msgin = str_replace('%LASTNAME%',strtoupper($profile['studentprofile_lastname']),$msgin);
		$msgin = str_replace('%MIDDLENAME%',strtoupper($profile['studentprofile_middlename']),$msgin);

		$msgout = str_replace('%FIRSTNAME%',strtoupper($profile['studentprofile_firstname']),$msgout);
		$msgout = str_replace('%LASTNAME%',strtoupper($profile['studentprofile_lastname']),$msgout);
		$msgout = str_replace('%MIDDLENAME%',strtoupper($profile['studentprofile_middlename']),$msgout);

		$msgin = str_replace('%d%',date('d',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%F%',date('F',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%m%',date('m',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%M%',date('M',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%n%',date('n',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%y%',date('y',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%Y%',date('Y',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%a%',date('a',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%A%',date('A',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%g%',date('g',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%G%',date('G',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%h%',date('h',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%H%',date('H',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%i%',date('i',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%s%',date('s',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%r%',date('r',$v['studentdtr_unixtime']),$msgin);

		$msgout = str_replace('%d%',date('d',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%F%',date('F',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%m%',date('m',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%M%',date('M',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%n%',date('n',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%y%',date('y',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%Y%',date('Y',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%a%',date('a',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%A%',date('A',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%g%',date('g',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%G%',date('G',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%h%',date('h',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%H%',date('H',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%i%',date('i',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%s%',date('s',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%r%',date('r',$v['studentdtr_unixtime']),$msgout);

		$dt = date('m/d/Y H:i:s',$v['studentdtr_unixtime']);

		$msgin = str_replace('%DATETIME%',$dt,$msgin);
		$msgout = str_replace('%DATETIME%',$dt,$msgout);

		$mobileno = getGuardianMobileNo($v['studentdtr_studentid']);

		if(!empty($mobileno)) {
		} else {

			$studentdtr_notified = time();

			$content = array();
			$content['studentdtr_notified'] = $studentdtr_notified;
			$content['studentdtr_notifystamp'] = 'now()';

			if(!($result = $appdb->update("tbl_studentdtr",$content,"studentdtr_id=".$v['studentdtr_id']))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

			continue;
		}

		/*if(getOption('$SETTINGS_SENDPUSHNOTIFICATION',false)) {

			$post = array();
			$post['topic'] = 'tapntxt'.$mobileno;

			if($v['studentdtr_type']=='IN') {
				$post['msg'] = $msgin;
			} else {
				$post['msg'] = $msgout;
			}

			$post['title'] = 'Tap N Txt';

			if(!($retcont = $ch->post(REMOTE_FCMSENDTOTOPIC_URL,$post))) {
				print_r(array('error'=>$retcont));
			}

			print_r(array('$retcont'=>$retcont));

			if(!empty($retcont['content'])) {
				$retval = json_decode($retcont['content'],true);
			}

			if(!empty($retval['message_id'])) {

				$content = array();
				$content['studentdtr_notified'] = $retval['message_id'];
				$content['studentdtr_notifystamp'] = 'now()';

				if(!($result = $appdb->update("tbl_studentdtr",$content,"studentdtr_id=".$v['studentdtr_id']))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;
				}

			} else {

				$content = array();
				$content['studentdtr_notified'] = 1;
				$content['studentdtr_notifystamp'] = 'now()';

				if(!($result = $appdb->update("tbl_studentdtr",$content,"studentdtr_id=".$v['studentdtr_id']))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;
				}

			}

		}*/

		//$settings_sendtimeinnotification  = getOption('$SETTINGS_SENDTIMEINNOTIFICATION',true);
		//$settings_sendtimeoutnotification  = getOption('$SETTINGS_SENDTIMEOUTNOTIFICATION',true);
		//$settings_sendlatenotification  = getOption('$SETTINGS_SENDLATENOTIFICATION',false);
		//$settings_sendabsentnotification  = getOption('$SETTINGS_SENDABSENTNOTIFICATION',false);
		//$settings_sendpushnotification  = getOption('$SETTINGS_SENDPUSHNOTIFICATION',false);
		//$settings_sendsmsnotification  = getOption('$SETTINGS_SENDSMSNOTIFICATION',true);

		$push = 0;

		if($settings_sendpushnotification) {
			$push = 1;
		}

		$status = 1; // waiting

		if($studentdtr_type=='IN') {
			if(!$settings_sendtimeinnotification) {
				$status = 4;
				$push = 0;
			}
		} else
		if($studentdtr_type=='OUT') {
			if(!$settings_sendtimeoutnotification) {
				$status = 4;
				$push = 0;
			}
		}

		if(!$settings_sendsmsnotification) {
			$status = 4;
		}

		$studentdtr_notified = time();

		$content = array();
		$content['studentdtr_notified'] = $studentdtr_notified;
		$content['studentdtr_notifystamp'] = 'now()';

		if(!($result = $appdb->update("tbl_studentdtr",$content,"studentdtr_id=".$v['studentdtr_id']))) {
			json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
			die;
		}

		$asim = getAllSims(3);

		pre(array('$asim'=>$asim));

		if(!empty($asim)) {

			shuffle($asim);

			foreach($asim as $m=>$n) {

				if($v['studentdtr_type']=='IN') {
					if(!empty($license['sc'])) {
						$msgin .= ' '.$license['sc'];
					}
					pre(array('$mobileno'=>$mobileno,'$m'=>$n['sim_number'],'$msgin'=>$msgin,'$license[sc]'=>$license['sc']));
					sendToOutBoxPriority($mobileno,$n['sim_number'],$msgin,$push,1,$status,0,0,$studentprofile_id);
				} else {
					if(!empty($license['sc'])) {
						$msgout .= ' '.$license['sc'];
					}
					pre(array('$mobileno'=>$mobileno,'$m'=>$n['sim_number'],'$msgin'=>$msgout,'$license[sc]'=>$license['sc']));
					sendToOutBoxPriority($mobileno,$n['sim_number'],$msgout,$push,1,$status,0,0,$studentprofile_id);
				}

				break;
			}

		} else {
			// no sim card detected or no connected gsm modem

			if($v['studentdtr_type']=='IN') {
				if(!empty($license['sc'])) {
					$msgin .= ' '.$license['sc'];
				}
				pre(array('$mobileno'=>$mobileno,'$m'=>false,'$msgin'=>$msgin,'$license[sc]'=>$license['sc']));
				sendToOutBoxPriority($mobileno,false,$msgin,$push,1,$status,0,0,$studentprofile_id);
			} else {
				if(!empty($license['sc'])) {
					$msgout .= ' '.$license['sc'];
				}
				pre(array('$mobileno'=>$mobileno,'$m'=>false,'$msgin'=>$msgout,'$license[sc]'=>$license['sc']));
				sendToOutBoxPriority($mobileno,false,$msgout,$push,1,$status,0,0,$studentprofile_id);
			}

		}

	}

}

///////////////////////////////////////////////////////////////////////////////

// check late/tardy student

if($settings_sendlatenotification) {

	$to = intval(getDbUnixDate());

	$cdt = date('m/d/Y',$to);

	$from = date2timestamp("$cdt 00:00:00",'m/d/Y H:i:s');
	//$to = date2timestamp("$cdt 23:59:59",'m/d/Y H:i:s');

	pre(array('$from'=>$from,'$fromdt'=>pgDateUnix($from),'$to'=>$to,'$todt'=>pgDateUnix($to)));

	//

	$notifications = false;

	$studentprofile_schoolyear = getCurrentSchoolYear();

	$sql = "select studentprofile_id from tbl_studentprofile where studentprofile_schoolyear='$studentprofile_schoolyear' and studentprofile_id not in (select distinct B.studentprofile_id from tbl_studentdtr as A, tbl_studentprofile as B where B.studentprofile_schoolyear='$studentprofile_schoolyear' and A.studentdtr_type='IN' and A.studentdtr_studentid=B.studentprofile_id and A.studentdtr_unixtime >= $from and A.studentdtr_unixtime <= $to)";

	if(!($result = $appdb->query($sql))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
		die;
	}

	//pre(array('$result'=>$result,'$sql'=>$sql));

	$ids = array();

	if(!empty($result['rows'][0]['studentprofile_id'])) {
		foreach($result['rows'] as $k=>$v) {
			if(!empty($v['studentprofile_id'])) {
				$ids[] = $v['studentprofile_id'];
			}
		}
	}

	if(!empty($ids)) {

		$sql = "select distinct smsoutbox_contactid from tbl_smsoutbox where smsoutbox_latenoti>0 and smsoutbox_contactid in (".implode(',',$ids).") and extract(epoch from smsoutbox_createstamp)>=$from and extract(epoch from smsoutbox_createstamp)<=$to";

		if(!($result = $appdb->query($sql))) {
			json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
			die;
		}

		$sids = array();

		if(!empty($result['rows'][0]['smsoutbox_contactid'])) {
			foreach($result['rows'] as $k=>$v) {
				if(!empty($v['smsoutbox_contactid'])) {
					$sids[] = $v['smsoutbox_contactid'];
				}
			}
		}

		foreach($ids as $k=>$v) {
			if(in_array($v,$sids)) {
				unset($ids[$k]);
			}
		}

		//pre(array('$result'=>$result,'$sql'=>$sql,'$ids'=>$ids,'$sids'=>$sids));

	} // if(!empty($ids)) {

	if(!empty($ids)) {

		foreach($ids as $k=>$studentprofile_id) {

			$fullname = getStudentFullName($studentprofile_id);

			$profile = getStudentProfile($studentprofile_id);

			if(!empty($profile['studentprofile_section'])) {
			} else {
				continue;
			}

			$mobileno = getGuardianMobileNo($studentprofile_id);

			if(!empty($mobileno)) {
			} else {
				continue;
			}

			$unixtime = intval(getDbUnixDate());

			$month = intval(date('m', $unixtime));
			$day = intval(date('d', $unixtime));
			$year = intval(date('Y', $unixtime));
			$hour = intval(date('H', $unixtime));
			$minute = intval(date('i', $unixtime));
			$second = intval(date('s', $unixtime));

			$settings_tardinessgraceperiodminute = getOption('$SETTINGS_TARDINESSGRACEPERIODMINUTE',30);

			$gracePeriod = $settings_tardinessgraceperiodminute * 60;

			$startTime = getSectionStartTime($profile['studentprofile_section']);
			$endTime = getSectionEndTime($profile['studentprofile_section']);

			$startTimeStamp = date2timestamp("$month/$day/$year $startTime",'m/d/Y H:i:s');
			$endTimeStamp = date2timestamp("$month/$day/$year $endTime",'m/d/Y H:i:s');

			$gracePeriodStamp = $gracePeriod + $startTimeStamp;


			//pre(array('$profile'=>$profile,'$startTime'=>$startTime,'$startTimeStamp'=>$startTimeStamp,'$startTimeDt'=>date('m/d/Y H:i:s',$startTimeStamp),'$endTime'=>$endTime,'$endTimeStamp'=>$endTimeStamp,'$endTimeDt'=>date('m/d/Y H:i:s',$endTimeStamp),'$gracePeriodStamp'=>$gracePeriodStamp,'$gracePeriodDt'=>date('m/d/Y H:i:s',$gracePeriodStamp)));

			if($unixtime>$gracePeriodStamp) {

				$msg = $settings_latenotification;

				//pre(array('late'=>'yes','$msg'=>$msg));

				if(!empty($fullname)) {
					$msg = str_replace('%STUDENTFULLNAME%',strtoupper($fullname),$msg);
				}

				$msg = str_replace('%FIRSTNAME%',strtoupper($profile['studentprofile_firstname']),$msg);
				$msg = str_replace('%LASTNAME%',strtoupper($profile['studentprofile_lastname']),$msg);
				$msg = str_replace('%MIDDLENAME%',strtoupper($profile['studentprofile_middlename']),$msg);

				$msg = str_replace('%d%',date('d',$unixtime),$msg);
				$msg = str_replace('%F%',date('F',$unixtime),$msg);
				$msg = str_replace('%m%',date('m',$unixtime),$msg);
				$msg = str_replace('%M%',date('M',$unixtime),$msg);
				$msg = str_replace('%n%',date('n',$unixtime),$msg);
				$msg = str_replace('%y%',date('y',$unixtime),$msg);
				$msg = str_replace('%Y%',date('Y',$unixtime),$msg);
				$msg = str_replace('%a%',date('a',$unixtime),$msg);
				$msg = str_replace('%A%',date('A',$unixtime),$msg);
				$msg = str_replace('%g%',date('g',$unixtime),$msg);
				$msg = str_replace('%G%',date('G',$unixtime),$msg);
				$msg = str_replace('%h%',date('h',$unixtime),$msg);
				$msg = str_replace('%H%',date('H',$unixtime),$msg);
				$msg = str_replace('%i%',date('i',$unixtime),$msg);
				$msg = str_replace('%s%',date('s',$unixtime),$msg);
				$msg = str_replace('%r%',date('r',$unixtime),$msg);

				$dt = date('m/d/Y H:i:s',$unixtime);

				$msg = str_replace('%DATETIME%',$dt,$msg);

				$status = 1; // waiting

				if(!$settings_sendsmsnotification) {
					$status = 4;
				}

				$push = 0;

				if($settings_sendpushnotification) {
					$push = 1;
				}

				$asim = getAllSims(3);

				pre(array('$asim'=>$asim));

				if(!empty($asim)) {

					shuffle($asim);

					foreach($asim as $m=>$n) {

						if(!empty($license['sc'])) {
							$msg .= ' '.$license['sc'];
						}
						pre(array('$mobileno'=>$mobileno,'$m'=>$n['sim_number'],'$msgin'=>$msg,'$license[sc]'=>$license['sc']));
						sendToOutBoxPriority($mobileno,$n['sim_number'],$msg,$push,1,$status,1,0,$studentprofile_id);

						break;
					}

				} else {
					// no sim card detected or no connected gsm modem

					if(!empty($license['sc'])) {
						$msg .= ' '.$license['sc'];
					}
					pre(array('$mobileno'=>$mobileno,'$m'=>$n['sim_number'],'$msgin'=>$msg,'$license[sc]'=>$license['sc']));
					sendToOutBoxPriority($mobileno,false,$msg,$push,1,$status,1,0,$studentprofile_id);

				}

			}
		}

	} // if(!empty($ids)) {

} // if($settings_sendlatenotification) {

///////////////////////////////////////////////////////////////////////////////

// send absent notification

if($settings_sendabsentnotification) {

	$to = intval(getDbUnixDate());

	$cdt = date('m/d/Y',$to);

	$from = date2timestamp("$cdt 00:00:00",'m/d/Y H:i:s');
	//$to = date2timestamp("$cdt 23:59:59",'m/d/Y H:i:s');

	//pre(array('$from'=>$from,'$fromdt'=>pgDateUnix($from),'$to'=>$to,'$todt'=>pgDateUnix($to)));

	//

	$notifications = false;

	$studentprofile_schoolyear = getCurrentSchoolYear();

	$sql = "select studentprofile_id from tbl_studentprofile where studentprofile_schoolyear='$studentprofile_schoolyear' and studentprofile_id not in (select distinct B.studentprofile_id from tbl_studentdtr as A, tbl_studentprofile as B where B.studentprofile_schoolyear='$studentprofile_schoolyear' and A.studentdtr_type='IN' and A.studentdtr_studentid=B.studentprofile_id and A.studentdtr_unixtime >= $from and A.studentdtr_unixtime <= $to)";

	if(!($result = $appdb->query($sql))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
		die;
	}

	//pre(array('$result'=>$result,'$sql'=>$sql));

	$ids = array();

	if(!empty($result['rows'][0]['studentprofile_id'])) {
		foreach($result['rows'] as $k=>$v) {
			if(!empty($v['studentprofile_id'])) {
				$ids[] = $v['studentprofile_id'];
			}
		}
	}

	/*if(!empty($ids)) {

		$sql = "select distinct smsoutbox_contactid from tbl_smsoutbox where smsoutbox_absentnoti>0 and smsoutbox_contactid in (".implode(',',$ids).") and extract(epoch from smsoutbox_createstamp)>=$from and extract(epoch from smsoutbox_createstamp)<=$to";

		if(!($result = $appdb->query($sql))) {
			json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
			die;
		}

		$sids = array();

		if(!empty($result['rows'][0]['smsoutbox_contactid'])) {
			foreach($result['rows'] as $k=>$v) {
				if(!empty($v['smsoutbox_contactid'])) {
					$sids[] = $v['smsoutbox_contactid'];
				}
			}
		}

		foreach($ids as $k=>$v) {
			if(in_array($v,$sids)) {
				unset($ids[$k]);
			}
		}

		pre(array('$result'=>$result,'$sql'=>$sql,'$ids'=>$ids,'$sids'=>$sids));

	} // if(!empty($ids)) {*/

	if(!empty($ids)) {

		foreach($ids as $k=>$studentprofile_id) {

			$fullname = getStudentFullName($studentprofile_id);

			$profile = getStudentProfile($studentprofile_id);

			if(!empty($profile['studentprofile_section'])) {
			} else {
				continue;
			}

			$mobileno = getGuardianMobileNo($studentprofile_id);

			if(!empty($mobileno)) {
			} else {
				continue;
			}

			$unixtime = intval(getDbUnixDate());

			$month = intval(date('m', $unixtime));
			$day = intval(date('d', $unixtime));
			$year = intval(date('Y', $unixtime));
			$hour = intval(date('H', $unixtime));
			$minute = intval(date('i', $unixtime));
			$second = intval(date('s', $unixtime));

			$absentnoticount = getOption('ABSENTNOTICOUNT_'.$studentprofile_id.'_'.date('Ymd', $unixtime),0);

			if($absentnoticount<1) {
				$settings_absentgraceperiodminute = getOption('$SETTINGS_ABSENTGRACEPERIODMINUTE1',40);
			} else
			if($absentnoticount==1) {
				$settings_absentgraceperiodminute = getOption('$SETTINGS_ABSENTGRACEPERIODMINUTE2',0);
			} else
			if($absentnoticount==2) {
				$settings_absentgraceperiodminute = getOption('$SETTINGS_ABSENTGRACEPERIODMINUTE3',0);
			}

			if($settings_absentgraceperiodminute>0) {
			} else {
				continue;
			}

			$gracePeriod = $settings_absentgraceperiodminute * 60;

			$startTime = getSectionStartTime($profile['studentprofile_section']);
			$endTime = getSectionEndTime($profile['studentprofile_section']);

			$startTimeStamp = date2timestamp("$month/$day/$year $startTime",'m/d/Y H:i:s');
			$endTimeStamp = date2timestamp("$month/$day/$year $endTime",'m/d/Y H:i:s');

			$gracePeriodStamp = $gracePeriod + $startTimeStamp;

			//pre(array('$profile'=>$profile,'$startTime'=>$startTime,'$startTimeStamp'=>$startTimeStamp,'$startTimeDt'=>date('m/d/Y H:i:s',$startTimeStamp),'$endTime'=>$endTime,'$endTimeStamp'=>$endTimeStamp,'$endTimeDt'=>date('m/d/Y H:i:s',$endTimeStamp),'$gracePeriodStamp'=>$gracePeriodStamp,'$gracePeriodDt'=>date('m/d/Y H:i:s',$gracePeriodStamp)));

			if($unixtime>$gracePeriodStamp) {

				setSetting('ABSENTNOTICOUNT_'.$studentprofile_id.'_'.date('Ymd', $unixtime),($absentnoticount+1));

				$msg = $settings_absentnotification;

				//pre(array('late'=>'yes','$msg'=>$msg));

				if(!empty($fullname)) {
					$msg = str_replace('%STUDENTFULLNAME%',strtoupper($fullname),$msg);
				}

				$msg = str_replace('%FIRSTNAME%',strtoupper($profile['studentprofile_firstname']),$msg);
				$msg = str_replace('%LASTNAME%',strtoupper($profile['studentprofile_lastname']),$msg);
				$msg = str_replace('%MIDDLENAME%',strtoupper($profile['studentprofile_middlename']),$msg);

				$msg = str_replace('%d%',date('d',$unixtime),$msg);
				$msg = str_replace('%F%',date('F',$unixtime),$msg);
				$msg = str_replace('%m%',date('m',$unixtime),$msg);
				$msg = str_replace('%M%',date('M',$unixtime),$msg);
				$msg = str_replace('%n%',date('n',$unixtime),$msg);
				$msg = str_replace('%y%',date('y',$unixtime),$msg);
				$msg = str_replace('%Y%',date('Y',$unixtime),$msg);
				$msg = str_replace('%a%',date('a',$unixtime),$msg);
				$msg = str_replace('%A%',date('A',$unixtime),$msg);
				$msg = str_replace('%g%',date('g',$unixtime),$msg);
				$msg = str_replace('%G%',date('G',$unixtime),$msg);
				$msg = str_replace('%h%',date('h',$unixtime),$msg);
				$msg = str_replace('%H%',date('H',$unixtime),$msg);
				$msg = str_replace('%i%',date('i',$unixtime),$msg);
				$msg = str_replace('%s%',date('s',$unixtime),$msg);
				$msg = str_replace('%r%',date('r',$unixtime),$msg);

				$dt = date('m/d/Y H:i:s',$unixtime);

				$msg = str_replace('%DATETIME%',$dt,$msg);

				$status = 1; // waiting

				if(!$settings_sendsmsnotification) {
					$status = 4;
				}

				$push = 0;

				if($settings_sendpushnotification) {
					$push = 1;
				}

				$asim = getAllSims(3);

				pre(array('$asim'=>$asim));

				if(!empty($asim)) {

					shuffle($asim);

					foreach($asim as $m=>$n) {

						if(!empty($license['sc'])) {
							$msg .= ' '.$license['sc'];
						}
						pre(array('$mobileno'=>$mobileno,'$m'=>$n['sim_number'],'$msgin'=>$msg,'$license[sc]'=>$license['sc']));
						sendToOutBoxPriority($mobileno,$n['sim_number'],$msg,$push,1,$status,0,1,$studentprofile_id);

						break;
					}

				} else {
					// no sim card detected or no connected gsm modem

					if(!empty($license['sc'])) {
						$msg .= ' '.$license['sc'];
					}
					pre(array('$mobileno'=>$mobileno,'$m'=>$n['sim_number'],'$msgin'=>$msg,'$license[sc]'=>$license['sc']));
					sendToOutBoxPriority($mobileno,false,$msg,$push,1,$status,0,1,$studentprofile_id);

				}

			}
		}

	} // if(!empty($ids)) {

} // if($settings_sendabsentnotification) {

///////////////////////////////////////////////////////////////////////////////

// send push notification

$notifications = false;

if(!($result = $appdb->query("select * from tbl_smsoutbox where smsoutbox_sendpush>0 and smsoutbox_pushstatus=1 and smsoutbox_pushid=0 order by smsoutbox_id asc limit 1"))) {
	json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
	die;
}

if(!empty($result['rows'][0]['smsoutbox_id'])) {
	$notifications = $result['rows'];
}

if(!empty($notifications)) {

	//print_r(array('outbox notifications'=>$notifications));

	foreach($notifications as $k=>$v) {

		$ch = new MyCURL;

		curl_setopt($ch->ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch->ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch->ch, CURLOPT_CAINFO, ABS_PATH . "cacert/cacert.pem");

		$smsoutbox_message = $v['smsoutbox_message'];

		$fullname = getStudentFullName($v['smsoutbox_contactid']);

		$profile = getStudentProfile($v['smsoutbox_contactid']);

		if(!empty($fullname)) {
			$smsoutbox_message = str_replace('%STUDENTFULLNAME%',strtoupper($fullname),$smsoutbox_message);
		}

		$smsoutbox_message = str_replace('%FIRSTNAME%',strtoupper($profile['studentprofile_firstname']),$smsoutbox_message);
		$smsoutbox_message = str_replace('%LASTNAME%',strtoupper($profile['studentprofile_lastname']),$smsoutbox_message);
		$smsoutbox_message = str_replace('%MIDDLENAME%',strtoupper($profile['studentprofile_middlename']),$smsoutbox_message);

		//$dt = date('m/d/Y H:i:s',$profile['studentdtr_unixtime']);

		//$smsoutbox_message = str_replace('%DATETIME%',$dt,$smsoutbox_message);

		$mobileno = getGuardianMobileNo($profile['studentprofile_id']);

		if(!empty($mobileno)) {
		} else {

			$content = array();
			$content['smsoutbox_pushid'] = 1;
			$content['smsoutbox_pushstatus'] = 5;
			$content['smsoutbox_pushsentstamp'] = 'now()';

			if(!($result = $appdb->update("tbl_smsoutbox",$content,"smsoutbox_id=".$v['smsoutbox_id']))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

			continue;
		}

		$content = array();
		$content['smsoutbox_pushstatus'] = 3;

		if(!($result = $appdb->update("tbl_smsoutbox",$content,"smsoutbox_id=".$v['smsoutbox_id']))) {
			json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
			die;
		}

		$post = array();
		$post['topic'] = 'tapntxt'.$mobileno;
		$post['msg'] = $smsoutbox_message;
		$post['title'] = 'TAP N TXT';

		//pre(array('$post'=>$post,'$profile'=>$profile));

		if(!($retcont = $ch->post(REMOTE_FCMSENDTOTOPIC_URL,$post))) {
			print_r(array('error'=>$retcont));
		}

		//print_r(array('$retcont'=>$retcont));

		if(!empty($retcont['content'])) {
			$retval = json_decode($retcont['content'],true);
		}

		if(!empty($retval['message_id'])) {

			$content = array();
			$content['smsoutbox_pushid'] = floatval($retval['message_id']);
			$content['smsoutbox_pushstatus'] = 4;
			$content['smsoutbox_pushsentstamp'] = 'now()';

			print_r(array('$retval'=>$retval,'$content'=>$content));

			if(!($result = $appdb->update("tbl_smsoutbox",$content,"smsoutbox_id=".$v['smsoutbox_id']))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

		} else {

			$content = array();
			$content['smsoutbox_pushid'] = 1;
			$content['smsoutbox_pushstatus'] = 5;
			$content['smsoutbox_pushsentstamp'] = 'now()';

			if(!($result = $appdb->update("tbl_smsoutbox",$content,"smsoutbox_id=".$v['smsoutbox_id']))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

		}

	}

}
