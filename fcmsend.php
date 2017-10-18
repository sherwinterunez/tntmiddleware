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

define( 'API_ACCESS_KEY', 'AIzaSyDXkejWV3kDxuC1xOyIqb7eGzMhwOy5R9E' );

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

$Title = 'Tap \'N Txt Attendance';
$Message = 'This is a sample message!';

if(!empty($_GET['msg'])) {
	$Message = trim($_GET['msg']);
}

if(!empty($_GET['title'])) {
	$Title = trim($_GET['title']);
}

if(!empty($_GET['rfid'])) {

	$rfid = trim($_GET['rfid']);

	if(!($result = $appdb->query("select * from tbl_fcm where fcm_rfid='$rfid' order by fcm_id desc limit 1"))) {
		die('an error has occured');
	}

	if(!empty($result['rows'][0]['fcm_token'])) {

		$registrationIds = array($result['rows'][0]['fcm_token']);

		$msg = array
		(
			'body' 	=> $Message,
			'title'		=> $Title,
			'vibrate'	=> 1,
			'sound'		=> 'default',
			'badge'		=> '3',
		    //'subtitle'  => 'This is a subtitle. subtitle',
		    //'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
		    //'largeIcon' => 'large_icon',
		    //'smallIcon' => 'small_icon',

		);

		$fields = array
		(
			'registration_ids' 	=> $registrationIds,
			'notification'	=> $msg,
			'data' => array(
				'msg1'=>'hello!',
				'msg2'=>'sherwin!',
				'message1'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tristique volutpat ex vel fermentum. Nam feugiat metus mi, et dapibus libero feugiat nec. Praesent cursus metus purus, sed dictum sapienx',
				'message2'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tristique volutpat ex vel fermentum. Nam feugiat metus mi, et dapibus libero feugiat nec. Praesent cursus metus purus, sed dictum sapienx',
				'message3'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tristique volutpat ex vel fermentum. Nam feugiat metus mi, et dapibus libero feugiat nec. Praesent cursus metus purus, sed dictum sapienx',
				'message4'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tristique volutpat ex vel fermentum. Nam feugiat metus mi, et dapibus libero feugiat nec. Praesent cursus metus purus, sed dictum sapienx',
				'message5'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tristique volutpat ex vel fermentum. Nam feugiat metus mi, et dapibus libero feugiat nec. Praesent cursus metus purus, sed dictum sapienx',
				'message6'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tristique volutpat ex vel fermentum. Nam feugiat metus mi, et dapibus libero feugiat nec. Praesent cursus metus purus, sed dictum sapienx',
				'message7'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tristique volutpat ex vel fermentum. Nam feugiat metus mi, et dapibus libero feugiat nec. Praesent cursus metus purus, sed dictum sapienx',
				'message8'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tristique volutpat ex vel fermentum. Nam feugiat metus mi, et dapibus libero feugiat nec. Praesent cursus metus purus, sed dictum sapienx',
				//'message9'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tristique volutpat ex vel fermentum. Nam feugiat metus mi, et dapibus libero feugiat nec. Praesent cursus metus purus, sed dictum sapienx',
				//'message10'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tristique volutpat ex vel fermentum. Nam feugiat metus mi, et dapibus libero feugiat nec. Praesent cursus metus purus, sed dictum sapienx',
				),
		    'priority' 	=> 'high',
		);
 
		$headers = array
		(
			'Authorization: key=' . API_ACCESS_KEY,
			'Content-Type: application/json'
		);
		 
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		curl_close( $ch );

		echo $result;

	}
}
