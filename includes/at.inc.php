<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Misc functions include file
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

/* INCLUDES_START */

function at_cmgs($sms=false,$bytesize=false,$msg=false) {

	if(!empty($sms)&&!empty($bytesize)&&!empty($msg)) {
	} else return false;

	at_atgt($sms);

	$simfunctions = array();

	$simfunctions[] = array(
				'command' => 'AT+CMGF=0',
				'regx' => array("(.+?)\r\nOK\r\n"),
				'timeout' => 5
		);

	$simfunctions[] = array(
				'command' => 'AT+CMGS='.$bytesize.'$CR',
				'regx' => array(">\r\n"),
		);

	$simfunctions[] = array(
				'command' => $msg . '$CTRLZ',
				//'regx' => array("OK\r\n"),
				'regx' => array("\+CMGS\:\s+\d+\r\n"),
		);

	return $sms->modemFunction($simfunctions);
}

// "AT+CMGS=".$stra['byte_size']."\n".$stra['message'].chr(26)

function at_cmgs1($sms=false,$bytesize=false,$msg=false) {

	if(!empty($sms)&&!empty($bytesize)&&!empty($msg)) {
	} else return false;

	$simfunctions = array();

	$simfunctions[] = array(
				'command' => 'AT+CMGF=0',
				'regx' => array("(.+?)\r\nOK\r\n"),
		);

	$simfunctions[] = array(
				'command' => 'AT+CMGS='.$bytesize.'$CR'.$msg.'$CTRLZ',
				//'regx' => array("OK\r\n"),
				'regx' => array("\+CMGS\:\s+\d+\r\n"),
		);

	print_r(array('$simfunctions'=>$simfunctions));

	return $sms->modemFunction($simfunctions);
}

function at_at($sms){

	$simfunctions = array();

	$simfunctions[] = array(
				'command' => 'AT',
				'regx' => array("OK\r\n"),
				'timeout' => 2,
		);

	return $sms->modemFunction($simfunctions);
}

function at_atgt($sms){

	$simfunctions = array();

	$simfunctions[] = array(
				'command' => 'AT',
				'regx' => array("\>\r\n"),
				'timeout' => 2,
		);

	if($sms->modemFunction($simfunctions)) {

		$simfunctions = array();

		$simfunctions[] = array(
					'command' => '$CTRLZ',
					'regx' => array("\+CMS\s+ERROR"),
					'timeout' => 5,
			);

	} else {
		return false;
	}

	return $sms->modemFunction($simfunctions);
}

function at_ate1($sms){

	$simfunctions = array();

	$simfunctions[] = array(
				'command' => 'AT',
				'regx' => array("OK\r\n"),
				'timeout' => 2,
		);

	return $sms->modemFunction($simfunctions);
}

function at_cnum($sms) {

	$simfunctions = array();

	$simfunctions[] = array(
				'command' => 'AT+CNUM',
				'regx' => array("\+CNUM\:\s+(.+?)\r\nOK\r\n","(\d+\d{10})"),
		);

	if($sms->modemFunction($simfunctions)) {
		if(!empty($sms->getLastResult(1))) {
			if(($res=parseMobileNo($sms->getLastResult(1)))) {
				return '0'.$res[2].$res[3];
			}
		}
	}

	return false;
}

function at_cmgl_4($sms) {

	$simfunctions = array();

	$simfunctions[] = array(
				'command' => 'AT+CMGF=0',
				'regx' => array("(.+?)\r\nOK\r\n"),
		);

	$simfunctions[] = array(
				'command' => 'AT+CMGL=4',
				'regx' => array("(.+?)\r\nOK\r\n","\+CMGL\:\s+.+?\r\nOK\r\n"),
		);

	return $sms->modemFunction($simfunctions);

}

/* INCLUDES_END */


#eof ./includes/functions/index.php
