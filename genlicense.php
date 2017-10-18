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

$privatekey = <<<PRIVATEKEY
-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQC/BDriR0nmbQO1zvHoYbSbO0EEy6WKHnAr/or/+wyWgjxxjp3A
G8NapviEoo+Xr5gNL0g6vPd/xiNIynTLohrWwuUMKvBIIfl1kPUYS4mMpdOwatf8
HoLCx0XZoIEZUa+ycCSUpMVwLDr+3b/9OpovK8wQGsdt7hh4c9YeNL+4KQIDAQAB
AoGACxHdr7td5wKuUlz52/O9mblnXTXIFCcLbSFFQOx1oEtP4WzYa4ewXJKMmHMr
Sykb8LeqweytkzQSW2eQOTTdxTa0DIs4OcLGthgHDrAgQ4qs+5jgkJO0hJM+0ONp
HZixc9JHa/qCtSqZqr6u3vLsq7SPniZ+uq7u1JdRmEhjOAECQQDva/I86L6vCbpk
Inqi68v7m+ZbCxRQtsP/g3Yxilh5gbOiSXqKCY7xiG+FaxFymks6lA62k3Gl27jd
OvVgzjOpAkEAzD49tAKeJE/KW8sEtPRSVCzZ+n1n2mUeNNwgsDXpB7I1A9R6oaFY
LSkZol36WrIy20ptGGG9nowMJinyougwgQJBAMoSMHxJ8A5pxoAXPaxeGWa92PlE
a5wH9XqlaM89NZkv5/3zyFHS6WtfvMg9apdwNEg3iAd+gC/9N5S42zseLikCQQC/
qKyITmiOFJ4FE3cgQ6E6Qjhu2d1p0LfOzL6T/JLnWPBs3qgRInG3NzlJ5zx2fYBB
zg6f8aBZtnv0GCsLei2BAkBAmDPMVVCaDrI2YwScz4wQ816Bx/NMg/LfTcEVKI7l
Jpv4HQP24tjwGZQ6LytVPtyDa98h+HaNey/XxZBQe2FK
-----END RSA PRIVATE KEY-----
PRIVATEKEY;
?>
<?php

if(!empty($_POST['key'])&&!empty($_POST['school'])) {

  $school = trim($_POST['school']);

  $key = trim($_POST['key']);

  $key = str_replace(' ','+',$key);

  $days = 30;

  if(!empty($_POST['days'])&&is_numeric($_POST['days'])&&intval($_POST['days'])>0) {
    $days = intval($_POST['days']);
  } else {
    $_POST['days'] = $days;
  }

  $numstudent = 100;

  if(!empty($_POST['numstudent'])&&is_numeric($_POST['numstudent'])&&intval($_POST['numstudent'])>0) {
    $numstudent = intval($_POST['numstudent']);
  } else {
    $_POST['numstudent'] = $numstudent;
  }

	$server = 'NO';

	if(!empty($_POST['server'])&&$_POST['server']=='YES') {
    $server = 'YES';
  } else {
    $_POST['server'] = $server;
  }

  //pre(array('$key'=>$key));

  $rsa2 = new Crypt_RSA();

  $rsa2->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);

  $rsa2->loadKey($privatekey);

  $decrypted = $rsa2->decrypt(base64_decode($key));

  //print_r(array('$decrypted'=>$decrypted));

  if(!empty($decrypted)) {
    $json = json_decode($decrypted,true);

    if(!empty($json)&&is_array($json)&&!empty($json['mc'])&&is_array($json['mc'])) {
      //pre(array('$json'=>$json));

      $license = array();
      $license['mc'] = $json['mc'];
      $license['sc'] = $school;
      $license['ux'] = intval(getDbUnixDate());
      $license['dt'] = pgDateUnix($license['ux']);
      $license['dd'] = $days;
      $license['ex'] = ($days * 86400) + $license['ux'];
      $license['de'] = pgDateUnix($license['ex']);
      $license['ns'] = $numstudent;
			$license['sr'] = $server;

      $rsa = new Crypt_RSA();

      $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);

      $plaintext = json_encode($license);

      //pre(array('$plaintext'=>$plaintext));

      $rsa->loadKey($privatekey);

      $ciphertext = base64_encode($rsa->encrypt($plaintext));

      $bypass = true;

      if(!empty($ciphertext)) {
        $bypass = false;
        //pre(array('$plaintext'=>$plaintext,'plength'=>strlen($plaintext),'$ciphertext'=>$ciphertext,'clength'=>strlen($ciphertext)));
        //die;
      }
    }
  }

  if(!empty($bypass)) {
    die("An error has occured while generating license information.");
  }

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"/>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<title>Generate License</title>
<script>if (typeof module === 'object') {window.module = module; module = undefined;}</script>
</head>
<body id="body">
  <form method="post">
    <label>Key:</label>
    <textarea name="key" style="display:block;width:1000px;height:100px;"><?php echo !empty($_POST['key']) ? $_POST['key'] : '';  ?></textarea>
    <label>School:</label>
    <textarea name="school" style="display:block;width:1000px;height:30px;"><?php echo !empty($_POST['school']) ? $_POST['school'] : '';  ?></textarea>
    <label>Number of Students:</label>
    <textarea name="numstudent" style="display:block;width:1000px;height:30px;"><?php echo !empty($_POST['numstudent']) ? $_POST['numstudent'] : '';  ?></textarea>
    <label>Number of Days:</label>
    <textarea name="days" style="display:block;width:1000px;height:30px;"><?php echo !empty($_POST['days']) ? $_POST['days'] : '';  ?></textarea>
		<label>Server (YES/NO)?</label>
    <textarea name="server" style="display:block;width:1000px;height:30px;"><?php echo !empty($_POST['server']) ? $_POST['server'] : 'NO';  ?></textarea>
    <label>Generated License:</label>
    <textarea name="license" style="display:block;width:1000px;height:100px;"><?php echo !empty($ciphertext) ? $ciphertext : '';  ?></textarea>
    <input type="submit" name="submit" />
  </form>
<?php
if(!empty($ciphertext)) {
  pre(array('$license'=>$license));
  pre(array('$plaintext'=>$plaintext,'plength'=>strlen($plaintext),'$ciphertext'=>$ciphertext,'clength'=>strlen($ciphertext)));
}
?>
</body>
</html>
