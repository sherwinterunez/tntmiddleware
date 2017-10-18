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

if(!class_exists('APP_app_tnt')) {

	class APP_app_tnt extends APP_Base_Ajax {
	
		var $desc = 'tnt';

		var $pathid = 'tnt';
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

			$appaccess->rules($this->desc,'TNT Module');
			$appaccess->rules($this->desc,'TNT Module New');
			$appaccess->rules($this->desc,'TNT Module Edit');
			$appaccess->rules($this->desc,'TNT Module Delete');

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

		function _form_eloadmainsend($routerid=false,$formid=false) {
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
			
		} // _form_eloadmainsend

		function _form_eloaddetailsend($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$readonly = true;

				$params = array();

				if(!empty($this->vars['post']['method'])&&($this->vars['post']['method']=='eloadedit'||$this->vars['post']['method']=='onrowselect')) {

					if($this->vars['post']['method']=='eloadedit') {
						$readonly = false;
					}

					$params['productinfo'] = array();

					if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {
						if(!($result = $appdb->query("select * from tbl_eloadproduct where eloadproduct_id=".$this->vars['post']['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['eloadproduct_id'])) {
							$params['productinfo'] = $result['rows'][0];
						}
					}

				} else
				if(!empty($this->vars['post']['method'])&&$this->vars['post']['method']=='eloadsend') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'eLoad request sent!';

					//pre(array('$vars'=>$this->vars));

					$gateway = getOption('$ELOAD_GATEWAY',false);

					if(!$gateway) {
						$retval['return_message'] = 'No $ELOAD_GATEWAY configured!';
						json_encode_return($retval);
						die;						
					}

					// sendToOutBox($contactnumber=false,$simnumber=false,$message=false,$status=1,$delay=0,$eload=0)

					if(($simnumber = getLoader(1))&&!empty($this->vars['post']['eloadtransaction_mobileno'])&&!empty($this->vars['post']['eloadtransaction_productcode'])) {
						$message = $this->vars['post']['eloadtransaction_mobileno'].'#'.$this->vars['post']['eloadtransaction_productcode'];
						if(sendToOutBox($gateway,$simnumber[0],$message,1,0,1)) {
							$content = array();
							$content['eloadtransaction_productcode'] = !empty($this->vars['post']['eloadtransaction_productcode']) ? $this->vars['post']['eloadtransaction_productcode'] : '';
							$content['eloadtransaction_productdesc'] = !empty($this->vars['post']['eloadtransaction_productdesc']) ? $this->vars['post']['eloadtransaction_productdesc'] : '';
							$content['eloadtransaction_provider'] = !empty($this->vars['post']['eloadtransaction_provider']) ? $this->vars['post']['eloadtransaction_provider'] : '';
							$content['eloadtransaction_cost'] = !empty($this->vars['post']['eloadtransaction_cost']) ? $this->vars['post']['eloadtransaction_cost'] : '';
							$content['eloadtransaction_mobileno'] = !empty($this->vars['post']['eloadtransaction_mobileno']) ? $this->vars['post']['eloadtransaction_mobileno'] : '';
							$content['eloadtransaction_simnumber'] = !empty($simnumber[0]) ? $simnumber[0] : '';
							$content['eloadtransaction_gateway'] = $gateway;
							$content['eloadtransaction_message'] = $message;

							if(!($result = $appdb->insert("tbl_eloadtransaction",$content,"eloadtransaction_id"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['returning'][0]['eloadtransaction_id'])) {
								//$retval['rowid'] = $result['returning'][0]['eloadproduct_id'];
							}

						}
					} else {
						$retval['return_message'] = 'No SIM loader configured!';						
					}

					json_encode_return($retval);
					die;
				}

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbDetails'] = array();

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'PRODUCT CODE',
					'name' => 'eloadtransaction_productcode',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					//'labelAlign' => $position,
					'value' => !empty($params['productinfo']['eloadproduct_code']) ? $params['productinfo']['eloadproduct_code'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'DESCRIPTION',
					'name' => 'eloadtransaction_productdesc',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'inputWidth' => 500,
					'value' => !empty($params['productinfo']['eloadproduct_desc']) ? $params['productinfo']['eloadproduct_desc'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'PROVIDER',
					'name' => 'eloadtransaction_provider',
					'readonly' => true,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['productinfo']['eloadproduct_provider']) ? $params['productinfo']['eloadproduct_provider'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'COST',
					'name' => 'eloadtransaction_cost',
					'readonly' => $readonly,
					'required' => !$readonly,
					'validate' => "NotEmpty",
					'value' => !empty($params['productinfo']['eloadproduct_cost']) ? $params['productinfo']['eloadproduct_cost'] : '',
				);

				$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'MOBILE NO',
					'name' => 'eloadtransaction_mobileno',
					'readonly' => false,
					'required' => true,
					'maxLength' => 11,
				);

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_eloaddetailsend

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
					if($this->post['table']=='product') {
/*
    eloadproduct_id bigint DEFAULT nextval(('tbl_eloadproduct_seq'::text)::regclass) NOT NULL,
    eloadproduct_code text DEFAULT ''::text NOT NULL,
    eloadproduct_desc text DEFAULT ''::text NOT NULL,
    eloadproduct_provider character varying(20) DEFAULT ''::text NOT NULL,
    eloadproduct_cost numeric NOT NULL DEFAULT 0.00,
    eloadproduct_status integer NOT NULL DEFAULT 0,
    eloadproduct_disabled integer NOT NULL DEFAULT 0,
    eloadproduct_flag integer NOT NULL DEFAULT 0, 
    eloadproduct_createstamp timestamp with time zone DEFAULT now(),
    eloadproduct_updatestamp timestamp with time zone DEFAULT now()

*/
						if(!($result = $appdb->query("select eloadproduct_id,eloadproduct_code,eloadproduct_desc,eloadproduct_provider,eloadproduct_cost,eloadproduct_disabled,eloadproduct_createstamp,eloadproduct_updatestamp from tbl_eloadproduct order by eloadproduct_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['eloadproduct_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['eloadproduct_id'],'data'=>array(0,$v['eloadproduct_id'],$v['eloadproduct_code'],$v['eloadproduct_desc'],$v['eloadproduct_provider'],$v['eloadproduct_cost'],$v['eloadproduct_disabled'],pgDate($v['eloadproduct_createstamp']),pgDate($v['eloadproduct_updatestamp'])));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='send') {
/*
    eloadproduct_id bigint DEFAULT nextval(('tbl_eloadproduct_seq'::text)::regclass) NOT NULL,
    eloadproduct_code text DEFAULT ''::text NOT NULL,
    eloadproduct_desc text DEFAULT ''::text NOT NULL,
    eloadproduct_provider character varying(20) DEFAULT ''::text NOT NULL,
    eloadproduct_cost numeric NOT NULL DEFAULT 0.00,
    eloadproduct_status integer NOT NULL DEFAULT 0,
    eloadproduct_disabled integer NOT NULL DEFAULT 0,
    eloadproduct_flag integer NOT NULL DEFAULT 0, 
    eloadproduct_createstamp timestamp with time zone DEFAULT now(),
    eloadproduct_updatestamp timestamp with time zone DEFAULT now()

*/
						if(!($result = $appdb->query("select eloadproduct_id,eloadproduct_code,eloadproduct_desc,eloadproduct_provider,eloadproduct_cost,eloadproduct_disabled,eloadproduct_createstamp,eloadproduct_updatestamp from tbl_eloadproduct where eloadproduct_disabled=0 order by eloadproduct_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['eloadproduct_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['eloadproduct_id'],'data'=>array(0,$v['eloadproduct_id'],$v['eloadproduct_code'],$v['eloadproduct_desc'],$v['eloadproduct_provider'],$v['eloadproduct_cost'],$v['eloadproduct_disabled'],pgDate($v['eloadproduct_createstamp']),pgDate($v['eloadproduct_updatestamp'])));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='inbox') {
						if(!($result = $appdb->query("select *,extract(epoch from smsinbox_timestamp) as unixstamp from tbl_smsinbox where smsinbox_deleted=0 and smsinbox_eload=1 order by smsinbox_id desc limit ".getOption('$INBOX_MAX_RESULT',1000)))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['smsinbox_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['smsinbox_id'],'unread'=>$v['smsinbox_unread'],'data'=>array(0,$v['smsinbox_id'],$v['smsinbox_contactnumber'],getSimNameByNumber($v['smsinbox_simnumber']),$v['smsinbox_message'],getNetworkName($v['smsinbox_contactnumber']),pgDate($v['smsinbox_timestamp'])));
							}

							$retval = array('rows'=>$rows);
						}
					} else
					if($this->post['table']=='outbox') {
						if(!($result = $appdb->query("select smsoutbox_id,smsoutbox_contactnumber,smsoutbox_simnumber,smsoutbox_part,smsoutbox_total,case when smsoutbox_type=0 then 'short' when smsoutbox_type=1 then 'long' end as smsoutbox_type,smsoutbox_message,case when smsoutbox_status=0 then 'queued' when smsoutbox_status=1 then 'waiting' when smsoutbox_status=3 then 'sending' when smsoutbox_status=4 then 'sent' when smsoutbox_status=5 then 'failed' end as smsoutbox_status,smsoutbox_createstamp,smsoutbox_sentstamp from tbl_smsoutbox where smsoutbox_eload=1 and smsoutbox_deleted=0 and smsoutbox_delay=0 order by smsoutbox_id desc limit ".getOption('$OUTBOX_MAX_RESULT',1000)))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['smsoutbox_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['smsoutbox_id'],'data'=>array(0,$v['smsoutbox_id'],$v['smsoutbox_contactnumber'],getSimNameByNumber($v['smsoutbox_simnumber']),$v['smsoutbox_total'],$v['smsoutbox_type'],$v['smsoutbox_message'],$v['smsoutbox_status'],pgDate($v['smsoutbox_createstamp']),pgDate($v['smsoutbox_sentstamp'])));
							}

							$retval = array('rows'=>$rows);
						}
					} else
					if($this->post['table']=='transaction') {

/*
sherwint_sms101=# \d tbl_eloadtransaction
                                                Table "public.tbl_eloadtransaction"
            Column             |           Type           |                               Modifiers                                
-------------------------------+--------------------------+------------------------------------------------------------------------
 eloadtransaction_id           | bigint                   | not null default nextval(('tbl_eloadtransaction_seq'::text)::regclass)
 eloadtransaction_productcode  | text                     | not null default ''::text
 eloadtransaction_productdesc  | text                     | not null default ''::text
 eloadtransaction_confirmation | text                     | not null default ''::text
 eloadtransaction_reference    | text                     | not null default ''::text
 eloadtransaction_provider     | character varying(20)    | not null default ''::text
 eloadtransaction_mobileno     | character varying(20)    | not null default ''::text
 eloadtransaction_simnumber    | character varying(20)    | not null default ''::text
 eloadtransaction_gateway      | character varying(20)    | not null default ''::text
 eloadtransaction_cost         | numeric                  | not null default 0.00
 eloadtransaction_actualcost   | numeric                  | not null default 0.00
 eloadtransaction_balance      | numeric                  | not null default 0.00
 eloadtransaction_completed    | integer                  | not null default 0
 eloadtransaction_attempt      | integer                  | not null default 0
 eloadtransaction_status       | integer                  | not null default 0
 eloadtransaction_disabled     | integer                  | not null default 0
 eloadtransaction_flag         | integer                  | not null default 0
 eloadtransaction_createstamp  | timestamp with time zone | default now()
 eloadtransaction_updatestamp  | timestamp with time zone | default now()
 eloadtransaction_message      | text                     | not null default ''::text
Indexes:
    "tbl_eloadtransaction_primary_key" PRIMARY KEY, btree (eloadtransaction_id)

sherwint_sms101=# 

*/

						if(!($result = $appdb->query("select * from tbl_eloadtransaction order by eloadtransaction_id desc limit ".getOption('$OUTBOX_MAX_RESULT',1000)))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['eloadtransaction_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['eloadtransaction_id'],'data'=>array(0,$v['eloadtransaction_id'],$v['eloadtransaction_productcode'],$v['eloadtransaction_mobileno'],$v['eloadtransaction_message'],$v['eloadtransaction_confirmation'],$v['eloadtransaction_reference'],$v['eloadtransaction_cost'],$v['eloadtransaction_actualcost'],$v['eloadtransaction_balance'],$v['eloadtransaction_completed'],pgDate($v['eloadtransaction_createstamp']),pgDate($v['eloadtransaction_updatestamp'])));
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

	$appapptnt = new APP_app_tnt;
}

# eof modules/app.user