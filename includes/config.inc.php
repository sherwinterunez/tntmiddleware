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
define('DB_NAME', 'sherwint_tntmiddleware');
define('DB_IP','127.0.0.1');
define('DB_PORT','5432');
define('DB_HOST', DB_IP.':'.DB_PORT);

define('APP_CODE', 'TNTMIDDLEWARE');

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

$PACS_URL = "http://pacs.tntad.fedex.com/MERIDIOSOAP/MeridioSoap.asp";
$PACS_IMAGEURL = 'http://pacs.tntad.fedex.com/TNTCache/retrieve.asp?Token=%TOKEN%&DocId=%DOCID%&VersionId=%VERSIONID%';
$PACS_USER = "P459PXX";  //  username
$PACS_PASS = "ReGQZ8DX"; // password

$FTP_HOST = 'ftp3.tnt.com';
$FTP_USER = 'TNTX582';
$FTP_PASS = '20EY9YER';

//$QUANTUM_FOLDER_SOURCE = "/tmp/data/d/"; //"/data/PH/Data/"; //"/tmp/data/";

$TNTACCESS_OSUPLOAD_SCRIPT = "cd /srv/www/tntaccess/osdata/; php osuploadv2.php";
$TNTACCESS_SECTORUPLOAD_SCRIPT = "cd /srv/www/tntaccess/sectordata/; php sduploadv2.php";

//$TNTACCESS_OSUPLOAD_SCRIPT = "cd /WEBDEV/tntaccess.local/osdata/; php osuploadv2.php";
//$TNTACCESS_SECTORUPLOAD_SCRIPT = "cd /WEBDEV/tntaccess.local/sectordata/; php sduploadv2.php";

//$QUANTUM_FOLDER_SOURCE = "/tmp/data/"; //"/data/PH/Data/"; //
//$QUANTUM_FOLDER_TARGET = "/tmp/data/t/";

$QUANTUM_FOLDER_SOURCE = "/data/PH/Data/";
$QUANTUM_FOLDER_TARGET = "/data/PH/Temp/";

$QUANTUM_FOLDER_TARGETA = "/tmp/data/a/";
$QUANTUM_FOLDER_TARGETB = "/tmp/data/b/";
$QUANTUM_FOLDER_TARGETC = "/tmp/data/c/";

$FTP_FOLDER = '/PH/Temp/';

$QUANTUM_VALID_FILES = array(
	'MNL_D02DAT' => array(
		//'regx'=>'MNL\_D02DAT.+?',
		'source'=>array('folder'=>array($QUANTUM_FOLDER_SOURCE)),
		'target'=>array(
			'folder'=>array($QUANTUM_FOLDER_TARGET),
		),
		'script'=>array(
			'php /srv/www/tntmiddleware.dev/processossectordata.php',
			//'php /WEBDEV/tntmiddleware.dev/processossectordata.php'
		),
	),
	'MNL_OSDATA' => array(
		//'regx'=>'MNL\_D02DAT.+?',
		'source'=>array('folder'=>array($QUANTUM_FOLDER_SOURCE)),
		'target'=>array(
			'folder'=>array($QUANTUM_FOLDER_TARGET),
		),
		'script'=>array(
			'php /srv/www/tntmiddleware.dev/processossectordata.php',
			//'php /WEBDEV/tntmiddleware.dev/processossectordata.php'
		),
	),
	/*'MNL_OSDATA' => array(
		//'regx'=>'MNL\_D02DAT.+?',
		'source'=>array('folder'=>array($QUANTUM_FOLDER_SOURCE)),
		'target'=>array(
			'folder'=>array($QUANTUM_FOLDER_TARGET),
			'ftp'=>array(
				array(
					'folder'=>array($FTP_FOLDER),
					'connection'=>array(
						'host'=>$FTP_HOST,
						'user'=>$FTP_USER,
						'password'=>$FTP_PASS
					)
				),
			),
		),
		'script'=>array(
			'php /srv/www/tntmiddleware.dev/processossectordata.php',
			//'php /WEBDEV/tntmiddleware.dev/processossectordata.php'
		),
	),*/
	/*'phmnlu01' => array(
		'source'=>array('folder'=>array($QUANTUM_FOLDER_SOURCE)),
		'target'=>array(
			'folder'=>array($QUANTUM_FOLDER_TARGET),
			'ftp'=>array(
				array(
					'folder'=>array('/ftp/TNTX582/PH/Temp/'),
					'connection'=>array(
						'host'=>$FTP_HOST, //'164.39.122.51',
						'user'=>$FTP_USER,
						'password'=>$FTP_PASS
					),
				),
			),
		),
		'processdelay'=>60*2, // in seconds
		'script'=>array(
			'php /srv/www/tntmiddleware.dev/processossectordata.php',
			//'php /WEBDEV/tntmiddleware.dev/processossectordata.php'
		),
	)*/
);

/* INCLUDES_END */

# eof includes/config/index.php
