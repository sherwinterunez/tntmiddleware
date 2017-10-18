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

if(!class_exists('APP_app_group')) {

	class APP_app_group extends APP_Base_Ajax {

		var $desc = 'group';

		var $pathid = 'group';
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

			$appaccess->rules($this->desc,'Group Module');
			$appaccess->rules($this->desc,'Group Module New');
			$appaccess->rules($this->desc,'Group Module Edit');
			$appaccess->rules($this->desc,'Group Module Delete');

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

		function _form_group($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				//pre(array($routerid,$formid));

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				//$settings_tardinessgraceperiodminute = getOption('$SETTINGS_TARDINESSGRACEPERIODMINUTE','');

				if(!empty($post['method'])&&($post['method']=='groupedit')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&$post['method']=='groupsave') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Group Reference successfully saved!';
					$retval['post'] = $post;

					//setSetting('$SETTINGS_TARDINESSGRACEPERIODMINUTE',!empty($post['group_tardinessgraceperiod'])?$post['group_tardinessgraceperiod']:0);

					//pre(array('$post',$post));

					if(!empty($post['studentsection_id'])&&is_array($post['studentsection_id'])) {

						foreach($post['studentsection_id'] as $k=>$v) {

							$groupref_id = false;

							$content = array();

							if(is_numeric($post['studentsection_id'][$k])&&intval($post['studentsection_id'][$k])>0) {
								//$content['groupref_id'] = $post['studentsection_id'][$k];
								$groupref_id = $post['studentsection_id'][$k];
							}

							$content['groupref_seq'] = !empty($post['studentsection_seq'][$k]) ? $post['studentsection_seq'][$k] : 0;

							if(!empty($post['studentsection_yearlevel'][$k]) && is_numeric($post['studentsection_yearlevel'][$k]) && intval($post['studentsection_yearlevel'][$k]) > 0) {
								$content['groupref_yearlevel'] = intval($post['studentsection_yearlevel'][$k]);
							}

							$content['groupref_name'] = !empty($post['studentsection_sectionname'][$k]) ? $post['studentsection_sectionname'][$k] : '';
							$content['groupref_starttime'] = !empty($post['studentsection_starttime'][$k]) ? $post['studentsection_starttime'][$k] : '';
							$content['groupref_endtime'] = !empty($post['studentsection_endtime'][$k]) ? $post['studentsection_endtime'][$k] : '';

							$content['groupref_type'] = 1;

							if(!empty($groupref_id)) {

								if(!empty($content['groupref_name'])) {

									$content['groupref_updatestamp'] = 'now()';

									if(!($result = $appdb->update("tbl_groupref",$content,"groupref_id=".$groupref_id))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;
									}

								} else {

									if(!($result = $appdb->query("delete from tbl_groupref where groupref_id=".$groupref_id))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;
									}

								}

							} else {

								if(!empty($content['groupref_name'])) {

									if(!($result = $appdb->insert("tbl_groupref",$content,"groupref_id"))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;
									}

									if(!empty($result['returning'][0]['groupref_id'])) {
										$groupref_id = $result['returning'][0]['groupref_id'];
									}

								}

							}

						}

					} // if(!empty($post['studentsection_id'])&&is_array($post['studentsection_id'])) {

					if(!empty($post['studentyearlevel_id'])&&is_array($post['studentyearlevel_id'])) {

						foreach($post['studentyearlevel_id'] as $k=>$v) {

							$groupref_id = false;

							$content = array();

							if(is_numeric($post['studentyearlevel_id'][$k])&&intval($post['studentyearlevel_id'][$k])>0) {
								//$content['groupref_id'] = $post['studentsection_id'][$k];
								$groupref_id = $post['studentyearlevel_id'][$k];
							}

							$content['groupref_seq'] = !empty($post['studentyearlevel_seq'][$k]) ? $post['studentyearlevel_seq'][$k] : 0;
							$content['groupref_name'] = !empty($post['studentyearlevel_yearlevel'][$k]) ? $post['studentyearlevel_yearlevel'][$k] : '';

							$content['groupref_type'] = 2;

							if(!empty($groupref_id)) {

								if(!empty($content['groupref_name'])) {

									$content['groupref_updatestamp'] = 'now()';

									if(!($result = $appdb->update("tbl_groupref",$content,"groupref_id=".$groupref_id))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;
									}

								} else {

									if(!($result = $appdb->query("delete from tbl_groupref where groupref_id=".$groupref_id))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;
									}

								}

							} else {

								if(!empty($content['groupref_name'])) {

									if(!($result = $appdb->insert("tbl_groupref",$content,"groupref_id"))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;
									}

									if(!empty($result['returning'][0]['groupref_id'])) {
										$groupref_id = $result['returning'][0]['groupref_id'];
									}

								}

							}

						}

					} // if(!empty($post['studentyearlevel_id'])&&is_array($post['studentyearlevel_id'])) {

					if(!empty($post['employeedepartment_id'])&&is_array($post['employeedepartment_id'])) {

						foreach($post['employeedepartment_id'] as $k=>$v) {

							$groupref_id = false;

							$content = array();

							if(is_numeric($post['employeedepartment_id'][$k])&&intval($post['employeedepartment_id'][$k])>0) {
								//$content['groupref_id'] = $post['studentsection_id'][$k];
								$groupref_id = $post['employeedepartment_id'][$k];
							}

							$content['groupref_seq'] = !empty($post['employeedepartment_seq'][$k]) ? $post['employeedepartment_seq'][$k] : 0;
							$content['groupref_name'] = !empty($post['employeedepartment_departmentname'][$k]) ? $post['employeedepartment_departmentname'][$k] : '';
							$content['groupref_starttime'] = !empty($post['employeedepartment_starttime'][$k]) ? $post['employeedepartment_starttime'][$k] : '';
							$content['groupref_endtime'] = !empty($post['employeedepartment_endtime'][$k]) ? $post['employeedepartment_endtime'][$k] : '';

							$content['groupref_type'] = 3;

							if(!empty($groupref_id)) {

								if(!empty($content['groupref_name'])) {

									$content['groupref_updatestamp'] = 'now()';

									if(!($result = $appdb->update("tbl_groupref",$content,"groupref_id=".$groupref_id))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;
									}

								} else {

									if(!($result = $appdb->query("delete from tbl_groupref where groupref_id=".$groupref_id))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;
									}

								}

							} else {

								if(!empty($content['groupref_name'])) {

									if(!($result = $appdb->insert("tbl_groupref",$content,"groupref_id"))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;
									}

									if(!empty($result['returning'][0]['groupref_id'])) {
										$groupref_id = $result['returning'][0]['groupref_id'];
									}

								}

							}

						}

					} // if(!empty($post['employeedepartment_id'])&&is_array($post['employeedepartment_id'])) {

					if(!empty($post['employeeposition_id'])&&is_array($post['employeeposition_id'])) {

						foreach($post['employeeposition_id'] as $k=>$v) {

							$groupref_id = false;

							$content = array();

							if(is_numeric($post['employeeposition_id'][$k])&&intval($post['employeeposition_id'][$k])>0) {
								//$content['groupref_id'] = $post['studentsection_id'][$k];
								$groupref_id = $post['employeeposition_id'][$k];
							}

							$content['groupref_seq'] = !empty($post['employeeposition_seq'][$k]) ? $post['employeeposition_seq'][$k] : 0;
							$content['groupref_name'] = !empty($post['employeeposition_positionname'][$k]) ? $post['employeeposition_positionname'][$k] : '';

							$content['groupref_type'] = 4;

							if(!empty($groupref_id)) {

								if(!empty($content['groupref_name'])) {

									$content['groupref_updatestamp'] = 'now()';

									if(!($result = $appdb->update("tbl_groupref",$content,"groupref_id=".$groupref_id))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;
									}

								} else {

									if(!($result = $appdb->query("delete from tbl_groupref where groupref_id=".$groupref_id))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;
									}

								}

							} else {

								if(!empty($content['groupref_name'])) {

									if(!($result = $appdb->insert("tbl_groupref",$content,"groupref_id"))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;
									}

									if(!empty($result['returning'][0]['groupref_id'])) {
										$groupref_id = $result['returning'][0]['groupref_id'];
									}

								}

							}

						}

					} // if(!empty($post['employeeposition_id'])&&is_array($post['employeeposition_id'])) {

					json_encode_return($retval);
					die;
				}

				$params['hello'] = 'Hello, Sherwin!';

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbStudentSection'] = array();
				$params['tbStudentYearlevel'] = array();
				$params['tbEmployeeDepartment'] = array();
				$params['tbEmployeePosition'] = array();
				$params['tbThreshold'] = array();

				/*$params['tbStudentSection'][] = array(
					'type' => 'input',
					'label' => 'SIM CARD NAME',
					'name' => 'simcard_name',
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['simcardinfo']['simcard_name']) ? $params['simcardinfo']['simcard_name'] : '',
				);*/

				$params['tbStudentSection'][] = array(
					'type' => 'container',
					'name' => 'group_studentsection',
					'inputWidth' => 600,
					'inputHeight' => 200,
					'className' => 'group_studentsection_'.$post['formval'],
				);

				$params['tbStudentYearlevel'][] = array(
					'type' => 'container',
					'name' => 'group_studentyearlevel',
					'inputWidth' => 600,
					'inputHeight' => 200,
					'className' => 'group_studentyearlevel_'.$post['formval'],
				);

				$params['tbEmployeeDepartment'][] = array(
					'type' => 'container',
					'name' => 'group_employeedepartment',
					'inputWidth' => 600,
					'inputHeight' => 200,
					'className' => 'group_employeedepartment_'.$post['formval'],
				);

				$params['tbEmployeePosition'][] = array(
					'type' => 'container',
					'name' => 'group_employeeposition',
					'inputWidth' => 600,
					'inputHeight' => 200,
					'className' => 'group_employeeposition_'.$post['formval'],
				);

				/*$params['tbThreshold'][] = array(
					'type' => 'container',
					'name' => 'group_threshold',
					'inputWidth' => 600,
					'inputHeight' => 200,
					'className' => 'group_threshold_'.$post['formval'],
				);*/

				/*$params['tbThreshold'][] = array(
					'type' => 'input',
					'label' => 'TARDINESS GRACE PERIOD (MINUTE)',
					'labelWidth' => 250,
					'name' => 'group_tardinessgraceperiod',
					'readonly' => $readonly,
					'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_tardinessgraceperiodminute)?$settings_tardinessgraceperiodminute:'',
				);*/

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
					if($this->post['table']=='studentsection') {

						if(!($result = $appdb->query("select * from tbl_groupref where groupref_type=1 order by groupref_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						$groupref = getGroupRef(2);

						$rows = array();

						$seq = 1;

						//$optyearlevel = array(array('text'=>'','value'=>''));
						$optyearlevel = array();

						foreach($groupref as $k=>$v) {
							$optyearlevel[] = array('text'=>$v['groupref_name'],'value'=>$v['groupref_id']);
						}

						if(!empty($result['rows'][0]['groupref_id'])) {

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$seq,'yearlevel'=>array('options'=>$optyearlevel),'data'=>array($v['groupref_id'],$seq,$v['groupref_name'],getGroupRefName($v['groupref_yearlevel']),$v['groupref_starttime'],$v['groupref_endtime']));
								$seq++;
							}

						}

						if($this->post['method']=='groupedit'||empty($rows)) {
							for($i=0;$i<10;$i++) {
								$rows[] = array('id'=>$seq,'data'=>array(0,$seq,'','','',''));
								$seq++;
							}
						}

						if(!empty($rows)) {
							$retval = array('rows'=>$rows);
						}
					} else
					if($this->post['table']=='studentyearlevel') {

						if(!($result = $appdb->query("select * from tbl_groupref where groupref_type=2 order by groupref_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						$rows = array();

						$seq = 1;

						if(!empty($result['rows'][0]['groupref_id'])) {

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$seq,'data'=>array($v['groupref_id'],$seq,$v['groupref_name']));
								$seq++;
							}

						}

						if($this->post['method']=='groupedit'||empty($rows)) {
							for($i=0;$i<10;$i++) {
								$rows[] = array('id'=>$seq,'data'=>array(0,$seq,''));
								$seq++;
							}
						}

						if(!empty($rows)) {
							$retval = array('rows'=>$rows);
						}
					} else
					if($this->post['table']=='employeedepartment') {

						if(!($result = $appdb->query("select * from tbl_groupref where groupref_type=3 order by groupref_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						$rows = array();

						$seq = 1;

						if(!empty($result['rows'][0]['groupref_id'])) {

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$seq,'data'=>array($v['groupref_id'],$seq,$v['groupref_name'],$v['groupref_starttime'],$v['groupref_endtime']));
								$seq++;
							}

						}

						if($this->post['method']=='groupedit'||empty($rows)) {
							for($i=0;$i<10;$i++) {
								$rows[] = array('id'=>$seq,'data'=>array(0,$seq,'','',''));
								$seq++;
							}
						}

						if(!empty($rows)) {
							$retval = array('rows'=>$rows);
						}
					} else
					if($this->post['table']=='employeeposition') {

						if(!($result = $appdb->query("select * from tbl_groupref where groupref_type=4 order by groupref_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						$rows = array();

						$seq = 1;

						if(!empty($result['rows'][0]['groupref_id'])) {

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$seq,'data'=>array($v['groupref_id'],$seq,$v['groupref_name']));
								$seq++;
							}

						}

						if($this->post['method']=='groupedit'||empty($rows)) {
							for($i=0;$i<10;$i++) {
								$rows[] = array('id'=>$seq,'data'=>array(0,$seq,''));
								$seq++;
							}
						}

						if(!empty($rows)) {
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

	$appappgroup = new APP_app_group;
}

# eof modules/app.user
