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

global $appdb;

if(!empty($_GET['size'])&&is_numeric($_GET['size'])&&intval($_GET['size'])>0) {
	$size = intval($_GET['size']);
}

$size = 500;

if(!empty($_GET['pid'])&&is_numeric($_GET['pid'])&&intval($_GET['pid'])>0) {

	$pid = intval($_GET['pid']);
	$ssize = '';

	if($size) {
		$ssize = '-'.$size;
	}

	$settings_autodetectface = getOption('$SETTINGS_AUTODETECTFACE',false);

	if($settings_autodetectface) {
		$ssize = $ssize . '-autodetect';
	}

	$imagefile = '/var/log/cache/'.$pid.$ssize.'.jpg';

	if(@file_exists($imagefile)) {
		header("Content-Type: image/jpg");
		readfile($imagefile);
		die;
	}

	if(!($result = $appdb->query("select * from tbl_upload where upload_studentprofileid=".intval($_GET['pid'])))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
		die;
	}

	if(!empty($result['rows'][0]['upload_content'])) {
		//$retval['uploadid'] = $result['rows'][0]['upload_id'];
		$content = base64_decode($result['rows'][0]['upload_content']);
	} else {

		define('TAP_PATH', ABS_PATH . 'templates/default/tap');

		$defaultphoto = TAP_PATH.'/user.jpg';

		if(file_exists($defaultphoto)&&($hf=fopen($defaultphoto,'r'))) {

	    $content = fread($hf,filesize($defaultphoto));

			//pre(array('$defaultphoto'=>$defaultphoto,'$size'=>$size)); die;

			//pre($content); die;

	    fclose($hf);

			header("Content-Type: image/jpg");

			if(!empty($content)) {
				$img = new APP_SimpleImage;

				$img->loadfromstring($content);

				if(!empty($size)) {
						$img->resize($size,$size);
				}

				$img->output();
			}

			die;

		}
	}

	if(!empty($content)) {

		header("Content-Type: image/jpg");

		if($settings_autodetectface) {

			$detector = new FaceDetector;

			$detector->faceDetectString($content);
			//$detector->faceDetect('duterte101.jpg');

			$detector->cropFaceToJpeg2();

			if(!empty($size)) {
				$detector->resize($size,$size);
			}

			@$detector->output(IMAGETYPE_JPEG, $imagefile);

			$detector->output();

			//$detector->cropFaceToJpeg();
			//$detector->cropFaceToJpeg2();

			//print_r($content);

		} else {

			$img = new APP_SimpleImage;

			$img->loadfromstring($content);

			$wd = $img->getWidth();
			$ht = $img->getHeight();

			if($wd>$ht) {
				$img->resizeToHeight($size);
			} else {
				$img->resizeToWidth($size);
			}

			$img->crop($size);

			//print_r($content);

			//pre($imagefile);

			@$img->output(IMAGETYPE_JPEG, $imagefile);

			$img->output();

		}


	}

	die();

	//pre($result);

}
