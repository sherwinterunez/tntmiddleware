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

define('INCLUDE_PATH', ABS_PATH . 'includes/');

//require_once(ABS_PATH.'includes/index.php');
//require_once(ABS_PATH.'modules/index.php');

require_once(INCLUDE_PATH.'config.inc.php');
require_once(INCLUDE_PATH.'miscfunctions.inc.php');
require_once(INCLUDE_PATH.'functions.inc.php');
require_once(INCLUDE_PATH.'errors.inc.php');
require_once(INCLUDE_PATH.'error.inc.php');
require_once(INCLUDE_PATH.'db.inc.php');
require_once(INCLUDE_PATH.'pdu.inc.php');
require_once(INCLUDE_PATH.'pdufactory.inc.php');
require_once(INCLUDE_PATH.'utf8.inc.php');
require_once(INCLUDE_PATH.'sms.inc.php');
require_once(INCLUDE_PATH.'userfuncs.inc.php');

date_default_timezone_set('Asia/Manila');

//require_once('gsmsms.class.inc.php');
//require_once('Pdu/Pdu.php');
//require_once('Pdu/PduFactory.php');
//require_once('Utf8/Utf8.php');

define ("DEVICE_NOTSET", 0);
define ("DEVICE_SET", 1);
define ("DEVICE_OPENED", 2);

function at_cpas($sms) {
	$sms->sendMessage("AT+CPAS\r\n");

	//$sms->readPort(5000);

	//print_r(array(preg_quote("+CPAS: 0\r")));

	if($sms->readPort("+CPAS: 0\r\n",60)) {
		return true;
	}
	return false;
}

function wind4($sms) {
	//print_r(array('wind4'=>'wind4'));
	if($sms->readPort("+WIND: 4\r\n", 120)) {
		return true;
	}
	return false;
}


class APP_SMS extends SMS {

	public function deviceInit($device=false,$baudrate=115200) {

		if(!($this->deviceSet($device)&&$this->deviceOpen('w+')&&$this->setBaudRate($baudrate))) {
			return false;
		}

		return true;
	}

}

$message1 = '1/2 31-May 11:56:639397599095 has loaded SMARTLoad All Text 10 (P9.55) to 09493621255. Your new Load Wallet Balance is P288.07.';

$message2 = '2/2 Maari ring ibenta ang Big Bytes 50 w/ 700 MB valid for 3 days! Text BIG50 to 2477. Ref:800008482739 ';

$message3 = '31May 0236: 09397599095 has loaded LOAD 5 (P4.77) to 09493621618. New Load Wallet Balance:P297.62. Ref:072011002464';

/*
$content = array();
$content['smsinbox_message'] = $message1;
$content['smsinbox_contactnumber'] = '09493621618';
$content['smsinbox_simnumber'] = '09397599095';

print_r(array('$content'=>$content));

//if(preg_match('/d+\/d+\s+.+?(\d+\d{3}\d{7}).+?loaded(.+?)to(.+?)(\d+\d{3}\d{7}).+?balance.+?(\d+\.\d+).+/si',$content['smsinbox_message'],$matches)) {

//if(preg_match('/d+\/\d+\s+.+?(\d+\d{3}\d{7}).+?loaded(.+?)to(.+?)(\d+\d{3}\d{7}).+?balance.+?(\d+\.\d+)/si',$content['smsinbox_message'],$matches)) {


if(preg_match('/(?<part>\d+)\/(?<total>\d+)\s+.+?(?<loadtransaction_simnumber>\d+\d{3}\d{7}).+?loaded(?<loadtransaction_product>.+?)to.+?(?<loadtransaction_recipientnumber>\d+\d{3}\d{7}).+?balance.+?(?<loadtransaction_balance>\d+\.\d+)/si',$content['smsinbox_message'],$matches)) {
	print_r(array('$matches'=>$matches));
}


$content = array();
$content['smsinbox_message'] = $message2;
$content['smsinbox_contactnumber'] = '09493621618';
$content['smsinbox_simnumber'] = '09397599095';

print_r(array('$content'=>$content));

//if(preg_match('/d+\/d+\s+.+?(\d+\d{3}\d{7}).+?loaded(.+?)to(.+?)(\d+\d{3}\d{7}).+?balance.+?(\d+\.\d+).+/si',$content['smsinbox_message'],$matches)) {

//if(preg_match('/d+\/\d+\s+.+?(\d+\d{3}\d{7}).+?loaded(.+?)to(.+?)(\d+\d{3}\d{7}).+?balance.+?(\d+\.\d+)/si',$content['smsinbox_message'],$matches)) {


if(preg_match('/(?<part>\d+)\/(?<total>\d+)\s+.+?ref.+?(?<loadtransaction_ref>\d+)/si',$content['smsinbox_message'],$matches)) {
	print_r(array('$matches'=>$matches));
}

$content = array();
$content['smsinbox_message'] = $message3;
$content['smsinbox_contactnumber'] = '09493621618';
$content['smsinbox_simnumber'] = '09397599095';

print_r(array('$content'=>$content));

//if(preg_match('/d+\/d+\s+.+?(\d+\d{3}\d{7}).+?loaded(.+?)to(.+?)(\d+\d{3}\d{7}).+?balance.+?(\d+\.\d+).+/si',$content['smsinbox_message'],$matches)) {

//if(preg_match('/d+\/\d+\s+.+?(\d+\d{3}\d{7}).+?loaded(.+?)to(.+?)(\d+\d{3}\d{7}).+?balance.+?(\d+\.\d+)/si',$content['smsinbox_message'],$matches)) {


if(preg_match('/.+?(?<loadtransaction_simnumber>\d+\d{3}\d{7}).+?loaded(?<loadtransaction_product>.+?)to.+?(?<loadtransaction_recipientnumber>\d+\d{3}\d{7}).+?balance.+?(?<loadtransaction_balance>\d+\.\d+).+?ref.+?(?<loadtransaction_ref>\d+.+)/si',$content['smsinbox_message'],$matches)) {
	print_r(array('$matches'=>$matches));
}
*/

$content = array();
$content['smsinbox_message'] = $message2;
$content['smsinbox_contactnumber'] = '09493621618';
$content['smsinbox_simnumber'] = '09397599095';

processSMS($content);




