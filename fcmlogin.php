<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: October 28, 2016
*
* Description:
*
* Application entry point.
*
*/

//define('ANNOUNCE', true);

error_reporting(E_ALL);

//ini_set("max_execution_time", 300);

define('APPLICATION_RUNNING', true);

define('ABS_PATH', dirname(__FILE__) . '/');

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

//define('INCLUDE_PATH', ABS_PATH . 'includes/');

require_once(ABS_PATH.'includes/index.php');

global $appdb, $appaccess, $appsession;

$appsession->start();

$json = json_decode(file_get_contents('php://input'), true);

if(!empty($json)&&!empty($json['username'])&&!empty($json['user_hash'])) {

	if(!($result = $appdb->query("select * from tbl_users where user_login='".pgFixString($json['username'])."'"))) {
		json_error_return(1); // 1 => 'Error in SQL execution.'
	}

	if(!empty($result['rows'][0]['user_id'])) {
	} else {
		json_error_return(2); // 2 => 'Invalid username/password.'
	}

	$userinfo = $result['rows'][0];

	if(!($result = $appdb->query("select * from tbl_roles where role_id='".$userinfo['role_id']."'"))) {
		json_error_return(1); // 1 => 'Error in SQL execution.'
	}

	if(!empty($result['rows'][0]['role_id'])) {
	} else {
		json_error_return(4); // 4 => 'Invalid Role ID.',
	}

	$roleinfo = $result['rows'][0];

	if($userinfo['flag']==255) {
		if(!($result = $appdb->update('tbl_users',array('loginfailed'=>'#loginfailed + 1#','loginfailedstamp'=>'now()'),"user_login='".pgFixString($json['username'])."'"))) {
			json_error_return(1); // 1 => 'Error in SQL execution.'
		}

		json_error_return(3); // 3 => 'Username has been disabled.'
	}

	if($userinfo['user_hash']!=$json['user_hash']) {

		if(!empty($userinfo['loginfailed'])&&intval($userinfo['loginfailed'])>7) {
			if(!($result = $appdb->update('tbl_users',array('loginfailed'=>'#loginfailed + 1#','loginfailedstamp'=>'now()','flag'=>'255'),"user_login='".pgFixString($json['username'])."'"))) {
				json_error_return(1); // 1 => 'Error in SQL execution.'
			}

			json_error_return(3); // 3 => 'Username has been disabled.'
		} 

		if(!($result = $appdb->update('tbl_users',array('loginfailed'=>'#loginfailed + 1#','loginfailedstamp'=>'now()'),"user_login='".pgFixString($json['username'])."'"))) {
			json_error_return(1); // 1 => 'Error in SQL execution.'
		}

		json_error_return(2); // 2 => 'Invalid username/password.'
	}

	if(!empty($userinfo['content'])) {
		$userinfo['content'] = json_decode($userinfo['content'],true);
	}

	if(!empty($roleinfo['content'])) {
		$roleinfo['content'] = $_SESSION['ACCESS'] = json_decode($roleinfo['content'],true);
	}

	$_SESSION['USER'] = $userinfo;
	$_SESSION['ROLE'] = $roleinfo;
		
	if(!($result = $appdb->update('tbl_users',array('lastloginstamp'=>'now()','loginfailed'=>0),'user_id='.$userinfo['user_id']))) {
		json_error_return(1); // 1 => 'Error in SQL execution.'
	}

	//pre(array('$this->post'=>$this->post,'$result'=>$result,'$_SESSION'=>$_SESSION));

	//json_error_return(0,'User successfully logged in.');

	$ret = array();
	$ret['message'] = 'User successfully logged in.';
	$ret['session'] = $_SESSION;
	$ret['server'] = $_SERVER;

	die(json_encode($ret));
}

//echo json_encode($json);

//echo json_encode(array('$_GET'=>$_GET,'$_POST'=>$_POST));

/*if(!empty($json)&&!empty($json['rfid'])&&!empty($json['token'])) {

	$content = array();
	$content['fcm_rfid'] = $rfid = trim($json['rfid']);
	$content['fcm_token'] = trim($json['token']);

	if(!($result = $appdb->query("select fcm_id from tbl_fcm where fcm_rfid='$rfid'"))) {
		die;
	}

	if(!empty($result['rows'][0]['fcm_id'])) {
		$fcm_id = $result['rows'][0]['fcm_id'];
		$content['fcm_updatestamp'] = 'now()';
		if(!($result = $appdb->update('tbl_fcm',$content,'fcm_id='.$fcm_id))) {
		}		
	} else {
		if(!($result = $appdb->insert('tbl_fcm',$content,'fcm_id'))) {
		}		
	}

	echo json_encode($json);
}*/


