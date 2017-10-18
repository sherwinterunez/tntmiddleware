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

//define('REMOTE_DB_URL','http://obis101.terunez.com/syncuser.php');
define('REMOTE_DB_URL','https://tntserver.obisph.com/syncuser.php');

date_default_timezone_set('Asia/Manila');

if(!getOption('$SETTINGS_SYNCTOSERVER',false)) {
	die;
}

global $appdb;

if(!($result = $appdb->query("select * from tbl_studentprofile where studentprofile_sync=0 order by studentprofile_id asc limit 10"))) {
	json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
	die;
}

if(!empty($result['rows'][0]['studentprofile_id'])) {

	$studentprofiles = $result['rows'];

	//print_r($studentprofiles);

	foreach($studentprofiles as $k=>$v) {

		if(!empty($v['studentprofile_guardianmobileno'])) {
		} else continue;

		print_r(array('$studentprofiles['.$k.']'=>$v));

		$ch = new MyCURL;

		curl_setopt($ch->ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch->ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch->ch, CURLOPT_CAINFO, ABS_PATH . "cacert/cacert.pem");

		$v['defaultpass'] = 'TAPNTXT143';

		$post = array();
		$post['params'] = base64_encode(serialize($v));

		if(!($retcont = $ch->post(REMOTE_DB_URL,$post))) {
			print_r(array('error'=>$retcont));
		}

		//print_r(array('$retcont'=>$retcont));

		if(!empty($retcont['content'])) {
			$retval = json_decode($retcont['content'],true);
			print_r(array('$retval'=>$retval));
		}

		if(!empty($retval['userid'])) {

			print_r(array('success'=>$retval));

			$content = array();
			$content['studentprofile_sync'] = $retval['userid'];

			if(!($result = $appdb->update("tbl_studentprofile",$content,"studentprofile_id=".$v['studentprofile_id']))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}
		}

	}

}


// update

if(!($result = $appdb->query("select * from tbl_studentprofile where studentprofile_update=1 order by studentprofile_id asc limit 10"))) {
	json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
	die;
}

if(!empty($result['rows'][0]['studentprofile_id'])) {

	$studentprofiles = $result['rows'];

	//print_r($studentprofiles);

	foreach($studentprofiles as $k=>$v) {

		if(!empty($v['studentprofile_sync'])) {
		} else {
			continue;
		}

		$ch = new MyCURL;

		curl_setopt($ch->ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch->ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch->ch, CURLOPT_CAINFO, ABS_PATH . "cacert/cacert.pem");

		$v['defaultpass'] = 'TAPNTXT143';

		$post = array();
		$post['params'] = base64_encode(serialize($v));
		$post['update'] = $v['studentprofile_sync'];

		if(!($retcont = $ch->post(REMOTE_DB_URL,$post))) {
			print_r(array('error'=>$retcont));
		}

		if(!empty($retcont['content'])) {
			$retval = json_decode($retcont['content'],true);
		}

		print_r(array('$retval'=>$retval));

		if(!empty($retval['userid'])) {

			print_r(array('success'=>$retval));

			$content = array();
			$content['studentprofile_update'] = 0;

			if(!($result = $appdb->update("tbl_studentprofile",$content,"studentprofile_id=".$v['studentprofile_id']))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}
		}

	}

}
