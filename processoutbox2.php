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

//require_once('gsmsms.class.inc.php');
//require_once('Pdu/Pdu.php');
//require_once('Pdu/PduFactory.php');
//require_once('Utf8/Utf8.php');

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

function processOutbox($dev=false,$mobileNo=false,$ip='') {
	global $appdb;

	if(!empty($dev)&&!empty($mobileNo)&&!empty($ip)) {
	} else return false;

	//echo "\nprocessOutbox started: ($dev) ($mobileNo).\n";

	//atLog('processOutbox starting','processoutbox',$dev,$mobileNo,$ip,logdt());

	$sms = new APP_SMS;

	$sms->dev = $dev;
	$sms->mobileNo = $mobileNo;
	$sms->ip = $ip;

	if(!$sms->deviceInit($dev)) {
		$em = 'Error initializing device!';
		atLog($em,'processoutbox',$dev,$mobileNo,$ip,logdt());
		trigger_error("$dev $mobileNo $ip $em",E_USER_NOTICE);
		setSetting('STATUS_SIMERROR','1');
		return false;
	}

	if(!$sms->at()) {

		$em = 'processOutbox failed (AT)';
		atLog($em,'processoutbox',$dev,$mobileNo,$ip,logdt());
		trigger_error("$dev $mobileNo $ip $em",E_USER_NOTICE);

		if(!$sms->atgt()) {
			$em = 'processOutbox failed (ATGT)';
			atLog($em,'processoutbox',$dev,$mobileNo,$ip,logdt());
			trigger_error("$dev $mobileNo $ip $em",E_USER_NOTICE);

			$sms->deviceClose();
			return false;
		} else {
			$em = 'processOutbox (ATGT) success!';
			atLog($em,'processoutbox',$dev,$mobileNo,$ip,logdt());
			trigger_error("$dev $mobileNo $ip $em",E_USER_NOTICE);
		}
	}

	$sms->clearHistory();


/////

	$delaysms = false;

	$limit = 1;

	if(!($result = $appdb->query("select *,(extract(epoch from now()) - extract(epoch from smsoutbox_createstamp)) as elapsedtime from tbl_smsoutbox where smsoutbox_simnumber='$mobileNo' and smsoutbox_deleted=0 and smsoutbox_delay>0 and smsoutbox_status=1 order by smsoutbox_id asc limit $limit"))) {
		//echo "\n0 message. processOutbox done.\n";
		return false;
	}

	if(!empty($result['rows'][0]['smsoutbox_id'])) {

		//print_r(array('$rows'=>$result['rows']));

		$delaysms = $result['rows'];

		if(!empty($delaysms)&&is_array($delaysms)) {
			foreach($delaysms as $k=>$v) {
				if($v['elapsedtime']>$v['smsoutbox_delay']) {

					if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>1,'smsoutbox_delay'=>0),"smsoutbox_id=".$v['smsoutbox_id']))) {
						return false;
					}

				}
			}
		}

	}

/////

	$sendsms = false;

	if(!($result = $appdb->query("select * from tbl_smsoutbox where smsoutbox_simnumber='$mobileNo' and smsoutbox_priority=1 and smsoutbox_deleted=0 and smsoutbox_delay=0 and smsoutbox_status=1 order by smsoutbox_id asc limit $limit"))) {
		//echo "\n0 message. processOutbox done.\n";
		return false;
	}

	if(!empty($result['rows'][0]['smsoutbox_id'])) {
	} else {
		if(!($result = $appdb->query("select * from tbl_smsoutbox where smsoutbox_simnumber='$mobileNo' and smsoutbox_deleted=0 and smsoutbox_delay=0 and smsoutbox_status=1 order by smsoutbox_id asc limit $limit"))) {
			//echo "\n0 message. processOutbox done.\n";
			return false;
		}
	}

	$failed = false;

	if(!empty($result['rows'][0]['smsoutbox_id'])) {

		//print_r(array('$result'=>$result['rows']));

		$sendsms = $result['rows'];

		//echo "\nstarted sending.\n";

		//atLog('processOutbox started sending sms','processoutbox',$dev,$mobileNo,$ip,logdt());

		if(!empty($sendsms)&&is_array($sendsms)) {

			$total = 0;

			foreach($sendsms as $k=>$v) {
				//if($v['smsoutbox_total']==1) {

					//if(sendSMS($v['smsoutbox_portdevice'],$v['smsoutbox_contactnumber'],$v['smsoutbox_message'])) {

					$unixtime = intval(getDbUnixDate());

					//$absentnoticount = getOption('ABSENTNOTICOUNT_'.$studentprofile_id.'_'.date('Ymd', $unixtime),0);

					$sentcount = getOption('SENTCOUNT_'.$mobileNo.'_'.date('Ymd', $unixtime),0);
					$failedcount = getOption('FAILEDCOUNT_'.$mobileNo.'_'.date('Ymd', $unixtime),0);

					$appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>3,'smsoutbox_sentstamp'=>'now()'),'smsoutbox_status=1 and smsoutbox_id='.$v['smsoutbox_id']);

					if(!empty($v['smsoutbox_promossentid'])) {
						$appdb->update("tbl_promossent",array('promossent_status'=>3,'promossent_sentstamp'=>'now()'),'promossent_id='.$v['smsoutbox_promossentid']);
					}

					if(!empty($v['smsoutbox_schedulersentid'])) {
						$appdb->update("tbl_schedulersent",array('schedulersent_status'=>3,'schedulersent_sentstamp'=>'now()'),'schedulersent_id='.$v['smsoutbox_schedulersentid']);
					}

					if(!empty($v['smsoutbox_referralsentid'])) {
						$appdb->update("tbl_referralsent",array('referralsent_status'=>3,'referralsent_sentstamp'=>'now()'),'referralsent_id='.$v['smsoutbox_referralsentid']);
					}

					if(($count=sendSMS($sms,$v['smsoutbox_contactnumber'],$v['smsoutbox_message']))) {

						setSetting('SENTCOUNT_'.$mobileNo.'_'.date('Ymd', $unixtime),($sentcount+1));

						setSetting('SENTSTAMP_'.$mobileNo, $unixtime);

						$appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>4,'smsoutbox_sentstamp'=>'now()'),'smsoutbox_id='.$v['smsoutbox_id']);

						if(!empty($v['smsoutbox_promossentid'])) {
							$appdb->update("tbl_promossent",array('promossent_status'=>4,'promossent_sentstamp'=>'now()'),'promossent_id='.$v['smsoutbox_promossentid']);
						}

						if(!empty($v['smsoutbox_schedulersentid'])) {
							$appdb->update("tbl_schedulersent",array('schedulersent_status'=>4,'schedulersent_sentstamp'=>'now()'),'schedulersent_id='.$v['smsoutbox_schedulersentid']);
						}

						if(!empty($v['smsoutbox_referralsentid'])) {
							$appdb->update("tbl_referralsent",array('referralsent_status'=>4,'referralsent_sentstamp'=>'now()'),'referralsent_id='.$v['smsoutbox_referralsentid']);
						}

						$total+=$count;

						if($total>5) {
							break;
						}

					} else {

						setSetting('FAILEDCOUNT_'.$mobileNo.'_'.date('Ymd', $unixtime),($failedcount+1));

						setSetting('FAILEDSTAMP_'.$mobileNo, $unixtime);

						$failed = true;

						$appdb->update("tbl_smsoutbox",array('smsoutbox_failedcount'=>'#smsoutbox_failedcount+1#','smsoutbox_status'=>5,'smsoutbox_failedstamp'=>'now()'),'smsoutbox_id='.$v['smsoutbox_id']);

						if(!empty($v['smsoutbox_promossentid'])) {
							$appdb->update("tbl_promossent",array('promossent_status'=>5),'promossent_id='.$v['smsoutbox_promossentid']);
						}

						if(!empty($v['smsoutbox_schedulersentid'])) {
							$appdb->update("tbl_schedulersent",array('schedulersent_status'=>5,'schedulersent_sentstamp'=>'now()'),'schedulersent_id='.$v['smsoutbox_schedulersentid']);
						}

						if(!empty($v['smsoutbox_referralsentid'])) {
							$appdb->update("tbl_referralsent",array('referralsent_status'=>5,'referralsent_sentstamp'=>'now()'),'referralsent_id='.$v['smsoutbox_referralsentid']);
						}
					}
				//} else
				//if($v['smsoutbox_total']>1) {
				//}
			}
		}

		//atLog('processOutbox done sending sms','processoutbox',$dev,$mobileNo,$ip,logdt());

		//echo "\ndone sending.\n";

	}

	//log_notice(array('processOutbox'=>'processOutbox','$failed'=>$failed));

	if(!$failed) {

		$sql = "select *,(extract(epoch from now()) - extract(epoch from smsoutbox_failedstamp)) as elapsedtime from tbl_smsoutbox where smsoutbox_failedcount<5 and smsoutbox_deleted=0 and smsoutbox_delay=0 and smsoutbox_status=5 order by smsoutbox_id asc limit 1";

		//log_notice(array('$sql'=>$sql));

		if(!($result = $appdb->query($sql))) {
			//echo "\n0 message. processOutbox done.\n";
			return false;
		}

		if(!empty($result['rows'][0]['smsoutbox_id'])&&!empty($result['rows'][0]['elapsedtime'])) {
			if(intval($result['rows'][0]['elapsedtime'])>60) {
				//pre(array('$result'=>$result));

				//log_notice(array('$result'=>$result));

				$appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>1,'smsoutbox_simnumber'=>$mobileNo),'smsoutbox_id='.$result['rows'][0]['smsoutbox_id']);
			}
		}

		$sql = "select *,(extract(epoch from now()) - extract(epoch from smsoutbox_failedstamp)) as elapsedtime from tbl_smsoutbox where smsoutbox_simnumber<>'$mobileNo' and smsoutbox_deleted=0 and smsoutbox_delay=0 and smsoutbox_status=1 order by smsoutbox_id asc limit 10";

		//log_notice(array('$sql'=>$sql));

		if(!($result = $appdb->query($sql))) {
			//echo "\n0 message. processOutbox done.\n";
			return false;
		}

		if(!empty($result['rows'][0]['smsoutbox_id'])&&!empty($result['rows'][0]['elapsedtime'])) {
			foreach($result['rows'] as $k=>$v) {
				if(intval($v['elapsedtime'])>180) {
					//pre(array('waiting'=>$v));
					//log_notice(array('waiting'=>$v));
					$appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>1,'smsoutbox_simnumber'=>$mobileNo,'smsoutbox_failedstamp'=>'now()'),'smsoutbox_id='.$v['smsoutbox_id']);
					break;
				}
			}
		}
	}

	$history = $sms->getHistory();

	if(!empty($history)) {
		foreach($history as $a=>$b) {
			foreach($b as $k=>$v) {
				if($k=='timestamp') continue;
				$dt = logdt($b['timestamp']);
				trigger_error("$dev $mobileNo $ip $v",E_USER_NOTICE);
				doLog("$dt $dev $mobileNo $ip $v",$mobileNo);
				//atLog($v,'processoutbox',$dev,$mobileNo,$ip,$dt);
			}
		}
	}

	$sms->deviceClose();

	$tstop = timer_stop();

	//print_r(array('$mobileNo'=>$mobileNo));

	//echo "\nprocessOutbox (".$tstop." secs).\n";

	atLog('processOutbox done ('.$tstop.' secs)','processoutbox',$dev,$mobileNo,$ip,logdt());

	return true;
}

if(getOption('$MAINTENANCE',false)) {
	die("\nprocessOutbox: Server under maintenance.\n");
}

if(!empty($_GET['dev'])&&!empty($_GET['sim'])&&!empty($_GET['ip'])&&isSimEnabled($_GET['sim'])) {

	if(!empty(($license=checkLicense()))) {
	} else {
		return false;
	}

	setSetting('STATUS_PROCESSOUTBOX_'.$_GET['sim'],'1');

	if(processOutbox($_GET['dev'],$_GET['sim'],$_GET['ip'])) {
		setSetting('STATUS_PROCESSOUTBOX_'.$_GET['sim'],'0');
	}

}
