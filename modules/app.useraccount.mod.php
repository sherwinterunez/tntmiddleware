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
* Date: November 13, 2015
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

if(!class_exists('APP_app_useraccount')) {

	class APP_app_useraccount extends APP_Base_Ajax {

		var $desc = 'User Account';

		var $pathid = 'useraccount';
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

			//$appaccess->rules($this->desc,'User Account');
			$appaccess->rules($this->desc,'User Account New Role');
			$appaccess->rules($this->desc,'User Account Edit Role');
			$appaccess->rules($this->desc,'User Account Delete Role');
			$appaccess->rules($this->desc,'User Account New User');
			$appaccess->rules($this->desc,'User Account Edit User');
			$appaccess->rules($this->desc,'User Account Delete User');
			$appaccess->rules($this->desc,'User Account Manage All');
			$appaccess->rules($this->desc,'User Account Change Role');
			$appaccess->rules($this->desc,'User Account Change User Login');

			/*$appaccess->rules($this->desc,'add-users','Add users');
			$appaccess->rules($this->desc,'edit-users','Edit users');
			$appaccess->rules($this->desc,'view-users','View users');
			$appaccess->rules($this->desc,'delete-users','Delete users');

			$appaccess->rules($this->desc,'add-roles','Add roles');
			$appaccess->rules($this->desc,'edit-roles','Edit roles');
			$appaccess->rules($this->desc,'view-roles','View roles');
			$appaccess->rules($this->desc,'delete-roles','Delete roles');*/
		}

		function _xml_useraccountcontrol($routerid=false,$xmlid=false) {
			global $appdb, $applogin;

			if(!empty($routerid)&&!empty($xmlid)) {
			} else {
				return false;
			}

				//pre(array('$routerid'=>$routerid,'$xmlid'=>$xmlid));

			if($applogin->isSystemAdministrator()) {
				if(!($result = $appdb->query("select * from tbl_roles where flag=0 order by role_id asc"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				}
			} else {
				if(!($result = $appdb->query("select * from tbl_roles where flag=0 and role_id!=1 order by role_id asc"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				}
			}

			$access = $applogin->getAccess();

			//pre(array('$result'=>$result));

			$rxml = array();

			if(in_array('useraccountmanageall', $access)) {

				//pre(array('$access'=>$access));

				foreach($result['rows'] as $k=>$v) {
					$rxml[] = array(
							'@attributes' => array(
									'text' => $v['role_name'],
									'id' => $v['role_id'],
									'tooltip' => $v['role_desc'],
									'child' => 1,
								),
							'item' => $this->_getUsersXML($v['role_id']),
						);
				}

			} else {

				foreach($result['rows'] as $k=>$v) {
					if($v['role_id']==$applogin->getRoleID()) {
						$rxml[] = array(
								'@attributes' => array(
										'text' => $v['role_name'],
										'id' => $v['role_id'],
										'tooltip' => $v['role_desc'],
										'child' => 1,
									),
								'item' => $this->_getUsersXML($v['role_id'],$applogin->getUserID()),
							);
					}
				}

			}

			$axml = array(
				'@attributes' => array(
					'id' => '0',
				),
				'item' => array(
					'@attributes' => array(
						'text' => 'Roles/User',
						'id' => '0|0',
						'open' => 1,
					),
					'item' => $rxml,
				),
			);

			$xml = Array2XML::createXML('tree', $axml);

			return $xml->saveXML();;
		}

		function _getUsersXML($roleid=false,$userid=false) {
			global $appdb;

			//if(!($result = $appdb->query("select user_id,role_id,content->>'user_fname' as user_fname,content->>'user_lname' as user_lname,content->>'user_mname' as user_mname from tbl_users where role_id=".$roleid." and flag=0 order by content->>'user_lname' asc"))) {
			//	json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
			//}

			if(!($result = $appdb->query("select user_id,role_id,content->>'user_fname' as user_fname,content->>'user_lname' as user_lname,content->>'user_mname' as user_mname from tbl_users where role_id=".$roleid." order by content->>'user_lname' asc"))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
			}

			if(empty($result['rows'][0]['user_id'])) {
				return array(
								'@attributes' => array(
									'text' => 'no users...',
									'id' => $roleid.'|-1',
									'tooltip' => 'Cannot find user...'
									)
							);
			}

			$uxml = array();

			if($userid) {

				foreach($result['rows'] as $k=>$v) {

					if($userid==$v['user_id']) {

						$uxml[] = array(
										'@attributes' => array(
											'text' => $v['user_lname'].', '.$v['user_fname'].' '.$v['user_mname'],
											'id' => $v['role_id'].'|'.$v['user_id'],
											//'tooltip' => 'Cannot find user...'
											)
									);

					}
				}

			} else {

				foreach($result['rows'] as $k=>$v) {
					$uxml[] = array(
									'@attributes' => array(
										'text' => $v['user_lname'].', '.$v['user_fname'].' '.$v['user_mname'],
										'id' => $v['role_id'].'|'.$v['user_id'],
										//'tooltip' => 'Cannot find user...'
										)
								);
				}

			}


			return $uxml;
		}

		function _form_useraccountcontrol($routerid=false,$formid=false) {
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

		} // _form_useraccountcontrol

		function _form_useraccountmain($routerid=false,$formid=false) {
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

		} // _form_useraccountmain

		function _form_useraccountrole($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb, $appaccess;

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$params['hello'] = 'Hello, Sherwin!';

				$access = $applogin->getAccess();

				if(!empty($this->vars['post']['method'])) {

					if($this->vars['post']['method']=='useraccountview'||$this->vars['post']['method']=='useraccountedit'||$this->vars['post']['method']=='useraccountnewrole') {

						//if(!empty($this->vars['post']['roleid'])) {
						//} else {
						//	json_encode_return(array('error_code'=>123,'error_message'=>'Invalid Role ID'));
						//}

						if(!empty($this->vars['post']['roleid'])&&is_numeric($this->vars['post']['roleid'])&&$this->vars['post']['roleid']>0) {
							if(!($result = $appdb->query("select * from tbl_roles where role_id=".$this->vars['post']['roleid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;
							}

							if(!empty($result['rows'][0]['role_id'])) {
								$params['rolesinfo'] = $result['rows'][0];
							}

							$chkarr = array();

							if(!empty($result['rows'][0]['content'])) {
								$chkarr = json_decode($result['rows'][0]['content']);
							}
						}

						// {type: "fieldset", name: "access", label: "Select Access", inputWidth: 500, list:[

						// {type: "checkbox", name: "access_one", label: "Access One", value: "one"}

						$readonly = true;

						if($this->vars['post']['method']=='useraccountedit'||$this->vars['post']['method']=='useraccountnewrole') {
							$readonly = false;
						}

						$params['rulesinfo'] = $appaccess->getAllRules();

						$rules = array();

						$ctr=1;

						$chk=0;

						foreach($params['rulesinfo'] as $a=>$b) {

							$fs = array(
									'type' => 'fieldset',
									'name' => 'access'.$ctr,
									'label' => 'Access for '.$a,
									'inputWidth' => 500,
									'list' => array(),
								);


							/*$fs['list'][] = array(
									'type' => 'checkbox',
									'name' => 'chk['.$chk++.']',
									'label' => 'Select All',
									'value' => 'All',
									'position' => 'label-right',
									'labelWidth' => 200,
									'readonly' => $readonly,
								);*/

							foreach($b as $k=>$v) {
								$checked = false;

								if(in_array($k, $chkarr)) {
									$checked = true;
								}

								if(!$checked&&!in_array('useraccounteditrole',$access)) {
									continue;
								}

								$fs['list'][] = array(
										'type' => 'checkbox',
										'name' => 'chk['.$chk++.']',
										'label' => $v,
										'value' => $k,
										'position' => 'label-right',
										'labelWidth' => 200,
										'readonly' => $readonly,
										'checked' => $checked,
									);
							}

							if(!empty($fs['list'])) {
								$rules[] = $fs;
							}

							$ctr++;
						}

						$params['rules'] = $rules;


					} else
					if($this->vars['post']['method']=='useraccountsave') {

						$ret = array();
						$ret['return_code'] = 'SUCCESS';
						$ret['return_message'] = 'Role successfully saved!';

						//pre(array('$vars'=>$this->vars));

						if(!empty($this->vars['post']['roleid'])) {

							$content = array();
							$content['role_name'] = $this->vars['post']['role_name'];
							$content['role_desc'] = $this->vars['post']['role_desc'];

							if(!empty($this->vars['post']['chk'])) {

								$chk = array();

								foreach($this->vars['post']['chk'] as $k=>$v) {
									$chk[] = $v;
								}

								$content['content'] = json_encode($chk);
							}

							if(!($result = $appdb->update("tbl_roles",$content,"role_id=".$this->vars['post']['roleid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;
							}

							$ret['treeid'] = $this->vars['post']['roleid'];

						} else {

							$content = array();
							$content['role_name'] = $this->vars['post']['role_name'];
							$content['role_desc'] = $this->vars['post']['role_desc'];

							if(!empty($this->vars['post']['chk'])) {

								$chk = array();

								foreach($this->vars['post']['chk'] as $k=>$v) {
									$chk[] = $v;
								}

								$content['content'] = json_encode($chk);
							}

							if(!($result = $appdb->insert("tbl_roles",$content,"role_id"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;
							}

							if(!empty($result['returning'][0]['role_id'])) {
								$ret['treeid'] = $result['returning'][0]['role_id'];
							}

						}

						$ret['xml'] = $this->_xml_useraccountcontrol($routerid,$formid);

						json_encode_return($ret);

					} else
					if($this->vars['post']['method']=='useraccountdelete') {

						$ret = array();
						$ret['return_code'] = 'SUCCESS';
						$ret['return_message'] = 'Role successfully deleted!';

						if(!empty($this->vars['post']['roleid'])) {

							if(!($result = $appdb->query("delete from tbl_roles where role_id=".$this->vars['post']['roleid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;
							}

						}

						$ret['xml'] = $this->_xml_useraccountcontrol($routerid,$formid);

						json_encode_return($ret);
					}


				}

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}
			}

			return false;

		} // _form_useraccountrole

		function _form_useraccountuser($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$params['hello'] = 'Hello, Sherwin!';

				$access = $applogin->getAccess();

				if(!empty($this->vars['post']['method'])) {

					if($applogin->isSystemAdministrator()) {
						if(!($result = $appdb->query("select * from tbl_roles where flag=0 order by role_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						}
					} else {
						if(!($result = $appdb->query("select * from tbl_roles where flag=0 and role_id!=1 order by role_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						}
					}

					$params['allroles'] = array();

					if($this->vars['post']['method']=='useraccountview'||$this->vars['post']['method']=='useraccountedit') {

						//$params['allroles'] = array(array('value'=>'','text'=>''));

						if(	!empty($this->vars['post']['roleid'])&&is_numeric($this->vars['post']['roleid'])&&$this->vars['post']['roleid']>0&&
							!empty($this->vars['post']['userid'])&&is_numeric($this->vars['post']['userid'])&&$this->vars['post']['userid']>0) {
						} else {
							json_encode_return(array('error_code'=>123,'error_message'=>'Invalid Role/User ID'));
						}

						if($this->vars['post']['method']=='useraccountedit') {

							if(in_array('useraccountchangerole',$access)) {
								foreach($result['rows'] as $k=>$v) {
									$selected = false;
									if($this->vars['post']['roleid']==$v['role_id']) {
										$params['rolesinfo'] = $v;
										$selected = true;
									}

									$params['allroles'][] = array('value'=>$v['role_id'],'text'=>$v['role_name'],'selected'=>$selected);
								}
							} else {
								foreach($result['rows'] as $k=>$v) {
									if($this->vars['post']['roleid']==$v['role_id']) {
										$params['rolesinfo'] = $v;
										$params['allroles'][] = array('value'=>$v['role_id'],'text'=>$v['role_name'],'selected'=>true);
										break;
									}
								}
							}

						} else {
							foreach($result['rows'] as $k=>$v) {
								if($this->vars['post']['roleid']==$v['role_id']) {
									$params['rolesinfo'] = $v;
									$params['allroles'][] = array('value'=>$v['role_id'],'text'=>$v['role_name'],'selected'=>true);
									break;
								}
							}
						}

						if(!($result = $appdb->query("select * from tbl_users where role_id=".$this->vars['post']['roleid']." and user_id=".$this->vars['post']['userid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						}

						if(!empty($result['rows'][0]['user_id'])) {
							$params['userinfo'] = $result['rows'][0];
							if(!empty($params['userinfo']['content'])) {
								$params['userinfo']['content'] = json_decode($params['userinfo']['content'],true);
							}
						}

					} else
					if($this->vars['post']['method']=='useraccountnewuser') {

						if(!empty($this->vars['post']['roleid'])) {

							foreach($result['rows'] as $k=>$v) {
								$selected = false;
								if($this->vars['post']['roleid']==$v['role_id']) {
									$selected = true;
								}

								$params['allroles'][] = array('value'=>$v['role_id'],'text'=>$v['role_name'],'selected'=>$selected);
							}

						}

					} else
					if($this->vars['post']['method']=='useraccountsave') {

						/*if(	!empty($this->vars['post']['roleid'])&&is_numeric($this->vars['post']['roleid'])&&$this->vars['post']['roleid']>0&&
							!empty($this->vars['post']['userid'])&&is_numeric($this->vars['post']['userid'])&&$this->vars['post']['userid']>0) {
						} else {
							json_encode_return(array('error_code'=>123,'error_message'=>'Invalid Role/User ID'));
						}*/

						$ret = array();
						$ret['return_code'] = 'SUCCESS';
						$ret['return_message'] = 'User successfully saved!';

						if(!empty($this->vars['post']['roleid'])&&!empty($this->vars['post']['userid'])) {

							if(!($result = $appdb->query("select * from tbl_users where role_id=".$this->vars['post']['roleid']." and user_id=".$this->vars['post']['userid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							}

							if(!empty($result['rows'][0]['user_id'])) {
								$userinfo = $result['rows'][0];
								if(!empty($userinfo['content'])) {
									$userinfoContent = json_decode($userinfo['content'],true);
								}
							}

							$content = array();
							$content['role_id'] = $ret['roleid'] = $this->vars['post']['user_role'];
							$content['user_login'] = $this->vars['post']['user_login'];
							$content['user_email'] = $this->vars['post']['user_email'];
							$content['flag'] = 0;

							if(!empty($this->vars['post']['new_hash'])) {
								$content['user_hash'] = $this->vars['post']['new_hash'];
							}

							$tounset = array('routerid','formval','action','module','formid','method','roleid','userid','user_role','user_login','user_email','new_hash');

							$cont = $this->vars['post'];

							foreach($tounset as $k=>$v) {
								unset($cont[$v]);
							}

							if(!empty($userinfoContent)) {
								//foreach($userinfoContent as $k=>$v) {
								//	$cont[$k] = $v;
								//}
								foreach($cont as $k=>$v) {
									$userinfoContent[$k] = $v;
								}
								$cont = $userinfoContent;
							}

							$content['content'] = json_encode($cont);

							if(!($result = $appdb->update("tbl_users",$content,"user_id=".$this->vars['post']['userid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;
							}

							$ret['userid'] = $this->vars['post']['userid'];

						} else {

							$content = array();
							$content['role_id'] = $ret['roleid'] = $this->vars['post']['user_role'];
							$content['user_login'] = $this->vars['post']['user_login'];
							$content['user_email'] = $this->vars['post']['user_email'];

							if(!empty($this->vars['post']['new_hash'])) {
								$content['user_hash'] = $this->vars['post']['new_hash'];
							}

							$tounset = array('routerid','formval','action','module','formid','method','roleid','userid','user_role','user_login','user_email','new_hash');

							$cont = $this->vars['post'];

							foreach($tounset as $k=>$v) {
								unset($cont[$v]);
							}

							$content['content'] = json_encode($cont);

							if(!($result = $appdb->insert("tbl_users",$content,"user_id"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;
							}

							if(!empty($result['returning'][0]['user_id'])) {
								$ret['userid'] = $result['returning'][0]['user_id'];
							}

						}


						//$ret['roleid'] = $this->vars['post']['roleid'];
						$ret['xml'] = $this->_xml_useraccountcontrol($routerid,$formid);
						//$ret['post'] = $this->vars['post'];

						json_encode_return($ret);
						//die;
					} else
					if($this->vars['post']['method']=='useraccountdelete') {

						$ret = array();
						$ret['return_code'] = 'SUCCESS';
						$ret['return_message'] = 'User successfully deleted!';

						if(!empty($this->vars['post']['userid'])) {

							if(!($result = $appdb->query("delete from tbl_users where user_id=".$this->vars['post']['userid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;
							}

						}

						$ret['xml'] = $this->_xml_useraccountcontrol($routerid,$formid);

						json_encode_return($ret);
					}

				}

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}
			}

			return false;

		} // _form_useraccountuser

		function router() {
			global $applogin, $toolbars, $forms, $apptemplate;

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
				}
			}

			return false;
		} // router($vars=false,$retflag=false)

	}

	$appappuseraccount = new APP_app_useraccount;
}

# eof modules/app.user
