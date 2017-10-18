<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: April 18, 2017 10:12AM
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

//pre(array('$_GET'=>$_GET));

if(!empty($_GET['size'])&&is_numeric($_GET['size'])&&intval($_GET['size'])>0) {
  $size = intval($_GET['size']);
}

		header("Content-Type: image/jpg");

		$img = new APP_SimpleImage;

		$img->load('./templates/default/tap/user.jpg');

		if(!empty($size)) {
			$width = $img->getWidth();
			$height = $img->getHeight();
			if($width<$height) {
				$img->crop($width);
				$img->resizeToWidth($size);
			} else
			if($height<$width) {
				$img->crop($height);
				$img->resizeToHeight($size);
			} else {
				$img->resizeToHeight($size);
			}
		}

		$img->output();

	die();



///
