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

if(!class_exists('APP_app_scheduler')) {

	class APP_app_scheduler extends APP_Base_Ajax {
	
		var $desc = 'Scheduler';

		var $pathid = 'scheduler';
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

			$appaccess->rules($this->desc,'Scheduler Module');
			$appaccess->rules($this->desc,'Scheduler Module New');
			$appaccess->rules($this->desc,'Scheduler Module Edit');
			$appaccess->rules($this->desc,'Scheduler Module Delete');

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

		function _form_schedulermaintemplate($routerid=false,$formid=false) {
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
			
		} // _form_schedulermaintemplate

		function _form_schedulermainscheduled($routerid=false,$formid=false) {
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
			
		} // _form_schedulermainscheduled

		function _form_schedulermainsend($routerid=false,$formid=false) {
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
			
		} // _form_schedulermainsend

		function _form_schedulermainsent($routerid=false,$formid=false) {
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
			
		} // _form_schedulermainsent

		function _form_schedulerdetailtemplate($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$post = $this->vars['post'];

				$readonly = true;

				$params = array();

				if(!empty($post['method'])&&($post['method']=='schedulernew'||$post['method']=='scheduleredit')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&($post['method']=='onrowselect'||$post['method']=='scheduleredit')) {
					if(!empty($post['rowid'])&&is_numeric($post['rowid'])&&$post['rowid']>0) {
						if(!($result = $appdb->query("select * from tbl_scheduler where scheduler_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['scheduler_id'])) {
							$params['schedulerinfo'] = $result['rows'][0];
						}
					}
				} else
				if(!empty($post['method'])&&$post['method']=='schedulersave') {

					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Message successfully saved!';

					$content = array();
					$content['scheduler_title'] = !empty($post['scheduler_title']) ? $post['scheduler_title'] : '';
					$content['scheduler_desc'] = !empty($post['scheduler_desc']) ? $post['scheduler_desc'] : '';
					$content['scheduler_sms'] = !empty($post['scheduler_sms']) ? $post['scheduler_sms'] : '';

					if(!empty($post['rowid'])) {
						$retval['rowid'] = $post['rowid'];

						$content['scheduler_updatestamp'] = 'now()';

						if(!($result = $appdb->update("tbl_scheduler",$content,"scheduler_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

					} else {
						if(!($result = $appdb->insert("tbl_scheduler",$content,"scheduler_id"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['returning'][0]['scheduler_id'])) {
							$retval['rowid'] = $result['returning'][0]['scheduler_id'];
						}
					}

					json_encode_return($retval);
					die;

				} else
				if(!empty($post['method'])&&$post['method']=='schedulerdelete') {

					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Message successfully deleted!';

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

							if(!($result = $appdb->query("delete from tbl_scheduler where scheduler_id in (".$rowids.")"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							json_encode_return($retval);
							die;
						}

					}

					if(!empty($post['rowid'])) {
						if(!($result = $appdb->query("delete from tbl_scheduler where scheduler_id=".$post['rowid']))) {
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
					'name' => 'scheduler_title',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => !empty($params['schedulerinfo']['scheduler_title']) ? $params['schedulerinfo']['scheduler_title'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'DESCRIPTION',
					'name' => 'scheduler_desc',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => !empty($params['schedulerinfo']['scheduler_desc']) ? $params['schedulerinfo']['scheduler_desc'] : '',
				);

/*
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
*/

				/*$params['tbDetails'][] = array(
					'type' => 'newcolumn',
					'offset' => 20,
				);*/

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TEXT MESSAGE',
					'name' => 'scheduler_sms',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['schedulerinfo']['scheduler_sms']) ? $params['schedulerinfo']['scheduler_sms'] : '',
					'rows' => 5,
					'inputWidth' => 500,
				);

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_schedulerdetailtemplate

		function _form_schedulerdetailscheduled($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$post = $this->vars['post'];

				$readonly = true;

				$params = array();

				if(!empty($post['method'])&&($post['method']=='schedulernew'||$post['method']=='scheduleredit')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&$post['method']=='onrowselect') {
					if(!empty($post['rowid'])&&is_numeric($post['rowid'])&&$post['rowid']>0) {
						if(!($result = $appdb->query("select *,case when schedulersent_status=0 then 'queued' when schedulersent_status=1 then 'waiting' when schedulersent_status=3 then 'sending' when schedulersent_status=4 then 'sent' when schedulersent_status=5 then 'failed' end as schedulersent_status2 from tbl_schedulersent where schedulersent_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['schedulersent_id'])) {
							$params['schedulersentinfo'] = $result['rows'][0];
						}
					}
				} else
				if(!empty($post['method'])&&$post['method']=='schedulerdelete') {

					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Message successfully deleted!';

					if(!empty($this->vars['post']['rowids'])) {

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

							if(!($result = $appdb->query("delete from tbl_schedulersent where schedulersent_id in (".$rowids.")"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							json_encode_return($retval);
							die;
						}

					}

					if(!empty($post['rowid'])) {
						if(!($result = $appdb->query("delete from tbl_schedulersent where schedulersent_id=".$post['rowid']))) {
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
					'label' => 'NAME',
					'name' => 'schedulersent_contactnumber',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => getContactNickByID($params['schedulersentinfo']['schedulersent_contactid']),
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'CONTACT',
					'name' => 'schedulersent_contactnumber',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => !empty($params['schedulersentinfo']['schedulersent_contactnumber']) ? $params['schedulersentinfo']['schedulersent_contactnumber'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TITLE',
					'name' => 'schedulersent_title',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => !empty($params['schedulersentinfo']['schedulersent_title']) ? $params['schedulersentinfo']['schedulersent_title'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'DESCRIPTION',
					'name' => 'schedulersent_desc',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => !empty($params['schedulersentinfo']['schedulersent_desc']) ? $params['schedulersentinfo']['schedulersent_desc'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TEXT MESSAGE',
					'name' => 'schedulersent_sms',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['schedulersentinfo']['schedulersent_sms']) ? $params['schedulersentinfo']['schedulersent_sms'] : '',
					'rows' => 5,
					'inputWidth' => 500,
				);

				$params['tbDetails'][] = array(
					'type' => 'newcolumn',
					'offset' => 50,
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'STATUS',
					'name' => 'schedulersent_status',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					//'inputWidth' => 500,
					'value' => !empty($params['schedulersentinfo']['schedulersent_status2']) ? strtoupper($params['schedulersentinfo']['schedulersent_status2']) : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'QUEUED',
					'name' => 'schedulersent_sentstamp',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					//'inputWidth' => 500,
					'value' => !empty($params['schedulersentinfo']['schedulersent_sentstamp']) ? pgDate($params['schedulersentinfo']['schedulersent_sentstamp']) : '',
				);

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_schedulerdetailscheduled

		function _form_schedulerdetailsend($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$post = $this->vars['post'];

				$readonly = true;

				$params = array();

				if(!empty($post['method'])&&($post['method']=='schedulernew'||$post['method']=='scheduleredit')) {
					$readonly = false;
				}

				if(!empty($post['rowid'])&&!empty($post['method'])&&$post['method']=='schedulersend'&&!empty($post['scheduler_schedule'])) {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';

					$retval['return_message'] = 'Message successfully scheduled for sending!';							

					if(!($result = $appdb->query("select *,(extract(epoch from '".$post['scheduler_schedule']."'::timestamptz) - extract(epoch from now())) as delaytime from tbl_scheduler where scheduler_id=".$post['rowid']))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						die;				
					}

					if(!empty($result['rows'][0]['scheduler_id'])) {

						//pre(array('$result'=>$result)); die;

						$schedulersent_title = $result['rows'][0]['scheduler_title'];
						$schedulersent_desc = $result['rows'][0]['scheduler_desc'];
						$schedulersent_sms = $result['rows'][0]['scheduler_sms'];

						$schedulersent_sentdate = $post['scheduler_schedule'];

						$schedulersent_delaytime = intval($result['rows'][0]['delaytime']);

						//$promossent_title = $result['rows'][0]['promos_title'];
						//$promossent_desc = $result['rows'][0]['promos_desc'];
						//$promossent_sms = $result['rows'][0]['promos_sms'];
						//$promossent_startdate = $result['rows'][0]['promos_startdate'];
						//$promossent_enddate = $result['rows'][0]['promos_enddate'];

						//pre(array('$result'=>$result));

						//if(!empty($result['rows'][0]['smsinbox_id'])) {
						//	$params['content'] = str_replace("\n",'<br>',$result['rows'][0]['smsinbox_message']);
						//}

						$smscontent = $result['rows'][0]['scheduler_sms'];

						//$smscontent = trim(htmlspecialchars_decode(strip_tags($smscontent)));
						//$smscontent = str_replace('&nbsp;',' ',$smscontent);

						$tosims = trim($post['tosims']);
						$togroups = trim($post['togroups']);
						$tonumbers = trim($post['tonumbers']);

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

								$textmsg = $smscontent; /// . ' ' . getOption('$PROMOCODE_MESSAGE');

								//$promossent_promocode = generatePromoCode();

								//$textmsg = str_replace('%promocode%', $promossent_promocode, $textmsg);

								if(strlen($smscontent)>160) {

									// long sms

									$smsparts = str_split($textmsg,152); 

									$smsoutbox_udhref = dechex_str(mt_rand(100,250)); 

									$smsoutbox_total = count($smsparts); 

									$content = array();
									$content['schedulersent_contactid'] = $contactid;
									$content['schedulersent_contactnumber'] = $contactnumber;
									$content['schedulersent_title'] = $schedulersent_title;
									$content['schedulersent_desc'] = $schedulersent_desc;
									$content['schedulersent_sms'] = $schedulersent_sms;
									$content['schedulersent_sentdate'] = $schedulersent_sentdate;

									if(!($result = $appdb->insert("tbl_schedulersent",$content,"schedulersent_id"))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}

									if(!empty($result['returning'][0]['schedulersent_id'])) {

										$smsoutbox_schedulersentid = $result['returning'][0]['schedulersent_id'];

										$content = array();
										$content['smsoutbox_contactid'] = $contactid;
										$content['smsoutbox_contactnumber'] = $contactnumber;
										$content['smsoutbox_message'] = $textmsg;
										$content['smsoutbox_udhref'] = $smsoutbox_udhref;
										$content['smsoutbox_part'] = $smsoutbox_total;
										$content['smsoutbox_total'] = $smsoutbox_total;
										$content['smsoutbox_simnumber'] = $simnumber;
										$content['smsoutbox_type'] = 1;
										$content['smsoutbox_schedulersentid'] = $smsoutbox_schedulersentid;
										$content['smsoutbox_status'] = 1;
										$content['smsoutbox_delay'] = $schedulersent_delaytime;

										if(!($result = $appdb->insert("tbl_smsoutbox",$content,"smsoutbox_id"))) {
											json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
											die;				
										}

									}

								} else {

									// short sms

									$content = array();
									$content['schedulersent_contactid'] = $contactid;
									$content['schedulersent_contactnumber'] = $contactnumber;
									$content['schedulersent_title'] = $schedulersent_title;
									$content['schedulersent_desc'] = $schedulersent_desc;
									$content['schedulersent_sms'] = $schedulersent_sms;
									$content['schedulersent_sentdate'] = $schedulersent_sentdate;

									if(!($result = $appdb->insert("tbl_schedulersent",$content,"schedulersent_id"))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}

									if(!empty($result['returning'][0]['schedulersent_id'])) {

										$smsoutbox_schedulersentid = $result['returning'][0]['schedulersent_id'];

										$content = array();
										$content['smsoutbox_contactid'] = $contactid;
										$content['smsoutbox_contactnumber'] = $contactnumber;
										$content['smsoutbox_message'] = $textmsg;
										$content['smsoutbox_simnumber'] = $simnumber;
										$content['smsoutbox_part'] = 1;
										$content['smsoutbox_total'] = 1;
										$content['smsoutbox_schedulersentid'] = $smsoutbox_schedulersentid;
										$content['smsoutbox_status'] = 1;
										$content['smsoutbox_delay'] = $schedulersent_delaytime;

										if(!($result = $appdb->insert("tbl_smsoutbox",$content,"smsoutbox_id"))) {
											json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
											die;				
										}

									}
								}

							} // foreach($vrecipients as $contactid=>$vv) {

						}

						json_encode_return($retval);
						die;
					}

				} //if($this->vars['post']['method']=='messagingsendtooutbox'||$this->vars['post']['method']=='messagingsendnow') {



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

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbDetails'] = array();

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'Schedule',
				);

				$params['tbDetails'][] = array(
					'type' => 'calendar',
					//'label' => 'START DATE',
					'name' => 'scheduler_schedule',
					'readonly' => true,
					'required' => !$readonly,
					'enableTime' => true,
					'enableTodayButton' => true,
					'calendarPosition' => 'right',
					'dateFormat' => '%m-%d-%Y %H:%i',
					'validate' => "NotEmpty",
					//'value' => !empty($params['promosinfo']['promossent_startdate']) ? $params['promosinfo']['promossent_startdate'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'newcolumn',
					'offset' => 20,
				);

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

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_schedulerdetailsend

		function _form_schedulerdetailsent($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$post = $this->vars['post'];

				$readonly = true;

				$params = array();

				if(!empty($post['method'])&&($post['method']=='schedulernew'||$post['method']=='scheduleredit')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&$post['method']=='onrowselect') {
					if(!empty($post['rowid'])&&is_numeric($post['rowid'])&&$post['rowid']>0) {
						if(!($result = $appdb->query("select *,case when schedulersent_status=0 then 'queued' when schedulersent_status=1 then 'waiting' when schedulersent_status=3 then 'sending' when schedulersent_status=4 then 'sent' when schedulersent_status=5 then 'failed' end as schedulersent_status2 from tbl_schedulersent where schedulersent_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['schedulersent_id'])) {
							$params['schedulersentinfo'] = $result['rows'][0];
						}
					}
				} else
				if(!empty($post['method'])&&$post['method']=='schedulerdelete') {

					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Message successfully deleted!';

					if(!empty($this->vars['post']['rowids'])) {

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

							if(!($result = $appdb->query("delete from tbl_schedulersent where schedulersent_id in (".$rowids.")"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!($result = $appdb->query("delete from tbl_smsoutbox where smsoutbox_schedulersentid in (".$rowids.")"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							json_encode_return($retval);
							die;
						}

					}

					if(!empty($post['rowid'])) {
						if(!($result = $appdb->query("delete from tbl_schedulersent where schedulersent_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
						if(!($result = $appdb->query("delete from tbl_smsoutbox where smsoutbox_schedulersentid=".$post['rowid']))) {
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
					'label' => 'NAME',
					'name' => 'schedulersent_contactnumber',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => getContactNickByID($params['schedulersentinfo']['schedulersent_contactid']),
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'CONTACT',
					'name' => 'schedulersent_contactnumber',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => !empty($params['schedulersentinfo']['schedulersent_contactnumber']) ? $params['schedulersentinfo']['schedulersent_contactnumber'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TITLE',
					'name' => 'schedulersent_title',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => !empty($params['schedulersentinfo']['schedulersent_title']) ? $params['schedulersentinfo']['schedulersent_title'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'DESCRIPTION',
					'name' => 'schedulersent_desc',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => !empty($params['schedulersentinfo']['schedulersent_desc']) ? $params['schedulersentinfo']['schedulersent_desc'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TEXT MESSAGE',
					'name' => 'schedulersent_sms',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['schedulersentinfo']['schedulersent_sms']) ? $params['schedulersentinfo']['schedulersent_sms'] : '',
					'rows' => 5,
					'inputWidth' => 500,
				);

				$params['tbDetails'][] = array(
					'type' => 'newcolumn',
					'offset' => 50,
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'STATUS',
					'name' => 'schedulersent_status',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					//'inputWidth' => 500,
					'value' => !empty($params['schedulersentinfo']['schedulersent_status2']) ? strtoupper($params['schedulersentinfo']['schedulersent_status2']) : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'SENT',
					'name' => 'schedulersent_sentstamp',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					//'inputWidth' => 500,
					'value' => !empty($params['schedulersentinfo']['schedulersent_sentstamp']) ? pgDate($params['schedulersentinfo']['schedulersent_sentstamp']) : '',
				);

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_schedulerdetailsent

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
					if($this->post['table']=='template') {
						if(!($result = $appdb->query("select * from tbl_scheduler order by scheduler_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['scheduler_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['scheduler_id'],'data'=>array(0,$v['scheduler_id'],$v['scheduler_title'],$v['scheduler_desc'],$v['scheduler_sms'],pgDate($v['scheduler_createstamp']),pgDate($v['scheduler_updatestamp'])));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='scheduled') {
						if(!($result = $appdb->query("select *,(extract(epoch from schedulersent_sentdate::timestamptz) - extract(epoch from now())) as elapsedtime,case when schedulersent_status=0 then 'queued' when schedulersent_status=1 then 'waiting' when schedulersent_status=3 then 'sending' when schedulersent_status=4 then 'sent' when schedulersent_status=5 then 'failed' end as schedulersent_status2 from tbl_schedulersent where schedulersent_status in (0,1,2,3,5) order by schedulersent_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['schedulersent_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {

								if($v['elapsedtime']>0) {
									$stt = secondsToTime(intval($v['elapsedtime']));

									$day = $stt['days'];
									$hour = $stt['hours'];
									$min = $stt['minutes'];
									$sec = $stt['seconds'];

									$str = '';

									if(!empty($day)) {
										$str .= $day.'day(s) ';
									}

									if(!empty($hour)) {
										$str .= $hour.'hr(s) ';
									}

									if(!empty($min)) {
										$str .= $min.'min(s) ';
									}

									if(!empty($sec)) {
										$str .= $sec.'sec(s) ';
									}
								} else {
									$stt = 'Expired';
								}

								$rows[] = array('id'=>$v['schedulersent_id'],'data'=>array(0,$v['schedulersent_id'],getContactNickByID($v['schedulersent_contactid']),$v['schedulersent_contactnumber'],$v['schedulersent_title'],$v['schedulersent_desc'],$v['schedulersent_sms'],$v['schedulersent_status2'],$str,pgDate($v['schedulersent_sentstamp'])));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='send') {
						if(!($result = $appdb->query("select * from tbl_scheduler order by scheduler_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['scheduler_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['scheduler_id'],'data'=>array(0,$v['scheduler_id'],$v['scheduler_title'],$v['scheduler_desc'],$v['scheduler_sms'],pgDate($v['scheduler_createstamp']),pgDate($v['scheduler_updatestamp'])));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='sent') {
						if(!($result = $appdb->query("select *,case when schedulersent_status=0 then 'queued' when schedulersent_status=1 then 'waiting' when schedulersent_status=3 then 'sending' when schedulersent_status=4 then 'sent' when schedulersent_status=5 then 'failed' end as schedulersent_status2 from tbl_schedulersent where schedulersent_status=4 order by schedulersent_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['schedulersent_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['schedulersent_id'],'data'=>array(0,$v['schedulersent_id'],getContactNickByID($v['schedulersent_contactid']),$v['schedulersent_contactnumber'],$v['schedulersent_title'],$v['schedulersent_desc'],$v['schedulersent_sms'],$v['schedulersent_status2'],pgDate($v['schedulersent_sentstamp'])));
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

	$appappscheduler = new APP_app_scheduler;
}

# eof modules/app.user