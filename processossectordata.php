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

global $appdb;

function processOS($pathfile=false,$file=false) {
	global $appdb, $TNTACCESS_OSUPLOAD_SCRIPT;

  if(!empty($pathfile)&&!empty($file)) {
  } else {
    return false;
  }

  if(!($fh = fopen($pathfile,'r'))) {
		@fclose($fh);
		die('error opening file: '.$pathfile);
	}

	$path = str_replace($file,'',$pathfile);

  $connotes = array();

  while(!feof($fh)){

    if(!empty($parse=fgets($fh))) {
      $connote = substr($parse,5,15);
      $connotes[] = $connote;
    }

  }

	@fclose($fh);

  if(!empty($connotes)) {
    print_r(array('$connotes'=>$connotes));

		foreach($connotes as $k=>$v) {

			$content = array();
			$content['pacsimage_connote'] = $v;
			$content['pacsimage_osdatafile'] = $file;
			$content['pacsimage_osdatapathfile'] = $pathfile;

			if(!($result = $appdb->insert('tbl_pacsimage',$content,'pacsimage_id'))) {
				//atLog('$appdb->lasterror ('.$appdb->lasterror.')','retrievesms',$dev,$mobileNo,$ip,logdt());
				print_r(array('$appdb->lasterror'=>$appdb->lasterror));
			}
		}

		$exec = $TNTACCESS_OSUPLOAD_SCRIPT;

		$descr = array(
				0 => array('pipe','r'),
				1 => array('pipe','w'),
				2 => array('pipe','w')
		);

		$pipes = array();

		//$process = proc_open("top -b -n 5", $descr, $pipes);

		$process = proc_open($exec.' '.$pathfile.' '.$file.' '.$path, $descr, $pipes);

		if (is_resource($process)) {
				while ($f = fgets($pipes[1])) {
						//echo "-pipe 1--->";
						echo $f;
				}
				fclose($pipes[1]);
				while ($f = fgets($pipes[2])) {
						//echo "-pipe 2--->";
						echo $f;
				}
				fclose($pipes[2]);
				proc_close($process);

				$content = array();
				$content['midlog_sourcefile'] = $file;
				$content['midlog_sourcepathfile'] = $pathfile;
				//$content['midlog_targetfile'] = $vf['file'];
				//$content['midlog_targetpathfile'] = $folder.$vf['file'];
				$content['midlog_type'] = TYPE_OSDATAIMPORT;
				$content['midlog_status'] = TRN_COMPLETED;
				//$content['midlog_targethost'] = $host;

				if(!($result = $appdb->insert('tbl_midlog',$content,'midlog_id'))) {
					//atLog('$appdb->lasterror ('.$appdb->lasterror.')','retrievesms',$dev,$mobileNo,$ip,logdt());
					print_r(array('$appdb->lasterror'=>$appdb->lasterror));
				}

		}

		//@unlink($pathfile);

    return $connotes;
  }

	//@unlink($pathfile);

  return false;
}

function processSector($pathfile=false,$file=false) {
	global $appdb, $TNTACCESS_SECTORUPLOAD_SCRIPT;

  if(!empty($pathfile)&&!empty($file)) {
  } else {
    return false;
  }

  if(!($fh = fopen($pathfile,'r'))) {
		@fclose($fh);
		die('error opening file: '.$pathfile);
	}

	$path = str_replace($file,'',$pathfile);

  $connotes = array();

  while(!feof($fh)){

    if(!empty($parse=fgets($fh))) {

      switch(substr($parse,0,2)) {
        case "03":
          $connote = substr($parse,2,9);
          $connotes[] = $connote;
      }

    }

  }

	@fclose($fh);

  if(!empty($connotes)) {
    print_r(array('$connotes'=>$connotes));

		foreach($connotes as $k=>$v) {

			$content = array();
			$content['pacsimage_connote'] = $v;
			$content['pacsimage_sectordatafile'] = $file;
			$content['pacsimage_sectordatapathfile'] = $pathfile;

			if(!($result = $appdb->insert('tbl_pacsimage',$content,'pacsimage_id'))) {
				//atLog('$appdb->lasterror ('.$appdb->lasterror.')','retrievesms',$dev,$mobileNo,$ip,logdt());
				print_r(array('$appdb->lasterror'=>$appdb->lasterror));
			}
		}

		$exec = $TNTACCESS_SECTORUPLOAD_SCRIPT;

		$descr = array(
				0 => array('pipe','r'),
				1 => array('pipe','w'),
				2 => array('pipe','w')
		);

		$pipes = array();

		//$process = proc_open("top -b -n 5", $descr, $pipes);

		$process = proc_open($exec.' '.$pathfile.' '.$file.' '.$path, $descr, $pipes);

		if (is_resource($process)) {
				while ($f = fgets($pipes[1])) {
						//echo "-pipe 1--->";
						echo $f;
				}
				fclose($pipes[1]);
				while ($f = fgets($pipes[2])) {
						//echo "-pipe 2--->";
						echo $f;
				}
				fclose($pipes[2]);
				proc_close($process);

				$content = array();
				$content['midlog_sourcefile'] = $file;
				$content['midlog_sourcepathfile'] = $pathfile;
				//$content['midlog_targetfile'] = $vf['file'];
				//$content['midlog_targetpathfile'] = $folder.$vf['file'];
				$content['midlog_type'] = TYPE_SECTORDATAIMPORT;
				$content['midlog_status'] = TRN_COMPLETED;
				//$content['midlog_targethost'] = $host;

				if(!($result = $appdb->insert('tbl_midlog',$content,'midlog_id'))) {
					//atLog('$appdb->lasterror ('.$appdb->lasterror.')','retrievesms',$dev,$mobileNo,$ip,logdt());
					print_r(array('$appdb->lasterror'=>$appdb->lasterror));
				}

		}

		//@unlink($pathfile);

    return $connotes;
  }

	//@unlink($pathfile);

  return false;
}

if(!empty($_SERVER['argv'])) {

	print_r(array('$_SERVER[argv]'=>$_SERVER['argv']));

	$file = $_SERVER['argv'][1];
	$pathfile = $_SERVER['argv'][2];

	//die;

	if(!($fh = fopen($pathfile,'r'))) {
		die('error opening file: '.$pathfile);
	}

	$parse = fgets($fh);

	@fclose($fh);

	if(!empty($parse)&&substr($parse,3,2)=='WW'){
		print_r(array('OS DATA'));
    processOS($pathfile,$file);
	} else
	if(!empty($parse)&&substr($parse,0,2)=='01'){
		print_r(array('SECTOR DATA'));
    processSector($pathfile,$file);
	} else {
		print_r(array('UNKNOWN DATA'));
	}

	//@unlink($pathfile);

}

//
