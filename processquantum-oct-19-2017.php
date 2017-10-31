<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: October 19, 2017 10:06:49
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

class myFtp {
    public $conn;

    public function __construct($url){
        $this->conn = ftp_connect($url);
        if(!$this->conn) {
          die('cannot connect to ftp.');
        }
    }

    public function __call($func,$a){
        if(strstr($func,'ftp_') !== false && function_exists($func)){
            array_unshift($a,$this->conn);
            return call_user_func_array($func,$a);
        }else{
            // replace with your own error handler.
            die("$func is not a valid FTP function");
        }
    }
}

/*
$ftp = new myFtp('tntaccess.sendsolutionsph.com');
$ftp->ftp_login('tntacc','tnt54321');
$list = $ftp->ftp_nlist('/PH/Temp/');

pre(array('$list'=>$list));

$remote_file = '/PH/Temp/MNL_OSDATA-20171019-050603.MNL';
$local_file = '/tmp/data/MNL_OSDATA-20171019-050603.MNL';

$ret = $ftp->ftp_put($remote_file,$local_file,FTP_BINARY);

pre(array('$ret'=>$ret));

$remotesize = $ftp->ftp_size($remote_file);
$localsize = filesize($local_file);

pre(array('$remotesize'=>$remotesize,'$localsize'=>$localsize));

$ftp->ftp_close();
*/

global $QUANTUM_VALID_FILES;

foreach($QUANTUM_VALID_FILES as $validFile=>$info) {
  print_r(array('$validFile'=>$validFile,'$info'=>$info));

  $regx = $validFile;

  if(!empty($info['regx'])) {
    $regx = $info['regx'];
  }

  $valid = array();

  if(!empty($info['source']['folder'])&&is_array($info['source']['folder'])) {
    foreach($info['source']['folder'] as $folder) {
      $dir = scandir($folder,SCANDIR_SORT_ASCENDING);

      //pre(array('$dir'=>$dir));
      if(!empty($dir)&&is_array($dir)) {
        foreach($dir as $k=>$v) {
          if(preg_match('/'.$regx.'/si',$v)) {
            //pre(array('$v'=>$v));
            $valid[] = array('file'=>$v,'pathfile'=>$folder.$v);
          }
        }
      }

    }
  }

  pre(array('$valid'=>$valid));

  if(!empty($valid)&&!empty($info['target']['folder'])&&is_array($info['target']['folder'])) {
    foreach($info['target']['folder'] as $folder) {
      foreach($valid as $vf) {
        $cpstr = 'copy '.$vf['pathfile'].' -> '.$folder.$vf['file'];
        if(!copy($vf['pathfile'],$folder.$vf['file'])) {
          die('copy error: '.$cpstr."\n");
        }
      }
    }
  }

  if(!empty($valid)&&!empty($info['target']['ftp']['folder'])&&is_array($info['target']['ftp']['folder'])
    &&!empty($info['target']['ftp']['connection']['host'])
    &&!empty($info['target']['ftp']['connection']['user'])
    &&!empty($info['target']['ftp']['connection']['password'])) {

    $ftp = new myFtp($info['target']['ftp']['connection']['host']);
    $ftp->ftp_login($info['target']['ftp']['connection']['user'],$info['target']['ftp']['connection']['password']);

    //$list = $ftp->ftp_nlist('/PH/Temp/');

    //pre(array('$list'=>$list));

    foreach($info['target']['ftp']['folder'] as $folder) {
      foreach($valid as $vf) {

        $remote_file = $folder.$vf['file'];
        $local_file = $vf['pathfile'];

        $ftpstr = 'upload '.$local_file.' -> '.$remote_file;

        pre(array('$ftpstr'=>$ftpstr));

        $ret = $ftp->ftp_put($remote_file,$local_file,FTP_BINARY);

        pre(array('$ret'=>$ret));

        $remotesize = $ftp->ftp_size($remote_file);
        $localsize = filesize($local_file);

        pre(array('$remotesize'=>$remotesize,'$localsize'=>$localsize));

      }
    }

    $ftp->ftp_close();

  }
}

/*
global $QUANTUM_FOLDER;
global $QUANTUM_FOLDER_VALID_FILES;

$dir = scandir($QUANTUM_FOLDER,SCANDIR_SORT_ASCENDING);

//pre(array('$dir'=>$dir));

$valid = array();

if(!empty($dir)&&is_array($dir)) {
  foreach($dir as $k=>$v) {
    foreach($QUANTUM_FOLDER_VALID_FILES as $f) {
      if(preg_match('/'.$f.'/si',$v)) {
        $valid[] = $v;
        break;
      }
    }
  }
}

pre(array('$valid'=>$valid));
*/


// cp MNL_OSDATA* /tmp/data/a/

//
