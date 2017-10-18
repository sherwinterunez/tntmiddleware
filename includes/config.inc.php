<?php
//--HEADSTART
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Config file
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

/* INCLUDES_START */

//--HEADEND

define('DB_USER', 'sherwint_sherwin');
define('DB_PASS', 'joshua04');
//define('DB_NAME', 'sherwint_sms102');
//define('DB_NAME', 'sherwint_shinagawa');
//define('DB_NAME', 'sherwint_tntattendance');
define('DB_NAME', 'sherwint_tntmobile');
define('DB_IP','127.0.0.1');
define('DB_PORT','5432');
define('DB_HOST', DB_IP.':'.DB_PORT);

define('APP_CODE', 'DEMO');

define('BASE_PATH', '/');

define('APP_NAME','Demo');

define('BACKTRACE', true);

define('MAX_USERACCOUNTS', 0); // 0 = unlimited, 1+ = limit

$toolbars = array();

$forms = array();

$publicKey = <<<PUBLICKEY
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC/BDriR0nmbQO1zvHoYbSbO0EE
y6WKHnAr/or/+wyWgjxxjp3AG8NapviEoo+Xr5gNL0g6vPd/xiNIynTLohrWwuUM
KvBIIfl1kPUYS4mMpdOwatf8HoLCx0XZoIEZUa+ycCSUpMVwLDr+3b/9OpovK8wQ
Gsdt7hh4c9YeNL+4KQIDAQAB
-----END PUBLIC KEY-----
PUBLICKEY;

/* INCLUDES_END */

# eof includes/config/index.php
