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

if(!class_exists('APP_app_promotion')) {

	class APP_app_promotion extends APP_Base_Ajax {
	
		var $desc = 'Promotion';

		var $pathid = 'promotion';
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

			$appaccess->rules($this->desc,'Promotion Module');

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

		function _form_promotioncontrol($routerid=false,$formid=false) {
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
			
		} // _form_promotioncontrol

		function _form_promotionmainpromos($routerid=false,$formid=false) {
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
			
		} // _form_promotionmainpromos

		function _form_promotionmainsend($routerid=false,$formid=false) {
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
			
		} // _form_promotionmainsend

		function _form_promotionmainsent($routerid=false,$formid=false) {
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
			
		} // _form_promotionmainsent

		function _form_promotionmainclaimed($routerid=false,$formid=false) {
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
			
		} // _form_promotionmainclaimed

		function _form_promotiondetailpromos($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$readonly = true;

				if(!empty($this->vars['post']['method'])&&($this->vars['post']['method']=='promotionnew'||$this->vars['post']['method']=='promotionedit')) {
					$readonly = false;
				}

				if(!empty($this->vars['post']['method'])&&($this->vars['post']['method']=='onrowselect'||$this->vars['post']['method']=='promotionedit')) {
					if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {
						if(!($result = $appdb->query("select * from tbl_promos where promos_id=".$this->vars['post']['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['promos_id'])) {
							$params['promosinfo'] = $result['rows'][0];
						}
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

				if(!empty($this->vars['post']['method'])&&$this->vars['post']['method']=='promotionsave') {

					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Promotion successfully saved!';

					$content = array();
					$content['promos_title'] = !empty($this->vars['post']['promos_title']) ? $this->vars['post']['promos_title'] : '';
					$content['promos_desc'] = !empty($this->vars['post']['promos_desc']) ? $this->vars['post']['promos_desc'] : '';
					$content['promos_sms'] = !empty($this->vars['post']['promos_sms']) ? $this->vars['post']['promos_sms'] : '';
					$content['promos_startdate'] = !empty($this->vars['post']['promos_startdate']) ? $this->vars['post']['promos_startdate'] : '';
					$content['promos_enddate'] = !empty($this->vars['post']['promos_enddate']) ? $this->vars['post']['promos_enddate'] : '';

					if(!empty($this->vars['post']['rowid'])) {
						$retval['rowid'] = $this->vars['post']['rowid'];

						$content['promos_updatestamp'] = 'now()';

						if(!($result = $appdb->update("tbl_promos",$content,"promos_id=".$this->vars['post']['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

					} else {
						if(!($result = $appdb->insert("tbl_promos",$content,"promos_id"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['returning'][0]['promos_id'])) {
							$retval['rowid'] = $result['returning'][0]['promos_id'];
						}
					}

					json_encode_return($retval);
					die;
				} else
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

							if(!($result = $appdb->query("delete from tbl_promos where promos_id in (".$rowids.")"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							json_encode_return($retval);
							die;
						}

					}

					if(!empty($this->vars['post']['rowid'])) {
						if(!($result = $appdb->query("delete from tbl_promos where promos_id=".$this->vars['post']['rowid']))) {
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
			
		} // _form_promotiondetailpromos

		function _form_promotiondetailsend($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$readonly = true;

				if(!empty($this->vars['post']['method'])&&($this->vars['post']['method']=='promotionnew'||$this->vars['post']['method']=='promotionedit')) {
					$readonly = false;
				}

				$params = array();

/////
				$params['contacts'] = array();
				$params['groups'] = array();
				$params['sims'] = array();

				if(!($result = $appdb->query("select * from tbl_contact where contact_deleted=0 order by contact_id asc"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;				
				}

				if(!empty($result['rows'][0]['contact_id'])) {

					$params['contacts'][] = array(
							'type' => 'checkbox',
							'label' => 'All Contacts',
							'name' => 'to_number_0',
							'checked' => false,
							'position' => 'label-right',
							'labelWidth' => 250,
							'value' => 'All Contacts',
						);

					foreach($result['rows'] as $k=>$v) {

						$params['contacts'][] = array(
								'type' => 'checkbox',
								'label' => $v['contact_number'].' / '.$v['contact_nick'],
								'name' => 'to_number_'.($k+1),
								'checked' => false,
								'position' => 'label-right',
								'labelWidth' => 250,
								'value' => $v['contact_number'],
							);
					}

				}

				$groups = getAllGroups();

				if(is_array($groups)&&!empty($groups[0]['group_id'])) {

					// array('text'=>,'value'=>,'count'=>$this->getAllContactsCount())

					//if(!empty($arowid)) {
					//	$groupNames = $this->getGroupNamesByArrayOfIDs($arowid);						
					//}

					$params['groups'][] = array(
							'type' => 'checkbox',
							'label' => 'All Groups ('.getAllContactsCount().')',
							'name' => 'to_groups_0',
							'checked' => false,
							'position' => 'label-right',
							'labelWidth' => 250,
							'value' => 'All Groups',
						);

					foreach($groups as $k=>$v) {

						$checked = false;

						$memberscount = '';

						if($groupid=getNetworkGroupIDFromName($v['group_name'])) {
							$memberscount = getGroupMembersCount($groupid);
						}

						if(!empty($memberscount)) {
							//$params['groups'][] = $v['group_name'] . ' ('.$memberscount.')';
							//$params['groups'][] = array('text'=>$v['group_name'].' ('.$memberscount.')','value'=>$v['group_name'],'count'=>$memberscount);

							if(empty($arowid)&&!empty($this->vars['post']['rowid'])&&!empty($this->vars['post']['from'])&&$this->vars['post']['from']=='groups'&&$groupid==$this->vars['post']['rowid']) {
								$checked = true;
								$params['composetogroups'] = $v['group_name'];
							}

							if(!empty($arowid)&&in_array($v['group_name'], $groupNames)) {
								$checked = true;
								$params['composetogroups'] .= $v['group_name'] . ';';
							}

							$params['groups'][] = array(
									'type' => 'checkbox',
									'label' => $v['group_name'].' ('.$memberscount.')',
									'name' => 'to_groups_'.($k+1),
									'checked' => $checked,
									'position' => 'label-right',
									'labelWidth' => 250,
									'value' => $v['group_name'],
								);

						}
					}
				}

				$sims = getAllSims(5); // get all online sim

				if(is_array($sims)&&!empty($sims[0]['sim_id'])) {

					$params['sims'][] = array(
							'type' => 'checkbox',
							'label' => 'All SIMs',
							'name' => 'to_sims_0',
							'checked' => false,
							'position' => 'label-right',
							'labelWidth' => 250,
							'value' => 'All SIMs',
						);

					foreach($sims as $k=>$v) {

						$params['sims'][] = array(
								'type' => 'checkbox',
								'label' => $v['sim_name'].' / '.getNetworkName($v['sim_number']),
								'name' => 'to_sims_'.($k+1),
								'checked' => false,
								'position' => 'label-right',
								'labelWidth' => 250,
								'value' => $v['sim_name'],
							);

					}
				}

/////
				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbDetails'] = array();

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'To Number',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'name' => 'txt_to_number',
					'readonly' => true,
					'hidden' => true,
					'value' => '',
				);

				$params['tbDetails'][] = array(
					'type' => 'block',
					'width' => 298,
					'offsetTop' => 5,
					'offsetLeft' => 4,
					'inputLeft' => 0,
					'blockOffset' => 5,
					'className' => 'cls_sherwin',
					'list' => $params['contacts'],
				);

				$params['tbDetails'][] = array(
					'type' => 'newcolumn',
					'offset' => 20,
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'To Groups',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'name' => 'txt_to_groups',
					'readonly' => true,
					'hidden' => true,
					'value' => '',
				);

				$params['tbDetails'][] = array(
					'type' => 'block',
					'width' => 298,
					'offsetTop' => 5,
					'offsetLeft' => 4,
					'inputLeft' => 0,
					'blockOffset' => 5,
					'className' => 'cls_sherwin',
					'list' => $params['groups'],
				);

				$params['tbDetails'][] = array(
					'type' => 'newcolumn',
					'offset' => 20,
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'SIMs',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'name' => 'txt_to_sims',
					'readonly' => true,
					'hidden' => true,
					'value' => '',
				);

				$params['tbDetails'][] = array(
					'type' => 'block',
					'width' => 298,
					'offsetTop' => 5,
					'offsetLeft' => 4,
					'inputLeft' => 0,
					'blockOffset' => 5,
					'className' => 'cls_sherwin',
					'list' => $params['sims'],
				);

/////

				if(!empty($this->vars['post']['rowid'])&&!empty($this->vars['post']['method'])&&($this->vars['post']['method']=='promotionsendtooutbox'||$this->vars['post']['method']=='promotionsendnow')) {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';

					if($this->vars['post']['method']=='promotionsendtooutbox') {
						$retval['return_message'] = 'Promotion successfully sent to Outbox!';							
					} else
					if($this->vars['post']['method']=='promotionsendnow') {
						$retval['return_message'] = 'Promotion successfully queued for immediate sending!';														
					}

					if(!($result = $appdb->query("select * from tbl_promos where promos_id=".$this->vars['post']['rowid']))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						die;				
					}

					if(!empty($result['rows'][0]['promos_id'])) {

						$promossent_title = $result['rows'][0]['promos_title'];
						$promossent_desc = $result['rows'][0]['promos_desc'];
						$promossent_sms = $result['rows'][0]['promos_sms'];
						$promossent_startdate = $result['rows'][0]['promos_startdate'];
						$promossent_enddate = $result['rows'][0]['promos_enddate'];

						//pre(array('$result'=>$result));

						//if(!empty($result['rows'][0]['smsinbox_id'])) {
						//	$params['content'] = str_replace("\n",'<br>',$result['rows'][0]['smsinbox_message']);
						//}

						$smscontent = $result['rows'][0]['promos_sms'];

						//$smscontent = trim(htmlspecialchars_decode(strip_tags($smscontent)));
						//$smscontent = str_replace('&nbsp;',' ',$smscontent);

						$tosims = trim($this->vars['post']['tosims']);
						$togroups = trim($this->vars['post']['togroups']);
						$tonumbers = trim($this->vars['post']['tonumbers']);

						//$smscontent = strip_tags($smscontent, '<br>');
						//$smscontent = str_replace('<br>',"\n",$smscontent);
						//$smscontent = str_replace('<br/>',"\n",$smscontent);
						//$smscontent = str_replace('<br />',"\n",$smscontent);

						$asim = explode(';', $tosims);
						$atogroups = explode(';', $togroups);
						$atonumbers = explode(';', $tonumbers);

						$recipients = array();

						$retval['asim'] = $asim;
						$retval['atogroups'] = $atogroups;
						$retval['atonumbers'] = $atonumbers;

						//pre(array('retval'=>$retval));

						$netports = array();

						if(preg_match('#All\sSIMs#', $tosims)) {
							$asim = getAllSimsName();

							//pre(array('$asim'=>$asim));
						}

						if(!empty($asim)&&is_array($asim)) {

							//$allsim = $this->getAllSims(1);

							$allsim = getAllSims(7); // get all online sim

							//pre(array('$allsim'=>$allsim));

							$netsim = array();

							$anetsim = array();

							foreach($asim as $sim) {
								if(!empty($allsim[$sim])) {
									$netsim[$allsim[$sim]['sim_network']][] = $anetsim[] = $allsim[$sim];
								}
							}
							//pre(array('$netsim'=>$netsim,'$anetsim'=>$anetsim));
						}

						if(preg_match('#All\sGroups#', $togroups)) {
							$atogroups = getAllGroupsWithMembers();
						}

						if(!empty($atogroups)&&is_array($atogroups)) {
							foreach($atogroups as $v) {
								$v = trim($v);
								if(!empty($v)) {
									$res = getGroupMembersByName($v);

									if(!empty($res)&&is_array($res)&&!empty($res[0]['groupcontact_id'])) {
										foreach($res as $j) {
											//pre(array('$j'=>$j));
											$ct = getContactNumber($j['groupcontact_contactid']);
											if(!empty($ct)) {
												$recipients[$j['groupcontact_contactid']] = $ct;
											}
										}
									}
								}
							}
						}

						if(preg_match('#All\sContacts#', $tonumbers)) {
							$atonumbers = getAllContacts(true);
							//pre(array('$atonumbers'=>$atonumbers));
						}

						if(!empty($atonumbers)&&is_array($atonumbers)) {
							foreach($atonumbers as $v) {
								$v = trim($v);
								if(!empty($v)) {
									$cid = getContactIDByNumber($v);
									if(!empty($cid)) {
										$recipients[$cid] = $v;
									}
								}
							}
						}

						$retval['recipients'] = $recipients;

						if(!empty($recipients)&&is_array($recipients)) {

							$actr = array();

							$vrecipients = array();

							$xrecipients = array();

							foreach($recipients as $contactid=>$contactnumber) {

								$cnetname = getNetworkName($contactnumber);

								//print_r(array('$cnetname'=>$cnetname));

								if(!isset($actr[$cnetname])) {
									$actr[$cnetname] = 1;
								}

								$simnumber = '';

								if(!empty($netsim[$cnetname][$actr[$cnetname]-1]['sim_number'])) {
									$simnumber = $netsim[$cnetname][$actr[$cnetname]-1]['sim_number'];
									$simname = $netsim[$cnetname][$actr[$cnetname]-1]['sim_name'];
									$vrecipients[$contactid] = array('contactnumber'=>$contactnumber,'simnumber'=>$simnumber,'simname'=>$simname);
									//print_r(array('$contactnumber'=>$contactnumber,'$cnetname'=>$cnetname,'$actr[$cnetname]'=>$actr[$cnetname],'$simnumber'=>$simnumber));
								} else {
									$xrecipients[$contactid] = $contactnumber;
								}

								//print_r(array('$contactnumber'=>$contactnumber,'$cnetname'=>$cnetname,'$actr[$cnetname]'=>$actr[$cnetname],'$simnumber'=>$simnumber));

								$actr[$cnetname]++;

								if(!isset($netsim[$cnetname][$actr[$cnetname]-1]['sim_number'])) {
									$actr[$cnetname] = 1;
								}

							}


							if(!empty($xrecipients)) {

								//print_r(array('$xrecipients'=>$xrecipients));

								$ctr=0;

								foreach($xrecipients as $contactid=>$contactnumber) {
									if(!empty($anetsim[$ctr])) {
										$vrecipients[$contactid] = array('contactnumber'=>$contactnumber,'simnumber'=>$anetsim[$ctr]['sim_number'],'simname'=>$anetsim[$ctr]['sim_name']);
									}

									//print_r(array('$contactnumber'=>$contactnumber,'$simnumber'=>$simnumber));

									$ctr++;

									if(empty($anetsim[$ctr])) {
										$ctr=0;
									}
								}

							}

							//print_r(array('$vrecipients'=>$vrecipients));

							foreach($vrecipients as $contactid=>$vv) {

								$contactnumber = $vv['contactnumber'];

								$simnumber = $vv['simnumber'];

								$simname = $vv['simname'];

								$textmsg = $smscontent . ' ' . getOption('$PROMOCODE_MESSAGE');

								$promossent_promocode = generatePromoCode();

								$textmsg = str_replace('%promocode%', $promossent_promocode, $textmsg);

								if(strlen($smscontent)>160) {

									// long sms

									$smsparts = str_split($textmsg,152); 

									$smsoutbox_udhref = dechex_str(mt_rand(100,250)); 

									$smsoutbox_total = count($smsparts); 

									$content = array();
									$content['promossent_contactid'] = $contactid;
									$content['promossent_contactnumber'] = $contactnumber;
									$content['promossent_title'] = $promossent_title;
									$content['promossent_desc'] = $promossent_desc;
									$content['promossent_sms'] = $promossent_sms;
									$content['promossent_promocode'] = $promossent_promocode;
									$content['promossent_startdate'] = $promossent_startdate;
									$content['promossent_enddate'] = $promossent_enddate;

									if(!($result = $appdb->insert("tbl_promossent",$content,"promossent_id"))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}

									if(!empty($result['returning'][0]['promossent_id'])) {

										$smsoutbox_promossentid = $result['returning'][0]['promossent_id'];

										$content = array();
										$content['smsoutbox_contactid'] = $contactid;
										$content['smsoutbox_contactnumber'] = $contactnumber;
										$content['smsoutbox_message'] = $textmsg;
										$content['smsoutbox_udhref'] = $smsoutbox_udhref;
										$content['smsoutbox_part'] = $smsoutbox_total;
										$content['smsoutbox_total'] = $smsoutbox_total;
										$content['smsoutbox_simnumber'] = $simnumber;
										$content['smsoutbox_type'] = 1;
										$content['smsoutbox_promossentid'] = $smsoutbox_promossentid;

										if($this->vars['post']['method']=='promotionsendnow') {
											$content['smsoutbox_status'] = 1;										
										}

										if(!($result = $appdb->insert("tbl_smsoutbox",$content,"smsoutbox_id"))) {
											json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
											die;				
										}

										$content = array();
										$content['promocodes_contactid'] = $contactid;
										$content['promocodes_contactnumber'] = $contactnumber;
										$content['promocodes_startdate'] = $promossent_startdate;
										$content['promocodes_enddate'] = $promossent_enddate;

										if(!($result = $appdb->update("tbl_promocodes",$content,"promocodes_promocode='$promossent_promocode'"))) {
											json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
											die;				
										}

									}

								} else {

									// short sms

									$content = array();
									$content['promossent_contactid'] = $contactid;
									$content['promossent_contactnumber'] = $contactnumber;
									$content['promossent_title'] = $promossent_title;
									$content['promossent_desc'] = $promossent_desc;
									$content['promossent_sms'] = $promossent_sms;
									$content['promossent_promocode'] = $promossent_promocode;
									$content['promossent_startdate'] = $promossent_startdate;
									$content['promossent_enddate'] = $promossent_enddate;

									if(!($result = $appdb->insert("tbl_promossent",$content,"promossent_id"))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}

									if(!empty($result['returning'][0]['promossent_id'])) {

										$smsoutbox_promossentid = $result['returning'][0]['promossent_id'];

										$content = array();
										$content['smsoutbox_contactid'] = $contactid;
										$content['smsoutbox_contactnumber'] = $contactnumber;
										$content['smsoutbox_message'] = $textmsg;
										$content['smsoutbox_simnumber'] = $simnumber;
										$content['smsoutbox_part'] = 1;
										$content['smsoutbox_total'] = 1;
										$content['smsoutbox_promossentid'] = $smsoutbox_promossentid;

										if($this->vars['post']['method']=='promotionsendnow') {
											$content['smsoutbox_status'] = 1;										
										}

										//pre(array('$content'=>$content));

										if(!($result = $appdb->insert("tbl_smsoutbox",$content,"smsoutbox_id"))) {
											json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
											die;				
										}

										$content = array();
										$content['promocodes_contactid'] = $contactid;
										$content['promocodes_contactnumber'] = $contactnumber;
										$content['promocodes_startdate'] = $promossent_startdate;
										$content['promocodes_enddate'] = $promossent_enddate;

										if(!($result = $appdb->update("tbl_promocodes",$content,"promocodes_promocode='$promossent_promocode'"))) {
											json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
											die;				
										}
									}
								}

							} // foreach($vrecipients as $contactid=>$vv) {

							if($this->vars['post']['method']=='promotionsendnow') {
								$this->vars['post']['method'] = 'promotionsendtooutbox';
							}

						}

						json_encode_return($retval);
						die;
					}

				} //if($this->vars['post']['method']=='messagingsendtooutbox'||$this->vars['post']['method']=='messagingsendnow') {

/////


				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_promotiondetailsend

		function _form_promotiondetailsent($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$readonly = true;

				//if(!empty($this->vars['post']['method'])&&($this->vars['post']['method']=='promotionnew'||$this->vars['post']['method']=='promotionedit')) {
				//	$readonly = false;
				//}

				if(!empty($this->vars['post']['method'])&&($this->vars['post']['method']=='onrowselect'||$this->vars['post']['method']=='promotionedit')) {
					if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {

						$sql = "select promossent_id,promossent_contactnumber,promossent_title,promossent_desc,promossent_sms,promossent_startdate,promossent_enddate,case when promossent_status=0 then 'queued' when promossent_status=1 then 'waiting' when promossent_status=3 then 'sending' when promossent_status=4 then 'sent' when promossent_status=5 then 'failed' end as promossent_status,promossent_promocode,promossent_sentstamp from tbl_promossent where promossent_id=".$this->vars['post']['rowid'];

						if(!($result = $appdb->query($sql))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['promossent_id'])) {
							$params['promosinfo'] = $result['rows'][0];
						}
					}
				}

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbDetails'] = array();

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'CONTACT',
					'name' => 'promossent_contactnumber',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['promosinfo']['promossent_contactnumber']) ? $params['promosinfo']['promossent_contactnumber'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TITLE',
					'name' => 'promossent_title',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['promosinfo']['promossent_title']) ? $params['promosinfo']['promossent_title'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'DESCRIPTION',
					'name' => 'promossent_desc',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['promosinfo']['promossent_desc']) ? $params['promosinfo']['promossent_desc'] : '',
				);

				if($readonly) {

					$params['tbDetails'][] = array(
						'type' => 'input',
						'label' => 'START DATE',
						'name' => 'promossent_startdate',
						'readonly' => true,
						'value' => !empty($params['promosinfo']['promossent_startdate']) ? $params['promosinfo']['promossent_startdate'] : '',
					);

					$params['tbDetails'][] = array(
						'type' => 'input',
						'label' => 'END DATE',
						'name' => 'promossent_enddate',
						'readonly' => true,
						'value' => !empty($params['promosinfo']['promossent_enddate']) ? $params['promosinfo']['promossent_enddate'] : '',
					);

				} else {

					$params['tbDetails'][] = array(
						'type' => 'calendar',
						'label' => 'START DATE',
						'name' => 'promossent_startdate',
						'readonly' => true,
						'required' => !$readonly,
						'enableTime' => true,
						'enableTodayButton' => true,
						'calendarPosition' => 'right',
						'dateFormat' => '%m-%d-%Y %H:%i',
						'validate' => "NotEmpty",
						'value' => !empty($params['promosinfo']['promossent_startdate']) ? $params['promosinfo']['promossent_startdate'] : '',
					);

					$params['tbDetails'][] = array(
						'type' => 'calendar',
						'label' => 'END DATE',
						'name' => 'promossent_enddate',
						'readonly' => true,
						'required' => !$readonly,
						'enableTime' => true,
						'calendarPosition' => 'right',
						'dateFormat' => '%m-%d-%Y %H:%i',
						'validate' => "NotEmpty",
						'value' => !empty($params['promosinfo']['promossent_enddate']) ? $params['promosinfo']['promossent_enddate'] : '',
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
					'value' => !empty($params['promosinfo']['promossent_sms']) ? $params['promosinfo']['promossent_sms'] : '',
					'rows' => 5,
					'inputWidth' => 500,
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'PROMO CODE',
					'name' => 'promossent_promocode',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['promosinfo']['promossent_promocode']) ? $params['promosinfo']['promossent_promocode'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'STATUS',
					'name' => 'promossent_status',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['promosinfo']['promossent_status']) ? $params['promosinfo']['promossent_status'] : '',
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

							if(!($result = $appdb->update("tbl_promocodes",array('promocodes_deleted'=>1),"promocodes_id in ($rowids)"))) {
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
						if(!($result = $appdb->update("tbl_promocodes",array('promocodes_deleted'=>1),"promocodes_id=".$this->vars['post']['rowid']))) {
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
			
		} // _form_promotiondetailsent

		function _form_promotiondetailclaimed($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$readonly = true;

				//if(!empty($this->vars['post']['method'])&&($this->vars['post']['method']=='promotionnew'||$this->vars['post']['method']=='promotionedit')) {
				//	$readonly = false;
				//}

				if(!empty($this->vars['post']['method'])&&($this->vars['post']['method']=='onrowselect'||$this->vars['post']['method']=='promotionedit')) {
					if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {
						if(!($result = $appdb->query("select * from tbl_promocodes where promocodes_id=".$this->vars['post']['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['promocodes_id'])) {
							$params['promosinfo'] = $result['rows'][0];

							if(!($result = $appdb->query("select * from tbl_promossent where promossent_promocode='".$params['promosinfo']['promocodes_promocode']."'"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['promossent_id'])) {
								$params['promossentinfo'] = $result['rows'][0];
							}

						}
					}
				}

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbDetails'] = array();

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'CONTACT',
					'name' => 'promocodes_contactnumber',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['promosinfo']['promocodes_contactnumber']) ? $params['promosinfo']['promocodes_contactnumber'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'PROMO CODE',
					'name' => 'promocodes_promocode',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['promosinfo']['promocodes_promocode']) ? $params['promosinfo']['promocodes_promocode'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'STATUS',
					'name' => 'promocodes_status',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => 'CLAIMED',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'CLAIMED DATE',
					'name' => 'promocodes_claimstamp',
					'readonly' => true,
					'value' => !empty($params['promosinfo']['promocodes_claimstamp']) ? pgDate($params['promosinfo']['promocodes_claimstamp']) : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'START DATE',
					'name' => 'promocodes_startdate',
					'readonly' => true,
					'value' => !empty($params['promosinfo']['promocodes_startdate']) ? $params['promosinfo']['promocodes_startdate'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'END DATE',
					'name' => 'promocodes_enddate',
					'readonly' => true,
					'value' => !empty($params['promosinfo']['promocodes_enddate']) ? $params['promosinfo']['promocodes_enddate'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'newcolumn',
					'offset' => 20,
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TITLE',
					'name' => 'promossent_title',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['promossentinfo']['promossent_title']) ? $params['promossentinfo']['promossent_title'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'DESCRIPTION',
					'name' => 'promossent_desc',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => !empty($params['promossentinfo']['promossent_desc']) ? $params['promossentinfo']['promossent_desc'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TEXT MESSAGE',
					'name' => 'promos_sms',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['promossentinfo']['promossent_sms']) ? $params['promossentinfo']['promossent_sms'] : '',
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
			
		} // _form_promotiondetailclaimed

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
								$rows[] = array('id'=>$v['promos_id'],'data'=>array(0,$v['promos_id'],$v['promos_title'],$v['promos_desc'],$v['promos_sms'],$v['promos_startdate'],$v['promos_enddate'],pgDate($v['promos_createstamp']),pgDate($v['promos_updatestamp'])));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='sent') {
						if(!($result = $appdb->query("select promossent_id,promossent_contactnumber,promossent_title,promossent_desc,promossent_sms,case when promossent_status=0 then 'queued' when promossent_status=1 then 'waiting' when promossent_status=3 then 'sending' when promossent_status=4 then 'sent' when promossent_status=5 then 'failed' end as promossent_status,promossent_promocode,promossent_sentstamp from tbl_promossent order by promossent_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['promossent_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['promossent_id'],'data'=>array(0,$v['promossent_id'],$v['promossent_contactnumber'],$v['promossent_title'],$v['promossent_desc'],$v['promossent_sms'],$v['promossent_status'],$v['promossent_promocode'],pgDate($v['promossent_sentstamp'])));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='claimed') {
						if(!($result = $appdb->query("select *,extract(epoch from promocodes_claimstamp) as claimstamp from tbl_promocodes where promocodes_claimed=1 and promocodes_deleted=0 order by claimstamp desc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['promocodes_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {

								if(!($res = $appdb->query("select * from tbl_promossent where promossent_promocode='".$v['promocodes_promocode']."'"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								if(!empty($res['rows'][0]['promossent_id'])) {

									$p = $res['rows'][0];

									$rows[] = array('id'=>$v['promocodes_id'],'data'=>array(0,$v['promocodes_id'],$v['promocodes_promocode'],$v['promocodes_contactnumber'],$p['promossent_title'],$p['promossent_desc'],pgDate($v['promocodes_claimstamp'])));
								}

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

	$appapppromotion = new APP_app_promotion;
}

# eof modules/app.user