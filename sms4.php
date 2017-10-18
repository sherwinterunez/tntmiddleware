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

define('APPLICATION_RUNNING', true);

define('ABS_PATH', dirname(__FILE__) . '/');

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

define('INCLUDE_PATH', ABS_PATH . 'includes/');

//require_once(ABS_PATH.'includes/index.php');
//require_once(ABS_PATH.'modules/index.php');

require_once(INCLUDE_PATH.'pdu.inc.php');
require_once(INCLUDE_PATH.'pdufactory.inc.php');
require_once(INCLUDE_PATH.'utf8.inc.php');
require_once(INCLUDE_PATH.'sms.inc.php');

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

	if($sms->readPort("+CPAS: 0\r\n", 10)) {
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


error_reporting(E_ALL);

$sms = new SMS();

//////////


//die;


/////////

if(!($sms->deviceSet("/dev/cu.usbserial")&&$sms->deviceOpen('w+')&&$sms->setBaudRate(115200))) {
	die("An error has occured!\n");
}
 
$sms->init();

if($sms->sendMessageOk("AT\r\n")&&$sms->sendMessageOk("AT+CMEE=1\r\n")&&$sms->sendMessageOk("AT+WIND=15\r\n")&&at_cpas($sms)&&$sms->sendMessageOk("AT+STSF=2,\"5FFFFFFF7F\",10,0\r\n")) {
	$sms->sendMessage("AT+CFUN=1\r\n");
	$sms->readPort(2);
	$sms->deviceClose();
}

$sms = new SMS();

if(!($sms->deviceSet("/dev/cu.usbserial")&&$sms->deviceOpen('w+')&&$sms->setBaudRate(115200))) {
	die("An error has occured!\n");
}

$sms->init();


//if($sms->sendMessageOk("AT\r")&&$sms->sendMessageOk("AT+CMEE=1\r")) {

if($sms->sendMessageOk("AT\r\n")&&wind4($sms)&&$sms->sendMessageOk("AT+CMEE=1\r\n")&&$sms->sendMessageOk("AT+COPS=2\r\n",60)&&$sms->sendMessageOk("AT+COPS=0\r\n",60)) {
	$sms->sendMessageOk("AT+CNUM\r\n",60);
	$sms->sendMessageOk("AT+STSF?\r\n");
	$sms->sendMessageOk("AT+CCLK?\r\n");
	$sms->sendMessageOk("AT+CPAS\r\n");
	$sms->sendMessageOk("AT+CGMI\r\n");
	$sms->sendMessageOk("AT+CGMM\r\n");
	$sms->sendMessageOk("AT+CGMR\r\n");
	$sms->sendMessageOk("AT+CCID\r\n");

	$sms->sendMessageOk("AT+CSMS=1\r\n");

	$sms->sendMessageOk("AT+CMGF=0\r\n");

	$sms->sendMessageOk("AT+CNMI=2,2,0,0,0\r\n");

	$sms->sendMessageOk("AT+CNMI?\r\n");

	$sms->sendMessageOk("AT+COPS?\r\n");

	$sms->sendMessageOk("AT+CREG?\r\n");

	$sms->sendMessageOk("AT+CSCA?\r\n",10);

	//$sms->sendMessageOk("AT+CMGF=1\r\n");

	//$sms->sendMessageOk("AT+CMGL=\"ALL\"\n",120);

	$max = 20;

	$chop = array();

	$msg = array();

///// check balance for smart sim
	$msg['message'] = '?1515';	
	$msg['number'] = '214';
	//$msg['smsc'] = '+639180000101';
	$msg['class'] = -1;
	$msg['alphabetSize'] = 7;
	$msg['pdu'] = true;
	$msg['receiverFormat'] = '81';
/////
	/*$msg['message'] = 'AT10';	
	$msg['number'] = '9999';
	$msg['smsc'] = '+639180000101';
	$msg['class'] = -1;
	$msg['alphabetSize'] = 7;
	$msg['pdu'] = true;
	$msg['receiverFormat'] = '81';*/
/////

	/*//$msg['message'] = '?1515';	
	//$msg['message'] = 'hello';
	//$msg['message'] = 'the quick brown fox jump over the lazy dog besides the river bank. the quick brown fox jump over the lazy dog besides the river bank. lorem ipsum dolor sit amet.';
	//$msg['message'] = 'testing message 123. the quick brown fox jump over the lazy dog besides the river bank. the quick brown fox jump over the lazy dog besides the river bank. lorem ipsum dolor sit amet. in the beginning was the word!';
	$msg['message'] = 'the quick brown fox jump over the lazy dog besides the river bank.';
	//$msg['message'] = 'Tol, this just a test message 123. Let me know kung nareceived mo! Thanks... Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed massa lacus, varius ac convallis vitae, ultrices sed neque. Nam posuere dui fringilla, dictum ex eget, consectetur libero. Vestibulum vulputate quam augue, non laoreet lectus molestie in. Nulla ac mattis arcu. Etiam nec dapibus ex. Pellentesque sagittis neque arcu, non mattis ex faucibus non. Donec nunc quam, tincidunt id nisl id, elementum hendrerit risus. Donec feugiat ipsum urna, quis imperdiet ex vestibulum sit amet. Suspendisse scelerisque sollicitudin ex, egestas luctus arcu. Ut et risus massa. Suspendisse nec augue justo. Curabitur eget ex sit amet mi tempor facilisis ut eget elit. Cras auctor nunc justo. Fusce ac neque ut ex scelerisque dapibus. Morbi porttitor mauris mauris, efficitur pellentesque nunc aliquam eu. Nam eu nisl ullamcorper, auctor enim ut, viverra dolor. Donec tristique suscipit ligula sit amet tristique. Sed ultricies sem nibh, non fringilla orci faucibus ultrices. Duis vel tempor ipsum. Fusce elit ex, cursus ac tincidunt finibus, placerat sit amet mi. Donec cursus in felis vitae mattis. Integer at erat vitae lacus commodo elementum. Quisque ipsum mauris, faucibus accumsan eros eget, venenatis sollicitudin ex. Donec tempus feugiat lacus, vitae placerat ligula ultrices ac. Donec non congue erat. Suspendisse sodales nibh sed nisl malesuada, vitae vehicula arcu placerat. In accumsan tempor pellentesque. Pellentesque sed fermentum sapien. Praesent ut neque at tortor feugiat mattis. Vestibulum sagittis tempor massa, quis tempor ipsum bibendum ac. Integer dictum, orci vitae tincidunt elementum, nibh leo dignissim dolor, in viverra justo urna at dolor. Praesent consectetur fermentum metus, quis gravida magna vestibulum non. Fusce eros quam, eleifend vitae libero vitae, convallis rutrum enim. Vestibulum nunc ex, blandit eget semper id, efficitur nec mauris. Donec eu velit non ligula suscipit vehicula a elementum lectus. Maecenas id nunc at ex facilisis sollicitudin at sed erat. Praesent rhoncus arcu turpis, sit amet ultricies magna porta eu. Praesent auctor scelerisque dui et malesuada. Nam vitae libero non leo mattis molestie nec at nibh. Praesent sit amet sapien dolor. Vestibulum cursus fermentum convallis. Curabitur nec posuere mi, nec interdum velit. Sed ut velit magna. Nullam purus magna, accumsan eget ipsum vel, consectetur ornare urna. Vestibulum consequat ullamcorper lectus, sed consequat sapien tempor ac. Duis auctor placerat condimentum. Sed lorem massa, elementum at metus eget, elementum iaculis enim. Curabitur malesuada efficitur quam, et pulvinar massa scelerisque vel. Sed id augue elit. Aenean vehicula, nulla quis blandit semper, magna ipsum fermentum tellus, sit amet finibus sem risus vitae felis. Proin eu cursus massa. Suspendisse maximus libero eget lacus euismod, a maximus tortor suscipit. Nullam ac augue eget massa commodo molestie vel ut libero. Fusce ut viverra justo.';

	//$msg['message'] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed massa lacus, varius ac convallis vitae, ultrices sed neque. Nam posuere dui fringilla, dictum ex eget, consectetur libero. Vestibulum vulputate quam augue, non laoreet lectus molestie in. Nulla ac mattis arcu. Etiam nec dapibus ex. Pellentesque sagittis neque arcu, non mattis ex faucibus non. Donec nunc quam, tincidunt id nisl id, elementum hendrerit risus. Donec feugiat ipsum urna, quis imperdiet ex vestibulum sit amet. Suspendisse scelerisque sollicitudin ex, egestas luctus arcu. Ut et risus massa. Suspendisse nec augue justo. Curabitur eget ex sit amet mi tempor facilisis ut eget elit. Cras auctor nunc justo.';

	$msg['number'] = '+639493621618';
	//$msg['number'] = '+639178580461';
	$msg['smsc'] = '+639180000101';
	//$msg['class'] = 2;
	$msg['class'] = -1;
	$msg['alphabetSize'] = 7;
	//$msg['pdu'] = true;
	$msg['pdu'] = true;
	//$msg['receiverFormat'] = '81';*/

	if(!empty($msg['pdu'])) {

		$sms->sendMessageOk("AT+CMGF=0\r\n");

		$pdu = new PduFactory();

		$x=1;

		if(strlen($msg['message'])>160) {
			$dta=str_split($msg['message'],152); 
			//$ref=mt_rand(1,255); 
			$ref=62;

			$sms->udh['msg_count']=$sms->dechex_str(count($dta)); 

			if(count($dta)>$max) {
				$sms->udh['msg_count']=$sms->dechex_str($max); 

			}

			$sms->udh['reference']=$sms->dechex_str($ref); 

			$ctr=1;

			foreach($dta as $part) { 
				$sms->udh['msg_part']=$sms->dechex_str($x); 
				$msg['message'] = $part . ' ';
				$msg['udh'] = implode('', $sms->udh);
				$chop[] = $msg;
				$x++; 

				$stra = $pdu->encode($msg,true);

				print_r(array('$msg'=>$msg,'$stra'=>$stra));

				print_r(array('$pdu->decode'=>$pdu->decode($stra['message'])));

				while(!$sms->sendMessageOk("AT+CMGS=".$stra['byte_size']."\n".$stra['message'].chr(26))) {
					sleep(5);
				};

				$ctr++;

				if($ctr>$max) break;
			} 

			//print_r(array('$chop'=>$chop));

		} else {

			$stra = $pdu->encode($msg);

			print_r(array('$stra'=>$stra));

			print_r(array('$pdu->decode'=>$pdu->decode($stra['message'])));

			//$sms->sendMessageOk("AT+CMGS=".$stra['byte_size']."\n".$stra['message'].chr(26));

			while(!$sms->sendMessageOk("AT+CMGS=".$stra['byte_size']."\n".$stra['message'].chr(26))) {
				sleep(5);
			};

		}
	} else {

		$sms->sendMessageOk("AT+CMGF=1\r\n");

		while(!$sms->sendMessageOk("AT+CMGS=".$msg['number']."\n".$msg['message'].chr(26))) {
			sleep(5);
		};

		$sms->sendMessageOk("AT+CMGF=0\r\n");
	}


}

$sms->readPort(5000);

$sms->deviceClose();


