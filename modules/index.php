<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Main module include file
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

define('MODULE_PATH', ABS_PATH . 'modules/');
//define('TEMPLATE_PATH', ABS_PATH . 'modules/');

require_once(MODULE_PATH.'baseclasses.mod.php');
require_once(MODULE_PATH.'index.mod.php');
require_once(MODULE_PATH.'login.mod.php');
//require_once(MODULE_PATH.'tap.mod.php');
//require_once(MODULE_PATH.'admin.mod.php');
require_once(MODULE_PATH.'app.mod.php');
//require_once(MODULE_PATH.'app.user.mod.php');
require_once(MODULE_PATH.'app.messaging.mod.php');
require_once(MODULE_PATH.'app.useraccount.mod.php');
require_once(MODULE_PATH.'app.promotion.mod.php');
require_once(MODULE_PATH.'app.eload.mod.php');
//require_once(MODULE_PATH.'app.layouts.mod.php');
//require_once(MODULE_PATH.'app.toolbars.mod.php');
require_once(MODULE_PATH.'app.forms.mod.php');
//require_once(MODULE_PATH.'test.mod.php');

#eof ./modules/index.php
