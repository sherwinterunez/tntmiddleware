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

/*
$ftp = new MyFTP('tntaccess.sendsolutionsph.com');
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
  $fileerror = array();

  if(!empty($info['source']['folder'])&&is_array($info['source']['folder'])) {
    foreach($info['source']['folder'] as $folder) {
      $dir = scandir($folder,SCANDIR_SORT_ASCENDING);

      //pre(array('$dir'=>$dir));
      if(!empty($dir)&&is_array($dir)) {
        foreach($dir as $k=>$v) {
          if(preg_match('/'.$regx.'/si',$v)) {
						$v = trim($v);
            //pre(array('$v'=>$v));
            $filetime = filemtime($folder.$v);
            $valid[] = array('file'=>$v,'pathfile'=>$folder.$v,'folder'=>$folder,'filesize'=>filesize($folder.$v),'filetime'=>$filetime,'elapsed'=>time()-$filetime);
          }
        }
      }

    }
  }

  pre(array('$valid'=>$valid));

  if(!empty($valid)&&!empty($info['target']['folder'])&&is_array($info['target']['folder'])) {
    foreach($info['target']['folder'] as $folder) {
      foreach($valid as $vi=>$vf) {
        $cpstr = 'copy '.$vf['pathfile'].' -> '.$folder.$vf['file'];

        if(!empty($info['processdelay'])&&is_numeric($info['processdelay'])&&intval($info['processdelay'])>0) {
          $processdelay = intval($info['processdelay']);
          $elapsed = intval($vf['elapsed']);

          if($elapsed>$processdelay) {
          } else {
            continue;
          }
        }

        if(file_exists($folder.$vf['file'])&&filesize($folder.$vf['file'])==$vf['filesize']) {
          print_r(array('file already exists!'=>$folder.$vf['file']));
          continue;
        }

        if(!copy($vf['pathfile'],$folder.$vf['file'])) {
          $fileerror[$vi] = true;
          print_r(array('copy error: '.$cpstr));
        } else {
					$content = array();
					$content['midlog_sourcefile'] = $vf['file'];
					$content['midlog_sourcepathfile'] = $vf['pathfile'];
					$content['midlog_targetfile'] = $vf['file'];
					$content['midlog_targetpathfile'] = $folder.$vf['file'];
					$content['midlog_type'] = TYPE_FILECOPY;
					$content['midlog_status'] = TRN_COPIED;

					if(!($result = $appdb->insert('tbl_midlog',$content,'midlog_id'))) {
						//atLog('$appdb->lasterror ('.$appdb->lasterror.')','retrievesms',$dev,$mobileNo,$ip,logdt());
						print_r(array('$appdb->lasterror'=>$appdb->lasterror));
					}
				}
      }
    }
  }

  if(!empty($valid)&&!empty($info['target']['ftp']['folder'])&&is_array($info['target']['ftp']['folder'])
    &&!empty($info['target']['ftp']['connection']['host'])
    &&!empty($info['target']['ftp']['connection']['user'])
    &&!empty($info['target']['ftp']['connection']['password'])) {

    //$ftp = new MyFTP($info['target']['ftp']['connection']['host']);
    //$ftp->ftp_login($info['target']['ftp']['connection']['user'],$info['target']['ftp']['connection']['password']);

    //$list = $ftp->ftp_nlist('/PH/Temp/');

    //pre(array('$list'=>$list));

    $host = $info['target']['ftp']['connection']['host'];
    $user = $info['target']['ftp']['connection']['user'];
    $pass = $info['target']['ftp']['connection']['password'];

    foreach($info['target']['ftp']['folder'] as $folder) {
      foreach($valid as $vi=>$vf) {

        if(!empty($fileerror[$vi])) continue;

        if(!empty($info['processdelay'])&&is_numeric($info['processdelay'])&&intval($info['processdelay'])>0) {
          $processdelay = intval($info['processdelay']);
          $elapsed = intval($vf['elapsed']);

          if($elapsed>$processdelay) {
          } else {
            continue;
          }
        }

        $remote_file = $folder.$vf['file'];
        $local_file = $vf['pathfile'];

        $ftpstr = 'upload '.$local_file.' -> '.$remote_file;

        pre(array('$ftpstr'=>$ftpstr));

//----------

				$ftp = new MyFTP($host);

        if(!$ftp->ftp_login($user,$pass)) {
          $fileerror[$vi] = true;
          //$ftp->ftp_close();
          //die('ftp login error');
          print_r(array('ftp login error'));
        }

        if(!empty($fileerror[$vi])) continue;

				$ret = $ftp->ftplist($remote_file,3);

				$remotesize = 0;

				if(!empty($ret[0]['size'])) {
					$remotesize = $ret[0]['size'];
				}

        //$remotesize = $ftp->ftp_size($remote_file);

        $localsize = filesize($local_file);

        pre(array('$remotesize'=>$remotesize,'$localsize'=>$localsize));

        $ftp->ftp_close();

				pre(array('bypass'=>$local_file));

//----------

				if($localsize!=$remotesize) {
					$ftp = new MyFTP($host);

	        if(!$ftp->ftp_login($user,$pass)) {
	          $fileerror[$vi] = true;
	          //$ftp->ftp_close();
	          //die('ftp login error');
	          print_r(array('ftp login error'));
	        }

	        if(!$ftp->ftp_put($remote_file,$local_file,FTP_ASCII)) {
	          $fileerror[$vi] = true;
	          //$ftp->ftp_close();
	          //die('ftp upload error');
	          print_r(array('ftp upload error'));
	        }

	        $ftp->ftp_close();

					$ftp = new MyFTP($host);

	        if(!$ftp->ftp_login($user,$pass)) {
	          $fileerror[$vi] = true;
	          //$ftp->ftp_close();
	          //die('ftp login error');
	          print_r(array('ftp login error'));
	        }

	        if(!empty($fileerror[$vi])) continue;

					$ret = $ftp->ftplist($remote_file,3);

					$remotesize = 0;

					if(!empty($ret[0]['size'])) {
						$remotesize = $ret[0]['size'];
					}

	        //$remotesize = $ftp->ftp_size($remote_file);

	        $localsize = filesize($local_file);

	        pre(array('$remotesize'=>$remotesize,'$localsize'=>$localsize));

	        $ftp->ftp_close();

					if($remotesize==$localsize) {
						$content = array();
						$content['midlog_sourcefile'] = $vf['file'];
						$content['midlog_sourcepathfile'] = $vf['pathfile'];
						$content['midlog_targetfile'] = $vf['file'];
						$content['midlog_targetpathfile'] = $folder.$vf['file'];
						$content['midlog_type'] = TYPE_FILEUPLOAD;
						$content['midlog_status'] = TRN_UPLOADED;
						$content['midlog_targethost'] = $host;

						if(!($result = $appdb->insert('tbl_midlog',$content,'midlog_id'))) {
							//atLog('$appdb->lasterror ('.$appdb->lasterror.')','retrievesms',$dev,$mobileNo,$ip,logdt());
							print_r(array('$appdb->lasterror'=>$appdb->lasterror));
						}
					}

				} else {
					pre(array('already exists!'=>$remote_file));
				}
      }
    }

    //$ftp->ftp_close();

  }

  if(!empty($valid)&&!empty($info['target']['ftp'][0]['folder'])&&is_array($info['target']['ftp'][0]['folder'])) {
    foreach($info['target']['ftp'] as $ftpinfo) {
      if(!empty($ftpinfo['connection']['host'])
        &&!empty($ftpinfo['connection']['user'])
        &&!empty($ftpinfo['connection']['password'])) {

        $host = $ftpinfo['connection']['host'];
        $user = $ftpinfo['connection']['user'];
        $pass = $ftpinfo['connection']['password'];

        foreach($ftpinfo['folder'] as $folder) {
          foreach($valid as $vi=>$vf) {

            if(!empty($fileerror[$vi])) continue;

            if(!empty($info['processdelay'])&&is_numeric($info['processdelay'])&&intval($info['processdelay'])>0) {
              $processdelay = intval($info['processdelay']);
              $elapsed = intval($vf['elapsed']);

              if($elapsed>$processdelay) {
              } else {
                continue;
              }
            }

            $remote_file = $folder.$vf['file'];
            $local_file = $vf['pathfile'];

            $ftpstr = 'upload '.$local_file.' -> '.$remote_file;

            pre(array('$ftpstr'=>$ftpstr));

//----------

		        $ftp = new MyFTP($host);

		        if(!$ftp->ftp_login($user,$pass)) {
		          $fileerror[$vi] = true;
		          //$ftp->ftp_close();
		          //die('ftp login error');
		          print_r(array('ftp login error'));
		        }

		        if(!empty($fileerror[$vi])) continue;

		        $ret = $ftp->ftplist($remote_file,3);

		        $remotesize = 0;

		        if(!empty($ret[0]['size'])) {
		          $remotesize = $ret[0]['size'];
		        }

		        //$remotesize = $ftp->ftp_size($remote_file);

		        $localsize = filesize($local_file);

		        pre(array('$remotesize'=>$remotesize,'$localsize'=>$localsize));

		        $ftp->ftp_close();

		        pre(array('bypass'=>$local_file));

//----------

		        if($localsize!=$remotesize) {
		          $ftp = new MyFTP($host);

		          if(!$ftp->ftp_login($user,$pass)) {
		            $fileerror[$vi] = true;
		            //$ftp->ftp_close();
		            //die('ftp login error');
		            print_r(array('ftp login error'));
		          }

		          if(!$ftp->ftp_put($remote_file,$local_file,FTP_ASCII)) {
		            $fileerror[$vi] = true;
		            //$ftp->ftp_close();
		            //die('ftp upload error');
		            print_r(array('ftp upload error'));
		          }

		          $ftp->ftp_close();

		          $ftp = new MyFTP($host);

		          if(!$ftp->ftp_login($user,$pass)) {
		            $fileerror[$vi] = true;
		            //$ftp->ftp_close();
		            //die('ftp login error');
		            print_r(array('ftp login error'));
		          }

		          if(!empty($fileerror[$vi])) continue;

		          $ret = $ftp->ftplist($remote_file,3);

		          $remotesize = 0;

		          if(!empty($ret[0]['size'])) {
		            $remotesize = $ret[0]['size'];
		          }

		          //$remotesize = $ftp->ftp_size($remote_file);

		          $localsize = filesize($local_file);

		          pre(array('$remotesize'=>$remotesize,'$localsize'=>$localsize));

		          $ftp->ftp_close();

		          if($remotesize==$localsize) {
		            $content = array();
		            $content['midlog_sourcefile'] = $vf['file'];
		            $content['midlog_sourcepathfile'] = $vf['pathfile'];
		            $content['midlog_targetfile'] = $vf['file'];
		            $content['midlog_targetpathfile'] = $folder.$vf['file'];
		            $content['midlog_type'] = TYPE_FILEUPLOAD;
		            $content['midlog_status'] = TRN_UPLOADED;
								$content['midlog_targethost'] = $host;

		            if(!($result = $appdb->insert('tbl_midlog',$content,'midlog_id'))) {
		              //atLog('$appdb->lasterror ('.$appdb->lasterror.')','retrievesms',$dev,$mobileNo,$ip,logdt());
		              print_r(array('$appdb->lasterror'=>$appdb->lasterror));
		            }
		          }

		        } else {
		          pre(array('already exists!'=>$remote_file));
		        }

//----------

          }
        }

      }
    }
  }

  if(!empty($valid)&&!empty($info['script'])&&is_array($info['script'])) {
    foreach($info['script'] as $exec) {
      foreach($valid as $vi=>$vf) {

        if(!empty($info['processdelay'])&&is_numeric($info['processdelay'])&&intval($info['processdelay'])>0) {
          $processdelay = intval($info['processdelay']);
          $elapsed = intval($vf['elapsed']);

          if($elapsed>$processdelay) {
          } else {
            continue;
          }
        }

        $descr = array(
            0 => array('pipe','r'),
            1 => array('pipe','w'),
            2 => array('pipe','w')
        );

        $pipes = array();

        //$process = proc_open("top -b -n 5", $descr, $pipes);

				$witherror = false;

				if(!empty($fileerror[$vi])) {
					$witherror = true;
				}

        $process = proc_open($exec.' '.$vf['file'].' '.$vf['pathfile'].' '.$vf['folder'].(!empty($witherror)?' error':''), $descr, $pipes);

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
        }

      }
    }
  }

}

//
