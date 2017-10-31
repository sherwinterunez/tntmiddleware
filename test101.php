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

global $appdb;

//$tb = $appdb->isTableExist('tbl_studentprofile');

/*if(!$appdb->isColumnExist('tbl_studentprofile','studentprofile_schoolyear')) {
	$appdb->query("alter table tbl_studentprofile add column studentprofile_schoolyear text DEFAULT ''::text NOT NULL");
}

pre(array('$appdb'=>$appdb));*/

/*$ch = new MyCurl;

$ch->setopt(CURLOPT_ENCODING,"gzip");

$cont = $ch->get('http://tntattendance.dev/app/getoutbox/waiting/10');

$info = $ch->getinfo();

pre(array('$cont'=>$cont,'$info'=>$info));*/

//isServerLicense();


/*$host = '10.1.2.6';
$port = 80;
$waitTimeoutInSeconds = 1;
if($fp = @fsockopen($host,$port,$errCode,$errStr,$waitTimeoutInSeconds)){
   // It worked
	 echo "\nit worked!\n";
} else {
   // It didn't work
	 echo "\nit didn't worked!\n";
}
@fclose($fp);*/

//$host = '10.1.2.5';

/*$host = 'tntattendance.dev';

if(pingDomain($host)>0) {

	$ch = new MyCurl;

	$ch->setopt(CURLOPT_ENCODING,"gzip");

	$cont = $ch->get('http://'.$host.'/app/getoutbox/waiting/10');

	$info = $ch->getinfo();

	//pre(array('$cont'=>$cont,'$info'=>$info));

	pre('success!');

}*/

//$test = 'hello';

//$x = explode('|',$test);

//pre(array('$x'=>$x));

//$r = pingDomain('10.1.2.86');

//pre(array('$r'=>$r));

/*
$file = '/data/PH/Data/phmnlu01.13842.inprogress';

$filesize = filesize($file);

$filemtime = date ("F d Y H:i:s.", filemtime($file));

print_r(array('$file'=>$file,'$filesize'=>$filesize,'$filemtime'=>$filemtime));
*/

//print_r(array('$_SERVER'=>$_SERVER));

/*
if(!empty($_SERVER['argv'])) {

	print_r(array('$_SERVER[argv]'=>$_SERVER['argv']));

	$file = $_SERVER['argv'][1];
	$pathfile = $_SERVER['argv'][2];

	if(!($fh = fopen($pathfile,'r'))) {
		die('error opening file: '.$pathfile);
	}

	$parse = fgets($fh);

	if(!empty($parse)&&substr($parse,3,2)=='WW'){
		print_r(array('OS DATA'));
	} else
	if(!empty($parse)&&substr($parse,0,2)=='01'){
		print_r(array('SECTOR DATA'));
	} else {
		print_r(array('UNKNOWN DATA'));
	}

}
*/

/*
function listDetailed($children) {
		if(is_array($children)) {
				$items = array();

				foreach ($children as $child) {
						$chunks = preg_split("/\s+/", $child);
						list($item['rights'], $item['number'], $item['user'], $item['group'], $item['size'], $item['month'], $item['day'], $item['time']) = $chunks;
						$item['type'] = $chunks[0]{0} === 'd' ? 'directory' : 'file';
						array_splice($chunks, 0, 8);
						$items[implode(" ", $chunks)] = $item;
				}

				return $items;
		}

		// Throw exception or return false < up to you
}

$localIP = getMyLocalIP();

print_r(array('$localIP'=>$localIP));

$ftp = new MyFTP($FTP_HOST);

if(!$ftp->ftp_login($FTP_USER,$FTP_PASS)) {
	print_r(array('ftp login error'));
}

$list = $ftp->ftp_raw('CWD /ftp/TNTX582/PH/Temp/');

print_r(array('$list'=>$list));

$ret = $ftp->ftplist(false,2);

print_r(array('$ret'=>$ret));

//$detailed = listDetailed($ret['array']);

//print_r(array('$ret'=>$ret,'$detailed'=>$detailed));

$ftp->ftp_close();

//-----

$ftp = new MyFTP($FTP_HOST);

if(!$ftp->ftp_login($FTP_USER,$FTP_PASS)) {
	print_r(array('ftp login error'));
}

$list = $ftp->ftp_raw('CWD /ftp/TNTX582/PH/Temp/');

print_r(array('$list'=>$list));

//$ret = $ftp->ftplist('/ftp/TNTX582/PH/Temp/phmnlu01.10124.inprogress',2);
$ret = $ftp->ftplist('phmnlu01.10124.inprogress',3);

print_r(array('$ret'=>$ret));

//$detailed = listDetailed($ret['array']);

//print_r(array('$ret'=>$ret,'$detailed'=>$detailed));

$ftp->ftp_close();
*/

/*
$filename = 'MNL_D02DAT-20171030-200804';
$tmp = split('-',$filename);
$val = array();
$val['filedate']=!empty($tmp[1])?$tmp[1]:'';
$val['filetime']=!empty($tmp[2])?substr($tmp[2],0,6):'';

print_r(array('$filename'=>$filename,'$tmp'=>$tmp,'$val'=>$val));
*/

$pathfile = '/tmp/data/phmnlu01.19058.inprogress';

$dt = filemtime($pathfile);

print_r(array('$dt'=>$dt,'$sdt'=>date('Ymd-His',$dt)));

//
