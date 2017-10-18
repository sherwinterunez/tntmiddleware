<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: July 31, 2017
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

//define('INCLUDE_PATH', ABS_PATH . 'includes/');

require_once(ABS_PATH.'includes/index.php');
//require_once(ABS_PATH.'modules/index.php');

/*require_once(INCLUDE_PATH.'config.inc.php');
require_once(INCLUDE_PATH.'miscfunctions.inc.php');
require_once(INCLUDE_PATH.'functions.inc.php');
require_once(INCLUDE_PATH.'errors.inc.php');
require_once(INCLUDE_PATH.'error.inc.php');
require_once(INCLUDE_PATH.'db.inc.php');
require_once(INCLUDE_PATH.'pdu.inc.php');
require_once(INCLUDE_PATH.'pdufactory.inc.php');
require_once(INCLUDE_PATH.'utf8.inc.php');
require_once(INCLUDE_PATH.'sms.inc.php');
require_once(INCLUDE_PATH.'userfuncs.inc.php');*/

date_default_timezone_set('Asia/Manila');

$localIP = getMyLocalIP();

if(trim($localIP)=='') {
	$localIP = '127.0.0.1';
}

$timeout = 120;

$timeoutat = time() + $timeout;

$flag = false;

$bypass = true;

do {

	$localIP = getMyLocalIP();

	if(trim($localIP)=='') {
		$localIP = '127.0.0.1';
	} else
	if(trim($localIP)=='0.0.0.0'||trim($localIP)=='127.0.0.1') {
	} else {
		$bypass = false;
		break;
	}

	sleep(1);

} while ($timeoutat > time());

echo "My IP Address: \n$localIP\n";

if(!$bypass) {
	sleep(60);	
}

if(!empty(($license=checkLicense()))) {
} else {
  $license = array('sc'=>'TAP N TEXT UNLICENSED VERSION');
}

$settings_loginnotificationschooladmin = ''.getOption('$SETTINGS_LOGINNOTIFICATIONSCHOOLADMIN','');
$settings_loginnotificationschooladminsendsms = getOption('$SETTINGS_LOGINNOTIFICATIONSCHOOLADMINSENDSMS',false);
$settings_loginnotificationostrelationshipmanager = ''.getOption('$SETTINGS_LOGINNOTIFICATIONOSTRELATIONSHIPMANAGER','');
$settings_loginnotificationostrelationshipmanagersendsms = getOption('$SETTINGS_LOGINNOTIFICATIONOSTRELATIONSHIPMANAGERSENDSMS',false);

$sendto = array();

if(!empty($settings_loginnotificationostrelationshipmanagersendsms)&&preg_match('/\;/si',$settings_loginnotificationostrelationshipmanager)) {
  $settings_loginnotificationostrelationshipmanager = explode(';',$settings_loginnotificationostrelationshipmanager);

  foreach($settings_loginnotificationostrelationshipmanager as $k=>$v) {
    if(($res=parseMobileNo($v))&&!empty($res[2])&&!empty($res[3])) {
      $mobileno = '0'.$res[2].$res[3];
      $sendto[] = $mobileno;
    }
  }
} else
if(!empty($settings_loginnotificationostrelationshipmanagersendsms)&&!empty($settings_loginnotificationostrelationshipmanager)) {
  if(($res=parseMobileNo($settings_loginnotificationostrelationshipmanager))&&!empty($res[2])&&!empty($res[3])) {
    $mobileno = '0'.$res[2].$res[3];
    $sendto[] = $mobileno;
  }
}

if(!empty($settings_loginnotificationschooladminsendsms)&&preg_match('/\;/si',$settings_loginnotificationschooladmin)) {
  $settings_loginnotificationschooladmin = explode(';',$settings_loginnotificationschooladmin);

  foreach($settings_loginnotificationschooladmin as $k=>$v) {
    if(($res=parseMobileNo($v))&&!empty($res[2])&&!empty($res[3])) {
      $mobileno = '0'.$res[2].$res[3];
      $sendto[] = $mobileno;
    }
  }
} else
if(!empty($settings_loginnotificationschooladminsendsms)&&!empty($settings_loginnotificationschooladmin)) {
  if(($res=parseMobileNo($settings_loginnotificationschooladmin))&&!empty($res[2])&&!empty($res[3])) {
    $mobileno = '0'.$res[2].$res[3];
    $sendto[] = $mobileno;
  }
}

$push = 0;



$msgdt = date('F j, Y, l - h:i:s A',intval(getDbUnixDate()));

// TNT Login Successfully to  PREMIERE HEIGHTS LEARNING CENTER , May 12,2017, Friday - 04:08:16 PM

//$msg = 'TNT Login Successfully to '.$license['sc'].', '.$msgdt;

$msg = 'TNT Server Started at '.$msgdt.' - '.$license['sc'];

/*if(!empty($settings_loginnotificationschooladminsendsms)&&!empty($settings_loginnotificationschooladmin)) {
  if(($res=parseMobileNo($settings_loginnotificationschooladmin))&&!empty($res[2])&&!empty($res[3])) {
    $mobileno = '0'.$res[2].$res[3];
    $asims = getAllSims(5);
    if(!empty($asims)&&is_array($asims)) {
      shuffle($asims);
      sendToOutBoxPriority($mobileno,$asims[0]['sim_number'],$msg,$push);
    }
  }
}

if(!empty($settings_loginnotificationostrelationshipmanagersendsms)&&!empty($settings_loginnotificationostrelationshipmanager)) {
  if(($res=parseMobileNo($settings_loginnotificationostrelationshipmanager))&&!empty($res[2])&&!empty($res[3])) {
    $mobileno = '0'.$res[2].$res[3];
    $asims = getAllSims(5);
    if(!empty($asims)&&is_array($asims)) {
      shuffle($asims);
      sendToOutBoxPriority($mobileno,$asims[0]['sim_number'],$msg,$push);
    }
  }
}*/

if(!empty($sendto)) {
  foreach($sendto as $k=>$mobileno) {
    $asims = getAllSims(5);
    if(!empty($asims)&&is_array($asims)) {
      shuffle($asims);
      sendToOutBoxPriority($mobileno,$asims[0]['sim_number'],$msg,$push);
    }
  }
}


//
