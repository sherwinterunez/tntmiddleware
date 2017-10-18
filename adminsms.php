<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: January 13, 2017
*
* Description:
*
* Application entry point.
*
*/

//define('ANNOUNCE', true);

error_reporting(E_ALL);

ini_set("max_execution_time", 300);

ini_set('precision',30);

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

if(!empty(($license=checkLicense()))) {
} else {
	print_r(array('ERROR'=>'Invalid or expired license!'));
	sleep(10);
	return false;
}

$settings_bridgetoadmin = getOption('$SETTINGS_BRIDGETOADMIN',false);

$settings_bridgetoadminip = getOption('$SETTINGS_BRIDGETOADMINIP','');

if(!empty($settings_bridgetoadmin)&&!empty($settings_bridgetoadminip)) {
} else {
  sleep(10);
  return false;
}

global $appdb;

$settings_sendpushnotification  = getOption('$SETTINGS_SENDPUSHNOTIFICATION',false);

$settings_sendsmsnotification  = getOption('$SETTINGS_SENDSMSNOTIFICATION',true);

print_r(array('BRIDGING ADMIN'=>$settings_bridgetoadminip));

$ch = new MyCURL;

$url = 'http://'.$settings_bridgetoadminip.'/getsms.php';

if(!($retcont = $ch->get($url))) {
  print_r(array('error'=>'error!'));
  return false;
}

print_r(array('$retcont'=>$retcont));

if(!empty($retcont['content'])) {
  $retval = json_decode($retcont['content'],true);
}

$mid = array();

$push = 0;

if($settings_sendpushnotification) {
  $push = 1;
}

$status = 1; // waiting

if(!empty($retval)&&is_array($retval)&&!empty($retval['data'][0]['MessageID'])) {
  print_r(array('$retval'=>$retval));

  $asim = getAllSims(3);

  pre(array('$asim'=>$asim));

  foreach($retval['data'] as $k=>$v) {
    $mid[] = $v['MessageID'];

/////

    if(!empty($asim)) {

      shuffle($asim);

      foreach($asim as $m=>$n) {
        pre(array('$mobileno'=>$v['SMSNumber'],'$m'=>$n['sim_number'],'$msg'=>$v['SMSMessage'],'$license[sc]'=>$license['sc']));
        sendToOutBoxPriority($v['SMSNumber'],$n['sim_number'],$v['SMSMessage'],$push,1,$status,0,0);
        break;
      }

    } else {
      // no sim card detected or no connected gsm modem

      pre(array('$mobileno'=>$v['SMSNumber'],'$m'=>false,'$msg'=>$v['SMSMessage'],'$license[sc]'=>$license['sc']));
      sendToOutBoxPriority($v['SMSNumber'],false,$v['SMSMessage'],$push,1,$status,0,0);

    }

/////


  }

  if(!empty($mid)&&is_array($mid)) {
    print_r(array('$mid'=>$mid));

    $url = 'http://'.$settings_bridgetoadminip.'/delsms.php';

    $var = array();
    $var['mid'] = implode(',',$mid);
    $var['delete'] = 'yes';

    if(!($retcont = $ch->post($url,$var))) {
      print_r(array('error'=>'error!'));
      return false;
    }

    print_r(array('$retcont'=>$retcont));


  }
}

//
