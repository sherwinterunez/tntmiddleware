<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: November 7, 2016
*
* Description:
*
* Application entry point.
*
*/

//define( 'API_ACCESS_KEY', 'AIzaSyDXkejWV3kDxuC1xOyIqb7eGzMhwOy5R9E' );

define( 'API_ACCESS_KEY', 'AIzaSyAvYuJu6CLvpedHAeKIh9HfwtBs86nN_gM' );

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

//echo json_encode($json);

if(!empty($json)&&!empty($json['msgid'])) {

	$msgid = $json['msgid'];

	if(!($result = $appdb->query("select * from tbl_notifications where notifications_hash='$msgid'"))) {
		die;
	}

	if(!empty($result['rows'][0]['notifications_id'])) {
		echo json_encode($result['rows']);
		die;
	}

} else
if(!empty($json)&&!empty($json['topic'])&&is_array($json['topic'])) {

	$limit = 5;
	$where = '';

	foreach($json['topic'] as $k=>$v) {
		$json['topic'][$k] = "'$v'";
	}

	$filter = implode(',', $json['topic']);

	if(!empty($json['back'])) {
		$where = "notifications_unixtime < ".$json['back']." and ";
	}

	if(!empty($json['ahead'])) {
		$where = "notifications_unixtime > ".$json['ahead']." and ";
	}

	$sql = "select * from (select * from tbl_notifications where $where notifications_topic in ($filter) order by notifications_unixtime desc limit $limit) as notifications order by notifications_unixtime asc";

	trigger_error("$sql",E_USER_NOTICE);

	if(!($result = $appdb->query($sql))) {
		die;
	}

	if(!empty($result['rows'][0]['notifications_id'])) {
		echo json_encode($result['rows']);
		die;
	}
}

echo json_encode(array('error'=>'error'));

