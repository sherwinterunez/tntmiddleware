<?php
// API access key from Google API's Console
define( 'API_ACCESS_KEY', 'AIzaSyDXkejWV3kDxuC1xOyIqb7eGzMhwOy5R9E' );

if(!empty($_GET['id'])) {
	$registrationIds = array( $_GET['id'] );
} else {
	$registrationIds = array();
	// sherwin
	$registrationIds[] = 'dnI7WS_h3xM:APA91bEU3QNjGxO4W3hsPIKNtnniho8sqBfaXjLnlq_E-KZRrLcB-g2KMbiMSlg29_9nMp6gCwYaLAWPo3mzR_LusVFDJ0S-IjpCatyjpMgkGvvqs1SIdPLjr2PukVZEKgAnXgd0oBvy';
	
	// celeste
	$registrationIds[] = 'dSMWHHks4h0:APA91bHwWKv2l2lhphAYAdD_ZC-UkgGG0bhY1D0FX0UHFhmGSTSoywPa5HWtOe4ieT5XBT26Ugtts1cFLzk63Qs_7CkVzR7fBAkiJKDjCnU5d7rr52T2ELnhQzny3MiTwieSzW-MLOIm';
}

if(!empty($_GET['body'])) {
	$body = $_GET['body'];
} else {
	$body = 'MEP latest software update!';
}

if(!empty($_GET['title'])) {
	$title = $_GET['title'];
} else {
	$title = 'MEP Technologies';
}

// prep the bundle
$msg = array
(
	'body' 	=> $body,
	'title'		=> $title,
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
	'notification'			=> $msg,
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

