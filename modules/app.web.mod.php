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

if(!class_exists('APP_app_web')) {

	class APP_app_web extends APP_Base_Ajax {
	
		var $desc = 'Websites';

		var $pathid = 'websites';
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

			$appaccess->rules($this->desc,'manage-websites','Manage Websites');
			//$appaccess->rules($this->pathid,'update-users','Update users');
			//$appaccess->rules($this->pathid,'view-users','View users');
			//$appaccess->rules($this->pathid,'delete-users','Delete users');
		}

		function _form_websitecontrol($routerid=false,$formid=false) {

			if(!empty($routerid)&&!empty($formid)) {

				$params = array('websitecontrol'=>'Hello, World!');

				$templatefile = $this->templatefile($routerid,$formid);

				/*if(!empty($this->vars['post']['roleid'])&&empty($this->vars['post']['userid'])) {
					$templatefile = $this->templatefile($routerid,'usermanagementnewroles');

					$params = array('readonly'=>true);
				} else
				if(!empty($this->vars['post']['roleid'])&&!empty($this->vars['post']['userid'])) {
					$templatefile = $this->templatefile($routerid,'usermanagementnewuser');

					$params = array('readonly'=>true);
				}*/

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
		}

		function _form_websitemanage($routerid=false,$formid=false) {

			if(!empty($routerid)&&!empty($formid)) {

				$params = array('websitemanage'=>'Hello, World!');

				$templatefile = $this->templatefile($routerid,$formid);

				/*if(!empty($this->vars['post']['roleid'])&&empty($this->vars['post']['userid'])) {
					$templatefile = $this->templatefile($routerid,'usermanagementnewroles');

					$params = array('readonly'=>true);
				} else
				if(!empty($this->vars['post']['roleid'])&&!empty($this->vars['post']['userid'])) {
					$templatefile = $this->templatefile($routerid,'usermanagementnewuser');

					$params = array('readonly'=>true);
				}*/

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
		}

		function _form_websitecreate($routerid=false,$formid=false) {

			if(!empty($routerid)&&!empty($formid)) {

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
		}

		function _form_usermanagementmanage($routerid=false,$formid=false) {

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$templatefile = $this->templatefile($routerid,$formid);

				if(!empty($this->vars['post']['roleid'])&&empty($this->vars['post']['userid'])) {
					$templatefile = $this->templatefile($routerid,'usermanagementnewroles');

					$params = array('readonly'=>true);
				} else
				if(!empty($this->vars['post']['roleid'])&&!empty($this->vars['post']['userid'])) {
					$templatefile = $this->templatefile($routerid,'usermanagementnewuser');

					$params = array('readonly'=>true);
				}


				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
		}

		function _form_usermanagementedit($routerid=false,$formid=false) {
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$templatefile = $this->templatefile($routerid,$formid);

				if(!empty($this->vars['post']['roleid'])&&empty($this->vars['post']['userid'])) {
					$templatefile = $this->templatefile($routerid,'usermanagementnewroles');

					$params = array('edit'=>true);
				} else
				if(!empty($this->vars['post']['roleid'])&&!empty($this->vars['post']['userid'])) {
					$templatefile = $this->templatefile($routerid,'usermanagementnewuser');

					$params = array('edit'=>true);
				}


				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
		}

		function _form_usermanagementdelete($routerid=false,$formid=false) {
			global $appdb;

			if(!empty($routerid)&&!empty($formid)&&!empty($this->vars['post']['formval'])) {
			} else {
				return false;
			}

			$this->_form_validate();

			/* delete role */
			if(!empty($this->vars['post']['roleid'])&&empty($this->vars['post']['userid'])) {
				if(!($result = $appdb->query("select * from tbl_roles where role_id=".$this->vars['post']['roleid']." and flag=0"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				}

				if(!empty($result['rows'][0]['role_id'])) {
					if(!($result = $appdb->update('tbl_roles',array('flag'=>1),'role_id='.$this->vars['post']['roleid']))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					}

					$xml = $this->_xml_usermanagementcontrol($routerid,$formid);
					json_encode_return(array('error_code'=>0,'message'=>'Role successfully deleted.','xml'=>$xml));				
				} else {
					json_encode_return(array('error_code'=>123,'error_message'=>'Role not found.'));				
				}

				//pre(array('$result'=>$result));
			} else
			/* delete user */
			if(!empty($this->vars['post']['roleid'])&&!empty($this->vars['post']['userid'])) {
				if(!($result = $appdb->query("select * from tbl_users where role_id=".$this->vars['post']['roleid']." and user_id=".$this->vars['post']['userid']." and flag=0"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				}

				if(!empty($result['rows'][0]['user_id'])) {
					if(!($result = $appdb->update('tbl_users',array('flag'=>1),'user_id='.$this->vars['post']['userid']))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					}

					$xml = $this->_xml_usermanagementcontrol($routerid,$formid);
					json_encode_return(array('error_code'=>0,'message'=>'User successfully deleted.','xml'=>$xml));				
				} else {
					json_encode_return(array('error_code'=>123,'error_message'=>'Role not found.'));				
				}

				//pre(array('$result'=>$result));
			}


			//pre(array('$this->vars'=>$this->vars));

			return false;
		}

		function _xml_usermanagementcontrol_get_users($roleid=false) {
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
									'id' => 'nousers_'.$roleid,
									'tooltip' => 'Cannot find user...'
									)
							);
			}

			$uxml = array();

			foreach($result['rows'] as $k=>$v) {
				$uxml[] = array(
								'@attributes' => array(
									'text' => $v['user_lname'].', '.$v['user_fname'].' '.$v['user_mname'],
									'id' => 'roleid_'.$v['role_id'].'_userid_'.$v['user_id'],
									//'tooltip' => 'Cannot find user...'
									)
							);
			}

			return $uxml;
		}

		function _xml_usermanagementcontrol($routerid=false,$xmlid=false) {
			global $appdb;

			if(!empty($routerid)&&!empty($xmlid)) {
			} else {
				return false;
			}

				//pre(array('$routerid'=>$routerid,'$xmlid'=>$xmlid));
			if(!($result = $appdb->query("select * from tbl_roles where flag=0 order by role_id asc"))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;				
			}

			//pre(array('$result'=>$result));

			$rxml = array();

			foreach($result['rows'] as $k=>$v) {
				$rxml[] = array(
						'@attributes' => array(
								'text' => $v['role_name'],
								'id' => 'roleid_'.$v['role_id'],
								'tooltip' => $v['role_desc'],
								'child' => 1,
							),
						'item' => $this->_xml_usermanagementcontrol_get_users($v['role_id']),
					);
			}

			$axml = array(
				'@attributes' => array(
					'id' => '0',
				),
				'item' => array(
					'@attributes' => array(
						'text' => 'Roles/User',
						'id' => 'roleid_0',
						'open' => 1,
					),
					'item' => $rxml,
				),
			);

			$xml = Array2XML::createXML('tree', $axml);

			return $xml->saveXML();;
		}

		function _form_usermanagementsave($routerid=false,$formid=false) {
			global $appdb;

			if(!empty($routerid)&&!empty($formid)&&!empty($this->vars['post']['formval'])) {
			} else {
				return false;
			}

			if(!empty($_SESSION['FORMS'][$this->vars['post']['formval']])) {
				if(time()>($_SESSION['FORMS'][$this->vars['post']['formval']]['since']+1800)) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Form expired!'));
					unset($_SESSION['FORMS'][$this->vars['post']['formval']]);
					die;
				}
			} else {
				//pre(array('$vars'=>$this->vars,'$_SESSION'=>$_SESSION));
				json_encode_return(array('error_code'=>123,'error_message'=>'Invalid form detected!'));				
				die;
			}

			/* updating user */
			if(!empty($this->post['user_login'])&&!empty($this->post['user_id'])&&!empty($this->post['user_role'])&&!empty($this->post['update'])) {

				$role = explode('_', $this->post['user_role']);

				if($role[0]!='roleid') {
					json_encode_return(array('error_code'=>123,'error_message'=>'Invalid Role ID!'));				
				}

				if(!is_numeric($role[1])) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Invalid Role ID!'));				
				}

				$roleid = $role[1];

				//pre(array('$this->post'=>$this->post));

				if(!($result = $appdb->query("select * from tbl_users where user_id=".pgFixString($this->post['user_id'])." and flag=0"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				}

				//pre(array('$result'=>$result));

				if(empty($result['rows'][0]['user_id'])) {
					json_encode_return(array('error_code'=>123,'error_message'=>'User not found.'));
				}

				$updates = array(
					'role_id'=>$roleid,
					'user_login'=>$this->post['user_login'],
					'user_email'=>$this->post['user_email'],
					'content'=>json_encode($this->post),
					'updatestamp'=>'now()',
				);

				if(!empty($this->post['newuser_hash'])) {
					$updates['user_hash'] = $this->post['newuser_hash'];
				}

				if(!($result = $appdb->update('tbl_users',$updates,'user_id='.$this->post['user_id']))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				}

				$xml = $this->_xml_usermanagementcontrol($routerid,$formid);

				$uid = 'roleid_'.$roleid.'_userid_'.$this->post['user_id'];

				json_encode_return(array('error_code'=>123,'error_message'=>'User account has been updated.','user_id'=>$uid,'xml'=>$xml));

			} else
			/* saving new user */
			if(!empty($this->post['user_login'])&&!empty($this->post['user_hash'])&&!empty($this->post['user_role'])) {

				//pre(array('$this->vars'=>$this->vars,'$this->post'=>$this->post));

				$role = explode('_', $this->post['user_role']);

				if($role[0]!='roleid') {
					json_encode_return(array('error_code'=>123,'error_message'=>'Invalid Role ID!'));				
					die;
				}

				if(!is_numeric($role[1])) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Invalid Role ID!'));				
					die;
				}

				$roleid = $role[1];

				//pre(array('$role'=>$role));

				if(!($result = $appdb->query("select * from tbl_roles where role_id='".pgFixString($roleid)."' and flag=0"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;				
				}

				//pre(array('$result'=>$result));

				if(!empty($result['rows'][0]['role_id'])) {
				} else {
					json_encode_return(array('error_code'=>123,'error_message'=>'Invalid Role ID!'));				
					die;					
				}

				if(!($result = $appdb->query("select * from tbl_users where user_login='".pgFixString($this->post['user_login'])."' and flag!=0"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;				
				}

				if(!empty($result['rows'][0]['user_id'])) {
					json_encode_return(array('error_code'=>123,'error_message'=>'User name already deleted and cannot be recreated. Contact administrator for help.'));				
					die;					
				}

				if(!($result = $appdb->query("select * from tbl_users where user_login='".pgFixString($this->post['user_login'])."' and flag=0"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;				
				}

				if(!empty($result['rows'][0]['user_id'])) {
					json_encode_return(array('error_code'=>123,'error_message'=>'User name already exists!'));				
					die;					
				}

				if(!($result = $appdb->query("select * from tbl_users where user_email='".pgFixString($this->post['user_email'])."'"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;				
				}

				if(!empty($result['rows'][0]['user_id'])) {
					json_encode_return(array('error_code'=>123,'error_message'=>'User email already exists!'));				
					die;					
				}

				//pre(array('$result'=>$result));

				if(!($result = $appdb->insert('tbl_users',array('role_id'=>$roleid,'user_hash'=>$this->post['user_hash'],'user_login'=>$this->post['user_login'],'user_email'=>$this->post['user_email'],'content'=>json_encode($this->post)),'user_id'))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;				
				}

				if(!empty($result['returning'][0]['user_id'])) {
					$xml = $this->_xml_usermanagementcontrol($routerid,$formid);
					$uid = 'roleid_'.$roleid.'_userid_'.$result['returning'][0]['user_id'];
					json_encode_return(array('error_code'=>123,'error_message'=>'User added successfully.','user_id'=>$uid,'xml'=>$xml));
				}

				//pre(array('$result'=>$result));

			} else
			/* update new role */
			if(!empty($this->post['role_name'])&&!empty($this->post['role_desc'])&&!empty($this->post['role_id'])) {

				if(!($result = $appdb->query("select * from tbl_roles where role_id=".$this->post['role_id']))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;				
				}

				if(!empty($result['rows'][0]['role_id'])) {
				} else {

				}

				if(!($result = $appdb->update('tbl_roles',array('role_name'=>$this->post['role_name'],'role_desc'=>$this->post['role_desc']),'role_id='.$this->post['role_id']))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				}

				$xml = $this->_xml_usermanagementcontrol($routerid,$formid);

				json_encode_return(array('error_code'=>123,'error_message'=>'Role successfully updated.','role_id'=>'roleid_'.$this->post['role_id'],'xml'=>$xml));	

			}
			/* saving new role */
			if(!empty($this->post['role_name'])&&!empty($this->post['role_desc'])) {

				if(!($result = $appdb->query("select * from tbl_roles where role_name='".pgFixString($this->post['role_name'])."' and flag!=0"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				}

				if(!empty($result['rows'])&&is_array($result['rows'])&&count($result['rows'])>0) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Role name has been deleted and cannot be recreated. Contact administrator for help.'));
				}

				if(!($result = $appdb->query("select * from tbl_roles where role_name='".pgFixString($this->post['role_name'])."'"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				}

				if(!empty($result['rows'])&&is_array($result['rows'])&&count($result['rows'])>0) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Role name already exists.'));
				}

				if(!($result = $appdb->insert('tbl_roles',array('role_name'=>$this->post['role_name'],'role_desc'=>$this->post['role_desc']),'role_id'))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				}

				if(!empty($result['returning'][0]['role_id'])) {
					$xml = $this->_xml_usermanagementcontrol($routerid,$formid);
					json_encode_return(array('error_code'=>123,'error_message'=>'Role successfully added.','role_id'=>'roleid_'.$result['returning'][0]['role_id'],'xml'=>$xml));	
				}

			}

			//pre($result);
			return false;
		}

		function router() {
			global $applogin, $toolbars, $forms, $apptemplate;

			$retflag=false;


			//if(!empty($vars)&&!empty($vars['post'])) {
			//	$this->vars = $vars;
			//	$this->post = $vars['post'];				
			//}

			header_json();

			//pre(array('$this->vars'=>$this->vars,'$this->post'=>$this->post));

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

	$appappweb = new APP_app_web;
}

# eof modules/app.web

