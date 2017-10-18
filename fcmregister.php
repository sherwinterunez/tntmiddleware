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

if(!empty($json)&&!empty($json['rfid'])&&!empty($json['token'])) {

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
}


