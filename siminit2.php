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

define ("DEVICE_NOTSET", 0);
define ("DEVICE_SET", 1);
define ("DEVICE_OPENED", 2);

class APP_SMS extends SMS {

	public function process() {

	}

	public function deviceInit($device=false,$baudrate=115200) {

		if(!($this->deviceSet($device)&&$this->deviceOpen()&&$this->setBaudRate($baudrate))) {
			return false;
		}

		return true;
	}

}

function at_cpas($sms) {
	$sms->sendMessage("AT+CPAS\r\n");

	//$sms->readPort(5000);

	//print_r(array(preg_quote("+CPAS: 0\r")));

	if($sms->readPort("\+CPAS\:\s+0\r\n",60)) {
		return true;
	}
	return false;
}

function wind4($sms) {
	//print_r(array('wind4'=>'wind4'));
	if($sms->readPort("\+WIND\:\s+4\r\n", 120)) {
		return true;
	}
	return false;
}

function modemInit($dev=false,$baud=115200,$simMenu=false) {

	if(!empty($dev)) {
	} else {
		return false;
	}

	echo "\ninit starting.\n";
 
	$sms = new APP_SMS;

	if(!$sms->deviceInit($dev,$baud)) {
		die('Error initializing device!');
	}

	// 	$sms->sendMessageOk("AT+STSF=2,\"5FFFFFFF7F\",10,0\r\n")

	/*if(	$sms->sendMessageOk("AT\r\n") &&
		$sms->sendMessageOk("AT+CMEE=1\r\n") &&
		$sms->sendMessageOk("AT+WIND=15\r\n") &&
		at_cpas($sms) &&
		$sms->sendMessageOk("AT+STSF=0\r\n")
	) {
		$sms->sendMessageOk("AT+CFUN=1\r\n",1);
		//$sms->readPort(2);
		$sms->deviceClose();
	}*/

	//$sms->showBuffer();

	echo "\ninit started.\n";

	if($sms->sendMessageOk("AT\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('1An error has occured!');
	}

	if($sms->sendMessageOk("AT+CMEE=1\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('2An error has occured!');
	}

	if($sms->sendMessageOk("AT+WIND=15\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('3An error has occured!');
	}

	if(at_cpas($sms)) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('4An error has occured!');
	}

	print_r(array('hello!1'));

	if($simMenu) {
		if($sms->sendMessageOk("AT+STSF=2,\"5FFFFFFF7F\",3,0\r\n")) {
			print_r($sms->history);
			$sms->clearHistory();
		} else {
			print_r($sms->history);
			die('5.1An error has occured!');
		}

		if($sms->sendMessageOk("AT+STSF=1\r\n")) {
			print_r($sms->history);
			$sms->clearHistory();
		} else {
			print_r($sms->history);
			die('5.2An error has occured!');
		}
	} else {
		if($sms->sendMessageOk("AT+STSF=0\r\n")) {
			print_r($sms->history);
			$sms->clearHistory();
		} else {
			print_r($sms->history);
			die('5.2An error has occured!');
		}
	}

	print_r(array('hello!2'));

	$sms->sendMessageOk("AT+CFUN=1\r\n",1);

	/*$sms->deviceClose();

	//sleep(1);

	if(!$sms->deviceInit($dev,$baud)) {
		die('Error initializing device!');
	}*/

	print_r(array('hello!3'));

	//$sms = new APP_SMS;

	//if(!($sms->deviceSet("/dev/cu.usbserial")&&$sms->deviceOpen('w+')&&$sms->setBaudRate(115200))) {
	//	die("An error has occured!\n");
	//}

	//$sms->init();


	//if($sms->sendMessageOk("AT\r")&&$sms->sendMessageOk("AT+CMEE=1\r")) {

	/*if(	$sms->sendMessageOk("AT\r\n") &&
		wind4($sms) &&
		$sms->sendMessageOk("AT+CMEE=1\r\n") &&
		$sms->sendMessageOk("AT+COPS=2\r\n",60) &&
		$sms->sendMessageOk("AT+COPS=0\r\n",60)
	) {
		$sms->sendMessageOk("AT+STSF?\r\n");
		$sms->sendMessageOk("AT+CCLK?\r\n");
		$sms->sendMessageOk("AT+CPAS\r\n");
		$sms->sendMessageOk("AT+CGMI\r\n");
		$sms->sendMessageOk("AT+CGMM\r\n");
		$sms->sendMessageOk("AT+CGMR\r\n");
		$sms->sendMessageOk("AT+CCID\r\n");

		$sms->sendMessageOk("AT+CSMS=1\r\n");

		$sms->sendMessageOk("AT+CMGF=0\r\n");

		//$sms->sendMessageOk("AT+CNMI=2,2,0,0,0\r\n");

		$sms->sendMessageOk("AT+CNMI=2,1,0,0,0\r\n");

		$sms->sendMessageOk("AT+CNMI?\r\n");

		$sms->sendMessageOk("AT+COPS?\r\n");

		$sms->sendMessageOk("AT+CREG?\r\n");

		$sms->sendMessageOk("AT+CSCA?\r\n",10);

		$sms->sendMessageOk("AT+CNUM\r\n");

		//$sms->sendMessageOk("AT+CMGF=1\r\n");

		//$sms->sendMessageOk("AT+CMGL=\"ALL\"\n",120);

	} else {
		print_r(array('ERROR'=>'An Error Occured!'));
	}*/

	//$sms->showBuffer();

	if($sms->sendMessageOk("AT\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('6An error has occured!');
	}

	if($sms->sendMessageOk("AT+CREG=1\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('6An error has occured!');
	}

	do {

		if($sms->sendMessageReadPort("AT+CNUM\r\n", "\+CNUM\:\s+(.+?)\r\nOK\r\n",10)) {
			$result = $sms->getResult();
			//print_r(array('$result'=>$result));
			break;
		}

	} while(1);

	$sms->clearHistory();

	if($sms->sendMessageOk("AT+CREG?\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('7An error has occured!');
	}


	/*if(wind4($sms)) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('7An error has occured!');
	}*/

	if($sms->sendMessageOk("AT+CMEE=1\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('8An error has occured!');
	}

	//if($sms->sendMessageOk("AT+COPS=2\r\n")) {
	//} else {
	//	print_r($sms->history);
	//	die('9An error has occured!');
	//}

	/*if($sms->sendMessageOk("AT+COPS=0\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		die('10An error has occured!');
	}*/

	$sms->sendMessageOk("AT+CSMS=1\r\n");

	$sms->sendMessageOk("AT+CMGF=0\r\n");

	//$sms->sendMessageOk("AT+CNMI=2,2,0,0,0\r\n");

	$sms->sendMessageOk("AT+CNMI=2,1,0,0,0\r\n");

	/*$sms->sendMessageOk("AT+STSF?\r\n");
	$sms->sendMessageOk("AT+CCLK?\r\n");
	$sms->sendMessageOk("AT+CPAS\r\n");
	$sms->sendMessageOk("AT+CGMI\r\n");
	$sms->sendMessageOk("AT+CGMM\r\n");
	$sms->sendMessageOk("AT+CGMR\r\n");
	$sms->sendMessageOk("AT+CCID\r\n");

	$sms->sendMessageOk("AT+CNMI?\r\n");

	$sms->sendMessageOk("AT+COPS?\r\n");

	$sms->sendMessageOk("AT+CREG?\r\n");

	$sms->sendMessageOk("AT+CSCA?\r\n");*/

	$sms->sendMessageOk("AT+CNUM\r\n");

	$sms->sendMessageOk("AT+CSQ\r\n");

	//$sms->sendMessageOk("AT+CMGL=4\r\n");

	//$sms->sendMessageOk("AT+STGI=0\r\n");

	//$sms->sendMessageOk("AT+STIN?\r\n");

	print_r($sms->history);

	$sms->showBuffer();

	$timeout = 120;

	$timeoutat = time() + $timeout;

	$flag = true;

	if($simMenu) {

		do {
			if($sms->sendMessageReadPort("AT+STIN?\r\n", "\+STIN:\s+(\d+)\r\n")) {
				$result = $sms->getResult();
				$result[1] = intval(trim($result[1]));
				//print_r(array('history'=>$sms->history,'result'=>$result));
				$sms->clearHistory();

				if($result[1]===0) {
					if($sms->sendMessageReadPort("AT+STGI=0\r\n", "(\+STGI:\s+.+)\r\nOK\r\n")) {
						$result = $sms->getResult();
						//$result[1] = $sms->tocrlf($result[1]);
						print_r(array('history'=>$sms->history,'result'=>$result));
						$sms->clearHistory();
						$flag = true;
						break;
					}
				}

				//break;
			}
			usleep(20000);
		} while ($timeoutat > time());

		if($sms->sendMessageReadPort("AT+STIN?\r\n", "\+STIN:\s+(\d+)\r\n")) {
			$result = $sms->getResult();
			$result[1] = intval(trim($result[1]));
			print_r(array('history'=>$sms->history,'result'=>$result));
			$sms->clearHistory();
		}

	}

	//print_r($sms->history);

	//$sms->sendMessageOk("AT+STGR=0,1,0");

	//$sms->sendMessageOk("AT+STIN?");

	//$sms->sendMessageOk("AT+CMGF=1\r\n");

	//$sms->sendMessageOk("AT+CMGL=\"ALL\"\n",120);

	//$sms->readPort(5000);

	print_r($sms->history);

	$sms->deviceClose();

	$tstop = timer_stop();

	echo "\ninit done (".$tstop." secs).\n";

	return $flag;
}

if(getOption('$MAINTENANCE',false)) {
	die("\nmodemInit: Server under maintenance.\n");
}

$_GET['dev'] = '/dev/ttyUSB0';

if(!empty($_GET['dev'])) {
	//pre(array('$_GET'=>$_GET));

	modemInit($_GET['dev']);
}



