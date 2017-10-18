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

//require_once(INCLUDE_PATH.'pdu.inc.php');
//require_once(INCLUDE_PATH.'pdufactory.inc.php');
//require_once(INCLUDE_PATH.'utf8.inc.php');
//require_once(INCLUDE_PATH.'sms.inc.php');

require_once(INCLUDE_PATH.'config.inc.php');
require_once(INCLUDE_PATH.'error.inc.php');
require_once(INCLUDE_PATH.'errors.inc.php');
require_once(INCLUDE_PATH.'functions.inc.php');
require_once(INCLUDE_PATH.'curl.inc.php');
require_once(INCLUDE_PATH.'db.inc.php');

//pre(array('time'=>microtime()));

if(!($result = $appdb->query("select * from tbl_smsinbox"))) {
	//return false;
}

$ctr = '';

if(!empty($_GET['ctr'])) {
	$ctr = $_GET['ctr'];
}

$tstop = timer_stop();

//pre(array('$tstop'=>$tstop));

//echo $ctr.' => '.$tstop." sec(s)\n";

$retval = array();
$retval['ctr'] = $ctr;
$retval['timer'] = $tstop;

die(json_encode($retval));





