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

if(!class_exists('APP_app_referral')) {

	class APP_app_referral extends APP_Base_Ajax {
	
		var $desc = 'Referral';

		var $pathid = 'referral';
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

			$appaccess->rules($this->desc,'Referral Module');
			$appaccess->rules($this->desc,'Referral Module New');
			$appaccess->rules($this->desc,'Referral Module Edit');
			$appaccess->rules($this->desc,'Referral Module Delete');

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

		function _form_referralmainpromos($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$params['hello'] = 'Hello, Sherwin!';

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_referralmainpromos

		function _form_referralmaintemplate($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$params['hello'] = 'Hello, Sherwin!';

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_referralmaintemplate

		function _form_referralmainclaimed($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$params['hello'] = 'Hello, Sherwin!';

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_referralmainclaimed

		function _form_referralmainreferral($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$params['hello'] = 'Hello, Sherwin!';

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_referralmainreferral

		function _form_referraldetailpromos($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$readonly = true;

				if(!empty($this->vars['post']['method'])&&$this->vars['post']['method']=='onrowselect') {
					if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {
						if(!($result = $appdb->query("select * from tbl_promos where promos_id=".$this->vars['post']['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['promos_id'])) {
							$params['promosinfo'] = $result['rows'][0];
						}
					}
				} else
				if(!empty($this->vars['post']['method'])&&$this->vars['post']['method']=='referralsettemplate') {

					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Promotion set as referral template!';

					$content = array();
					$content['promos_referral'] = 1;

					if(!empty($this->vars['post']['rowid'])) {
						$retval['rowid'] = $this->vars['post']['rowid'];

						$content['promos_updatestamp'] = 'now()';

						if(!($result = $appdb->update("tbl_promos",array('promos_referral'=>0),"promos_referral=1"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!($result = $appdb->update("tbl_promos",$content,"promos_id=".$this->vars['post']['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						json_encode_return($retval);
						die;
					}
				}

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbDetails'] = array();

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TITLE',
					'name' => 'promos_title',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['promosinfo']['promos_title']) ? $params['promosinfo']['promos_title'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'DESCRIPTION',
					'name' => 'promos_desc',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['promosinfo']['promos_desc']) ? $params['promosinfo']['promos_desc'] : '',
				);

				if($readonly) {

					$params['tbDetails'][] = array(
						'type' => 'input',
						'label' => 'START DATE',
						'name' => 'promos_startdate',
						'readonly' => true,
						'value' => !empty($params['promosinfo']['promos_startdate']) ? $params['promosinfo']['promos_startdate'] : '',
					);

					$params['tbDetails'][] = array(
						'type' => 'input',
						'label' => 'END DATE',
						'name' => 'promos_enddate',
						'readonly' => true,
						'value' => !empty($params['promosinfo']['promos_enddate']) ? $params['promosinfo']['promos_enddate'] : '',
					);

				} else {

					$params['tbDetails'][] = array(
						'type' => 'calendar',
						'label' => 'START DATE',
						'name' => 'promos_startdate',
						'readonly' => true,
						'required' => !$readonly,
						'enableTime' => true,
						'enableTodayButton' => true,
						'calendarPosition' => 'right',
						'dateFormat' => '%m-%d-%Y %H:%i',
						'validate' => "NotEmpty",
						'value' => !empty($params['promosinfo']['promos_startdate']) ? $params['promosinfo']['promos_startdate'] : '',
					);

					$params['tbDetails'][] = array(
						'type' => 'calendar',
						'label' => 'END DATE',
						'name' => 'promos_enddate',
						'readonly' => true,
						'required' => !$readonly,
						'enableTime' => true,
						'calendarPosition' => 'right',
						'dateFormat' => '%m-%d-%Y %H:%i',
						'validate' => "NotEmpty",
						'value' => !empty($params['promosinfo']['promos_enddate']) ? $params['promosinfo']['promos_enddate'] : '',
					);

				}

				$params['tbDetails'][] = array(
					'type' => 'newcolumn',
					'offset' => 20,
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TEXT MESSAGE',
					'name' => 'promos_sms',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['promosinfo']['promos_sms']) ? $params['promosinfo']['promos_sms'] : '',
					'rows' => 5,
					'inputWidth' => 500,
				);

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_referraldetailpromos

		function _form_referraldetailtemplate($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$post = $this->vars['post'];

				$readonly = true;

				$params = array();

				if(!empty($post['method'])&&($post['method']=='referralnew'||$post['method']=='referraledit')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&($post['method']=='onrowselect'||$post['method']=='referraledit')) {
					if(!empty($post['rowid'])&&is_numeric($post['rowid'])&&$post['rowid']>0) {
						if(!($result = $appdb->query("select * from tbl_referral where referral_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['referral_id'])) {
							$params['referralinfo'] = $result['rows'][0];
						}
					}
				} else
				if(!empty($post['method'])&&$post['method']=='referralsettemplate') {

					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Template set as active template!';

					$content = array();
					$content['referral_template'] = 1;

					if(!empty($post['rowid'])) {
						$retval['rowid'] = $post['rowid'];

						$content['referral_updatestamp'] = 'now()';

						if(!($result = $appdb->update("tbl_referral",array('referral_template'=>0),"referral_template=1"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!($result = $appdb->update("tbl_referral",$content,"referral_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						json_encode_return($retval);
						die;
					}
				} else
				if(!empty($post['method'])&&$post['method']=='referralsave') {

					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Template successfully saved!';

					$content = array();
					$content['referral_title'] = !empty($post['referral_title']) ? $post['referral_title'] : '';
					$content['referral_desc'] = !empty($post['referral_desc']) ? $post['referral_desc'] : '';
					$content['referral_sms'] = !empty($post['referral_sms']) ? $post['referral_sms'] : '';

					if(!empty($post['rowid'])) {
						$retval['rowid'] = $post['rowid'];

						$content['referral_updatestamp'] = 'now()';

						if(!($result = $appdb->update("tbl_referral",$content,"referral_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

					} else {
						if(!($result = $appdb->insert("tbl_referral",$content,"referral_id"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['returning'][0]['referral_id'])) {
							$retval['rowid'] = $result['returning'][0]['referral_id'];
						}
					}

					json_encode_return($retval);
					die;

				} else
				if(!empty($post['method'])&&$post['method']=='referraldelete') {

					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Template successfully deleted!';

					if(!empty($post['rowids'])) {

						$rowids = explode(',', $post['rowids']);

						$arowid = array();

						for($i=0;$i<count($rowids);$i++) {
							$rowid = intval(trim($rowids[$i]));
							if(!empty($rowid)) {
								$arowid[] = $rowid;
							}
						}

						//pre(array('$arowid'=>$arowid));

						if(!empty($arowid)) {
							$rowids = implode(',', $arowid);

							if(!($result = $appdb->query("delete from tbl_referral where referral_id in (".$rowids.")"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							json_encode_return($retval);
							die;
						}

					}

					if(!empty($post['rowid'])) {
						if(!($result = $appdb->query("delete from tbl_referral where referral_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
					}

					json_encode_return($retval);
					die;
				}

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbDetails'] = array();

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TITLE',
					'name' => 'referral_title',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => !empty($params['referralinfo']['referral_title']) ? $params['referralinfo']['referral_title'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'DESCRIPTION',
					'name' => 'referral_desc',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => !empty($params['referralinfo']['referral_desc']) ? $params['referralinfo']['referral_desc'] : '',
				);

				//$params['tbDetails'][] = array(
				//	'type' => 'newcolumn',
				//	'offset' => 20,
				//);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TEXT MESSAGE',
					'name' => 'referral_sms',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['referralinfo']['referral_sms']) ? $params['referralinfo']['referral_sms'] : '',
					'rows' => 5,
					'inputWidth' => 500,
				);

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_referraldetailtemplate

		function _form_referraldetailreferral($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				//if(!empty($this->vars['post']['method'])&&($this->vars['post']['method']=='promotionnew'||$this->vars['post']['method']=='promotionedit')) {
				//	$readonly = false;
				//}

				if(!empty($post['method'])&&$post['method']=='onrowselect') {
					if(!empty($post['rowid'])&&is_numeric($post['rowid'])&&$post['rowid']>0) {

						$sql = "select *,case when referralsent_status=0 then 'queued' when referralsent_status=1 then 'waiting' when referralsent_status=3 then 'sending' when referralsent_status=4 then 'sent' when referralsent_status=5 then 'failed' end as referralsent_status from tbl_referralsent where referralsent_id=".$post['rowid'];

						if(!($result = $appdb->query($sql))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['referralsent_id'])) {
							$params['referralsentinfo'] = $result['rows'][0];
						}
					}
				} else
				if(!empty($post['method'])&&$post['method']=='referraldelete') {

					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Referral successfully deleted!';

					if(!empty($post['rowids'])) {

						$rowids = explode(',', $post['rowids']);

						$arowid = array();

						for($i=0;$i<count($rowids);$i++) {
							$rowid = intval(trim($rowids[$i]));
							if(!empty($rowid)) {
								$arowid[] = $rowid;
							}
						}

						//pre(array('$arowid'=>$arowid));

						if(!empty($arowid)) {
							$rowids = implode(',', $arowid);

							if(!($result = $appdb->query("delete from tbl_referralsent where referralsent_id in (".$rowids.")"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							json_encode_return($retval);
							die;
						}

					}

					if(!empty($post['rowid'])) {
						if(!($result = $appdb->query("delete from tbl_referralsent where referralsent_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
					}

					json_encode_return($retval);
					die;
				}
				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbDetails'] = array();

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'CONTACT',
					'name' => 'referralsent_contactnumber',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['referralsentinfo']['referralsent_contactnumber']) ? $params['referralsentinfo']['referralsent_contactnumber'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'REFERRED BY',
					'name' => 'referralsent_referredby',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['referralsentinfo']['referralsent_referredby']) ? $params['referralsentinfo']['referralsent_referredby'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'PROMO CODE',
					'name' => 'referralsent_referralcode',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['referralsentinfo']['referralsent_referralcode']) ? $params['referralsentinfo']['referralsent_referralcode'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'STATUS',
					'name' => 'referralsent_status',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['referralsentinfo']['referralsent_status']) ? strtoupper($params['referralsentinfo']['referralsent_status']) : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'newcolumn',
					'offset' => 20,
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TITLE',
					'name' => 'referralsent_title',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => !empty($params['referralsentinfo']['referralsent_title']) ? $params['referralsentinfo']['referralsent_title'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'DESCRIPTION',
					'name' => 'referralsent_desc',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => !empty($params['referralsentinfo']['referralsent_desc']) ? $params['referralsentinfo']['referralsent_desc'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TEXT MESSAGE',
					'name' => 'referralsent_sms',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['referralsentinfo']['referralsent_sms']) ? $params['referralsentinfo']['referralsent_sms'] : '',
					'rows' => 3,
					'inputWidth' => 500,
				);

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_referraldetailreferral

		function _form_referraldetailclaimed($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				//if(!empty($this->vars['post']['method'])&&($this->vars['post']['method']=='promotionnew'||$this->vars['post']['method']=='promotionedit')) {
				//	$readonly = false;
				//}

				if(!empty($post['method'])&&($post['method']=='onrowselect'||$post['method']=='promotionedit')) {
					if(!empty($post['rowid'])&&is_numeric($post['rowid'])&&$post['rowid']>0) {
						if(!($result = $appdb->query("select *,case when referralsent_status=0 then 'queued' when referralsent_status=1 then 'waiting' when referralsent_status=3 then 'sending' when referralsent_status=4 then 'sent' when referralsent_status=5 then 'failed' end as referralsent_status from tbl_referralsent where referralsent_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['referralsent_id'])) {
							$params['claimedinfo'] = $result['rows'][0];
						}
					}
				}

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbDetails'] = array();

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'CONTACT',
					'name' => 'referralsent_contactnumber',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['claimedinfo']['referralsent_contactnumber']) ? $params['claimedinfo']['referralsent_contactnumber'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'REFERRED BY',
					'name' => 'referralsent_referredby',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['claimedinfo']['referralsent_referredby']) ? $params['claimedinfo']['referralsent_referredby'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'REFERRAL CODE',
					'name' => 'referralsent_referralcode',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['claimedinfo']['referralsent_referralcode']) ? $params['claimedinfo']['referralsent_referralcode'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'STATUS',
					'name' => 'referralsent_status',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => 'CLAIMED',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'CLAIMED DATE',
					'name' => 'referralsent_claimstamp',
					'readonly' => true,
					'value' => !empty($params['claimedinfo']['referralsent_claimstamp']) ? pgDate($params['claimedinfo']['referralsent_claimstamp']) : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'newcolumn',
					'offset' => 20,
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TITLE',
					'name' => 'referralsent_title',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['claimedinfo']['referralsent_title']) ? $params['claimedinfo']['referralsent_title'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'DESCRIPTION',
					'name' => 'referralsent_desc',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => !empty($params['claimedinfo']['referralsent_desc']) ? $params['claimedinfo']['referralsent_desc'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TEXT MESSAGE',
					'name' => 'referralsent_sms',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['claimedinfo']['referralsent_sms']) ? $params['claimedinfo']['referralsent_sms'] : '',
					'rows' => 5,
					'inputWidth' => 500,
				);

				if(!empty($this->vars['post']['method'])&&$this->vars['post']['method']=='promotiondelete') {

					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Promotion successfully deleted!';

					if(!empty($this->vars['post']['rowids'])) {

						$rowids = explode(',', $this->vars['post']['rowids']);

						$arowid = array();

						for($i=0;$i<count($rowids);$i++) {
							$rowid = intval(trim($rowids[$i]));
							if(!empty($rowid)) {
								$arowid[] = $rowid;
							}
						}

						//pre(array('$arowid'=>$arowid));

						if(!empty($arowid)) {
							$rowids = implode(',', $arowid);

							if(!($result = $appdb->query("delete from tbl_promossent where promossent_id in (".$rowids.")"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							json_encode_return($retval);
							die;
						}

					}

					if(!empty($this->vars['post']['rowid'])) {
						if(!($result = $appdb->query("delete from tbl_promossent where promossent_id=".$this->vars['post']['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
					}

					json_encode_return($retval);
					die;
				}

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_referraldetailclaimed

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

					$form = $this->_form($this->post['routerid'], $this->post['formid']);

					$jsontoolbar = $this->_toolbar($this->post['routerid'], $this->post['formid']);

					$jsonlayout = $this->_layout($this->post['routerid'], $this->post['formid']);

					$jsonxml = $this->_xml($this->post['routerid'], $this->post['formid']);

					if(empty($form)&&empty($jsontoolbar)&&empty($jsonlayout)) return false;

					$formid = $this->post['formid'];

					if(!empty($this->post['tabid'])) {
						$formid = $this->post['tabid'];
					}

					if(!empty($form)) {
						$formval = sha1($this->post['routerid'].$form.$formid);

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

					$form = $this->_form($this->post['routerid'], $this->post['formid']);

					$jsonxml = $this->_xml($this->post['routerid'], $this->post['formid']);

					if(!empty($this->post['formval'])) {
						$form = str_replace('%formval%',$this->post['formval'],$form);
					}

					$retval = array('html'=>$form);

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
					if($this->post['table']=='promos') {
						if(!($result = $appdb->query("select * from tbl_promos order by promos_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['promos_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['promos_id'],'template'=>$v['promos_referral'],'data'=>array(0,$v['promos_id'],$v['promos_title'],$v['promos_desc'],$v['promos_sms'],$v['promos_startdate'],$v['promos_enddate'],pgDate($v['promos_createstamp']),pgDate($v['promos_updatestamp'])));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='template') { // referral_template
						if(!($result = $appdb->query("select * from tbl_referral where referral_template>0"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['referral_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['referral_id'],'template'=>$v['referral_template'],'data'=>array(0,$v['referral_id'],$v['referral_title'],$v['referral_desc'],$v['referral_sms'],pgDate($v['referral_createstamp']),pgDate($v['referral_updatestamp'])));
							}

							$retval = array('rows'=>$rows);
						}


						if(!($result = $appdb->query("select * from tbl_referral where referral_template=0 order by referral_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['referral_id'])) {

							if(!empty($rows)) {
							} else {
								$rows = array();
							}

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['referral_id'],'data'=>array(0,$v['referral_id'],$v['referral_title'],$v['referral_desc'],$v['referral_sms'],pgDate($v['referral_createstamp']),pgDate($v['referral_updatestamp'])));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='referral') {
						if(!($result = $appdb->query("select *,case when referralsent_status=0 then 'queued' when referralsent_status=1 then 'waiting' when referralsent_status=3 then 'sending' when referralsent_status=4 then 'sent' when referralsent_status=5 then 'failed' end as referralsent_status from tbl_referralsent order by referralsent_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['referralsent_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['referralsent_id'],'data'=>array(0,$v['referralsent_id'],$v['referralsent_contactnumber'],$v['referralsent_title'],$v['referralsent_desc'],$v['referralsent_sms'],$v['referralsent_status'],$v['referralsent_referralcode'],pgDate($v['referralsent_sentstamp'])));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='claimed') {
						if(!($result = $appdb->query("select *,case when referralsent_status=0 then 'queued' when referralsent_status=1 then 'waiting' when referralsent_status=3 then 'sending' when referralsent_status=4 then 'sent' when referralsent_status=5 then 'failed' end as referralsent_status from tbl_referralsent where referralsent_claimed>0 order by referralsent_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['referralsent_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['referralsent_id'],'data'=>array(0,$v['referralsent_id'],$v['referralsent_contactnumber'],$v['referralsent_title'],$v['referralsent_desc'],$v['referralsent_sms'],$v['referralsent_referralcode'],pgDate($v['referralsent_claimstamp'])));
							}

							$retval = array('rows'=>$rows);
						}
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
			}

			return false;
		} // router($vars=false,$retflag=false)
		
	}

	$appappreferral = new APP_app_referral;
}

# eof modules/app.user