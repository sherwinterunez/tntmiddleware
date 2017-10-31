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

if(!empty($_SERVER['argv'])) {

	print_r(array('$_SERVER[argv]'=>$_SERVER['argv']));

  if(!empty($_SERVER['argv'][1])&&is_numeric($_SERVER['argv'][1])&&intval($_SERVER['argv'][1])>0&&!empty($_SERVER['argv'][2])&&is_numeric($_SERVER['argv'][2])&&intval($_SERVER['argv'][2])>0) {

    $conn = $_SERVER['argv'][1];

    $pacsimage_id = $_SERVER['argv'][2];

    print_r(array('$conn'=>$conn,'$pacsimage_id'=>$pacsimage_id));

    if(!($result = $appdb->update("tbl_pacsimage",array('pacsimage_status'=>10,'pacsimage_updatestamp'=>'now()'),"pacsimage_id=".$pacsimage_id))) {
      return false;
    }

    $token = false;

    $res = pacsDoSoapLogin();

  	if(preg_match('/Token="(.+?)"/si',$res,$match)) {
  	  $token = $match[1];
  	}

    if(!empty($token)) {
          if(($ret=pacsDoProcess($conn,$token))) {
            if(!($result = $appdb->update("tbl_pacsimage",array('pacsimage_status'=>$ret,'pacsimage_updatestamp'=>'now()'),"pacsimage_id=".$pacsimage_id))) {
              return false;
            }
						$content = array();
						$content['midlog_targetfile'] = $conn.'.tiff';
						$content['midlog_targetpathfile'] = '/var/log/cache/'.$conn.'.tiff';
						$content['midlog_type'] = TYPE_PACSDOWNLOAD;
						$content['midlog_status'] = TRN_DOWNLOADED;

						if(!($result = $appdb->insert('tbl_midlog',$content,'midlog_id'))) {
							//atLog('$appdb->lasterror ('.$appdb->lasterror.')','retrievesms',$dev,$mobileNo,$ip,logdt());
							print_r(array('$appdb->lasterror'=>$appdb->lasterror));
						}

          } else {
            if(!($result = $appdb->update("tbl_pacsimage",array('pacsimage_status'=>5,'pacsimage_updatestamp'=>'now()'),"pacsimage_id=".$pacsimage_id))) {
              return false;
            }
          }
    } // if(!empty($token)) {

  } // if(!empty($_SERVER['argv'][1])&&is_numeric($_SERVER['argv'][1])) {

}

if(!($result = $appdb->query("select *,(extract(epoch from now()) - extract(epoch from pacsimage_createstamp)) as elapsedtime from tbl_pacsimage where pacsimage_status=0 order by pacsimage_id asc limit 1"))) {
	//echo "\n0 message. processOutbox done.\n";
	return false;
}

//if(!($result = $appdb->query("update tbl_pacsimage set pacsimage_status=0 where pacsimage_id in (select pacsimage_id from (select *,(extract(epoch from now()) - extract(epoch from pacsimage_updatestamp)) as elapsedtime from tbl_pacsimage where pacsimage_status=10 order by pacsimage_id asc) as a where elapsedtime>300)"))) {
  //echo "\n0 message. processOutbox done.\n";
  //return false;
//}

if(!empty($result['rows'][0]['pacsimage_id'])) {
	$ret = array();

	foreach($result['rows'] as $k=>$v) {
		$t = array();
		$t['id'] = $v['pacsimage_id'];
		$t['conn'] = $v['pacsimage_connote'];

		if(!($result = $appdb->update("tbl_pacsimage",array('pacsimage_status'=>20,'pacsimage_updatestamp'=>'now()'),"pacsimage_id=".$v['pacsimage_id']))) {
			return false;
		}

		$ret[] = $t;
	}

	echo json_encode($ret);
}

/*
$limit = 5;

if(!($result = $appdb->query("select *,(extract(epoch from now()) - extract(epoch from pacsimage_createstamp)) as elapsedtime from tbl_pacsimage where pacsimage_status=0 order by pacsimage_id asc limit $limit"))) {
  //echo "\n0 message. processOutbox done.\n";
  return false;
}

if(!empty($result['rows'][0]['pacsimage_id'])) {
  print_r(array('$result'=>$result));

  $pacsdata = $result['rows'];

  $token = false;

  $res = pacsDoSoapLogin();

	if(preg_match('/Token="(.+?)"/si',$res,$match)) {
	  $token = $match[1];
	}

  if(!empty($token)) {
    foreach($pacsdata as $k=>$v) {
      if(!empty($v['pacsimage_connote'])&&is_numeric($v['pacsimage_connote'])) {
        if(($ret=pacsDoProcess($v['pacsimage_connote'],$token))) {
          if(!($result = $appdb->update("tbl_pacsimage",array('pacsimage_status'=>$ret,'pacsimage_updatestamp'=>'now()'),"pacsimage_id=".$v['pacsimage_id']))) {
            return false;
          }
        } else {
          if(!($result = $appdb->update("tbl_pacsimage",array('pacsimage_status'=>5,'pacsimage_updatestamp'=>'now()'),"pacsimage_id=".$v['pacsimage_id']))) {
            return false;
          }
        }
      }
      sleep(1);
    }
  }
}
*/

///
