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

$rsa = new Crypt_RSA();

$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);

if(!empty(($mac = getMacAddress()))) {

} else {
  die("Cannot get hardware ID for license generation. Please contact support.\n");
}

$retval = array();

$retval['ux'] = intval(getDbUnixDate());
$retval['dt'] = pgDateUnix($retval['ux']);

if(!empty($mac)) {
  foreach($mac as $v) {
    $retval['mc'][] = $v;
  }
}

$plaintext = json_encode($retval);

$rsa->loadKey($publicKey);

$ciphertext = base64_encode($rsa->encrypt($plaintext));

//print_r(array('$plaintext'=>$plaintext,'plength'=>strlen($plaintext),'$ciphertext'=>$ciphertext,'clength'=>strlen($ciphertext)));

if(!empty($ciphertext)) {
  echo '<label>Generated Key:</label><br />';
  echo '<textarea style="width:1000px;height:100px;">'.htmlentities($ciphertext).'</textarea>';
} else {
  die("An error has occured while generating key for license generation. Please contact support.\n");
}


//
