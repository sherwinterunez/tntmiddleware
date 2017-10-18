<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: March 1, 2017
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

$lines = file('tntimport.csv');

//pre(array('$lines'=>$lines));

/*
[$data] => Array
    (
        [0][StudentRFID] => 0001206200
        [1][StudentNo] => 00385
        [2][FirstName] => KIANA SOPHIA
        [3][MiddleName] => R.
        [4][LastName] => MALAKI
        [5][SchoolYear] => 2016-2017
        [6][Birthdate] => 1992-10-28 00:00:00
        [7][YearLevelID] => 12
        [8][SectionID] => 12
        [9][GuardianName] =>
        [10][GuardianPhoneNumber] => 09153079388
        [11][EmailAddress] =>
        [12][IsActive] => 1
        [13][photoname] =>
        [14][SectionName] => A
        [15][SectionDesc] =>
        [16][StartTime] => 1900-01-01 07:00:00
        [17][EndTime] => 1900-01-01 17:00:00
        [18][YearLevelName] => Grade 10

    )

    sherwint_tntmobile=# \d tbl_studentprofile
                                                     Table "public.tbl_studentprofile"
                 Column              |           Type           |                              Modifiers
    ---------------------------------+--------------------------+----------------------------------------------------------------------
     studentprofile_id               | bigint                   | not null default nextval(('tbl_studentprofile_seq'::text)::regclass)
     studentprofile_number           | text                     | not null default ''::text
     studentprofile_rfid             | text                     | not null default ''::text
     studentprofile_firstname        | text                     | not null default ''::text
     studentprofile_lastname         | text                     | not null default ''::text
     studentprofile_middlename       | text                     | not null default ''::text
     studentprofile_birthdate        | text                     | not null default ''::text
     studentprofile_yearlevel        | integer                  | not null default 0
     studentprofile_guardianname     | text                     | not null default ''::text
     studentprofile_guardianmobileno | text                     | not null default ''::text
     studentprofile_guardianemail    | text                     | not null default ''::text
     studentprofile_section          | integer                  | not null default 0
     studentprofile_active           | integer                  | not null default 0
     studentprofile_deleted          | integer                  | not null default 0
     studentprofile_flag             | integer                  | not null default 0
     studentprofile_updatestamp      | timestamp with time zone | default now()
     studentprofile_createstamp      | timestamp with time zone | default now()
     studentprofile_db               | integer                  | not null default 0
     studentprofile_in               | integer                  | not null default 0
     studentprofile_out              | integer                  | not null default 0
     studentprofile_late             | integer                  | not null default 0
     studentprofile_sync             | integer                  | not null default 0
     studentprofile_update           | integer                  | not null default 0
     studentprofile_schoolyear       | text                     | not null default ''::text
     studentprofile_schoolyearstart  | integer                  | not null default 0
     studentprofile_schoolyearend    | integer                  | not null default 0
    Indexes:
        "tbl_studentprofile_primary_key" PRIMARY KEY, btree (studentprofile_id)

    sherwint_tntmobile=#

*/

//$appdb->begin();

$studentprofile_rfid = 1;
$studentprofile_number = 1;

foreach($lines as $k=>$v) {

	$appdb->begin();

  $data = explode(',',$v);

  pre(array('$data'=>$data));

  $content = array();
  $content['studentprofile_number'] = !empty($data[1]) ? trim($data[1]) : '';
  $content['studentprofile_rfid'] = !empty($data[0]) ? trim($data[0]) : '';
  $content['studentprofile_firstname'] = !empty($data[2]) ? trim($data[2]) : '';
  $content['studentprofile_lastname'] = !empty($data[4]) ? trim($data[4]) : '';
  $content['studentprofile_middlename'] = !empty($data[3]) ? trim($data[3]) : '';
  //$content['studentprofile_guardianmobileno'] = !empty($data[10]) ? trim($data[10]) : '';
  $content['studentprofile_active'] = !empty($data[12]) ? trim($data[12]) : '1';

	if(trim($content['studentprofile_rfid'])!='') {
	} else {
		$content['studentprofile_rfid'] = $studentprofile_rfid;
		$studentprofile_rfid++;
	}

	if(trim($content['studentprofile_number'])!='') {
	} else {
		$content['studentprofile_number'] = $studentprofile_number;
		$studentprofile_number++;
	}

	if(trim($content['studentprofile_firstname'])!='') {
	} else {
		continue;
	}

	if(!empty($data[10])) {
		$data[10] = numberonly($data[10]);

		if(!empty($data[10])&&($res=parseMobileNo('0'.$data[10]))) {
			$content['studentprofile_guardianmobileno'] = '0'.$res[2].$res[3];
		}
	}

	$content['studentprofile_schoolyear'] = getCurrentSchoolYear();

	$sy = explode('-',$content['studentprofile_schoolyear']);

	$content['studentprofile_schoolyearstart'] = $sy[0];
	$content['studentprofile_schoolyearend'] = $sy[1];

  $email = sha1(json_encode($content).microtime()).'@yahoo.com';

  $guardian = array();

  if(!empty($content['studentprofile_firstname'])) {
    $guardian[] = $content['studentprofile_firstname'];
  }

  if(!empty($content['studentprofile_middlename'])) {
    $guardian[] = $content['studentprofile_middlename'];
  }

  if(!empty($content['studentprofile_lastname'])) {
    $guardian[] = $content['studentprofile_lastname'];
  }

  $guardianname = implode(' ',$guardian);

  $content['studentprofile_guardianname'] = !empty($data[9]) ? trim($data[9]) : $guardianname;

  $content['studentprofile_guardianemail'] = !empty($data[11])&&trim($data[11])!='' ? trim($data[11]) : $email;

  if(!empty($data[6])&&preg_match('/(\d+)\-(\d+)\-(\d+)\s+.+?/si',trim($data[6]),$matches)) {
    //pre(array('$matches'=>$matches));
    $content['studentprofile_birthdate'] = $matches[2].'-'.$matches[3].'-'.$matches[1];
  } else
	if(!empty($data[6])&&preg_match('/(\d+)\/(\d+)\/(\d+)/si',trim($data[6]),$matches)) {
		$yr = intval($matches[3]);
		if($yr<100) {
			$matches[3] = $yr + 2000;
		}
		$content['studentprofile_birthdate'] = $matches[1].'-'.$matches[2].'-'.$matches[3];
	}

  if(!empty($data[18])) {

		$data[18] = strtoupper(trim($data[18]));

    $studentprofile_yearlevel = getGroupRefId($data[18]);

    if(!empty($studentprofile_yearlevel)) {
      $content['studentprofile_yearlevel'] = $studentprofile_yearlevel;
    } else {
      $studentprofile_yearlevel = insertYearLevel($data[18]);
      if(!empty($studentprofile_yearlevel)) {
        $content['studentprofile_yearlevel'] = $studentprofile_yearlevel;
      }
    }
  }

  if(!empty($data[14])&&!empty($studentprofile_yearlevel)) {
    //$studentprofile_section = getGroupRefId(trim($data[14]));

		$data[14] = strtoupper(trim($data[14]));

		$studentprofile_section = getSectionId($data[14],$studentprofile_yearlevel);

    if(!empty($studentprofile_section)) {
      $content['studentprofile_section'] = $studentprofile_section;
    } else {
      $starttime = '07:00:00';
      $endtime = '17:00:00';
      if(!empty($data[16])&&preg_match('/.+?\s+(\d+\:\d+\:\d+)/si',$data[16],$matches)) {
        //pre(array('$matches'=>$matches));
        $starttime = $matches[1];
      }
      if(!empty($data[17])&&preg_match('/.+?\s+(\d+\:\d+\:\d+)/si',$data[17],$matches)) {
        //pre(array('$matches'=>$matches));
        $endtime = $matches[1];
      }

      $studentprofile_section = insertSection($data[14],$studentprofile_yearlevel,$starttime,$endtime);

      if(!empty($studentprofile_section)) {
        $content['studentprofile_section'] = $studentprofile_section;
      }
    }
  }

	pre(array('$content'=>$content));

	if(!($result = $appdb->insert("tbl_studentprofile",$content,"studentprofile_id"))) {
		if(preg_match('/duplicate key value violates unique constraint/si',$appdb->lasterror)) {
			$appdb->rollback();
			continue;
		}
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror));
		$appdb->rollback();
		die;
	}

	if(!empty($result['returning'][0]['studentprofile_id'])) {
		//return $result['returning'][0]['studentprofile_id'];

		$studentprofile_id = $result['returning'][0]['studentprofile_id'];

		pre(array('studentprofile_id'=>$result['returning'][0]['studentprofile_id']));
	}

  $filepath = './studentphotos/';

  if(!empty($studentprofile_id)&&!empty($data[13])) {

		$bypass = true;

		if(file_exists($filepath.trim($data[13]))&&($hf=fopen($filepath.trim($data[13]),'r'))) {
			$bypass = false;
			$filepath .= trim($data[13]);
		} else
		if(file_exists($filepath.trim($data[13]).'.jpeg')&&($hf=fopen($filepath.trim($data[13]).'.jpeg','r'))) {
			$bypass = false;
			$filepath .= trim($data[13]).'.jpeg';
		} else
		if(file_exists($filepath.trim($data[13]).'.jpg')&&($hf=fopen($filepath.trim($data[13]).'.jpg','r'))) {
			$bypass = false;
			$filepath .= trim($data[13]).'.jpg';
		} else
		if(file_exists($filepath.trim($data[13]).'.png')&&($hf=fopen($filepath.trim($data[13]).'.png','r'))) {
			$bypass = false;
			$filepath .= trim($data[13]).'.png';
		} else
		if(file_exists($filepath.trim($data[13]).'.JPEG')&&($hf=fopen($filepath.trim($data[13]).'.JPEG','r'))) {
			$bypass = false;
			$filepath .= trim($data[13]).'.JPEG';
		} else
		if(file_exists($filepath.trim($data[13]).'.JPG')&&($hf=fopen($filepath.trim($data[13]).'.JPG','r'))) {
			$bypass = false;
			$filepath .= trim($data[13]).'.JPG';
		} else
		if(file_exists($filepath.trim($data[13]).'.PNG')&&($hf=fopen($filepath.trim($data[13]).'.PNG','r'))) {
			$bypass = false;
			$filepath .= trim($data[13]).'.PNG';
		}

		if(!$bypass) {

	    $content['photoname'] = trim($data[13]);

			$size = filesize($filepath);

	    $fcontent = fread($hf,$size);

	    fclose($hf);
	    //@unlink($filepath);

			$img = new APP_SimpleImage;
			$img->loadfromstring($fcontent);

			pre(array('mimetype'=>$img->mimetype()));

			if(preg_match('/gif|jpeg|png/si',$img->mimetype())) {
			} else {
				pre(array('error'=>'invalid image!'));
				$appdb->rollback();
				continue;
			}

	    $b64content = base64_encode($fcontent);

	    //pre(array('$b64content'=>$b64content)); die;

			if($b64content) {
				$content = array();
				$content['upload_sid'] = sha1($b64content);
				$content['upload_type'] = $img->mimetype();
				$content['upload_studentprofileid'] = $studentprofile_id;
				$content['upload_content'] = $b64content;
				$content['upload_size'] = $size;
				$content['upload_name'] = 'customer_photo';

				//pre(array('$content'=>$content));

				if(!($result = $appdb->insert("tbl_upload",$content,"upload_id"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					$appdb->rollback();
					die;
				}

				//$appdb->commit();

			}

		}

  }

	if(!empty($studentprofile_id)) {
		$appdb->commit();
	} else {
		$appdb->rollback();
	}

}

$appdb->rollback();

//
