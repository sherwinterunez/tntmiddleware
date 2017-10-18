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

$json = json_decode(file_get_contents('php://input'), true);

//echo json_encode(array('$_GET'=>$_GET,'$_POST'=>$_POST));

/*
6b14b034474edd8f751fa7a7cb8b6a83857243be | 09493621618
TAPNTXT143
*/

$ret = array('error'=>'Error!');
//$ret['json'] = $json;

if(!empty($json['username'])&&!empty($json['user_hash'])) {

	$content = array();
	$content['user_hash'] = $json['user_hash'];

	if(!($result = $appdb->update('tbl_users',$content,"user_login='".$json['username']."'"))) {
		$ret = array('lasterror'=>$appdb->lasterror,'lastquery'=>$appdb->lastquery);
		die(json_encode($ret));
	}		

	$ret = array('success'=>'Success!');
}

echo json_encode($ret);



