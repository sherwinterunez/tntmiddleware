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

if(!class_exists('APP_app_newmessage')) {

	class APP_app_newmessage extends APP_Base_Ajax {

		var $desc = 'newmessage';

		var $pathid = 'newmessage';
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

			$appaccess->rules($this->desc,'New Message Module');
			$appaccess->rules($this->desc,'New Message Module New');
			$appaccess->rules($this->desc,'New Message Module Edit');
			$appaccess->rules($this->desc,'New Message Module Delete');

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

		function _form_newmessage($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				//pre(array($routerid,$formid));

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				$push = 0;

				if(!empty($post['method'])&&($post['method']=='newmessageedit')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&($post['method']=='newmessageoutbox'||$post['method']=='newmessagenow')) {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'New Message Queued to Outbox!';
					$retval['post'] = $post;

					if(!empty($post['sendpushnotification'])) {
						$push = 1;
					}

					$contacts = array();

					if(!empty($post['sendto'])) {
						$sendto = explode(',',$post['sendto']);
						if(!empty($sendto)&&is_array($sendto)) {
							foreach($sendto as $k=>$v) {
								$contacts[$v] = $v;
							}
						}
					}

					if(!empty($post['contacts'])) {
						if(!($result = $appdb->query("select * from tbl_studentprofile where studentprofile_id in (".$post['contacts'].")"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
						if(!empty($result['rows'][0]['studentprofile_id'])) {
							foreach($result['rows'] as $k=>$v) {
								if(!empty($v['studentprofile_guardianmobileno'])) {
									$contacts[$v['studentprofile_guardianmobileno']] = $v['studentprofile_guardianmobileno'];
								}
							}
						}
					}

					if(!empty($post['yearlevel'])) {
						if(!($result = $appdb->query("select * from tbl_studentprofile where studentprofile_yearlevel in (".$post['yearlevel'].")"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
						if(!empty($result['rows'][0]['studentprofile_id'])) {
							foreach($result['rows'] as $k=>$v) {
								if(!empty($v['studentprofile_guardianmobileno'])) {
									$contacts[$v['studentprofile_guardianmobileno']] = $v['studentprofile_guardianmobileno'];
								}
							}
						}
					}

					if(!empty($post['section'])) {
						if(!($result = $appdb->query("select * from tbl_studentprofile where studentprofile_section in (".$post['section'].")"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
						if(!empty($result['rows'][0]['studentprofile_id'])) {
							foreach($result['rows'] as $k=>$v) {
								if(!empty($v['studentprofile_guardianmobileno'])) {
									$contacts[$v['studentprofile_guardianmobileno']] = $v['studentprofile_guardianmobileno'];
								}
							}
						}
					}

					$retval['contacts'] = $contacts;

					/*if(!($result = $appdb->query("select * from tbl_studentprofile where upload_studentprofileid=0 and upload_sid='".$content['upload_sid']."' and upload_name='".$post['itemId']."'"))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						die;
					}*/

					if(!empty($contacts)) {
						$asim = getAllSims(8);

						//pre(array('$asim'=>$asim));

						if(!empty($asim)&&is_array($asim)) {

							foreach($contacts as $mobileno) {
								shuffle($asim);

								foreach($asim as $m=>$n) {
									//pre(array('$mobileno'=>$mobileno,'$m'=>$n['sim_number'],'$sms'=>$post['sms']));
									//sendToOutBox($mobileno,$n['sim_number'],$post['sms']);
									sendToOutBoxPush($mobileno,$n['sim_number'],$post['sms'],$push);
									break;
								}

							}
						} else {
							foreach($contacts as $mobileno) {
								//shuffle($asim);

								//foreach($asim as $m=>$n) {
									//pre(array('$mobileno'=>$mobileno,'$m'=>$n['sim_number'],'$sms'=>$post['sms']));
									//sendToOutBox($mobileno,$n['sim_number'],$post['sms']);
									sendToOutBoxPush($mobileno,false,$post['sms'],$push);
									//break;
								//}

							}
						}
					}

					json_encode_return($retval);
					die;
				}

				/*if(!empty($post['method'])&&$post['method']=='newmessagenow') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'New Message Sending Now!';
					$retval['post'] = $post;

					//pre(array('$post',$post));

					json_encode_return($retval);
					die;
				}*/

				if(!empty($post['method'])&&$post['method']=='newmessagesave') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'New Message successfully saved!';
					$retval['post'] = $post;

					//pre(array('$post',$post));

					json_encode_return($retval);
					die;
				}

				$params['hello'] = 'Hello, Sherwin!';

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbDetails'] = array();

				$block = array();

				$block[] = array(
					'type' => 'input',
					'label' => 'SEND TO',
					'labelWidth' => 60,
					'inputWidth' => 240,
					'name' => 'newmessage_sendto',
					'readonly' => false,
					'inputMask' => array('alias'=>'Regex','regex'=>'[0-9\;\,\s]+','prefix'=>'','autoUnmask'=>true),
					//'required' => !$readonly,
					//'inputMask' => 'Regex',
					//'inputMaskParam' => array('regex'=>'[a-zA-Z0-9]*'),
					'value' => '',
				);

				$block[] = array(
					'type' => 'checkbox',
					'label' => 'SEND PUSH NOTIFICATION',
					'labelWidth' => 250,
					'name' => 'newmessage_sendpushnotification',
					'checked' => true,
					'readonly' => true,
					//'readonly' => $readonly,
					//'checked' => !empty($settings_loginnotificationschooladminsendsms) ? true : false,
					'position' => 'label-right',
				);

				$block[] = array(
					'type' => 'input',
					//'label' => 'SEND TO',
					//'labelWidth' => 250,
					'inputWidth' => 300,
					'name' => 'newmessage_sms',
					'readonly' => false,
					'rows' => 10,
					'maxLength' => 480,
					//'required' => !$readonly,
					'value' => 'Enter your message here',
				);

				$block[] = array(
					'type' => 'input',
					//'label' => 'SEND TO',
					//'labelWidth' => 60,
					'inputWidth' => 300,
					'name' => 'newmessage_totalchars',
					'readonly' => true,
					'disabled' => true,
					//'required' => !$readonly,
					'value' => 'Characters:',
				);

				$params['tbDetails'][] = array(
					'type' => 'block',
					'width' => 300,
					'blockOffset' => 0,
					'offsetTop' => 5,
					'list' => $block,
				);

				$params['tbDetails'][] = array(
					'type' => 'newcolumn',
					'offset' => 20,
				);

				$block = array();

				$block[] = array(
					'type' => 'container',
					'name' => 'newmessage_contacts',
					'inputWidth' => 400,
					'inputHeight' => 347,
					'className' => 'newmessage_contacts_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'block',
					'width' => 400,
					'blockOffset' => 0,
					'offsetTop' => 5,
					'list' => $block,
					'className' => 'newmessage_blockcontacts_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'newcolumn',
					'offset' => 15,
				);

				$block = array();

				$block[] = array(
					'type' => 'container',
					'name' => 'newmessage_yearlevel',
					'inputWidth' => 400,
					'inputHeight' => 168,
					'className' => 'newmessage_yearlevel_'.$post['formval'],
				);

				$block[] = array(
					'type' => 'container',
					'name' => 'newmessage_section',
					'inputWidth' => 400,
					'inputHeight' => 170,
					'className' => 'newmessage_section_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'block',
					'width' => 400,
					'blockOffset' => 0,
					'offsetTop' => 5,
					'list' => $block,
					'className' => 'newmessage_blockyearlevel_'.$post['formval'],
				);

				$templatefile = $this->templatefile($routerid,$formid);

				//pre(array($routerid,$formid,$params,$templatefile));

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}
			}

			return false;

		} // _form_group

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
					if($this->post['table']=='newmessagecontacts') {

						if(!($result = $appdb->query("select * from tbl_studentprofile order by studentprofile_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						if(!empty($result['rows'][0]['studentprofile_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								//$rows[] = array('id'=>$v['studentprofile_id'],'data'=>array(0,$v['studentprofile_id'],$v['studentprofile_number'],$v['studentprofile_rfid'],$v['studentprofile_firstname'],$v['studentprofile_lastname'],$v['studentprofile_middlename'],getGroupRefName($v['studentprofile_yearlevel']),getGroupRefName($v['studentprofile_section']),$v['studentprofile_guardianname'],$v['studentprofile_guardianmobileno'],$v['studentprofile_guardianemail']));
								$studentname = '';

								if(!empty($v['studentprofile_firstname'])) {
									$studentname .= $v['studentprofile_firstname'];
								}

								if(!empty($v['studentprofile_middlename'])) {
									$studentname .= ' '.$v['studentprofile_middlename'];
								}

								if(!empty($v['studentprofile_lastname'])) {
									$studentname .= ' '.$v['studentprofile_lastname'];
								}

								$rows[] = array('id'=>$v['studentprofile_id'],'data'=>array(0,$v['studentprofile_id'],$v['studentprofile_guardianmobileno'],$studentname));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='newmessageyearlevel') {

						if(!($result = $appdb->query("select * from tbl_groupref where groupref_type=2 order by groupref_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['groupref_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['groupref_id'],'data'=>array(0,$v['groupref_id'],$v['groupref_name']));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='newmessagesection') {

						if(!($result = $appdb->query("select * from tbl_groupref where groupref_type=1 order by groupref_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['groupref_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['groupref_id'],'data'=>array(0,$v['groupref_id'],getGroupRefName($v['groupref_yearlevel']).' / '.$v['groupref_name']));
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

	$appappnewmessage = new APP_app_newmessage;
}

# eof modules/app.user
