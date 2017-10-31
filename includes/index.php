<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Main include file
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

define('INCLUDE_PATH', ABS_PATH . 'includes/');

require_once(INCLUDE_PATH.'config.inc.php');
require_once(INCLUDE_PATH.'constants.inc.php');
require_once(INCLUDE_PATH.'phpseclib/Math/BigInteger.php');
require_once(INCLUDE_PATH.'phpseclib/Crypt/RSA.php');
require_once(INCLUDE_PATH.'array2xml.inc.php');
require_once(INCLUDE_PATH.'framework.inc.php');
require_once(INCLUDE_PATH.'useragents.inc.php');
require_once(INCLUDE_PATH.'isbot.inc.php');
require_once(INCLUDE_PATH.'miscfunctions.inc.php');
require_once(INCLUDE_PATH.'bingimages.inc.php');
require_once(INCLUDE_PATH.'retrieveimages.inc.php');
require_once(INCLUDE_PATH.'geo.cities.inc.php');
require_once(INCLUDE_PATH.'geo.countries.inc.php');
require_once(INCLUDE_PATH.'language.inc.php');
//require_once(INCLUDE_PATH.'session/index.php');
require_once(INCLUDE_PATH.'session.pgsql.inc.php');
require_once(INCLUDE_PATH.'functions.inc.php');
require_once(INCLUDE_PATH.'fpdf.inc.php');
require_once(INCLUDE_PATH.'errors.inc.php');
require_once(INCLUDE_PATH.'error.inc.php');
require_once(INCLUDE_PATH.'db.inc.php');
require_once(INCLUDE_PATH.'hooks.inc.php');
require_once(INCLUDE_PATH.'router.inc.php');
require_once(INCLUDE_PATH.'template.inc.php');
require_once(INCLUDE_PATH.'form.inc.php');
require_once(INCLUDE_PATH.'defaults.inc.php');
require_once(INCLUDE_PATH.'access.inc.php');
require_once(INCLUDE_PATH.'filecache5.inc.php');
require_once(INCLUDE_PATH.'curl.inc.php');
require_once(INCLUDE_PATH.'ftp.inc.php');
require_once(INCLUDE_PATH.'simpleimage.inc.php');
require_once(INCLUDE_PATH.'pdf2img.inc.php');
require_once(INCLUDE_PATH.'download.inc.php');
require_once(INCLUDE_PATH.'pdu.inc.php');
require_once(INCLUDE_PATH.'pdufactory.inc.php');
require_once(INCLUDE_PATH.'utf8.inc.php');
require_once(INCLUDE_PATH.'at.inc.php');
require_once(INCLUDE_PATH.'sms.inc.php');
require_once(INCLUDE_PATH.'facedetector.inc.php');
require_once(INCLUDE_PATH.'userfuncs.inc.php');
require_once(INCLUDE_PATH.'usermodules.inc.php');
require_once(INCLUDE_PATH.'userdbupdates.inc.php');

#eof ./includes/index.php
