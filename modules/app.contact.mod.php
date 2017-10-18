<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* App User Module
*
* Date: June 9, 2016
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

if(!class_exists('APP_app_contact')) {

	class APP_app_contact extends APP_Base_Ajax {

		var $desc = 'contact';

		var $pathid = 'contact';
		var $parent = false;

		/*function __construct($mypathid,$myparent) {
			$this->pathid = $mypathid;
			$this->parent = $myparent;
			$this->init();
		}*/

		function __construct() {
			$this->init();
		}

		function __destruct() {
		}

		function init() {
			$this->add_rules();
		}

		function add_rules() {
			global $appaccess;

			$appaccess->rules($this->desc,'Contact Module');
			$appaccess->rules($this->desc,'Contact Module New');
			$appaccess->rules($this->desc,'Contact Module Edit');
			$appaccess->rules($this->desc,'Contact Module Delete');

			//$appaccess->rules($this->desc,'User Account');
			/*$appaccess->rules($this->desc,'User Account New Role');
			$appaccess->rules($this->desc,'User Account Edit Role');
			$appaccess->rules($this->desc,'User Account Delete Role');
			$appaccess->rules($this->desc,'User Account New User');
			$appaccess->rules($this->desc,'User Account Edit User');
			$appaccess->rules($this->desc,'User Account Delete User');
			$appaccess->rules($this->desc,'User Account Manage All');
			$appaccess->rules($this->desc,'User Account Change Role');
			$appaccess->rules($this->desc,'User Account Change User Login');*/
		}

		function _form_contact($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				//pre(array($routerid,$formid));

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				if(!empty($post['method'])&&($post['method']=='contactedit')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&$post['method']=='contactsave') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Contact successfully saved!';
					$retval['post'] = $post;

					//pre(array('$post',$post));

					json_encode_return($retval);
					die;
				}

				$params['hello'] = 'Hello, Sherwin!';

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbContactRecords'] = array();

				/*$params['tbContactRecords'][] = array(
					'type' => 'input',
					'label' => 'TARDINESS GRACE PERIOD (MINUTE)',
					'labelWidth' => 250,
					'name' => 'setting_tardinessgraceperiod',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['settinginfo']['setting_tardinessgraceperiod']) ? $params['settinginfo']['group_tardinessgraceperiod'] : '',
				);*/

				$params['tbContactRecords'][] = array(
					'type' => 'container',
					'name' => 'contact_grid',
					'inputWidth' => 400,
					'inputHeight' => 347,
					'className' => 'contact_grid_'.$post['formval'],
				);

				$templatefile = $this->templatefile($routerid,$formid);

				//pre(array($routerid,$formid,$params,$templatefile));

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}
			}

			return false;

		} // _form_contact

		function _form_contactdetailstudentprofile($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb, $appsession;

			if(!empty($routerid)&&!empty($formid)) {

				//pre(array($routerid,$formid));

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				if(!empty($post['method'])&&($post['method']=='contactedit'||$post['method']=='contactnew')) {
					$readonly = false;
				}

				if($post['method']=='contactnew') {
					$license = checkLicense();

					if(!empty($license)&&!empty($license['ns'])&&intval($license['ns'])>0&&intval($license['ns'])>getTotalStudentCurrentSchoolYear()) {
					} else {
						$retval = array();
						$retval['error_code'] = '345346';
						$retval['error_message'] = 'Invalid license or maximum number of allowed student for this school year has been reached!';

						json_encode_return($retval);
					}
				}

				if(!empty($post['method'])&&($post['method']=='onrowselect'||$post['method']=='contactedit'||$post['method']=='contactrefresh'||$post['method']=='contactcancel')) {
					if(!empty($post['rowid'])&&is_numeric($post['rowid'])&&$post['rowid']>0) {
						if(!($result = $appdb->query("select * from tbl_studentprofile where studentprofile_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						if(!empty($result['rows'][0]['studentprofile_id'])) {
							$params['studentinfo'] = $result['rows'][0];
						}
					}
				} else
				if(!empty($post['method'])&&$post['method']=='contactphotoget') {

					if(!empty($post['_method'])&&$post['_method']=='contactnew'&&empty($_GET['itemId'])) {
						header("Content-Type: image/jpg");
						die();
					}

					/*$retval = array();
					$retval['vars'] = $this->vars;
					$retval['$_SESSION'] = $_SESSION;
					$retval['$_GET'] = $_GET;

					pre($retval);

					json_encode_return($retval);
					die;*/

					if(!empty($post['rowid'])) {
					} else {
						$post['rowid'] = 0;
					}

					if(!empty($_GET['itemId'])) {
						if(!($result = $appdb->query("select * from tbl_upload where upload_id=".$_GET['itemId']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
					} else {
						if(!($result = $appdb->query("select * from tbl_upload where upload_name='".$post['name']."' and upload_studentprofileid=".$post['rowid']." order by upload_id desc limit 1"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
						$pid = $post['rowid'];
					}

					if(!empty($result['rows'][0]['upload_content'])) {
						//$retval['uploadid'] = $result['rows'][0]['upload_id'];
						$content = base64_decode($result['rows'][0]['upload_content']);
					}

					$size = 500;

					$settings_autodetectface = getOption('$SETTINGS_AUTODETECTFACE',false);

					if(!empty($content)) {

						header("Content-Type: image/jpg");

						if($settings_autodetectface) {

							$detector = new FaceDetector;

							$detector->faceDetectString($content);
							//$detector->faceDetect('duterte101.jpg');

							//$detector->cropFaceToJpeg();
							$detector->cropFaceToJpeg2();

							$detector->resize($size,$size);

							if(!empty($pid)) {

								$imagefile = '/var/log/cache/'.$pid.'-'.$size.'.jpg';

								@$detector->output(IMAGETYPE_JPEG, $imagefile);

							}

							$detector->output();

						} else {

							$img = new APP_SimpleImage;

							$img->loadfromstring($content);

							$wd = $img->getWidth();
							$ht = $img->getHeight();

							if($wd>$ht) {
								$img->resizeToHeight($size);
							} else {
								$img->resizeToWidth($size);
							}

							//print_r($content);

							$img->output();

						}

						//print_r($content);

					} else {

						define('TAP_PATH', ABS_PATH . 'templates/default/tap');

						$defaultphoto = TAP_PATH.'/user.jpg';

						if(file_exists($defaultphoto)&&($hf=fopen($defaultphoto,'r'))) {

					    $content = fread($hf,filesize($defaultphoto));

							//pre(array('$defaultphoto'=>$defaultphoto,'$size'=>$size)); die;

							//pre($content); die;

					    fclose($hf);

							header("Content-Type: image/jpg");

							if(!empty($content)) {
								$img = new APP_SimpleImage;

								$img->loadfromstring($content);

								//if(!empty($size)) {
								//		$img->resize($size,$size);
								//}

								$img->output();
							}

							die;

						}
					}

					die();

				} else
				if(!empty($post['method'])&&$post['method']=='contactphotoupload') {

					$filename = $_FILES["file"]["name"];

					$retval = array();
					$retval['state'] = true;
					$retval['itemId'] = $post['itemId'];
					$retval['filename'] = str_replace("'","\\'",$filename);
					$retval['vars'] = $this->vars;
					$retval['$_FILES'] = $_FILES;

					$filepath = $_FILES['file']['tmp_name'];

					if(is_readable($filepath)&&($hf=fopen($filepath,'r'))) {

						$fcontent = fread($hf,filesize($filepath));
						fclose($hf);
						@unlink($filepath);

						$b64content = base64_encode($fcontent);

						if($b64content) {
							$content = array();
							$content['upload_sid'] = $appsession->id();
							$content['upload_type'] = $_FILES['file']['type'];
							$content['upload_temp'] = 1;
							$content['upload_content'] = $b64content;
							$content['upload_size'] = $_FILES['file']['size'];
							$content['upload_name'] = $post['itemId'];
							//$content['upload_customerid'] = $post['rowid'];

							if(!($result = $appdb->query("select * from tbl_upload where upload_studentprofileid=0 and upload_sid='".$content['upload_sid']."' and upload_name='".$post['itemId']."'"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;
							}

							if(!empty($result['rows'][0]['upload_id'])) {

								$retval['uploadid'] = $result['rows'][0]['upload_id'];

								if(!($result = $appdb->update("tbl_upload",$content,"upload_id=".$retval['uploadid']))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;
								}

								if(!in_array($retval['uploadid'], $_SESSION['UPLOADS'])) {
									$_SESSION['UPLOADS'][] = $retval['uploadid'];
								}

							} else {
								if(!($result = $appdb->insert("tbl_upload",$content,"upload_id"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;
								}

								if(!empty($result['returning'][0]['upload_id'])) {
									$retval['uploadid'] = $result['returning'][0]['upload_id'];
									$_SESSION['UPLOADS'][] = $retval['uploadid'];
								}
							}

							$retval['itemValue'] = $retval['uploadid'];
						}
					}



					//json_encode_return($retval);
					header("Content-Type: text/html");
					print_r(json_encode($retval));
					die;

				} else
				if(!empty($post['method'])&&$post['method']=='contactdelete') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Contact successfully deleted!';
					$retval['wid'] = $post['wid'];
					$retval['post'] = $post;

					if(!empty($post['rowid'])) {
						if(!($result = $appdb->query("delete from tbl_studentprofile where studentprofile_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
					}

					json_encode_return($retval);
					die;
				} else
				if(!empty($post['method'])&&$post['method']=='contactsave') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Contact successfully saved!';
					$retval['post'] = $post;

					$license = checkLicense();

					if(!empty($post['rowid'])&&is_numeric($post['rowid'])&&$post['rowid']>0&&!empty($license)&&!empty($license['ns'])&&intval($license['ns'])>0&&intval($license['ns'])>=getTotalStudentCurrentSchoolYear()) {
					} else
					if(!empty($license)&&!empty($license['ns'])&&intval($license['ns'])>0&&intval($license['ns'])>getTotalStudentCurrentSchoolYear()) {
					} else {
						$retval = array();
						$retval['error_code'] = '345346';
						$retval['error_message'] = 'Invalid license or maximum number of allowed student for this school year has been reached!';

						json_encode_return($retval);
					}

					//pre(array('$post',$post));
					$content = array();
					$content['studentprofile_number'] = !empty($post['studentprofile_number']) ? $post['studentprofile_number'] : '';
					$content['studentprofile_rfid'] = !empty($post['studentprofile_rfid']) ? $post['studentprofile_rfid'] : '';
					$content['studentprofile_active'] = !empty($post['studentprofile_active']) ? 1 : 0;
					$content['studentprofile_schoolyear'] = !empty($post['studentprofile_schoolyear']) ? $post['studentprofile_schoolyear'] : '';

					if(!isValidSchoolYear($content['studentprofile_schoolyear'])) {
						$retval = array();
						$retval['error_code'] = 4581;
						$retval['error_message'] = 'Invalid school year!';

						header_json();
						json_encode_return($retval);
						die;
					}

					$studentprofile_schoolyear = explode('-',$content['studentprofile_schoolyear']);

					$content['studentprofile_schoolyearstart'] = !empty($studentprofile_schoolyear[0]) ? $studentprofile_schoolyear[0] : 0;
					$content['studentprofile_schoolyearend'] = !empty($studentprofile_schoolyear[1]) ? $studentprofile_schoolyear[1] : 0;

					$content['studentprofile_firstname'] = !empty($post['studentprofile_firstname']) ? $post['studentprofile_firstname'] : '';
					$content['studentprofile_lastname'] = !empty($post['studentprofile_lastname']) ? $post['studentprofile_lastname'] : '';
					$content['studentprofile_middlename'] = !empty($post['studentprofile_middlename']) ? $post['studentprofile_middlename'] : '';
					$content['studentprofile_birthdate'] = !empty($post['studentprofile_birthdate']) ? $post['studentprofile_birthdate'] : '';
					$content['studentprofile_yearlevel'] = !empty($post['studentprofile_yearlevel']) ? $post['studentprofile_yearlevel'] : 0;
					$content['studentprofile_section'] = !empty($post['studentprofile_section']) ? $post['studentprofile_section'] : 0;

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

					$content['studentprofile_guardianname'] = !empty($post['studentprofile_guardianname']) ? $post['studentprofile_guardianname'] : $guardianname;
					$content['studentprofile_guardianmobileno'] = !empty($post['studentprofile_guardianmobileno']) ? $post['studentprofile_guardianmobileno'] : '';

					$studentprofile_guardianemail = sha1(microtime()).'@yahoo.com';

					if(!empty($post['studentprofile_guardianemail'])&&$post['studentprofile_guardianemail']!='_@_._') {
						$studentprofile_guardianemail = $post['studentprofile_guardianemail'];
					}

					$content['studentprofile_guardianemail'] = $studentprofile_guardianemail;

					if(!empty($post['rowid'])&&is_numeric($post['rowid'])&&$post['rowid']>0) {

						$retval['rowid'] = $post['rowid'];

						$content['studentprofile_updatestamp'] = 'now()';
						$content['studentprofile_update'] = 1;

						if(!($result = $appdb->update("tbl_studentprofile",$content,"studentprofile_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

					} else {

						if(!($result = $appdb->insert("tbl_studentprofile",$content,"studentprofile_id"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						if(!empty($result['returning'][0]['studentprofile_id'])) {
							$retval['rowid'] = $result['returning'][0]['studentprofile_id'];
						}

					}

					if(!empty($retval['rowid'])) {
						if(!empty($_SESSION['UPLOADS'])) {

							if(!($result = $appdb->query("delete from tbl_upload where upload_studentprofileid=".$retval['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;
							}

							$content = array();
							$content['upload_studentprofileid'] = $retval['rowid'];
							$content['upload_temp'] = 0;
							$content['upload_updatestamp'] = 'now()';

							foreach($_SESSION['UPLOADS'] as $uid) {
								if(!($result = $appdb->update("tbl_upload",$content,"upload_id=$uid"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;
								}
							}

							unset($_SESSION['UPLOADS']);
						}
					}

					json_encode_return($retval);
					die;
				}

				$params['hello'] = 'Hello, Sherwin!';

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbStudentProfile'] = array();

				/*$params['tbStudentProfile'][] = array(
					'type' => 'input',
					'label' => 'TARDINESS GRACE PERIOD (MINUTE)',
					'labelWidth' => 250,
					'name' => 'setting_tardinessgraceperiod',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['settinginfo']['setting_tardinessgraceperiod']) ? $params['settinginfo']['group_tardinessgraceperiod'] : '',
				);*/

				$block = array();

				$block[] = array(
					'type' => 'input',
					'label' => 'STUDENT NO.',
					'labelWidth' => 120,
					'inputWidth' => 120,
					'name' => 'studentprofile_number',
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['studentinfo']['studentprofile_number']) ? $params['studentinfo']['studentprofile_number'] : '',
				);

				$block[] = array(
					'type' => 'newcolumn',
					'offset' => 5,
				);

				$block[] = array(
					'type' => 'checkbox',
					'label' => 'IS ACTIVE',
					'labelWidth' => 80,
					'name' => 'studentprofile_active',
					'readonly' => $readonly,
					'checked' => !empty($params['studentinfo']['studentprofile_active']) ? true : false,
					'position' => 'label-right',
				);

				$params['tbStudentProfile'][] = array(
					'type' => 'block',
					'width' => 360,
					'blockOffset' => 0,
					'offsetTop' => 5,
					'list' => $block,
				);

				$params['tbStudentProfile'][] = array(
					'type' => 'input',
					'label' => 'RFID',
					'labelWidth' => 120,
					'name' => 'studentprofile_rfid',
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['studentinfo']['studentprofile_rfid']) ? $params['studentinfo']['studentprofile_rfid'] : '',
				);

				if($post['method']=='contactnew') {
					$params['tbStudentProfile'][] = array(
						'type' => 'input',
						'label' => 'SCHOOL YEAR',
						'labelWidth' => 120,
						'name' => 'studentprofile_schoolyear',
						'readonly' => $readonly,
						'inputMask' => array('mask'=>'2099-2099'),
						//'required' => !$readonly,
						'value' => getCurrentSchoolYear(),
					);
				} else {
					$params['tbStudentProfile'][] = array(
						'type' => 'input',
						'label' => 'SCHOOL YEAR',
						'labelWidth' => 120,
						'name' => 'studentprofile_schoolyear',
						'readonly' => $readonly,
						'inputMask' => array('mask'=>'2099-2099'),
						//'required' => !$readonly,
						'value' => !empty($params['studentinfo']['studentprofile_schoolyear']) ? $params['studentinfo']['studentprofile_schoolyear'] : '',
					);
				}

				$params['tbStudentProfile'][] = array(
					'type' => 'input',
					'label' => 'LAST NAME',
					'labelWidth' => 120,
					'name' => 'studentprofile_lastname',
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['studentinfo']['studentprofile_lastname']) ? $params['studentinfo']['studentprofile_lastname'] : '',
				);

				$params['tbStudentProfile'][] = array(
					'type' => 'input',
					'label' => 'FIRST NAME',
					'labelWidth' => 120,
					'name' => 'studentprofile_firstname',
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['studentinfo']['studentprofile_firstname']) ? $params['studentinfo']['studentprofile_firstname'] : '',
				);

				$params['tbStudentProfile'][] = array(
					'type' => 'input',
					'label' => 'MIDDLE NAME',
					'labelWidth' => 120,
					'name' => 'studentprofile_middlename',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['studentinfo']['studentprofile_middlename']) ? $params['studentinfo']['studentprofile_middlename'] : '',
				);

				$params['tbStudentProfile'][] = array(
					'type' => 'newcolumn',
					'offset' => 20,
				);

				/*$params['tbStudentProfile'][] = array(
					'type' => 'input',
					'label' => 'BIRTH DATE',
					'labelWidth' => 150,
					'name' => 'studentprofile_birthdate',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'inputMask' => array('alias'=>'mm/dd/yyyy','prefix'=>'','autoUnmask'=>true),
					'value' => !empty($params['studentinfo']['studentprofile_birthdate']) ? $params['studentinfo']['studentprofile_birthdate'] : '',
				);*/

				if($readonly) {
					$params['tbStudentProfile'][] = array(
						'type' => 'input',
						'label' => 'BIRTH DATE',
						'labelWidth' => 180,
						'name' => 'studentprofile_birthdate',
						'readonly' => $readonly,
						//'required' => !$readonly,
						//'inputMask' => array('alias'=>'mm/dd/yyyy','prefix'=>'','autoUnmask'=>true),
						'value' => !empty($params['studentinfo']['studentprofile_birthdate']) ? $params['studentinfo']['studentprofile_birthdate'] : '',
					);
				} else {
					$params['tbStudentProfile'][] = array(
						'type' => 'calendar',
						'label' => 'BIRTH DATE',
						'labelWidth' => 180,
						'name' => 'studentprofile_birthdate',
						'readonly' => $readonly,
						'calendarPosition' => 'right',
						'dateFormat' => '%m-%d-%Y',
						'inputMask' => array('alias'=>'mm/dd/yyyy','prefix'=>'','autoUnmask'=>true),
						//'required' => !$readonly,
						'value' => !empty($params['studentinfo']['studentprofile_birthdate']) ? $params['studentinfo']['studentprofile_birthdate'] : '',
					);
				}

				/*$opt = array();

				if(!$readonly) {
					$opt[] = array('text'=>'','value'=>'','selected'=>false);
				}

				$gender = array('MALE','FEMALE');

				foreach($gender as $v) {
					$selected = false;
					if(!empty($params['studentinfo']['studentprofile_gender'])&&$params['studentinfo']['studentprofile_gender']==$v) {
						$selected = true;
					}
					if($readonly) {
						if($selected) {
							$opt[] = array('text'=>$v,'value'=>$v,'selected'=>$selected);
						}
					} else {
						$opt[] = array('text'=>$v,'value'=>$v,'selected'=>$selected);
					}
				}

				$params['tbStudentProfile'][] = array(
					'type' => 'combo',
					'label' => 'GENDER',
					'labelWidth' => 150,
					'name' => 'studentprofile_gender',
					'readonly' => true,
					'inputWidth' => 200,
					//'required' => !$readonly,
					'options' => $opt,
				);*/

				$yearlevel = getGroupRef(2);

				$opt = array();

				if(!$readonly) {
					$opt[] = array('text'=>'','value'=>'','selected'=>false);
				}

				foreach($yearlevel as $v) {
					$selected = false;
					if(!empty($params['studentinfo']['studentprofile_yearlevel'])&&$params['studentinfo']['studentprofile_yearlevel']==$v['groupref_id']) {
						$selected = true;
					}
					if($readonly) {
						if($selected) {
							$opt[] = array('text'=>$v['groupref_name'],'value'=>$v['groupref_id'],'selected'=>$selected);
						}
					} else {
						$opt[] = array('text'=>$v['groupref_name'],'value'=>$v['groupref_id'],'selected'=>$selected);
					}
				}

				$params['tbStudentProfile'][] = array(
					'type' => 'combo',
					'label' => 'YEAR LEVEL',
					'labelWidth' => 180,
					'name' => 'studentprofile_yearlevel',
					'readonly' => true, //$readonly,
					'required' => !$readonly,
					//'value' => !empty($params['studentinfo']['studentprofile_yearlevel']) ? $params['studentinfo']['studentprofile_yearlevel'] : '',
					'options' => $opt,
				);

				$section = getGroupRef(1);

				$opt = array();

				if(!$readonly) {
					$opt[] = array('text'=>'','value'=>'','selected'=>false);
				}

				foreach($section as $v) {
					$selected = false;
					if(!empty($params['studentinfo']['studentprofile_section'])&&$params['studentinfo']['studentprofile_section']==$v['groupref_id']) {
						$selected = true;
					}
					if($readonly) {
						if($selected) {
							$opt[] = array('text'=>getGroupRefName($v['groupref_yearlevel']).' / '.$v['groupref_name'],'value'=>$v['groupref_id'],'selected'=>$selected);
						}
					} else {
						$opt[] = array('text'=>getGroupRefName($v['groupref_yearlevel']).' / '.$v['groupref_name'],'value'=>$v['groupref_id'],'selected'=>$selected);
					}
				}

				$params['tbStudentProfile'][] = array(
					'type' => 'combo',
					'label' => 'SECTION',
					'labelWidth' => 180,
					'name' => 'studentprofile_section',
					'readonly' => true, //$readonly,
					'required' => !$readonly,
					//'value' => !empty($params['studentinfo']['studentprofile_section']) ? $params['studentinfo']['studentprofile_section'] : '',
					'options' => $opt,
				);

				$params['tbStudentProfile'][] = array(
					'type' => 'input',
					'label' => 'GUARDIAN NAME',
					'labelWidth' => 180,
					'name' => 'studentprofile_guardianname',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['studentinfo']['studentprofile_guardianname']) ? $params['studentinfo']['studentprofile_guardianname'] : '',
				);

				$params['tbStudentProfile'][] = array(
					'type' => 'input',
					'label' => 'GUARDIAN MOBILE NO.',
					'labelWidth' => 180,
					'name' => 'studentprofile_guardianmobileno',
					'inputMask' => array('mask'=>'09999999999'),
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['studentinfo']['studentprofile_guardianmobileno']) ? $params['studentinfo']['studentprofile_guardianmobileno'] : '',
				);

				$params['tbStudentProfile'][] = array(
					'type' => 'input',
					'label' => 'GUARDIAN EMAIL ADD.',
					'labelWidth' => 180,
					'name' => 'studentprofile_guardianemail',
					'readonly' => $readonly,
					'inputMask' => array('alias'=>'email','prefix'=>'','autoUnmask'=>true),
					//'required' => !$readonly,
					'value' => !empty($params['studentinfo']['studentprofile_guardianemail']) ? $params['studentinfo']['studentprofile_guardianemail'] : '',
				);

				$params['tbStudentProfile'][] = array(
					'type' => 'newcolumn',
					'offset' => 20,
				);

				$params['tbStudentProfile'][] = array(
					'type' => 'label',
					'label' => 'STUDENT PHOTO (click box to upload)',
					'labelWidth' => 250,
				);

				$imagepost = $post;
				$imagepost['method'] = 'contactphotoget';
				$imagepost['name'] = 'customer_photo';
				$imagepost['_method'] = $post['method'];

				$imagedata = urlencode(base64_encode(gzcompress(json_encode($imagepost),9)));

				$params['tbStudentProfile'][] = array(
					'type' => 'image',
					//'label' => 'Customer Photo',
					//'labelWidth' => 120,
					'name' => 'customer_photo',
					'inputWidth' => 250,
					'inputHeight' => 250,
					'imageWidth' => 250,
					'imageHeight' => 250,
					'disabled' => $readonly,
					'url' => '/app/json/',
					'image_url' => '/app/api/'.$imagedata.'/',
					'routerid' => $post['routerid'],
					'action' => $post['action'],
					'formid' => $post['formid'],
					'module' => $post['module'],
					'method' => 'contactphotoupload',
					'rowid' => !empty($post['rowid']) ? $post['rowid'] : 0,
					'formval' => $post['formval'],
					'wid' => $post['wid'],
				);

				/*$params['tbContactRecords'][] = array(
					'type' => 'container',
					'name' => 'contact_grid',
					'inputWidth' => 400,
					'inputHeight' => 347,
					'className' => 'contact_grid_'.$post['formval'],
				);*/

				$templatefile = $this->templatefile($routerid,$formid);

				//pre(array($routerid,$formid,$params,$templatefile));

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}
			}

			return false;

		} // _form_contactdetailstudentprofile

		function router() {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			$retflag=false;

			header_json();

			if(!empty($this->post['routerid'])&&!empty($this->post['action'])) {

				if( $this->post['action']=='toolbar' && !empty($this->post['toolbarid']) ) {

					if(!empty($toolbar = $this->_toolbar($this->post['routerid'], $this->post['toolbarid']))) {
						$jsonval = json_encode($toolbar,JSON_OBJECT_AS_ARRAY);
						if($retflag===false) {
							die($jsonval);
						} else
						if($retflag==1) {
							return $toolbar;
						} else
						if($retflag==2) {
							return $jsonval;
						}
					}
				} else
				if( $this->post['action']=='form' && !empty($this->post['buttonid']) ) {

					if(!empty($form = $this->_form($this->post['routerid'], $this->post['buttonid']))) {

						$jsontoolbar = $this->_toolbar($this->post['routerid'], $this->post['buttonid']);

						$formid = $this->post['buttonid'];

						if(!empty($this->post['tabid'])) {
							$formid = $this->post['tabid'];
						}

						$formval = sha1($this->post['routerid'].$form.$formid);

						$sform = str_replace('%formval%',$formval,$form);

						$sform = '<div class="srt_cell_cont_tabbar">'.$sform.'</div>';

						$retval = array('html'=>$sform,'formval'=>$formval);

						$_SESSION['FORMS'][$formval] = array('since'=>time(),'formid'=>(!empty($this->post['tabid']) ? $this->post['tabid'] : $this->post['buttonid']),'routerid'=>$this->post['routerid']);

						//$prebuf = prebuf($_SESSION);

						//$retval['html'] .= '<br /><br />' . $prebuf;;

						if(!empty($jsontoolbar)) {
							$retval['toolbar'] = $jsontoolbar;
						}

						$jsonval = json_encode($retval,JSON_OBJECT_AS_ARRAY);

						if($retflag===false) {
							die($jsonval);
						} else
						if($retflag==1) {
							return $form;
						} else
						if($retflag==2) {
							return $jsonval;
						}
					}

				} else
				if( $this->post['action']=='form' && !empty($this->post['formid']) ) {

					$formval = sha1($this->post['routerid'].$this->post['formid'].time());

					$this->vars['post']['formval'] = $this->post['formval'] = $formval;

					$form = $this->_form($this->post['routerid'], $this->post['formid']);

					//pre($this->post); die;

					$jsontoolbar = $this->_toolbar($this->post['routerid'], $this->post['formid']);

					$jsonlayout = $this->_layout($this->post['routerid'], $this->post['formid']);

					$jsonxml = $this->_xml($this->post['routerid'], $this->post['formid']);

					if(empty($form)&&empty($jsontoolbar)&&empty($jsonlayout)) return false;

					$formid = $this->post['formid'];

					if(!empty($this->post['tabid'])) {
						$formid = $this->post['tabid'];
					}

					if(!empty($form)) {
						//$formval = sha1($this->post['routerid'].$form.$formid);

						$sform = str_replace('%formval%',$formval,$form);

						$sform = '<div class="srt_cell_cont_tabbar">'.$sform.'</div>';

						$retval = array('html'=>$sform,'formval'=>$formval);

						$_SESSION['FORMS'][$formval] = array('since'=>time(),'formid'=>(!empty($this->post['tabid']) ? $this->post['tabid'] : $this->post['formid']),'routerid'=>$this->post['routerid']);
					} else {
						$retval = array();
					}

					if(!empty($jsontoolbar)) {
						$retval['toolbar'] = $jsontoolbar;
					}

					if(!empty($jsonxml)) {
						$retval['xml'] = $jsonxml;
					}

					if(!empty($jsonlayout)) {

						$formval = sha1($this->post['routerid'].json_encode($jsonlayout).$formid);

						$_SESSION['FORMS'][$formval] = array('since'=>time(),'formid'=>(!empty($this->post['tabid']) ? $this->post['tabid'] : $this->post['formid']),'routerid'=>$this->post['routerid']);

						$retval['formval'] = $formval;
						$retval['layout'] = $jsonlayout;
					}

					$jsonval = json_encode($retval,JSON_OBJECT_AS_ARRAY);

					if($retflag===false) {
						die($jsonval);
					} else
					if($retflag==1) {
						return $form;
					} else
					if($retflag==2) {
						return $jsonval;
					}
				} else
				if( $this->post['action']=='formonly' && !empty($this->post['formid']) ) {

					//pre(array('post'=>$this->post));

					$toolbar = false;

					if(!empty($this->post['wid'])) {
						if(!empty($toolbar = $this->_toolbar($this->post['routerid'], $this->post['module']))) {
						}
					}

					$form = $this->_form($this->post['routerid'], $this->post['formid']);

					$jsonxml = $this->_xml($this->post['routerid'], $this->post['formid']);

					if(!empty($this->post['formval'])) {
						$form = str_replace('%formval%',$this->post['formval'],$form);
					}

					$retval = array('html'=>$form);

					if(!empty($toolbar)) {
						$retval['toolbar'] = $toolbar;
					}

					if(!empty($jsonxml)) {
						$retval['xml'] = $jsonxml;
					}

					//pre(array('$retval'=>$retval));

					$jsonval = json_encode($retval,JSON_OBJECT_AS_ARRAY);

					if($retflag===false) {
						die($jsonval);
					} else
					if($retflag==1) {
						return $form;
					} else
					if($retflag==2) {
						return $jsonval;
					}
				} else
				if( $this->post['action']=='grid' && !empty($this->post['formid']) && !empty($this->post['table']) ) {

					$retval = array();

					//pre(array($this->post));

					if($this->post['table']=='modemcommands') {
						if(!($result = $appdb->query("select * from tbl_modemcommands order by modemcommands_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['modemcommands_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['modemcommands_id'],'data'=>array(0,$v['modemcommands_id'],$v['modemcommands_name'],$v['modemcommands_desc']));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='contacts') {

						if(!($result = $appdb->query("select * from tbl_studentprofile order by studentprofile_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['studentprofile_id'])) {
							$rows = array();

							$seq = 1;

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['studentprofile_id'],'data'=>array(0,$seq,$v['studentprofile_id'],$v['studentprofile_schoolyear'],$v['studentprofile_number'],$v['studentprofile_rfid'],$v['studentprofile_firstname'],$v['studentprofile_lastname'],$v['studentprofile_middlename'],getGroupRefName($v['studentprofile_yearlevel']),getGroupRefName($v['studentprofile_section']),$v['studentprofile_guardianname'],$v['studentprofile_guardianmobileno'],$v['studentprofile_guardianemail']));
								$seq++;
							}

							$retval = array('rows'=>$rows);
						}

					}

					$jsonval = json_encode($retval,JSON_OBJECT_AS_ARRAY);

					//pre(array('$jsonval'=>$jsonval));

					if($retflag===false) {
						die($jsonval);
					} else
					if($retflag==1) {
						return $form;
					} else
					if($retflag==2) {
						return $jsonval;
					}

				}
			}

			return false;
		} // router($vars=false,$retflag=false)

	}

	$appappcontact = new APP_app_contact;
}

# eof modules/app.user
