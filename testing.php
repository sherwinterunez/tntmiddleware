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

if(!empty(($license=checkLicense()))) {
	pre(array('$license'=>$license));
}

/*$rsa = new Crypt_RSA();

$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);

$keys = $rsa->createKey(1024);

print_r(array('$keys'=>$keys));

if(!empty($keys['privatekey'])&&!empty($keys['publickey'])) {
	$privatekey = $keys['privatekey'];
	$publickey = $keys['publickey'];

  $retval = array();
  //$retval['data1'] = 'the quick brown fox jump over the lazy dog besides the river bank. the quick brown fox jump over the lazy dog besides the river bank. the quick brown fox jump over the lazy dog besides the river bank. the quick brown fox jump over the lazy dog besides the river bank. the quick brown fox jump over the lazy dog besides the river bank. the quick brown fox jump over the lazy dog besides the river bank. the quick brown fox jump over the lazy dog besides the river bank. the quick brown fox jump over the lazy dog besides the river bank.';
  //$retval['data2'] = 'lorem ipsum dolor sit amet. lorem ipsum dolor sit amet. lorem ipsum dolor sit amet. lorem ipsum dolor sit amet. lorem ipsum dolor sit amet. lorem ipsum dolor sit amet. lorem ipsum dolor sit amet. lorem ipsum dolor sit amet. lorem ipsum dolor sit amet. lorem ipsum dolor sit amet. lorem ipsum dolor sit amet. lorem ipsum dolor sit amet. lorem ipsum dolor sit amet. lorem ipsum dolor sit amet. lorem ipsum dolor sit amet. lorem ipsum dolor sit amet. lorem ipsum dolor sit amet. lorem ipsum dolor sit amet.';

	$retval['m1'] = '0c:c4:7a:b3:28:5a';
	$retval['m2'] = 'fe:54:00:52:1f:96';
	$retval['ex'] = '2018/03/30';
	$retval['sc'] = 'SHERWIN MONTESSORI';

  $plaintext = json_encode($retval);

  //$rsa->loadKey($publickey);

  $rsa->loadKey($privatekey);

  $ciphertext = base64_encode($rsa->encrypt($plaintext));

  print_r(array('$plaintext'=>$plaintext,'plength'=>strlen($plaintext),'$ciphertext'=>$ciphertext,'clength'=>strlen($ciphertext)));

  $rsa2 = new Crypt_RSA();

  $rsa2->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);

  $rsa2->loadKey($publickey);

  //$rsa2->loadKey($privatekey);

  $decrypted = $rsa2->decrypt(base64_decode($ciphertext));

  print_r(array('$decrypted'=>$decrypted));

}*/

/*
$studentprofile_schoolyear = explode('-','2017-2018');

pre(array('$studentprofile_schoolyear'=>$studentprofile_schoolyear));


$oneyear = 60 * 60 * 24 * 365;

$dbdate = intval(getDbUnixDate());

$dbyear = date('m/d/Y H:i:s',$dbdate);

$nextyear = $dbdate+$oneyear;

$dbnextyear = date('m/d/Y H:i:s',$nextyear);

pre(array('$dbdate'=>$dbdate,'$dbyear'=>$dbyear,'$nextyear'=>$nextyear,'$dbnextyear'=>$dbnextyear));
*/

$total = getTotalStudentCurrentSchoolYear();

pre(array('$total'=>$total));

//
