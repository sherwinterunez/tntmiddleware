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

date_default_timezone_set('Asia/Manila');

// new user
if(!empty($_POST)&&!empty($_POST['params'])&&empty($_POST['update'])) {

	$params = unserialize(base64_decode($_POST['params']));

	if(!empty($params)&&is_array($params)&&!empty($params['defaultpass'])&&!empty($params['studentprofile_guardianmobileno'])) {

		$hash = computeHash($params['defaultpass'],$params['studentprofile_guardianmobileno']);

		$params['computehash'] = array($params['studentprofile_guardianmobileno'],$params['defaultpass'],base64_encode($params['studentprofile_guardianmobileno']),base64_encode($params['defaultpass']));

		if(!($result = $appdb->query("select * from tbl_users where user_login='".$params['studentprofile_guardianmobileno']."'"))) {
			json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
			die;
		}

		//pre(array('$result'=>$result)); die;

		if(!empty($result['rows'][0]['user_id'])) {
			$ret = array();
			$ret['userid'] = $result['rows'][0]['user_id'];
			$ret['success'] = 1;

			header_json();
			//json_encode_return($params);
			json_encode_return($ret);
			die;
		}

		$content = array();
		$content['role_id'] = 19; // Guardians
		$content['user_login'] = $params['studentprofile_guardianmobileno'];
		$content['user_email'] = !empty($params['studentprofile_guardianemail'])?$params['studentprofile_guardianemail']:'';

		$params['user_fname'] = !empty($params['studentprofile_guardianname'])?$params['studentprofile_guardianname']:'';

		$content['content'] = json_encode($params);

		$content['user_hash'] = $hash;

		if(!($result = $appdb->insert("tbl_users",$content,"user_id"))) {
			json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
			die;
		}

		$ret = array();

		if(!empty($result['returning'][0]['user_id'])) {
			$ret['userid'] = $params['userid'] = $result['returning'][0]['user_id'];
		}

		$ret['success'] = 1;

		header_json();
		//json_encode_return($params);
		json_encode_return($ret);
		die;

	}

}

// update user
if(!empty($_POST)&&!empty($_POST['params'])&&!empty($_POST['update'])&&is_numeric($_POST['update'])) {

	$params = unserialize(base64_decode($_POST['params']));

	if(!empty($params)&&is_array($params)&&!empty($params['defaultpass'])&&!empty($params['studentprofile_guardianmobileno'])) {

		if(!($result = $appdb->query("select * from tbl_users where role_id=19 and user_id=".intval($_POST['update'])))) {
			json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
			die;
		}

		if(!empty($result['rows'][0]['user_id'])) {

			$userId = $result['rows'][0]['user_id'];
			$userLogin = $result['rows'][0]['user_login'];

			$userContent = array();

			if(!empty($result['rows'][0]['content'])) {
				$tmp = json_decode($result['rows'][0]['content'],true);
				if(!empty($tmp)&&is_array($tmp)) {
					$userContent = $tmp;
				}
			}

			$content = array();

			if($userLogin!==$params['studentprofile_guardianmobileno']) {

				$hash = computeHash($params['defaultpass'],$params['studentprofile_guardianmobileno']);

				$params['computehash'] = array($params['studentprofile_guardianmobileno'],$params['defaultpass'],base64_encode($params['studentprofile_guardianmobileno']),base64_encode($params['defaultpass']));

				$content['user_hash'] = $hash;
				$content['user_login'] = $params['studentprofile_guardianmobileno'];

			}

			$content['user_email'] = !empty($params['studentprofile_guardianemail'])?$params['studentprofile_guardianemail']:'';

			$params['user_fname'] = !empty($params['studentprofile_guardianname'])?$params['studentprofile_guardianname']:'';

			foreach($params as $k=>$v) {
				$userContent[$k] = $v;
			}

			$content['content'] = json_encode($userContent);

			if(!($result = $appdb->update("tbl_users",$content,"user_id=".$userId))) {

				if(!empty($appdb->lasterror)&&preg_match('/duplicate key value violates unique constraint/si',$appdb->lasterror)) {
				} else {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;
				}

			}

			$ret = array();

			$ret['userid'] = $userId;
			$ret['success'] = 1;

			header_json();
			//json_encode_return($params);
			json_encode_return($ret);
			die;

		}

	}

}
