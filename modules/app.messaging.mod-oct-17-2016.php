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

if(!class_exists('APP_app_messaging')) {

define ("DEVICE_NOTSET", 0);
define ("DEVICE_SET", 1);
define ("DEVICE_OPENED", 2);

	class APP_app_messaging extends APP_Base_Ajax {
	
		var $desc = 'Messaging';

		var $pathid = 'messaging';
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

			$appaccess->rules($this->desc,'Compose');

			$appaccess->rules($this->desc,'Contacts');
			$appaccess->rules($this->desc,'Contacts New');
			$appaccess->rules($this->desc,'Contacts Edit');
			$appaccess->rules($this->desc,'Contacts Delete');

			$appaccess->rules($this->desc,'Groups');
			$appaccess->rules($this->desc,'Groups New');
			$appaccess->rules($this->desc,'Groups Edit');
			$appaccess->rules($this->desc,'Groups Delete');

			$appaccess->rules($this->desc,'Inbox');
			$appaccess->rules($this->desc,'Inbox Delete');

			$appaccess->rules($this->desc,'Outbox');
			$appaccess->rules($this->desc,'Sent');
			$appaccess->rules($this->desc,'Sent Delete');

			$appaccess->rules($this->desc,'Networks');
			$appaccess->rules($this->desc,'Networks New');
			$appaccess->rules($this->desc,'Networks Edit');
			$appaccess->rules($this->desc,'Networks Delete');

			$appaccess->rules($this->desc,'SIM');
			$appaccess->rules($this->desc,'SIM Edit');
			$appaccess->rules($this->desc,'SIM Delete');

			$appaccess->rules($this->desc,'Options');
			$appaccess->rules($this->desc,'Options New');
			$appaccess->rules($this->desc,'Options Edit');
			$appaccess->rules($this->desc,'Options Clone');
			$appaccess->rules($this->desc,'Options Delete');

			$appaccess->rules($this->desc,'AutoReply');
			$appaccess->rules($this->desc,'AutoReply New');
			$appaccess->rules($this->desc,'AutoReply Edit');
			$appaccess->rules($this->desc,'AutoReply Clone');
			$appaccess->rules($this->desc,'AutoReply Delete');

			$appaccess->rules($this->desc,'SMS Commands');
			$appaccess->rules($this->desc,'SMS Commands New');
			$appaccess->rules($this->desc,'SMS Commands Edit');
			$appaccess->rules($this->desc,'SMS Commands Clone');
			$appaccess->rules($this->desc,'SMS Commands Delete');

			$appaccess->rules($this->desc,'MODEM Commands');
			$appaccess->rules($this->desc,'MODEM Commands New');
			$appaccess->rules($this->desc,'MODEM Commands Edit');
			$appaccess->rules($this->desc,'MODEM Commands Clone');
			$appaccess->rules($this->desc,'MODEM Commands Delete');
		}

		function parseMobileNo($mno=false,$regx = '(\d+)(\d{3})(\d{7})$') {
			if(!empty($mno)) {
			} else return false;

			if(preg_match('#'.$regx.'#',$mno,$matches)) {
				//print_r(array('$mno'=>$mno,'$matches'=>$matches));

				//return $matches;
				return array('orig'=>$mno,'country'=>$matches[1],'network'=>$matches[2],'number'=>$matches[3]);
			}

			return false;
		}

		function dechex_str($ref) { 
			$hex = ($ref <= 15 )?'0'.dechex($ref):dechex($ref);
			return strtoupper($hex); 
		} 

		function insertGroupContact($groupid=false,$contactid=false) {
			global $appdb;

			if(!empty($groupid)&&!empty($contactid)) {
			} else return false;

			if(!($result = $appdb->query("select group_id from tbl_group where group_id=".$groupid))) {
				return false;
			}

			if(!empty($result['rows'][0]['group_id'])) {
			} else return false;

			if(!($result = $appdb->query("select contact_id from tbl_contact where contact_id=".$contactid))) {
				return false;
			}

			if(!empty($result['rows'][0]['contact_id'])) {
			} else return false;

			$content = array('groupcontact_groupid'=>$groupid,'groupcontact_contactid'=>$contactid);

			if(!($result = $appdb->insert("tbl_groupcontact",$content,"groupcontact_id"))) {
				return false;
			}

			if(!empty($result['returning'][0]['groupcontact_id'])) {
				return $result['returning'][0]['groupcontact_id'];
			}

			return false;
		}

		function getContactNumber($contactid=false) {
			global $appdb;

			if(!empty($contactid)&&is_numeric($contactid)) {
			} else return false;

			$sql = "select contact_number from tbl_contact where contact_id=".$contactid;

			//pre(array('$sql'=>$sql));

			if(!($result = $appdb->query($sql))) {
				return false;
			}

			if(!empty($result['rows'][0]['contact_number'])) {
				return $result['rows'][0]['contact_number'];
			}
			return false;
		}

		function getContactIDFromInbox($id=false) {
			global $appdb;

			if(!empty($id)&&is_numeric($id)) {
			} else return false;

			$sql = "select smsinbox_contactsid from tbl_smsinbox where smsinbox_id=".$id;

			//pre(array('$sql'=>$sql));

			if(!($result = $appdb->query($sql))) {
				return false;
			}

			if(!empty($result['rows'][0]['smsinbox_contactsid'])) {
				//pre(array('$result'=>$result['rows']));
				return $result['rows'][0]['smsinbox_contactsid'];
			}
			return false;
		}

		function getContactIDByNumber($number=false) {
			global $appdb;

			if(!empty($number)) {
			} else return false;

			/*$number = strrev($number);

			if(strlen($number)>=10) {
				$number = strrev(substr($number, 0, 10));
			} else {
				$number = strrev($number);
			}*/

			$parsedMobileNo=$this->parseMobileNo($number);

			//pre(array('$parsedMobileNo'=>$parsedMobileNo));

			if($parsedMobileNo) {

				$number = $parsedMobileNo['network'] . $parsedMobileNo['number'];

				$sql = "select contact_id from tbl_contact where contact_number like '%".$number."'";

			} else {

				$sql = "select contact_id from tbl_contact where contact_number='".$number."'";

			}


			//pre(array('$sql'=>$sql));

			if(!($result = $appdb->query($sql))) {
				return false;
			}

			if(!empty($result['rows'][0]['contact_id'])) {
				return $result['rows'][0]['contact_id'];
			}
			return false;
		}

		function getAllContacts($contactsonly=false) {
			global $appdb;

			$sql = "select * from tbl_contact where contact_deleted=0";

			if(!($result = $appdb->query($sql))) {
				return false;
			}

			if($contactsonly) {
				$contacts = array();

				if(!empty($result['rows'][0]['contact_id'])) {
					foreach($result['rows'] as $k=>$v) {
						$contacts[] = $v['contact_number'];
					}

					if(!empty($contacts)) {
						return $contacts;
					}

					return false;
				}
			}

			if(!empty($result['rows'][0]['contact_id'])) {
				return $result['rows'];
			}

			return false;
		}

		function getGroup($id=false) {
			global $appdb;

			$sql = "select * from tbl_group";

			if(!empty($id)&&is_numeric($id)) {
				$sql .= " where group_id=".$id;
			}

			if(!($result = $appdb->query($sql))) {
				return array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries);
			}

			if(!empty($result['rows'][0]['group_id'])) {
				if(!empty($id)) {
					return $result['rows'][0];
				}
				return $result['rows'];
			}

			return false;
		}

		function getAllPorts() {
			global $appdb;

			if(!($result = $appdb->query("select * from tbl_port where port_disabled=0 and port_deleted=0"))) {
				return false;
			}

			//pre(array($result)); die;

			if(!empty($result['rows'][0]['port_id'])) {
				return $result['rows'];
			}

			return false;
		}

		function getAllSims($mode=0) {
			return getAllSims($mode);
		}

		function getAllContactsCount() {
			global $appdb;

			if(!($result = $appdb->query("select count(contact_id) from tbl_contact where contact_deleted=0"))) {
				return false;
			}

			if(!empty($result['rows'][0]['count'])) {
				return $result['rows'][0]['count'];
			}

			return false;
		}

		function getAllGroups() {
			global $appdb;

			if(!($result = $appdb->query("select * from tbl_group where group_deleted=0"))) {
				return false;
			}

			if(!empty($result['rows'][0]['group_id'])) {
				return $result['rows'];
			}

			return false;
		}

		function getAllGroupsWithMembers() {

			$agroups = $this->getAllGroups();

			$groups = array();

			if(is_array($agroups)&&!empty($agroups[0]['group_id'])) {

				foreach($agroups as $k=>$v) {
					if($groupid=$this->getNetworkGroupIDFromName($v['group_name'])) {
						$memberscount = $this->getGroupMembersCount($groupid);
						if(!empty($memberscount)) {
							$groups[] = $v['group_name'];
						}
					}
				}

			}

			if(!empty($groups)) {
				return $groups;
			}

			return false;
		}

		function getGroupMembersCount($groupid=false) {
			global $appdb;

			if(!empty($groupid)&&is_numeric($groupid)) {
			} else return 0;

			if(!($result = $appdb->query("select count(groupcontact_id) from tbl_groupcontact where groupcontact_groupid=".$groupid))) {
				return 0;
			}

			if(!empty($result['rows'][0]['count'])) {
				return $result['rows'][0]['count'];
			}

			return 0;
		}

		function getGroupID($groupname=false) {
			global $appdb;

			if(!empty($groupname)) {
			} else return false;

			$sql = "select * from tbl_group where group_name='".$groupname."'";

			//pre(array('$sql'=>$sql));

			if(!($result = $appdb->query($sql))) {
				return false;
			}

			//pre(array('$result'=>$result));

			if(!empty($result['rows'][0]['group_id'])) {
				return $result['rows'][0]['group_id'];
			}
			return false;
		}

		function getGroupNameByID($id=false) {
			global $appdb;

			if(!empty($id)) {
			} else return false;

			if(is_numeric($id)) {
				$sql = 'select group_name from tbl_group where group_id='.$id;

				if(!($result = $appdb->query($sql))) {
					return false;
				}

				//pre(array('$result'=>$result));

				if(!empty($result['rows'][0]['group_name'])) {
					return $result['rows'][0]['group_name'];
				}
			}

			return false;
		}

		function getGroupNamesByArrayOfIDs($ids=array()) {
			global $appdb;

			if(!empty($ids)) {
			} else return false;

			$groupNames = array();

			foreach($ids as $v) {
				$v = intval(trim($v));
				if(is_numeric($v)) {
					if(!empty($gname = $this->getGroupNameByID($v))) {
						$groupNames[] = $gname;
					}
				}
			}

			if(!empty($groupNames)) {
				return $groupNames;
			}

			return false;
		}

		function getGroupMembersByName($groupname=false) {
			global $appdb;

			if(!empty($groupname)) {
			} else return false;

			//pre(array('$groupname'=>$groupname));

			$groupid = $this->getGroupID($groupname);

			if(!$groupid) return false;

			//pre(array('$groupid'=>$groupid));

			$groupmembers = $this->getGroupMembers($groupid);

			if(is_array($groupmembers)) {
				return $groupmembers;
			}

			return false;
		}

		function getGroupMembers($groupid=false) {
			global $appdb;

			if(!empty($groupid)&&is_numeric($groupid)) {
			} else return false;

			$sql = "select * from tbl_groupcontact where groupcontact_groupid=".$groupid;

			if(!($result = $appdb->query($sql))) {
				return false;
			}

			if(!empty($result['rows'][0]['groupcontact_id'])) {
				return $result['rows'];
			}
			return false;
		}

		function getMembersGroups($id=false) {
			global $appdb;

			if(!empty($id)&&is_numeric($id)) {
			} else return false;

			$sql = "select * from tbl_groupcontact where groupcontact_contactid=".$id;

			if(!($result = $appdb->query($sql))) {
				return array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries);
			}

			if(!empty($result['rows'][0]['groupcontact_id'])) {
				//return $result['rows'];

				$groups = array();

				foreach($result['rows'] as $k=>$v) {
					if(!($group = $this->getGroup($v['groupcontact_groupid']))) {
						return array('error_code'=>125,'error_message'=>'Error in SQL execution.');
					}

					$groups[] = array('id'=>$group['group_id'],'name'=>$group['group_name'],'desc'=>$group['group_desc']);
				}

				if(!empty($groups)) {
					return $groups;
				}
			}

			return false;
		}

		function getNetworkGroupIDFromName($netname=false) {
			global $appdb;

			if(!empty($netname)) {
			} else return false;

			$sql = "select group_id from tbl_group where group_name='".$netname."'";

			if(!($result = $appdb->query($sql))) {
				return false;
			}

			if(!empty($result['rows'][0]['group_id'])) {
				return $result['rows'][0]['group_id'];
			}

			if(!($result = $appdb->insert("tbl_group",array('group_name'=>$netname,'group_desc'=>'Group for '.$netname,'group_flag'=>1),'group_id'))) {
				return false;
			}

			if(!empty($result['returning'][0]['group_id'])) {
				return $result['returning'][0]['group_id'];
			}

			return false;
		}

		function getNetworkGroupID($number=false) {
			global $appdb;

			if(!empty($number)) {
			} else return false;

			$number = trim($number);

			if(preg_match("#^\+\d+$#",$number,$matches)) {
				//pre(array('$matches1'=>$matches));
			} else
			if(preg_match("#^\d+#",$number,$matches)) {
				//pre(array('$matches2'=>$matches));
			} else return false;

			$netname = $this->getNetworkName($number);

			return $this->getNetworkGroupIDFromName($netname);

			/*$sql = "select group_id from tbl_group where group_name='".$netname."'";

			if(!($result = $appdb->query($sql))) {
				return false;
			}

			if(!empty($result['rows'][0]['group_id'])) {
				return $result['rows'][0]['group_id'];
			}

			if(!($result = $appdb->insert("tbl_group",array('group_name'=>$netname,'group_desc'=>'Group for '.$netname,'group_flag'=>1),'group_id'))) {
				return false;
			}

			if(!empty($result['returning'][0]['group_id'])) {
				return $result['returning'][0]['group_id'];
			}

			return false;*/
		}

		function getNetworkCode($number=false) {

			if(!empty($number)) {
			} else return false;

			$number = trim($number);

			if(preg_match("#^\+\d+$#",$number,$matches)) {
				//pre(array('$matches1'=>$matches));
			} else
			if(preg_match("#^\d+#",$number,$matches)) {
				//pre(array('$matches2'=>$matches));
			} else return false;

			$rnumber = strrev($number);

			if(strlen($rnumber)>=10) {
				$netnum = strrev(substr($rnumber, 7, 3));
				return $netnum;
			}

			return false;
		}


		/*function getNetworkName($number=false) {
			global $appdb;

			if(!empty($number)) {
			} else return false;

			$number = trim($number);

			if(preg_match("#^\+\d+$#",$number,$matches)) {
				//pre(array('$matches1'=>$matches));
			} else
			if(preg_match("#^\d+#",$number,$matches)) {
				//pre(array('$matches2'=>$matches));
			} else return 'Unknown';

			$rnumber = strrev($number);

			if(strlen($rnumber)>=10) {
				$netnum = strrev(substr($rnumber, 7, 3));
				//pre(array('$netnum'=>$netnum));

				$sql = "select network_name from tbl_network where network_number='".$netnum."'";

				if(!($result = $appdb->query($sql))) {
					return 'Error';
				}

				if(!empty($result['rows'][0]['network_name'])) {
					return $result['rows'][0]['network_name'];
				}

			}

			return 'Unknown';
		}*/

		function getNetworkName($number=false) {
			return getNetworkName($number);
		}

		function getAllNetworkName() {
			global $appdb;

			if(!($result = $appdb->query("select distinct network_name from tbl_network"))) {
				return array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries);
			}

			if(!empty($result['rows'][0]['network_name'])) {
				return $result['rows'];
			}

			return false;
		}

		function getNetworkContactCount($number=false) {
			global $appdb;

			if(!empty($number)&&is_numeric($number)) {
			} else return false;

		}

		function getAllSimsName() {
			return getAllSimsName();
		}

		function getSimNameByNumber($number=false) {
			return getSimNameByNumber($number);
		}

		function _xml_explorer($routerid=false,$xmlid=false) {
			global $appdb, $applogin;

			if(!empty($routerid)&&!empty($xmlid)) {
			} else {
				return false;
			}

			//pre(array('$routerid'=>$routerid,'$xmlid'=>$xmlid));

			/*if($applogin->isSystemAdministrator()) {
				if(!($result = $appdb->query("select * from tbl_roles where flag=0 order by role_id asc"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				}
			} else {
				if(!($result = $appdb->query("select * from tbl_roles where flag=0 and role_id!=1 order by role_id asc"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				}
			}

			//pre(array('$result'=>$result));*/

			$result = array(1=>'Compose',2=>'Contacts',3=>'Groups',4=>'Inbox',5=>'Outbox',6=>'Sent');

			$rxml = array();

			foreach($result as $k=>$v) {
				$rxml[] = array(
						'@attributes' => array(
								'text' => $v,
								'id' => strtolower($v),
								//'id' => 'expid_'.$k,
								//'tooltip' => $v['role_desc'],
								//'child' => 1,
							),
						//'item' => $this->_xml_usermanagementcontrol_get_users($v['role_id']),
					);
			}

			$axml = array(
				'@attributes' => array(
					'id' => '0',
				),
				'item' => array(
					'@attributes' => array(
						'text' => 'Menus',
						'id' => 'menus',
						'open' => 1,
					),
					'item' => $rxml,
				),
			);

			$xml = Array2XML::createXML('tree', $axml);

			return $xml->saveXML();;
		}

		function _form_messagingmainserver($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				//pre(array('$vars'=>$this->vars));

				if(!empty($this->vars['post']['method'])) {

					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Contact successfully deleted!';

					$curl = new MyCurl;

					if($this->vars['post']['method']=='serverstart') {
						if($curl->get('http://127.0.0.1:8080/resume')) {
							$retval['return_message'] = 'Server started/resumed.';							
						} else {
							$retval['return_message'] = 'Can\'t connect to server.';							
						}
					} else
					if($this->vars['post']['method']=='serverstop') {
						if($curl->get('http://127.0.0.1:8080/pause')) {
							$retval['return_message'] = 'Server stopped/paused.';							
						} else {
							$retval['return_message'] = 'Can\'t connect to server.';							
						}
					} else
					if($this->vars['post']['method']=='serverscan') {
						if($curl->get('http://127.0.0.1:8080/portcheck')) {
							$retval['return_message'] = 'Server portcheck started.';
						} else {
							$retval['return_message'] = 'Can\'t connect to server.';							
						}
					} else
					if($this->vars['post']['method']=='serverrestart') {
						if($curl->get('http://127.0.0.1:8080/terminate')) {
							$retval['return_message'] = 'Server restarted.';
						} else {
							$retval['return_message'] = 'Can\'t connect to server.';							
						}
					} else
					if($this->vars['post']['method']=='serverstatus') {
						if($cont = $curl->get('http://127.0.0.1:8080/status')) {
							$status = 'unknown';
							if(!empty($cont['content'])) {
								$status = $cont['content'];
							}
							$retval['return_message'] = "Server is $status";
							$retval['status'] = $status;							
						} else {
							$retval['return_message'] = "Can't connect to server";
							$retval['status'] = 'unknown';							
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
			
		} // _form_messagingmainserver

		function _form_messagingmaincompose($routerid=false,$formid=false,$flag=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();
				$params['contacts'] = array();
				$params['groups'] = array();
				$params['ports'] = array();
				$params['composeto'] = '';
				$params['composetogroups'] = '';

				//$params = array('hello'=>'sherwin');

				if(!empty($this->vars['post']['rowids'])) {

					$rowids = explode(',', $this->vars['post']['rowids']);

					$arowid = array();

					for($i=0;$i<count($rowids);$i++) {
						$rowid = intval(trim($rowids[$i]));
						if(!empty($rowid)) {
							$arowid[] = $rowid;
						}
					}
				}

				if(!($result = $appdb->query("select * from tbl_contact where contact_deleted=0"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;				
				}

				if(!empty($result['rows'][0]['contact_id'])) {
					//$params['contacts'] = $result['rows'] ;

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

						$checked = false;

						if($flag=='reply'&&!empty($this->vars['post']['rowid'])&&$v['contact_id']==$this->getContactIDFromInbox($this->vars['post']['rowid'])) {
							$checked = true;
							$params['reply'] = $v['contact_number'];
						}

						if(!empty($this->vars['post']['from'])&&$this->vars['post']['from']=='contacts') {
							if(empty($arowid)&&!empty($this->vars['post']['rowid'])&&$v['contact_id']==$this->vars['post']['rowid']) {
								$checked = true;
								$params['composeto'] = $v['contact_number'];
							} else
							if(!empty($arowid)&&in_array($v['contact_id'], $arowid)) {
								$checked = true;
								$params['composeto'] .= $v['contact_number'] . ';';
							}
						}

						$params['contacts'][] = array(
								'type' => 'checkbox',
								'label' => $v['contact_number'].' / '.$v['contact_nick'],
								'name' => 'to_number_'.($k+1),
								'checked' => $checked,
								'position' => 'label-right',
								'labelWidth' => 250,
								'value' => $v['contact_number'],
							);
					}

				}

				//pre(array('networks'=>$this->getAllNetworkName()));
				//die;

				//$networks = array();

				/*$nets = $this->getAllNetworkName();

				if(is_array($nets)&&!empty($nets[0]['network_name'])) {
					foreach($nets as $v) {

						$memberscount = '';

						if($groupid=$this->getNetworkGroupIDFromName($v['network_name'])) {
							$memberscount = $this->getGroupMembersCount($groupid);
						}

						if(!empty($memberscount)) {
							$params['groups'][] = $v['network_name'] . ' ('.$memberscount.')';
						}
					}
				}*/

				// 

				$groups = $this->getAllGroups();

				if(is_array($groups)&&!empty($groups[0]['group_id'])) {

					// array('text'=>,'value'=>,'count'=>$this->getAllContactsCount())

					if(!empty($arowid)) {
						$groupNames = $this->getGroupNamesByArrayOfIDs($arowid);						
					}

					$params['groups'][] = array(
							'type' => 'checkbox',
							'label' => 'All Groups ('.$this->getAllContactsCount().')',
							'name' => 'to_groups_0',
							'checked' => false,
							'position' => 'label-right',
							'labelWidth' => 250,
							'value' => 'All Groups',
						);

					foreach($groups as $k=>$v) {

						$checked = false;

						$memberscount = '';

						if($groupid=$this->getNetworkGroupIDFromName($v['group_name'])) {
							$memberscount = $this->getGroupMembersCount($groupid);
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

				/*$ports = $this->getAllPorts();

				if(is_array($ports)&&!empty($ports[0]['port_id'])) {

					$params['ports'][] = array(
							'type' => 'checkbox',
							'label' => 'All Ports',
							'name' => 'to_ports_0',
							'checked' => false,
							'position' => 'label-right',
							'labelWidth' => 250,
							'value' => 'All Ports',
						);

					foreach($ports as $k=>$v) {
						//$params['ports'][] = array('text'=>$v['port_name'].'/'.$v['port_desc'],'value'=>$v['port_name']);

						$params['ports'][] = array(
								'type' => 'checkbox',
								'label' => $v['port_name'].'/'.$v['port_desc'],
								'name' => 'to_ports_'.($k+1),
								'checked' => false,
								'position' => 'label-right',
								'labelWidth' => 250,
								'value' => $v['port_name'],
							);

					}
				}*/

				$ports = $this->getAllSims(5);

				if(is_array($ports)&&!empty($ports[0]['sim_id'])) {

					$params['ports'][] = array(
							'type' => 'checkbox',
							'label' => 'All SIMs',
							'name' => 'to_ports_0',
							'checked' => false,
							'position' => 'label-right',
							'labelWidth' => 250,
							'value' => 'All SIMs',
						);

					foreach($ports as $k=>$v) {
						//$params['ports'][] = array('text'=>$v['port_name'].'/'.$v['port_desc'],'value'=>$v['port_name']);

						$params['ports'][] = array(
								'type' => 'checkbox',
								'label' => $v['sim_name'].' / '.$this->getNetworkName($v['sim_number']),
								'name' => 'to_ports_'.($k+1),
								'checked' => false,
								'position' => 'label-right',
								'labelWidth' => 250,
								'value' => $v['sim_name'],
							);

					}
				}

				//pre(array('networks'=>$networks));
				//die;

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_messagingmaincompose

		function _form_messagingmainreply($routerid=false,$formid=false) {
			return $this->_form_messagingmaincompose($routerid,$formid,$flag='reply');
		} // _form_messagingmainreply

		function _form_messagingmainforward($routerid=false,$formid=false) {
			return $this->_form_messagingmaincompose($routerid,$formid,$flag='forward');
		} // _form_messagingmainreply

		function _form_messagingmainsent($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_sent'=>1),"smsoutbox_status=4"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;				
				}

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_messagingmainsent

		function _form_messagingmainsim($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_messagingmainsim

		function _form_messagingmainoptions($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_messagingmainoptions

		function _form_messagingmainsmscommands($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_messagingmainsmscommands

		function _form_messagingmainmodemcommands($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_messagingmainmodemcommands

		function _form_messagingmainautoreply($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_messagingmainautoreply

		function _form_messagingdetailscompose($routerid=false,$formid=false,$flag=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				//$params = array('hello'=>'sherwin');

				if(!empty($this->vars['post']['method'])) {

					if($this->vars['post']['method']=='messagingforward') {

						if(!empty($this->vars['post']['rowid'])&&!empty($this->vars['post']['from'])) {

							if($flag=='forward') {

								if($this->vars['post']['from']=='inbox') {
									if(!($result = $appdb->query("select * from tbl_smsinbox where smsinbox_id=".$this->vars['post']['rowid']))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}

									//pre(array('$result'=>$result));

									if(!empty($result['rows'][0]['smsinbox_id'])) {
										$params['content'] = str_replace("\n",'<br>',$result['rows'][0]['smsinbox_message']);
									}
								} else
								if($this->vars['post']['from']=='outbox'||$this->vars['post']['from']=='sent') {
									if(!($result = $appdb->query("select * from tbl_smsoutbox where smsoutbox_id=".$this->vars['post']['rowid']))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}

									//pre(array('$result'=>$result));

									if(!empty($result['rows'][0]['smsoutbox_id'])) {
										$params['content'] = str_replace("\n",'<br>',$result['rows'][0]['smsoutbox_message']);
									}
								}

							} else {

								if(!($result = $appdb->query("select * from tbl_smsinbox where smsinbox_id=".$this->vars['post']['rowid']))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								//pre(array('$result'=>$result));

								if(!empty($result['rows'][0]['smsinbox_id'])) {
									$params['content'] = str_replace("\n",'<br>',$result['rows'][0]['smsinbox_message']);
								}

							}
						}

					} else

					if($this->vars['post']['method']=='messagingsendtooutbox'||$this->vars['post']['method']=='messagingsendnow') {
						$retval = array();
						$retval['return_code'] = 'SUCCESS';

						if($this->vars['post']['method']=='messagingsendtooutbox') {
							$retval['return_message'] = 'SMS successfully sent to Outbox!';							
						} else
						if($this->vars['post']['method']=='messagingsendnow') {
							$retval['return_message'] = 'SMS successfully queued for sending!';														
						}

						$smscontent = trim(htmlspecialchars_decode(strip_tags($this->vars['post']['content'], '<br>')));
						$smscontent = str_replace('&nbsp;',' ',$smscontent);
						
						$ports = trim($this->vars['post']['ports']);
						$togroups = trim($this->vars['post']['togroups']);
						$tonumbers = trim($this->vars['post']['tonumbers']);

						$smscontent = strip_tags($smscontent, '<br>');
						$smscontent = str_replace('<br>',"\n",$smscontent);
						$smscontent = str_replace('<br/>',"\n",$smscontent);
						$smscontent = str_replace('<br />',"\n",$smscontent);

						$asim = explode(';', $ports);
						$atogroups = explode(';', $togroups);
						$atonumbers = explode(';', $tonumbers);

						$recipients = array();

						$retval['asim'] = $asim;
						$retval['atogroups'] = $atogroups;
						$retval['atonumbers'] = $atonumbers;

						//pre(array('retval'=>$retval));

						$netports = array();

						if(preg_match('#All\sSIMs#', $ports)) {
							$asim = $this->getAllSimsName();

							//pre(array('$asim'=>$asim));
						}

						if(!empty($asim)&&is_array($asim)) {

							//$allsim = $this->getAllSims(1);

							$allsim = $this->getAllSims(7); // get all online sim

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
							$atogroups = $this->getAllGroupsWithMembers();
						}

						if(!empty($atogroups)&&is_array($atogroups)) {
							foreach($atogroups as $v) {
								$v = trim($v);
								if(!empty($v)) {
									$res = $this->getGroupMembersByName($v);

									if(!empty($res)&&is_array($res)&&!empty($res[0]['groupcontact_id'])) {
										foreach($res as $j) {
											//pre(array('$j'=>$j));
											$ct = $this->getContactNumber($j['groupcontact_contactid']);
											if(!empty($ct)) {
												$recipients[$j['groupcontact_contactid']] = $ct;
											}
										}
									}
								}
							}
						}

						if(preg_match('#All\sContacts#', $tonumbers)) {
							$atonumbers = $this->getAllContacts(true);
							//pre(array('$atonumbers'=>$atonumbers));
						}

						if(!empty($atonumbers)&&is_array($atonumbers)) {
							foreach($atonumbers as $v) {
								$v = trim($v);
								if(!empty($v)) {
									$cid = $this->getContactIDByNumber($v);
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

								$cnetname = $this->getNetworkName($contactnumber);

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

								if(strlen($smscontent)>160) {

									// long sms

									$smsparts = str_split($smscontent,152); 

									$smsoutbox_udhref = $this->dechex_str(mt_rand(100,250)); 

									$smsoutbox_total = count($smsparts); 

									$content = array();
									$content['smsoutbox_contactid'] = $contactid;
									$content['smsoutbox_contactnumber'] = $contactnumber;
									//$content['smsoutbox_portdevice'] = $portdevice;
									$content['smsoutbox_message'] = $smscontent;
									//$content['smsoutbox_fullmessage'] = $smscontent;
									$content['smsoutbox_udhref'] = $smsoutbox_udhref;
									$content['smsoutbox_part'] = $smsoutbox_total;
									$content['smsoutbox_total'] = $smsoutbox_total;
									$content['smsoutbox_simnumber'] = $simnumber;
									//$content['smsoutbox_simname'] = $simname;
									$content['smsoutbox_type'] = 1;

									if($this->vars['post']['method']=='messagingsendnow') {
										$content['smsoutbox_status'] = 1;										
									}

									//pre(array('$content'=>$content));

									if(!($result = $appdb->insert("tbl_smsoutbox",$content,"smsoutbox_id"))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}

								} else {

									// short sms

									$content = array();
									$content['smsoutbox_contactid'] = $contactid;
									$content['smsoutbox_contactnumber'] = $contactnumber;
									//$content['smsoutbox_portdevice'] = $portdevice;
									$content['smsoutbox_message'] = $smscontent;
									//$content['smsoutbox_fullmessage'] = $smscontent;
									$content['smsoutbox_simnumber'] = $simnumber;
									//$content['smsoutbox_simname'] = $simname;
									$content['smsoutbox_part'] = 1;
									$content['smsoutbox_total'] = 1;

									if($this->vars['post']['method']=='messagingsendnow') {
										$content['smsoutbox_status'] = 1;										
									}

									//pre(array('$content'=>$content));

									if(!($result = $appdb->insert("tbl_smsoutbox",$content,"smsoutbox_id"))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}

								}

							} // foreach($vrecipients as $contactid=>$vv) {

							if($this->vars['post']['method']=='messagingsendnow') {
								$this->vars['post']['method'] = 'messagingsendtooutbox';
							}

						}

						json_encode_return($retval);
						die;

					} //if($this->vars['post']['method']=='messagingsendtooutbox'||$this->vars['post']['method']=='messagingsendnow') {

				}

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_messagingdetailscompose

		function _form_messagingdetailsreply($routerid=false,$formid=false) {
			return $this->_form_messagingdetailscompose($routerid,$formid,'reply');
		} // _form_messagingdetailsreply

		function _form_messagingdetailsforward($routerid=false,$formid=false) {
			return $this->_form_messagingdetailscompose($routerid,$formid,'forward');
		} // _form_messagingdetailsreply

		function _form_messagingdetailscontact($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				//$params = array('hello'=>'sherwin');

				if(!empty($this->vars['post']['method'])) {

					$post = $this->vars['post'];

					if($this->vars['post']['method']=='onrowselect'||$this->vars['post']['method']=='messagingedit') {
						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {
							if(!($result = $appdb->query("select * from tbl_contact where contact_deleted=0 and contact_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['contact_id'])) {

								$params['contactinfo'] = $result['rows'][0];

							}
						}
					} else
					if($this->vars['post']['method']=='messagingaddcontact') {

						$ret = array();
						$ret['return_code'] = 'SUCCESS';
						$ret['return_message'] = 'Contact successfully added!';

						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {
							if(!($result = $appdb->query("select * from tbl_smsinbox where smsinbox_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['smsinbox_id'])) {
								$smsinbox = $result['rows'][0];
							} else {
								$ret['return_message'] = 'An error has occured!';
								json_encode_return($ret);
								die;								
							}

							if(!empty($smsinbox['smsinbox_contactnumber'])) {
								$contactinfo = getContactByNumber($smsinbox['smsinbox_contactnumber']);
							}

							if(!empty($contactinfo)) {
								//pre(array('$contactinfo'=>$contactinfo));

								$content = array();
								$content['contact_deleted'] = 0;

								if(!($result = $appdb->update("tbl_contact",$content,"contact_id=".$contactinfo['contact_id']))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								if($smsinbox['smsinbox_contactnumber']!=$contactinfo['contact_number']) {
									$content = array();
									$content['smsinbox_contactnumber'] = $contactinfo['contact_number'];
									$content['smsinbox_contactsid'] = $contactinfo['contact_id'];

									if(!($result = $appdb->update("tbl_smsinbox",$content,"smsinbox_contactnumber='".$smsinbox['smsinbox_contactnumber']."'"))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}

									$retval['rowid'] = $smsinbox['smsinbox_id'];
								}

								$contactnumber = $contactinfo['contact_number'];
								$contactid = $contactinfo['contact_id'];

							} else {
/////
								$contactnumber = false;

								if($res=parseMobileNo($smsinbox['smsinbox_contactnumber'])) {
									$contactnumber = '0' . $res[2] . $res[3];
								} else
								if(preg_match('/^\d+$/', $smsinbox['smsinbox_contactnumber'])) {
									$contactnumber = $smsinbox['smsinbox_contactnumber'];
								}

								//pre(array('$contactnumber'=>$contactnumber));

								if(!empty($contactnumber)) {
									$content = array();
									$content['contact_number'] = $contactnumber;
									$content['contact_nick'] = 'NICK'.$contactnumber;

									if(!($result = $appdb->insert("tbl_contact",$content,"contact_id"))) {
										return false;
									}

									if(!empty($result['returning'][0]['contact_id'])) {
										$contactid = $result['returning'][0]['contact_id'];

										$content = array();
										$content['smsinbox_contactnumber'] = $contactnumber;
										$content['smsinbox_contactsid'] = $contactid;

										if(!($result = $appdb->update("tbl_smsinbox",$content,"smsinbox_contactnumber='".$smsinbox['smsinbox_contactnumber']."'"))) {
											json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
											die;				
										}

									}							
								} else {
									$ret['return_message'] = 'An error has occured!';
								}
/////
							}

							if(!empty($contactnumber)&&!empty($contactid)) {
								$this->getNetworkGroupID($contactnumber);

								if(!($groupid = $this->getNetworkGroupID($contactnumber))) {
								} else {
									if(!empty($contactid)) {
										$this->insertGroupContact($groupid,$contactid);									
									}
								}
							}

						} else {
							$ret['return_message'] = 'An error has occured!';
						}

						json_encode_return($ret);
						die;
					}
					if($this->vars['post']['method']=='messagingregister') {

						//pre(array('$vars'=>$this->vars));

						if(!empty($this->vars['post']['name'])&&!empty($this->vars['post']['mobileno'])&&($res=parseMobileNo($this->vars['post']['mobileno']))) {

							//pre(array('$res'=>$res));

							$simnumber = false;

							$contactnumber = '0' . $res[2] . $res[3];

							$cnetname = getNetworkName($contactnumber);

							//pre(array('$contactnumber'=>$contactnumber,'$cnetname'=>$cnetname));

							if(!($result = $appdb->query("select * from tbl_contact where contact_deleted=1 and contact_number='$contactnumber'"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							$recover = false;

							//pre(array('$result'=>$result));

							if(!empty($result['rows'][0]['contact_id'])) {
								$recover = true;
								$this->vars['post']['rowid'] = $result['rows'][0]['contact_id'];
							}

							$content = array();
							$content['contact_number'] = $contactnumber;
							$content['contact_nick'] = !empty($this->vars['post']['name']) ? substrNumChar(trim($this->vars['post']['name']),30) : '';

							if($recover) {
								$content['contact_deleted'] = 0;

								$content['contact_updatestamp'] = 'now()';

								if(!($result = $appdb->update("tbl_contact",$content,"contact_id=".$this->vars['post']['rowid']))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								$contactid = $this->vars['post']['rowid'];

							} else {
								$duplicate = false;

								if(!($result = $appdb->insert("tbl_contact",$content,"contact_id"))) {
									if(preg_match('/duplicate\s+key/si', $appdb->lasterror)) {
										$duplicate = true;
									} else {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;
									}
								}

								if(!empty($result['returning'][0]['contact_id'])) {
									$contactid = $result['returning'][0]['contact_id'];
								}								
							}

							$this->getNetworkGroupID($content['contact_number']);

							if(!($groupid = $this->getNetworkGroupID($content['contact_number']))) {
							} else {
								if(!empty($contactid)) {
									$this->insertGroupContact($groupid,$contactid);									
								}
							}

							$asim = getAllSimsName();

							if(!empty($asim)&&is_array($asim)) {

								$allsim = getAllSims(7); // get all online sim

								$netsim = array();

								$anetsim = array();

								foreach($asim as $sim) {
									if(!empty($allsim[$sim])) {
										$netsim[$allsim[$sim]['sim_network']][] = $anetsim[] = $allsim[$sim];
									}
								}
								
								//pre(array('$netsim'=>$netsim,'$anetsim'=>$anetsim));
							}

							if(!empty($netsim)&&!empty($anetsim)) {
								foreach($anetsim as $k=>$v) {
									if(!empty($v['sim_hotline'])&&$v['sim_network']==$cnetname) {
										$simnumber = $v['sim_number'];
										break;
									}
								}

								if(empty($simnumber)) {
									foreach($anetsim as $k=>$v) {
										if(!empty($v['sim_hotline'])) {
											$simnumber = $v['sim_number'];
											break;
										}
									}									
								}

								if(empty($simnumber)) {
									if(!empty($anetsim[0]['sim_number'])) {
										$simnumber = $anetsim[0]['sim_number'];
									}
								}
							}

							if(!empty($simnumber)) {

								if($duplicate) {

									sendToOutBox($contactnumber, $simnumber, getOption('$WELCOME_MESSAGE_DUPLICATE'), 1);

								} else {

									sendToOutBox($contactnumber, $simnumber, getOption('$WELCOME_MESSAGE'), 1);

									sendToOutBox($contactnumber, $simnumber, getOption('$SURVEY_MESSAGE'), 1, getOption('$SURVEY_MESSAGE_DELAY'));
								}

							}

						}

						//sendToOutBox($contactnumber=false,$simnumber=false,$message=false,$status=1)

						//$ret = array();
						//$ret['return_code'] = 'SUCCESS';
						//$ret['return_message'] = 'Contact successfully saved!';

						//json_encode_return($ret);

						echo '1';
						die;

					} else
					if($this->vars['post']['method']=='messagingclaimpromo') {

						if(!empty($this->vars['post']['promocode'])&&!empty($this->vars['post']['mobileno'])&&($res=parseMobileNo($this->vars['post']['mobileno']))) {

							$simnumber = false;

							$contactnumber = '0' . $res[2] . $res[3];

							$promocodes_promocode = trim($this->vars['post']['promocode']);

							$cnetname = getNetworkName($contactnumber);

							if(!($result = $appdb->query("select *,extract(epoch from promocodes_startdate::timestamptz) as startdate,extract(epoch from promocodes_enddate::timestamptz) as enddate,now(),extract(epoch from now()) as nowdate,(extract(epoch from now()) - extract(epoch from promocodes_enddate::timestamptz)) as elapsedtime from tbl_promocodes where promocodes_promocode='$promocodes_promocode' and promocodes_contactnumber='$contactnumber' and promocodes_expired=0 and promocodes_claimed=0"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							//pre(array('$result'=>$result));

							if(!empty($result['rows'][0]['promocodes_id'])) {

								$promocodes_id = $result['rows'][0]['promocodes_id'];

								if(is_numeric($result['rows'][0]['elapsedtime'])&&$result['rows'][0]['elapsedtime']>=0) {

									if(!($result = $appdb->update("tbl_promocodes",array('promocodes_expired'=>1),'promocodes_id='.$promocodes_id))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}

									json_encode_return(array('error_code'=>123,'error_message'=>'Sorry, promo code has already expired!'));
									die;				
								} else
								if(is_numeric($result['rows'][0]['elapsedtime'])&&$result['rows'][0]['elapsedtime']<0) {

									if(!($result = $appdb->update("tbl_promocodes",array('promocodes_expired'=>2,'promocodes_claimed'=>1,'promocodes_claimstamp'=>'now()'),'promocodes_id='.$promocodes_id))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}

								} else {
									json_encode_return(array('error_code'=>123,'error_message'=>'Sorry, but we cannot verify this promo code for now. Please try again later.'));
									die;				
								}
							} else {
								json_encode_return(array('error_code'=>123,'error_message'=>'Sorry, we cannot verify this promo code or it has already expired.'));
								die;								
							}

							$asim = getAllSimsName();

							if(!empty($asim)&&is_array($asim)) {

								$allsim = getAllSims(7); // get all online sim

								$netsim = array();

								$anetsim = array();

								foreach($asim as $sim) {
									if(!empty($allsim[$sim])) {
										$netsim[$allsim[$sim]['sim_network']][] = $anetsim[] = $allsim[$sim];
									}
								}
								
								//pre(array('$netsim'=>$netsim,'$anetsim'=>$anetsim));
							}

							if(!empty($netsim)&&!empty($anetsim)) {
								foreach($anetsim as $k=>$v) {
									if(!empty($v['sim_hotline'])&&$v['sim_network']==$cnetname) {
										$simnumber = $v['sim_number'];
										break;
									}
								}

								if(empty($simnumber)) {
									if(!empty($anetsim[0]['sim_number'])) {
										$simnumber = $anetsim[0]['sim_number'];
									}
								}
							}

							if(!empty($simnumber)) {

								$promomsg = getOption('$PROMOCODE_CLAIMED_MESSAGE');

								$promomsg = str_replace('%promocode%', $promocodes_promocode, $promomsg);

								sendToOutBox($contactnumber, $simnumber, $promomsg, 1);

							}

						}

						echo '1';
						die;
					} else
					if($this->vars['post']['method']=='messagingreferral') {

						if(!empty($post['name'])&&!empty($post['referralcode'])&&!empty($post['yourmobileno'])&&($res=parseMobileNo($post['yourmobileno']))) {

							//pre(array('$vars'=>$this->vars,'$res'=>$res));

							$name = trim($post['name']);
							$referralcode = trim($post['referralcode']);

							$simnumber = false;

							$contactnumber = '0' . $res[2] . $res[3];

							$cnetname = getNetworkName($contactnumber);

							if(getContactIDByNumber($contactnumber)) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Sorry, this referral code is valid only to unregistered customer.'));
								die;
							}

							if(!($result = $appdb->query("select * from tbl_referralsent where referralsent_contactnumber='$contactnumber' and referralsent_referralcode='$referralcode' and referralsent_claimed=0"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['referralsent_id'])) {
								//pre(array('$result'=>$result));

								$referralsent_id = $result['rows'][0]['referralsent_id'];
/////
								$asim = getAllSimsName();

								if(!empty($asim)&&is_array($asim)) {

									$allsim = getAllSims(7); // get all online sim

									$netsim = array();

									$anetsim = array();

									foreach($asim as $sim) {
										if(!empty($allsim[$sim])) {
											$netsim[$allsim[$sim]['sim_network']][] = $anetsim[] = $allsim[$sim];
										}
									}
									
									//pre(array('$netsim'=>$netsim,'$anetsim'=>$anetsim));
								}

								if(!empty($netsim)&&!empty($anetsim)) {
									foreach($anetsim as $k=>$v) {
										if(!empty($v['sim_hotline'])&&$v['sim_network']==$cnetname) {
											$simnumber = $v['sim_number'];
											break;
										}
									}

									if(empty($simnumber)) {
										if(!empty($anetsim[0]['sim_number'])) {
											$simnumber = $anetsim[0]['sim_number'];
										}
									}
								}

								if(!empty($simnumber)) {

									$referralmsg = getOption('$REFERRALCODE_CLAIMED_MESSAGE');

									$referralmsg = str_replace('%referralcode%', $referralcode, $referralmsg);

									sendToOutBox($contactnumber, $simnumber, $referralmsg, 1);

									if(!($result = $appdb->update("tbl_referralsent",array('referralsent_claimed'=>1,'referralsent_claimstamp'=>'now()'),'referralsent_id='.$referralsent_id))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}

									if(!($result = $appdb->update("tbl_referralcode",array('referralcode_claimed'=>1,'referralcode_claimstamp'=>'now()'),"referralcode_referralcode='$referralcode'"))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}

								}
/////
								if(!($result = $appdb->query("select * from tbl_contact where contact_deleted=1 and contact_number='$contactnumber'"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								$recover = false;

								//pre(array('$result'=>$result));

								if(!empty($result['rows'][0]['contact_id'])) {
									$recover = true;
									$this->vars['post']['rowid'] = $result['rows'][0]['contact_id'];
								}

								$content = array();
								$content['contact_number'] = $contactnumber;
								$content['contact_nick'] = !empty($this->vars['post']['name']) ? substrNumChar(trim($this->vars['post']['name']),30) : '';

								if($recover) {
									$content['contact_deleted'] = 0;

									$content['contact_updatestamp'] = 'now()';

									if(!($result = $appdb->update("tbl_contact",$content,"contact_id=".$this->vars['post']['rowid']))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}

									$contactid = $this->vars['post']['rowid'];

								} else {
									$duplicate = false;

									if(!($result = $appdb->insert("tbl_contact",$content,"contact_id"))) {
										if(preg_match('/duplicate\s+key/si', $appdb->lasterror)) {
											$duplicate = true;
										} else {
											json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
											die;
										}
									}

									if(!empty($result['returning'][0]['contact_id'])) {
										$contactid = $result['returning'][0]['contact_id'];
									}								
								}

								$this->getNetworkGroupID($content['contact_number']);

								if(!($groupid = $this->getNetworkGroupID($content['contact_number']))) {
								} else {
									if(!empty($contactid)) {
										$this->insertGroupContact($groupid,$contactid);									
									}
								}

								if(!empty($simnumber)) {

									if($duplicate) {

										sendToOutBox($contactnumber, $simnumber, getOption('$WELCOME_MESSAGE_DUPLICATE'), 1);

									} else {

										sendToOutBox($contactnumber, $simnumber, getOption('$WELCOME_MESSAGE'), 1);

										sendToOutBox($contactnumber, $simnumber, getOption('$SURVEY_MESSAGE'), 1, getOption('$SURVEY_MESSAGE_DELAY'));
									}

								}

								echo 1;
								die;
/////
							}


						}

						json_encode_return(array('error_code'=>123,'error_message'=>'Sorry, we cannot verify this referral code or it has already expired.'));
						die;		

					} else
					if($this->vars['post']['method']=='messagingsave') {

						if($res=parseMobileNo($this->vars['post']['txt_number'])) {
							$this->vars['post']['txt_number'] = '0' . $res[2] . $res[3];
						}

						$contact_number = !empty($this->vars['post']['txt_number']) ? substrNumChar(trim($this->vars['post']['txt_number']),20) : '';
						$contact_nick = !empty($this->vars['post']['txt_nick']) ? substrNumChar(trim($this->vars['post']['txt_nick']),30) : '';
						$contact_fname = !empty($this->vars['post']['txt_fname']) ? substrNumChar(trim($this->vars['post']['txt_fname']),30) : '';
						$contact_lname = !empty($this->vars['post']['txt_lname']) ? substrNumChar(trim($this->vars['post']['txt_lname']),30) : '';
						$contact_address = !empty($this->vars['post']['txt_address']) ? substrNumChar(trim($this->vars['post']['txt_address']),100) : '';
						$contact_city = !empty($this->vars['post']['txt_city']) ? substrNumChar(trim($this->vars['post']['txt_city']),100) : '';
						$contact_country = !empty($this->vars['post']['txt_country']) ? substrNumChar(trim($this->vars['post']['txt_country']),100) : '';

						if(!($result = $appdb->query("select * from tbl_contact where contact_deleted=1 and contact_number='$contact_number'"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						$recover = false;

						if(!empty($result['rows'][0]['contact_id'])) {
							$recover = true;
							$this->vars['post']['rowid'] = $result['rows'][0]['contact_id'];
						}

						$content = array(
							'contact_number'=>$contact_number,
							'contact_nick'=>$contact_nick,
							'contact_fname'=>$contact_fname,
							'contact_lname'=>$contact_lname,
							'contact_address'=>$contact_address,
							'contact_city'=>$contact_city,
							'contact_country'=>$contact_country,
							'contact_network'=>$this->getNetworkCode($contact_number),
						);

						if($recover) {
							$content['contact_deleted'] = 0;
						}

						if(!empty($contact_number)&&!empty($contact_nick)) {

							$ret = array();
							$ret['return_code'] = 'SUCCESS';
							$ret['return_message'] = 'Contact successfully saved!';

							if(!empty($this->vars['post']['rowid'])) {

								$content['contact_updatestamp'] = 'now()';

								if(!($result = $appdb->update("tbl_contact",$content,"contact_id=".$this->vars['post']['rowid']))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								$ret['rowid'] = $contactid = $this->vars['post']['rowid'];

							} else {
								if(!($result = $appdb->insert("tbl_contact",$content,"contact_id"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								if(!empty($result['returning'][0]['contact_id'])) {
									$ret['rowid'] = $contactid = $result['returning'][0]['contact_id'];
								}

							}

							if(!($groupid = $this->getNetworkGroupID($content['contact_number']))) {
							} else {
								$this->insertGroupContact($groupid,$contactid);
							}

							json_encode_return($ret);

							die;
						}

						json_encode_return(array('return_code'=>'SUCCESS','return_message'=>'Contact successfully saved!','$content'=>$content));

						die;

					} else
					if($this->vars['post']['method']=='xmessagingupload') {
						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Contact successfully imported!';

						$retval['$_FILES'] = $_FILES;

						$post = $this->vars['post'];

						$content = false;

						if(!empty($_FILES['file']['tmp_name'])) {
							$content = readFromFile($_FILES['file']['tmp_name']);
						}

						if(!empty($content)) {
							$content = str_replace("\n"," ",$content);
							$content = str_replace("\t"," ",$content);
							$content = str_replace("\r"," ",$content);
							$content = explode(" ", $content);
							//pre(array('$content'=>$content));

							$mobileNos = array();

							foreach($content as $k=>$v) {
								if($res=parseMobileNo($v,'(\d+)(\d{3})(\d{7})')) {
									//pre(array('$res'=>$res));
									$mobileNos[] = $mobileno = '0'.$res[2].$res[3];

									$content = array();
									$content['contact_number'] = $mobileno;
									$content['contact_nick'] = 'IMP'.$mobileno;
									$content['contact_network'] = $this->getNetworkCode($mobileno);

									if(!($result = $appdb->insert("tbl_contact",$content,"contact_id"))) {

										if(preg_match('/duplicate\s+key/si', $appdb->lasterror)) {
											continue;
										} else {
											json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
											die;
										}
									}

									if(!empty($result['returning'][0]['contact_id'])) {
										if(!($groupid = $this->getNetworkGroupID($mobileno))) {
										} else {
											$this->insertGroupContact($groupid,$result['returning'][0]['contact_id']);
										}
									}
								}
							}

							//pre(array('$mobileNos'=>$mobileNos));

							$retval['mobileNos'] = $mobileNos;
						}

						//if(!empty($post[''])) {

						//}

						header("Content-Type: text/html");
						print_r(json_encode($retval));
						die;

						//json_encode_return($retval);
						//die;
					} else
					if($this->vars['post']['method']=='messagingupload') {
						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Contact successfully imported!';

						$retval['$_FILES'] = $_FILES;

						$post = $this->vars['post'];

						$content = false;

						if(!empty($_FILES['file']['tmp_name'])) {
							$content = readFromFile($_FILES['file']['tmp_name']);
						}

						if(!empty($content)) {

							$lines = array();

							if(preg_match_all("/\n/si", $content, $matches1)&&preg_match_all("/\r/si", $content, $matches1)) {
								$content = str_replace("\r"," ",$content);
								$lines = explode("\n", $content);
							} else
							if(preg_match_all("/\n/si", $content, $matches1)) {
								$lines = explode("\n", $content);
							} else
							if(preg_match_all("/\r/si", $content, $matches1)) {
								$lines = explode("\r", $content);
							}

							//pre(array('$mobileNos'=>$mobileNos));

							$mobileNos = array();

							foreach($lines as $k=>$line) {
								$item = explode(',', $line);

								if($res=parseMobileNo($item[0],'(\d+)(\d{3})(\d{7})')) {

									$mobileNos[] = $mobileno = '0'.$res[2].$res[3];

									$content = array();
									$content['contact_number'] = $mobileno;

									$nick = '';

									if(!empty($item[1])) {
										$nick .= trim($item[1]) . ' ';
										$content['contact_fname'] = trim($item[1]);
									}

									if(!empty($item[2])) {
										$nick .= trim($item[2]);
										$content['contact_lname'] = trim($item[2]);
									}

									if(!empty($nick)) {
										$content['contact_nick'] = $nick;
									} else {
										$content['contact_nick'] = 'IMP'.$mobileno;
									}

									$content['contact_network'] = $this->getNetworkCode($mobileno);

									if(!($result = $appdb->insert("tbl_contact",$content,"contact_id"))) {

										if(preg_match('/duplicate\s+key/si', $appdb->lasterror)) {
											continue;
										} else {
											json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
											die;
										}
									}

									if(!empty($result['returning'][0]['contact_id'])) {
										if(!($groupid = $this->getNetworkGroupID($mobileno))) {
										} else {
											$this->insertGroupContact($groupid,$result['returning'][0]['contact_id']);
										}
									}


								}
							}

							$retval['mobileNos'] = $mobileNos;
						}

						//if(!empty($post[''])) {

						//}

						header("Content-Type: text/html");
						print_r(json_encode($retval));
						die;

						//json_encode_return($retval);
						//die;
					} else
					if($this->vars['post']['method']=='messagingexport') {

						if(!empty($this->vars['post']['rowids'])) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'Contact successfully exported!';

							$rowids = explode(',', $this->vars['post']['rowids']);

							$arowid = array();

							for($i=0;$i<count($rowids);$i++) {
								$rowid = intval(trim($rowids[$i]));
								if(!empty($rowid)) {
									$arowid[] = $rowid;
								}
							}

							if(!empty($arowid)) {
								$rowids = implode(',', $arowid);

								if(!($result = $appdb->query("select * from tbl_contact where contact_deleted=0 and contact_id in ($rowids) order by contact_id asc"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								$forexport = '';

								if(!empty($result['rows'][0]['contact_id'])) {
									foreach($result['rows'] as $k=>$v) {
										$forexport .= $v['contact_number'] . chr(9) . $v['contact_nick'] . "\n";
									}

									if(($file = createRandomFile())) {

										if(!saveToFile($file, $forexport)) {
											$retval['return_message'] = 'An error occured while exporting!';
										} else {
											$sha = sha1($file.time());
											$retval['export'] = $sha;
											$_SESSION['EXPORT'][$sha] = $file;
										}

									}
								}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'Contact successfully exported!';

								if(!($result = $appdb->query("select * from tbl_contact where contact_deleted=0 and contact_id=".$this->vars['post']['rowid']))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								$forexport = '';

								if(!empty($result['rows'][0]['contact_id'])) {
									foreach($result['rows'] as $k=>$v) {
										$forexport .= $v['contact_number'] . chr(9) . $v['contact_nick'] . "\n";
									}

									if(($file = createRandomFile())) {

										if(!saveToFile($file, $forexport)) {
											$retval['return_message'] = 'An error occured while exporting!';
										} else {
											$sha = sha1($file.time());
											$retval['export'] = $sha;
											$_SESSION['EXPORT'][$sha] = $file;
										}

									}
								}

							json_encode_return($retval);
							die;

						}

					} else
					if($this->vars['post']['method']=='messagingdelete') {

						if(!empty($this->vars['post']['rowids'])) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'Contact successfully deleted!';

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

								/*if(!($result = $appdb->update("tbl_contact",array('contact_deleted'=>1,'contact_updatestamp'=>'now()'),"contact_id in (".$rowids.")"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}*/

								if(!($result = $appdb->query("delete from tbl_contact where contact_id in (".$rowids.")"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								if(!($result = $appdb->query("delete from tbl_groupcontact where groupcontact_contactid in (".$rowids.")"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'Contact successfully deleted!';


							$content['contact_updatestamp'] = 'now()';

							/*if(!($result = $appdb->update("tbl_contact",array('contact_deleted'=>1),"contact_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}*/

							if(!($result = $appdb->query("delete from tbl_contact where contact_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!($result = $appdb->query("delete from tbl_groupcontact where groupcontact_contactid=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

						}

						json_encode_return($retval);

						die;

					}

				}


				//if(!empty($this->vars['post']['method'])&&!empty($this->vars['post']['rowid'])&&$this->vars['post']['method']=='onrowselect'&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {


				//}

				$templatefile = $this->templatefile($routerid,$formid);

				/*if(!empty($this->vars['post']['roleid'])&&empty($this->vars['post']['userid'])) {
					$templatefile = $this->templatefile($routerid,'usermanagementnewroles');

					$params = array('edit'=>true);
				} else
				if(!empty($this->vars['post']['roleid'])&&!empty($this->vars['post']['userid'])) {
					$templatefile = $this->templatefile($routerid,'usermanagementnewuser');

					$params = array('edit'=>true);
				}*/


				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;

		} // _form_messagingdetailscontact

		function _form_messagingdetailsgroups($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				//$params = array('hello'=>'sherwin');

				$params['contacts'] = array();
				$params['groupmembers'] = array();

				//$params = array('hello'=>'sherwin');

				if(!empty($this->vars['post']['method'])) {

					if($this->vars['post']['method']=='onrowselect'||$this->vars['post']['method']=='messagingedit'||$this->vars['post']['method']=='messagingnew') {

						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {

							if(!($result = $appdb->query("select * from tbl_group where group_deleted=0 and group_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['group_id'])) {
								$params['groupinfo'] = $result['rows'][0];
							}

						}

						if(!($result = $appdb->query("select * from tbl_contact where contact_deleted=0"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['contact_id'])) {
							$params['contacts'] = $rescontacts = $result['rows'] ;

							if($this->vars['post']['method']!='messagingnew') {
								if(!($result = $appdb->query("select * from tbl_groupcontact where groupcontact_groupid=".$this->vars['post']['rowid']))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								if(!empty($result['rows'][0]['groupcontact_id'])) {
									foreach($rescontacts as $k=>$contact) {
										foreach($result['rows'] as $j=>$member) {
											if($contact['contact_id']==$member['groupcontact_contactid']) {
												$params['groupmembers'][] = $contact;
												unset($params['contacts'][$k]);
												break;
											}
										}
									}
								}
							}
						}

					} else
					if($this->vars['post']['method']=='messagingsave') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Group successfully saved!';
						$retval['vars'] = $this->vars;

						$groupmembers = array();

						if(!empty($this->vars['post']['groupmembers'])) {
							$groupmembers = @json_decode($this->vars['post']['groupmembers']);
						//	pre(array('groupmembers'=>json_decode($this->vars['post']['groupmembers'])));
						//	die;
							if(!is_array($groupmembers)) {
								$groupmembers = array();
							}
						}

						if(!empty($this->vars['post']['rowid'])) {
							$retval['rowid'] = $this->vars['post']['rowid'];

							if(!($result = $appdb->query("delete from tbl_groupcontact where groupcontact_groupid=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

/*
sherwint_sms101=# \d tbl_groupcontact
                                            Table "public.tbl_groupcontact"
         Column         |           Type           |                             Modifiers                              
------------------------+--------------------------+--------------------------------------------------------------------
 groupcontact_id        | bigint                   | not null default nextval(('tbl_groupcontact_seq'::text)::regclass)
 groupcontact_groupid   | integer                  | not null default 0
 groupcontact_contactid | integer                  | not null default 0
 groupcontact_flag      | integer                  | not null default 0
 groupcontact_timestamp | timestamp with time zone | default now()
Indexes:
    "tbl_groupcontact_primary_key" PRIMARY KEY, btree (groupcontact_id)

*/

							foreach($groupmembers as $contactid) {

								$content = array();
								$content['groupcontact_groupid'] = $this->vars['post']['rowid'];
								$content['groupcontact_contactid'] = $contactid;

								if(!($result = $appdb->insert("tbl_groupcontact",$content,"groupcontact_id"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

							}

							$content = array();
							$content['group_name'] = $this->vars['post']['txt_groupname'];
							$content['group_desc'] = $this->vars['post']['txt_groupdesc'];

							if(!($result = $appdb->update("tbl_group",$content,"group_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

						} else {

/*
sherwint_sms101=# \d tbl_group
                                          Table "public.tbl_group"
      Column       |           Type           |                          Modifiers                          
-------------------+--------------------------+-------------------------------------------------------------
 group_id          | bigint                   | not null default nextval(('tbl_group_seq'::text)::regclass)
 group_name        | character varying(30)    | not null default ''::text
 group_desc        | character varying(30)    | not null default ''::text
 group_deleted     | integer                  | not null default 0
 group_flag        | integer                  | not null default 0
 group_timestamp   | timestamp with time zone | default now()
 group_updatestamp | timestamp with time zone | default now()
Indexes:
    "tbl_group_primary_key" PRIMARY KEY, btree (group_id)
*/

							if(!empty($this->vars['post']['txt_groupdesc'])&&!empty($this->vars['post']['txt_groupname'])) {

								$content = array();
								$content['group_name'] = $this->vars['post']['txt_groupname'];
								$content['group_desc'] = $this->vars['post']['txt_groupdesc'];

								if(!($result = $appdb->insert("tbl_group",$content,"group_id"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								if(!empty($result['returning'][0]['group_id'])) {
									$retval['rowid'] = $result['returning'][0]['group_id'];

									$groupmembers = array();

									if(!empty($this->vars['post']['groupmembers'])) {
										$groupmembers = @json_decode($this->vars['post']['groupmembers']);
										if(!is_array($groupmembers)) {
											$groupmembers = array();
										}
									}

									if(!empty($groupmembers)) {
										foreach($groupmembers as $contactid) {

											$content = array();
											$content['groupcontact_groupid'] = $retval['rowid'];
											$content['groupcontact_contactid'] = $contactid;

											if(!($result = $appdb->insert("tbl_groupcontact",$content,"groupcontact_id"))) {
												json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
												die;				
											}

										}
									}

								}

							}

						}

						json_encode_return($retval);
						die;

					} else
					if($this->vars['post']['method']=='messagingdelete') {

						if(!empty($this->vars['post']['rowids'])) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'Group successfully deleted!';

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

								if(!($result = $appdb->query("delete from tbl_groupcontact where groupcontact_groupid in (".$rowids.")"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								if(!($result = $appdb->query("delete from tbl_group where group_id in (".$rowids.")"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'Group successfully deleted!';

							if(!($result = $appdb->query("delete from tbl_groupcontact where groupcontact_groupid=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!($result = $appdb->query("delete from tbl_group where group_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							json_encode_return($retval);
							die;

						}

					}

				}


				//if(!empty($this->vars['post']['method'])&&!empty($this->vars['post']['rowid'])&&$this->vars['post']['method']=='onrowselect'&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {


				//}

				$templatefile = $this->templatefile($routerid,$formid);

				/*if(!empty($this->vars['post']['roleid'])&&empty($this->vars['post']['userid'])) {
					$templatefile = $this->templatefile($routerid,'usermanagementnewroles');

					$params = array('edit'=>true);
				} else
				if(!empty($this->vars['post']['roleid'])&&!empty($this->vars['post']['userid'])) {
					$templatefile = $this->templatefile($routerid,'usermanagementnewuser');

					$params = array('edit'=>true);
				}*/


				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;

		} // _form_messagingdetailsgroups

		function _form_messagingdetailsinbox($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				//$params = array('hello'=>'sherwin');

				if(!empty($this->vars['post']['method'])) {

					if($this->vars['post']['method']=='onrowselect') {
						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {
							if(!($result = $appdb->query("select * from tbl_smsinbox where smsinbox_deleted=0 and smsinbox_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							$rowid = $this->vars['post']['rowid'];

							if(!empty($result['rows'][0]['smsinbox_id'])) {
								$params['smsinboxinfo'] = $result['rows'][0];
							}

							if(!($result = $appdb->update("tbl_smsinbox",array('smsinbox_unread'=>0),"smsinbox_id=".$rowid))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}
						}
					} else
					if($this->vars['post']['method']=='messagingexport') {

						if(!empty($this->vars['post']['rowids'])) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'Inbox successfully exported!';

							$rowids = explode(',', $this->vars['post']['rowids']);

							$arowid = array();

							for($i=0;$i<count($rowids);$i++) {
								$rowid = intval(trim($rowids[$i]));
								if(!empty($rowid)) {
									$arowid[] = $rowid;
								}
							}

							if(!empty($arowid)) {
								$rowids = implode(',', $arowid);

								if(!($result = $appdb->query("select *,extract(epoch from smsinbox_timestamp) as unixstamp from tbl_smsinbox where smsinbox_deleted=0 and smsinbox_eload=0 and smsinbox_id in ($rowids) order by smsinbox_id desc"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								$forexport = '';

								if(!empty($result['rows'][0]['smsinbox_id'])) {
									foreach($result['rows'] as $k=>$v) {
										$forexport .= $v['smsinbox_id'] . chr(9) . getContactNickByID($v['smsinbox_contactsid']) . chr(9) . $v['smsinbox_contactnumber'] . chr(9) . getSimNameByNumber($v['smsinbox_simnumber']) . chr(9) . $v['smsinbox_message'] . chr(9) . getNetworkName($v['smsinbox_contactnumber']) . chr(9) . pgDate($v['smsinbox_timestamp']) . "\n";
									}

									if(($file = createRandomFile())) {

										if(!saveToFile($file, $forexport)) {
											$retval['return_message'] = 'An error occured while exporting!';
										} else {
											$sha = sha1($file.time());
											$retval['export'] = $sha;
											$_SESSION['EXPORT'][$sha] = $file;
										}

									}
								}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'Inbox successfully exported!';

								if(!($result = $appdb->query("select *,extract(epoch from smsinbox_timestamp) as unixstamp from tbl_smsinbox where smsinbox_deleted=0 and smsinbox_eload=0 and smsinbox_id=".$this->vars['post']['rowid']))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								$forexport = '';

								if(!empty($result['rows'][0]['smsinbox_id'])) {
									foreach($result['rows'] as $k=>$v) {
										$forexport .= $v['smsinbox_id'] . chr(9) . getContactNickByID($v['smsinbox_contactsid']) . chr(9) . $v['smsinbox_contactnumber'] . chr(9) . getSimNameByNumber($v['smsinbox_simnumber']) . chr(9) . $v['smsinbox_message'] . chr(9) . getNetworkName($v['smsinbox_contactnumber']) . chr(9) . pgDate($v['smsinbox_timestamp']) . "\n";
									}

									if(($file = createRandomFile())) {

										if(!saveToFile($file, $forexport)) {
											$retval['return_message'] = 'An error occured while exporting!';
										} else {
											$sha = sha1($file.time());
											$retval['export'] = $sha;
											$_SESSION['EXPORT'][$sha] = $file;
										}

									}
								}

							json_encode_return($retval);
							die;

						}

					} else
					if($this->vars['post']['method']=='messagingdelete') {

						if(!empty($this->vars['post']['rowids'])) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'SMS successfully deleted!';

							$rowids = explode(',', $this->vars['post']['rowids']);

							$arowid = array();

							for($i=0;$i<count($rowids);$i++) {
								$rowid = intval(trim($rowids[$i]));
								if(!empty($rowid)) {
									$arowid[] = $rowid;
								}
							}

							if(!empty($arowid)) {
								$rowids = implode(',', $arowid);

								if(!($result = $appdb->query("delete from tbl_smsinbox where smsinbox_id in (".$rowids.")"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								//if(!($result = $appdb->update('tbl_smsinbox',array('smsinbox_deleted'=>1),'smsinbox_id in ('.$rowids.')'))) {
								//	json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								//	die;				
								//}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'SMS successfully deleted!';

							if(!($result = $appdb->update('tbl_smsinbox',array('smsinbox_deleted'=>1),'smsinbox_id='.$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							json_encode_return($retval);
							die;

						}

					}

				}

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;

		} // _form_messagingdetailsinbox

		function _form_messagingdetailsoutbox($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				//$params = array('hello'=>'sherwin');

				if(!empty($this->vars['post']['method'])) {

					if($this->vars['post']['method']=='onrowselect') {
						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {
							if(!($result = $appdb->query("select * from tbl_smsoutbox where smsoutbox_deleted=0 and smsoutbox_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['smsoutbox_id'])) {

								$params['smsoutboxinfo'] = $result['rows'][0];

							}
						}
					} else

					if($this->vars['post']['method']=='messagingsendstart') {

/////

						if(!empty($this->vars['post']['rowids'])) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'Sending of SMS successfully started!';

							$rowids = explode(',', $this->vars['post']['rowids']);

							$arowid = array();

							for($i=0;$i<count($rowids);$i++) {
								$rowid = intval(trim($rowids[$i]));
								if(!empty($rowid)) {
									$arowid[] = $rowid;
								}
							}

							if(!empty($arowid)) {
								$rowids = implode(',', $arowid);

								if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>1),'smsoutbox_status=0 and smsoutbox_id in ('.$rowids.')'))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								json_encode_return($retval);
								die;
							}

						}

/////

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Sending of SMS successfully started!';

						if(!empty($this->vars['post']['rowid'])) {
							$retval['rowid'] = $this->vars['post']['rowid'];
						}

						if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>1),'smsoutbox_status=0'))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						json_encode_return($retval);
						die;

					} else

					if($this->vars['post']['method']=='messagingsendstop') {

/////

						if(!empty($this->vars['post']['rowids'])) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'Sending of SMS successfully stopped!';

							$rowids = explode(',', $this->vars['post']['rowids']);

							$arowid = array();

							for($i=0;$i<count($rowids);$i++) {
								$rowid = intval(trim($rowids[$i]));
								if(!empty($rowid)) {
									$arowid[] = $rowid;
								}
							}

							if(!empty($arowid)) {
								$rowids = implode(',', $arowid);

								if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>0),'smsoutbox_status=1 and smsoutbox_id in ('.$rowids.')'))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								json_encode_return($retval);
								die;
							}

						}

/////

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Sending of SMS successfully started!';

						if(!empty($this->vars['post']['rowid'])) {
							$retval['rowid'] = $this->vars['post']['rowid'];
						}

						if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>0),'smsoutbox_status=1'))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						json_encode_return($retval);
						die;

					} else

					if($this->vars['post']['method']=='messagingresend') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Resending SMS.';

						if(!empty($this->vars['post']['rowids'])) {

							//$retval = array();
							//$retval['return_code'] = 'SUCCESS';
							//$retval['return_message'] = 'SMS successfully deleted!';

							$rowids = explode(',', $this->vars['post']['rowids']);

							$arowid = array();

							for($i=0;$i<count($rowids);$i++) {
								$rowid = intval(trim($rowids[$i]));
								if(!empty($rowid)) {
									$arowid[] = $rowid;
								}
							}

							if(!empty($arowid)) {
								$rowids = implode(',', $arowid);

								if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>1),'smsoutbox_id in ('.$rowids.')'))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])) {

							if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>1),'smsoutbox_id='.$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

						}


						json_encode_return($retval);
						die;

					} else

					if($this->vars['post']['method']=='messagingdelete') {

						if(!empty($this->vars['post']['rowids'])) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'SMS successfully deleted!';

							$rowids = explode(',', $this->vars['post']['rowids']);

							$arowid = array();

							for($i=0;$i<count($rowids);$i++) {
								$rowid = intval(trim($rowids[$i]));
								if(!empty($rowid)) {
									$arowid[] = $rowid;
								}
							}

							if(!empty($arowid)) {
								$rowids = implode(',', $arowid);

								//if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_deleted'=>1),'smsoutbox_id in ('.$rowids.')'))) {
								//	json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								//	die;				
								//}

								if(!($result = $appdb->query("delete from tbl_smsoutbox where smsoutbox_id in (".$rowids.")"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'SMS successfully deleted!';

							if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_deleted'=>1),'smsoutbox_id='.$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							json_encode_return($retval);
							die;
						}
					}

				}

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;

		} // _form_messagingdetailsoutbox

		function _form_messagingdetailsoutbox2($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				//$params = array('hello'=>'sherwin');

				if(!empty($this->vars['post']['method'])) {

					if($this->vars['post']['method']=='onrowselect') {
						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {
							if(!($result = $appdb->query("select * from tbl_smsoutbox where smsoutbox_deleted=0 and smsoutbox_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['smsoutbox_id'])) {

								$params['smsoutboxinfo'] = $result['rows'][0];

							}
						}
					} else

					if($this->vars['post']['method']=='messagingsendstart') {

/////

						if(!empty($this->vars['post']['rowids'])) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'SMS successfully deleted!';

							$rowids = explode(',', $this->vars['post']['rowids']);

							$arowid = array();

							for($i=0;$i<count($rowids);$i++) {
								$rowid = intval(trim($rowids[$i]));
								if(!empty($rowid)) {
									$arowid[] = $rowid;
								}
							}

							if(!empty($arowid)) {
								$rowids = implode(',', $arowid);

								if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>1),'smsoutbox_status=0 and smsoutbox_id in ('.$rowids.')'))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								json_encode_return($retval);
								die;
							}

						}

/////

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Sending of SMS successfully started!';

						if(!empty($this->vars['post']['rowid'])) {
							$retval['rowid'] = $this->vars['post']['rowid'];
						}

						if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>1),'smsoutbox_status=0'))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						json_encode_return($retval);
						die;

					} else

					if($this->vars['post']['method']=='messagingresend') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Resending SMS.';

						if(!empty($this->vars['post']['rowids'])) {

							//$retval = array();
							//$retval['return_code'] = 'SUCCESS';
							//$retval['return_message'] = 'SMS successfully deleted!';

							$rowids = explode(',', $this->vars['post']['rowids']);

							$arowid = array();

							for($i=0;$i<count($rowids);$i++) {
								$rowid = intval(trim($rowids[$i]));
								if(!empty($rowid)) {
									$arowid[] = $rowid;
								}
							}

							if(!empty($arowid)) {
								$rowids = implode(',', $arowid);

								if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>1),'smsoutbox_id in ('.$rowids.')'))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])) {

							if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_status'=>1),'smsoutbox_id='.$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

						}


						json_encode_return($retval);
						die;

					} else

					if($this->vars['post']['method']=='messagingdelete') {

						if(!empty($this->vars['post']['rowids'])) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'SMS successfully deleted!';

							$rowids = explode(',', $this->vars['post']['rowids']);

							$arowid = array();

							for($i=0;$i<count($rowids);$i++) {
								$rowid = intval(trim($rowids[$i]));
								if(!empty($rowid)) {
									$arowid[] = $rowid;
								}
							}

							if(!empty($arowid)) {
								$rowids = implode(',', $arowid);

								if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_deleted'=>1),'smsoutbox_id in ('.$rowids.')'))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'SMS successfully deleted!';

							if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_deleted'=>1),'smsoutbox_id='.$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							json_encode_return($retval);
							die;
						}
					}

				}

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;

		} // _form_messagingdetailsoutbox2

		function _form_messagingdetailssent($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				//$params = array('hello'=>'sherwin');

				if(!empty($this->vars['post']['method'])) {

					if($this->vars['post']['method']=='onrowselect') {
						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {
							if(!($result = $appdb->query("select * from tbl_smsoutbox where smsoutbox_deleted=0 and smsoutbox_sent=1 and smsoutbox_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['smsoutbox_id'])) {

								$params['smsoutboxinfo'] = $result['rows'][0];

							}
						}
					} else

					if($this->vars['post']['method']=='messagingresend') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Resending of SMS to Outbox!';

						if(!empty($this->vars['post']['rowids'])) {

							//$retval = array();
							//$retval['return_code'] = 'SUCCESS';
							//$retval['return_message'] = 'SMS successfully deleted!';

							$rowids = explode(',', $this->vars['post']['rowids']);

							$arowid = array();

							for($i=0;$i<count($rowids);$i++) {
								$rowid = intval(trim($rowids[$i]));
								if(!empty($rowid)) {
									$arowid[] = $rowid;
								}
							}

							if(!empty($arowid)) {
								//$rowids = implode(',', $arowid);

								//if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_deleted'=>1),'smsoutbox_id in ('.$rowids.')'))) {
								//	json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								//	die;				
								//}

								foreach($arowid as $k=>$rowid) {
/////
									if(!($result = $appdb->query("select * from tbl_smsoutbox where smsoutbox_id=".$rowid))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}

									if(!empty($result['rows'][0]['smsoutbox_id'])) {
										$retval['result'] = $result['rows'];

										$content = array();
										$content['smsoutbox_contactid'] = $result['rows'][0]['smsoutbox_contactid'];
										$content['smsoutbox_contactnumber'] = $result['rows'][0]['smsoutbox_contactnumber'];
										$content['smsoutbox_simnumber'] = $result['rows'][0]['smsoutbox_simnumber'];
										$content['smsoutbox_simname'] = $result['rows'][0]['smsoutbox_simname'];
										$content['smsoutbox_message'] = $result['rows'][0]['smsoutbox_message'];
										//$content['smsoutbox_fullmessage'] = $result['rows'][0]['smsoutbox_fullmessage'];
										$content['smsoutbox_part'] = $result['rows'][0]['smsoutbox_part'];
										$content['smsoutbox_total'] = $result['rows'][0]['smsoutbox_total'];
										$content['smsoutbox_type'] = $result['rows'][0]['smsoutbox_type'];

										if(!($result = $appdb->insert("tbl_smsoutbox",$content,"smsoutbox_id"))) {
											json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
											die;				
										}
									}
/////
								}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])) {

							if(!($result = $appdb->query("select * from tbl_smsoutbox where smsoutbox_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['smsoutbox_id'])) {
								$retval['result'] = $result['rows'];

								$content = array();
								$content['smsoutbox_contactid'] = $result['rows'][0]['smsoutbox_contactid'];
								$content['smsoutbox_contactnumber'] = $result['rows'][0]['smsoutbox_contactnumber'];
								$content['smsoutbox_simnumber'] = $result['rows'][0]['smsoutbox_simnumber'];
								$content['smsoutbox_simname'] = $result['rows'][0]['smsoutbox_simname'];
								$content['smsoutbox_message'] = $result['rows'][0]['smsoutbox_message'];
								//$content['smsoutbox_fullmessage'] = $result['rows'][0]['smsoutbox_fullmessage'];
								$content['smsoutbox_part'] = $result['rows'][0]['smsoutbox_part'];
								$content['smsoutbox_total'] = $result['rows'][0]['smsoutbox_total'];
								$content['smsoutbox_type'] = $result['rows'][0]['smsoutbox_type'];

								if(!($result = $appdb->insert("tbl_smsoutbox",$content,"smsoutbox_id"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}
							}
						}


						json_encode_return($retval);
						die;

					} else

					if($this->vars['post']['method']=='messagingdelete') {

						if(!empty($this->vars['post']['rowids'])) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'SMS successfully deleted!';

							$rowids = explode(',', $this->vars['post']['rowids']);

							$arowid = array();

							for($i=0;$i<count($rowids);$i++) {
								$rowid = intval(trim($rowids[$i]));
								if(!empty($rowid)) {
									$arowid[] = $rowid;
								}
							}

							if(!empty($arowid)) {
								$rowids = implode(',', $arowid);

								if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_deleted'=>1),'smsoutbox_id in ('.$rowids.')'))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {

							$retval = array();
							$retval['return_code'] = 'SUCCESS';
							$retval['return_message'] = 'SMS successfully deleted!';

							if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_deleted'=>1),'smsoutbox_id='.$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							json_encode_return($retval);
							die;
						}
					}
				}

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;

		} // _form_messagingdetailssent


		function _form_messagingdetailsnetworks($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				//$params = array('hello'=>'sherwin');

				if(!empty($this->vars['post']['method'])) {

					if($this->vars['post']['method']=='onrowselect'||$this->vars['post']['method']=='messagingedit'||$this->vars['post']['method']=='messagingnew') {

						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {
							if(!($result = $appdb->query("select * from tbl_network where network_deleted=0 and network_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['network_id'])) {
								$params['networkinfo'] = $result['rows'][0];
							}
						}

						$params['networks'] = getOptionValuesWithType('NETWORK');

						$params['networks_json'] = array();

						if(!empty($params['networks'])) {
							$t = array();
							foreach($params['networks'] as $k=>$v) {
								$selected = false;
								if(!empty($params['networkinfo']['network_name'])&&$params['networkinfo']['network_name']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v,'selected'=>$selected);
							}
							$params['networks_json'] = json_encode($t);
						}


					} else
					if($this->vars['post']['method']=='messagingsave') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Network successfully saved!';

						$networkname = !empty($this->vars['post']['txt_networkname']) ? substrNumChar(trim($this->vars['post']['txt_networkname']),30) : '';
						$networknumber = !empty($this->vars['post']['txt_networknumber']) ? substrNumChar(trim($this->vars['post']['txt_networknumber']),30) : '';

						$content = array('network_number'=>intval($networknumber),'network_name'=>$networkname);

						if(!empty($this->vars['post']['rowid'])) {
							$retval['rowid'] = $this->vars['post']['rowid'];

							if(!($result = $appdb->update("tbl_network",$content,"network_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}
						} else {

							if(!($result = $appdb->insert("tbl_network",$content,"network_id"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['returning'][0]['network_id'])) {
								$retval['rowid'] = $result['returning'][0]['network_id'];
							}

							$appdb->insert("tbl_group",array('group_name'=>$networkname,'group_desc'=>'Group for '.$networkname,'group_flag'=>1));
						}

						json_encode_return($retval);
						die;

					} else
					if($this->vars['post']['method']=='messagingdelete') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Network successfully deleted!';

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

								if(!($result = $appdb->query("delete from tbl_network where network_id in (".$rowids.")"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])) {
							if(!($result = $appdb->query("delete from tbl_network where network_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}
						}

						json_encode_return($retval);
						die;
					}					

				}


				//if(!empty($this->vars['post']['method'])&&!empty($this->vars['post']['rowid'])&&$this->vars['post']['method']=='onrowselect'&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {


				//}

				$templatefile = $this->templatefile($routerid,$formid);

				/*if(!empty($this->vars['post']['roleid'])&&empty($this->vars['post']['userid'])) {
					$templatefile = $this->templatefile($routerid,'usermanagementnewroles');

					$params = array('edit'=>true);
				} else
				if(!empty($this->vars['post']['roleid'])&&!empty($this->vars['post']['userid'])) {
					$templatefile = $this->templatefile($routerid,'usermanagementnewuser');

					$params = array('edit'=>true);
				}*/


				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;

		} // _form_messagingdetailsnetworks

		function _form_messagingdetailssim($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				if(!empty($this->vars['post']['method'])) {

					if($this->vars['post']['method']=='onrowselect'||$this->vars['post']['method']=='messagingedit') {
						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {
							if(!($result = $appdb->query("select * from tbl_sim where sim_deleted=0 and sim_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['sim_id'])) {

								$params['siminfo'] = $result['rows'][0];

								if($this->vars['post']['method']=='messagingedit'&&$params['siminfo']['sim_number']=='UNKNOWN') {
									$params['siminfo']['sim_number'] = false;
								}

							}
						}
					} else
					if($this->vars['post']['method']=='messagingsave') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'SIM successfully saved!';

						//pre(array('$vars'=>$this->vars));

						$sim_device = !empty($this->vars['post']['txt_simdevice']) ? trim($this->vars['post']['txt_simdevice']) : '';

						$sim_number = !empty($this->vars['post']['txt_simnumber']) ? trim($this->vars['post']['txt_simnumber']) : '';

						$mobileNetwork = getNetworkName($sim_number);

						$content = array(
							'sim_number' => $sim_number,
							'sim_desc' => !empty($this->vars['post']['txt_simdesc']) ? trim($this->vars['post']['txt_simdesc']) : '',
							'sim_name' => !empty($this->vars['post']['txt_simname']) ? substrNumChar(trim($this->vars['post']['txt_simname']),20) : '',
							'sim_updatestamp' => 'now()',
							'sim_disabled' => !empty($this->vars['post']['txt_simdisabled']) ? 1 : 0,
							'sim_hotline' => !empty($this->vars['post']['txt_simhotline']) ? 1 : 0,
							'sim_menu' => !empty($this->vars['post']['txt_simmenu']) ? 1 : 0,
							'sim_eload' => !empty($this->vars['post']['txt_simeload']) ? 1 : 0,
							'sim_network' => $mobileNetwork,
						);

						if(!empty($this->vars['post']['rowid'])) {
							$retval['rowid'] = $this->vars['post']['rowid'];

							if(!($result = $appdb->update("tbl_sim",$content,"sim_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}
						} else {

							if(!($result = $appdb->insert("tbl_sim",$content,"sim_id"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['returning'][0]['sim_id'])) {
								$retval['rowid'] = $result['returning'][0]['sim_id'];
							}

						}

						$curl = new MyCurl;

						$curl->get('http://127.0.0.1:8080/pause');

						$ctr=0;
						do {
							if($cont = $curl->get('http://127.0.0.1:8080/processcount')) {
								$count = intval(trim($cont['content']));
								if($count===0) {
									break;
								}
							}

							sleep(1);
						} while($ctr<60);

						//sleep(10);

						setSimNumber($sim_device,$sim_number);

						$curl->get('http://127.0.0.1:8080/resume');

						//if(!empty($this->vars['post']['txt_simnumber'])) {
						//	setSetting('TIME_MODEMINIT_'.$this->vars['post']['txt_simnumber'],1);
						//}

						json_encode_return($retval);
						die;

					} else
					if($this->vars['post']['method']=='messagingdelete') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'SIM successfully deleted!';

						if(!empty($this->vars['post']['rowids'])) {

							$rowids = explode(',', $this->vars['post']['rowids']);

							$arowid = array();

							for($i=0;$i<count($rowids);$i++) {
								$rowid = intval(trim($rowids[$i]));
								if(!empty($rowid)) {
									$arowid[] = $rowid;
								}
							}

							if(!empty($arowid)) {
								$rowids = implode(',', $arowid);

								if(!($result = $appdb->query("delete from tbl_sim where sim_id in (".$rowids.")"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])) {
							if(!($result = $appdb->query("delete from tbl_sim where sim_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}
						}

						json_encode_return($retval);
						die;
					}

				}

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;

		} // _form_messagingdetailssim

		function _form_messagingdetailsports($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				if(!empty($this->vars['post']['method'])) {

					if($this->vars['post']['method']=='onrowselect'||$this->vars['post']['method']=='messagingedit') {
						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {
							if(!($result = $appdb->query("select * from tbl_port where port_deleted=0 and port_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['port_id'])) {

								$params['portinfo'] = $result['rows'][0];

							}
						}
					} else
					if($this->vars['post']['method']=='messagingsave') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Port successfully saved!';

						$portdesc = !empty($this->vars['post']['txt_portdesc']) ? trim($this->vars['post']['txt_portdesc']) : '';
						$portdevice = !empty($this->vars['post']['txt_portdevice']) ? substrNumChar(trim($this->vars['post']['txt_portdevice']),20) : '';
						$portname = !empty($this->vars['post']['txt_portname']) ? substrNumChar(trim($this->vars['post']['txt_portname']),20) : '';
						$portsimnumber = !empty($this->vars['post']['txt_portsimnumber']) ? substrNumChar(trim($this->vars['post']['txt_portsimnumber']),20) : '';
						$portdisabled = !empty($this->vars['post']['txt_portdisabled']) ? $this->vars['post']['txt_portdisabled'] : '0';

						$content = array(
							'port_desc'=>$portdesc,
							'port_device'=>$portdevice,
							'port_name'=>$portname,
							'port_simnumber'=>$portsimnumber,
							'port_disabled'=>$portdisabled,
						);

						if(!empty($this->vars['post']['rowid'])) {
							$retval['rowid'] = $this->vars['post']['rowid'];

							if(!($result = $appdb->update("tbl_port",$content,"port_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}
						} else {

							if(!($result = $appdb->insert("tbl_port",$content,"port_id"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['returning'][0]['port_id'])) {
								$retval['rowid'] = $result['returning'][0]['port_id'];
							}

						}

						json_encode_return($retval);
						die;

					} else
					if($this->vars['post']['method']=='messagingdelete') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Port successfully deleted!';

						if(!empty($this->vars['post']['rowids'])) {

							$rowids = explode(',', $this->vars['post']['rowids']);

							$arowid = array();

							for($i=0;$i<count($rowids);$i++) {
								$rowid = intval(trim($rowids[$i]));
								if(!empty($rowid)) {
									$arowid[] = $rowid;
								}
							}

							if(!empty($arowid)) {
								$rowids = implode(',', $arowid);

								if(!($result = $appdb->query("delete from tbl_port where port_id in (".$rowids.")"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])) {
							if(!($result = $appdb->query("delete from tbl_port where port_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}
						}

						json_encode_return($retval);
						die;
					}

				}

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;

		} // _form_messagingdetailsports

		function _form_messagingmiscinbox($routerid=false,$formid=false,$from=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				//$params['hello'] = 'Hello, Sherwin!';

				if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {

					if($from=='contacts') {
						$contactid = $this->vars['post']['rowid'];
						//pre(array('$contactid'=>$contactid));
					} else {
						if(!($result = $appdb->query("select * from tbl_smsinbox where smsinbox_id=".$this->vars['post']['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['smsinbox_contactsid'])) {
							$contactid = $result['rows'][0]['smsinbox_contactsid'];
						}
					}

					if(!empty($contactid)) {

						$sql = 'select * from (select smsinbox_contactsid as contactid, smsinbox_contactnumber as contactnumber, smsinbox_message as message, smsinbox_timestamp as timestamp, extract(epoch from smsinbox_timestamp) as tt,1 as smstype from tbl_smsinbox where smsinbox_deleted=0 and smsinbox_contactsid='.$contactid.' union all select smsoutbox_contactid as contactid, smsoutbox_contactnumber as contactnumber, smsoutbox_message as message, smsoutbox_sentstamp as timestamp, extract(epoch from smsoutbox_sentstamp) as tt,2 as smstype from tbl_smsoutbox where smsoutbox_deleted=0 and smsoutbox_sent=1 and smsoutbox_contactid='.$contactid.') as tbl order by tt asc';

						//if($from=='contacts') {
						//	pre(array('$sql'=>$sql));
						//}

						if(!($result = $appdb->query($sql))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						$params['messages'] = '';

						//$params['rows'] = $result['rows'];
/*
		<blockquote class="example-right">
		<p>It’s not what you look at that matters, it’s what you see.</p>
		</blockquote>
		<p>Henry David Thoreau</p>
*/

						if(!empty($result['rows'][0]['contactid'])) {
							foreach($result['rows'] as $k=>$v) {
								if($v['smstype']==2) {
									$params['messages'] .= '<blockquote class="example-right">';
								} else {
									$params['messages'] .= '<blockquote class="example-obtuse">';
								}
								$params['messages'] .= '<p>'.str_replace("\n",'<br />',$v['message']).'</p>';
								$params['messages'] .= '</blockquote>';

								if($v['smstype']==1) {
									$params['messages'] .= '<p>'.$v['contactnumber'].'<br />'.$v['timestamp'].'</p>';
								} else {
									$params['messages'] .= '<p>System<br />'.$v['timestamp'].'</p>';									
								}								
							}
						} else {
							$params['messages'] = '<blockquote class="example-right">';
							$params['messages'] .= '<p>SMS History is not available.</p>';
							$params['messages'] .= '</blockquote>';
							$params['messages'] .= '<p>System</p>';
						}
					} else {
						$params['messages'] = '<blockquote class="example-right">';
						$params['messages'] .= '<p>SMS History is not available.</p>';
						$params['messages'] .= '</blockquote>';
						$params['messages'] .= '<p>System</p>';
					}
				}

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;
			
		} // _form_messagingmiscinbox

		function _form_messagingmisccontacts($routerid=false,$formid=false) {
			//pre(array('$vars'=>$this->vars));
			return $this->_form_messagingmiscinbox($routerid,$formid,'contacts');
		}

		function _form_messagingdetailsoptions($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				//$params = array('hello'=>'sherwin');

				if(!empty($this->vars['post']['method'])) {

					if($this->vars['post']['method']=='onrowselect'||$this->vars['post']['method']=='messagingedit') {
						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {
							if(!($result = $appdb->query("select * from tbl_options where options_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['options_id'])) {

								$params['optionsinfo'] = $result['rows'][0];

							}
						}
					} else
					if($this->vars['post']['method']=='messagingsave') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Options successfully saved!';

						//$networkname = !empty($this->vars['post']['txt_networkname']) ? substrNumChar(trim($this->vars['post']['txt_networkname']),30) : '';
						//$networknumber = !empty($this->vars['post']['txt_networknumber']) ? substrNumChar(trim($this->vars['post']['txt_networknumber']),30) : '';

						$content = array(
							'options_name'=>$this->vars['post']['txt_optionsname'],
							'options_type'=>$this->vars['post']['txt_optionstype'],
							'options_value'=>$this->vars['post']['txt_optionsvalue'],
							//'options_priority'=>intval($this->vars['post']['txt_optionspriority']),
						);

						$content['options_hidden'] = !empty($this->vars['post']['txt_optionshidden']) ? 1 : 0;

						//pre(array('$content'=>$content));

						if(!empty($this->vars['post']['rowid'])) {
							$retval['rowid'] = $this->vars['post']['rowid'];

							if(!($result = $appdb->update("tbl_options",$content,"options_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}
						} else {

							if(!($result = $appdb->insert("tbl_options",$content,"options_id"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['returning'][0]['options_id'])) {
								$retval['rowid'] = $result['returning'][0]['options_id'];
							}
						}

						json_encode_return($retval);
						die;

					} else
					if($this->vars['post']['method']=='messagingclone') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Options successfully cloned!';

						if(!empty($this->vars['post']['rowid'])) {

							if(!($result = $appdb->query("select * from tbl_options where options_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							//pre(array('result'=>$result));

							if(!empty($result['rows'][0]['options_id'])) {
								$content = $result['rows'][0];
								unset($content['options_id']);
								unset($content['options_timestamp']);
								$content['options_name'] = $content['options_name'] . '_clone_' . time();

								if(!($result = $appdb->insert("tbl_options",$content,"options_id"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								if(!empty($result['returning'][0]['options_id'])) {
									$retval['rowid'] = $result['returning'][0]['options_id'];
								}

							}
						}

						json_encode_return($retval);
						die;

					} else 			
					if($this->vars['post']['method']=='messagingdelete') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Options successfully deleted!';

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

								if(!($result = $appdb->query("delete from tbl_options where options_id in (".$rowids.")"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])) {
							if(!($result = $appdb->query("delete from tbl_options where options_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}
						}

						json_encode_return($retval);
						die;
					}					

				}

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;

		} // _form_messagingdetailsoptions

		function _form_messagingdetailssmscommands($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				//$params = array('hello'=>'sherwin');

				if(!empty($this->vars['post']['method'])) {

					if($this->vars['post']['method']=='onrowselect'||$this->vars['post']['method']=='messagingedit'||$this->vars['post']['method']=='messagingnew') {

						$params['smscommandsinfo'] = array();

						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {
							if(!($result = $appdb->query("select * from tbl_smscommands where smscommands_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['smscommands_id'])) {
								$params['smscommandsinfo'] = $result['rows'][0];
							}
						}

						//$params['keycodes'] = getOptionNamesWithType('KEYCODE');
						//$params['productcodes'] = getOptionNamesWithType('PRODUCTCODE');
						//$params['regex'] = getOptionNamesWithType('REGEX');
						//$params['errormessage'] = getOptionNamesWithType('ERRORMESSAGE');

						//$networks = $this->getAllNetworkName();

						$networks = getOptionValuesWithType('NETWORK');

						$options = getAllOptionNames();

						$params['setting'] = $params['errormessage'] = $params['options'] = array();

						$params['errormessage'] = getOptionNamesWithType('ERRORMESSAGE');

						$params['setting'] = getOptionNamesWithType('SETTING');

						if(empty($params['setting'])) {
							$params['setting'] = array();
						}

						if(empty($params['errormessage'])) {
							$params['errormessage'] = array();
						}

						foreach($options as $v) {
							if(!in_array($v, $params['errormessage'])&&!in_array($v,$params['setting'])) {
								$params['options'][] = $v;
							}
						}

						$params['networks'] = $networks;

						//$params['networks'] = array();

						//foreach($networks as $v) {
						//	$params['networks'][] = $v['network_name'];
						//}

						if(!isset($params['smscommandsinfo']['smscommands_priority'])) {
							$params['smscommandsinfo']['smscommands_priority'] = 10;
						}

						if(!empty($params['options'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['options'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key0'])&&$params['smscommandsinfo']['smscommands_key0']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['options1_json'] = json_encode($t);
						}

						if(!empty($params['options'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['options'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key1'])&&$params['smscommandsinfo']['smscommands_key1']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['options2_json'] = json_encode($t);
						}

						if(!empty($params['options'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['options'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key2'])&&$params['smscommandsinfo']['smscommands_key2']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['options3_json'] = json_encode($t);
						}

						if(!empty($params['options'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['options'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key3'])&&$params['smscommandsinfo']['smscommands_key3']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['options4_json'] = json_encode($t);
						}

						if(!empty($params['options'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['options'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key4'])&&$params['smscommandsinfo']['smscommands_key4']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['options5_json'] = json_encode($t);
						}

						if(!empty($params['options'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['options'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key5'])&&$params['smscommandsinfo']['smscommands_key5']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['options6_json'] = json_encode($t);
						}

						if(!empty($params['options'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['options'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key6'])&&$params['smscommandsinfo']['smscommands_key6']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['options7_json'] = json_encode($t);
						}

						if(!empty($params['options'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['options'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key7'])&&$params['smscommandsinfo']['smscommands_key7']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['options8_json'] = json_encode($t);
						}

						if(!empty($params['options'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['options'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key8'])&&$params['smscommandsinfo']['smscommands_key8']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['options9_json'] = json_encode($t);
						}

						if(!empty($params['options'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['options'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key9'])&&$params['smscommandsinfo']['smscommands_key9']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['options10_json'] = json_encode($t);
						}

						if(!empty($params['networks'])) {
							$t = array(array('text'=>'All Network'));
							//$t = array();
							foreach($params['networks'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_network'])&&$params['smscommandsinfo']['smscommands_network']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v,'selected'=>$selected);
							}
							$params['networks_json'] = json_encode($t);
						}

						if(!empty($params['errormessage'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['errormessage'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key0_error'])&&$params['smscommandsinfo']['smscommands_key0_error']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['errormessage1_json'] = json_encode($t);
						}

						if(!empty($params['errormessage'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['errormessage'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key1_error'])&&$params['smscommandsinfo']['smscommands_key1_error']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['errormessage2_json'] = json_encode($t);
						}

						if(!empty($params['errormessage'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['errormessage'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key2_error'])&&$params['smscommandsinfo']['smscommands_key2_error']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['errormessage3_json'] = json_encode($t);
						}

						if(!empty($params['errormessage'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['errormessage'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key3_error'])&&$params['smscommandsinfo']['smscommands_key3_error']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['errormessage4_json'] = json_encode($t);
						}

						if(!empty($params['errormessage'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['errormessage'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key4_error'])&&$params['smscommandsinfo']['smscommands_key4_error']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['errormessage5_json'] = json_encode($t);
						}

						if(!empty($params['errormessage'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['errormessage'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key5_error'])&&$params['smscommandsinfo']['smscommands_key5_error']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['errormessage6_json'] = json_encode($t);
						}

						if(!empty($params['errormessage'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['errormessage'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key6_error'])&&$params['smscommandsinfo']['smscommands_key6_error']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['errormessage7_json'] = json_encode($t);
						}

						if(!empty($params['errormessage'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['errormessage'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key7_error'])&&$params['smscommandsinfo']['smscommands_key7_error']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['errormessage8_json'] = json_encode($t);
						}

						if(!empty($params['errormessage'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['errormessage'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key8_error'])&&$params['smscommandsinfo']['smscommands_key8_error']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['errormessage9_json'] = json_encode($t);
						}

						if(!empty($params['errormessage'])) {
							$t = array(array('text'=>'','value'=>'|'));
							//$t = array();
							foreach($params['errormessage'] as $k=>$v) {
								$selected = false;
								if(isset($params['smscommandsinfo']['smscommands_key9_error'])&&$params['smscommandsinfo']['smscommands_key9_error']==$v) {
									$selected = true;
								}
								$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
							}
							$params['errormessage10_json'] = json_encode($t);
						}


						$actionOptions = array();
						$actionOptions[] = array('text'=>'','value'=>0);
						$actionOptions[] = array('text'=>'_SendSMS','value'=>'_SendSMS');
						$actionOptions[] = array('text'=>'_SendSMStoMobileNumber','value'=>'_SendSMStoMobileNumber');
						$actionOptions[] = array('text'=>'_ReferSMSCommand','value'=>'_ReferSMSCommand');
						$actionOptions[] = array('text'=>'_LoadWalletProcessSMS','value'=>'_LoadWalletProcessSMS');
						$actionOptions[] = array('text'=>'_OverLoadProcessSMS','value'=>'_OverLoadProcessSMS');
						$actionOptions[] = array('text'=>'_OverLoadBalanceProcessSMS','value'=>'_OverLoadBalanceProcessSMS');
						$actionOptions[] = array('text'=>'_eLoadProcessSMS','value'=>'_eLoadProcessSMS');
						$actionOptions[] = array('text'=>'_doProcessSMSCommands','value'=>'_doProcessSMSCommands');

						foreach($actionOptions as $k=>$v) {
							if($v['text']==$params['smscommandsinfo']['smscommands_action0']) {
								$actionOptions[$k]['selected'] = true;
							}
						}

						$params['actions_json'] = json_encode($actionOptions);

						$sims = getAllSims();

						//print_r(array('$sims'=>$sims));

						if(is_array($sims)&&!empty($sims[0]['sim_id'])) {

							/*if(!empty($params['smscommandsinfo']['smscommands_simassignment'])) {
								$simassignment = explode(',',$params['smscommandsinfo']['smscommands_simassignment']);
							} else {
								$simassignment = array();
							}*/

							$readonly = true;

							if($this->vars['post']['method']=='messagingedit'||$this->vars['post']['method']=='messagingnew') {
								$readonly = false;
							}

							$params['sims'][] = array();

							/*$params['sims'][] = array(
									'type' => 'label',
									'label' => 'SIM Assignment & Action',
									'labelWidth' => 300,
								);*/

							$simsactions = array();
							$simassignment = array();
							$allsimsactions = array();
							$modemcommands = array();

							if($this->vars['post']['method']=='onrowselect'||$this->vars['post']['method']=='messagingedit') {
								if(!($result = $appdb->query('select * from tbl_smsactions where smsactions_smscommandsid='.$this->vars['post']['rowid']))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								if(!empty($result['rows'][0]['smsactions_id'])) {
									foreach($result['rows'] as $v) {
										$simsactions[$v['smsactions_simnumber']] = $v;
										$simassignment[] = $v['smsactions_simnumber'];
										$allsimsactions[] = $v['smsactions_action'];
									}
								}
							}

							if($this->vars['post']['method']=='messagingedit'||$this->vars['post']['method']=='messagingnew') {

								//pre(array('$simsactions'=>$simsactions,'$simassignment'=>$simassignment));

								if(!($result = $appdb->query('select * from tbl_modemcommands order by modemcommands_id asc'))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								//pre(array('$result'=>$result));

								if(!empty($result['rows'][0]['modemcommands_id'])) {
									$modemcommands = $result['rows'];
									/*
									$comboOptions = array(array('text'=>''));
									foreach($result['rows'] as $v) {
										$selected = false;
										if(in_array($v['modemcommands_name'],$allsimsactions)) {
											$selected = true;
										}
										$comboOptions[] = array('text'=>$v['modemcommands_name'],'value'=>$v['modemcommands_name'],'selected'=>$selected);
									}
									*/

									//pre(array('$comboOptions'=>$comboOptions));
								}
							}

							/*$comboOptions = array();
							$comboOptions[] = array('text'=>'no action','value'=>0);
							$comboOptions[] = array('text'=>'action #1','value'=>'action #1');
							$comboOptions[] = array('text'=>'action #2','value'=>'action #2');*/

							foreach($sims as $k=>$v) {

								$comboOptions = array(array('text'=>''));

								if(!empty($modemcommands)) {
									foreach($modemcommands as $m) {

										//pre(array('$m'=>$m,'$v[sim_number]'=>$v['sim_number'],'$simsactions[$v[sim_number]]'=>$simsactions[$v['sim_number']]));

										$selected = false;
										//if(in_array($simsactions[$v['sim_number']]['smsactions_action'],$allsimsactions)) {
										if(!empty($simsactions[$v['sim_number']]['smsactions_action'])&&$m['modemcommands_name']==$simsactions[$v['sim_number']]['smsactions_action']) {
											$selected = true;
										}
										$comboOptions[] = array('text'=>$m['modemcommands_name'],'value'=>$m['modemcommands_name'],'selected'=>$selected);
									}
								}

								//pre(array('$comboOptions'=>$comboOptions));

								$checked = false;

								if(in_array($v['sim_number'], $simassignment)) {
									$checked = true;
								}

								$chkbox = array(
										'type' => 'checkbox',
										'label' => $v['sim_name'] . ' / ' . $this->getNetworkName($v['sim_number']), /*.'/'.$v['sim_desc'],*/
										'name' => 'chk_sim_'.$k,
										'disabled' => false,
										'readonly' => $readonly,
										'checked' => $checked,
										'position' => 'label-right',
										'labelWidth' => 250,
										'value' => $v['sim_number'],
									);

								$newcolumn = array(
										'type' => 'newcolumn',
										'offset' => 10,
									);

								$action = array(
										'type' => 'input',
										'name' => 'txt_smsactions'.$k,
										'inputWidth' => 404,
										'readonly' => true,
										'value' => !empty($simsactions[$v['sim_number']]['smsactions_action']) ? $simsactions[$v['sim_number']]['smsactions_action'] : '',
									);

								$combo = array(
										'type' => 'combo',
										'name' => 'txt_smsactions'.$k,
										'required' => false,
										'readonly' => !$checked,
										'disabled' => !$checked,
										'inputWidth' => 404,
										'options' => $comboOptions,										
									);

								if($readonly) {
									$block = array(
											'type' => 'block',
											'width' => 1000,
											'blockOffset' => 0,
											'list' => array($chkbox,$newcolumn,$action)
										);
								} else {
									$block = array(
											'type' => 'block',
											'width' => 1000,
											'blockOffset' => 0,
											'list' => array($chkbox,$newcolumn,$combo)
										);									
								}

								/*$params['sims'][] = array(
										'type' => 'checkbox',
										'label' => $v['sim_name'].'/'.$v['sim_desc'],
										'name' => 'chk_sim_'.$k,
										'disabled' => false,
										'readonly' => $readonly,
										'checked' => $checked,
										'position' => 'label-right',
										'labelWidth' => 250,
										'value' => $v['sim_number'],
									);*/

								$params['sims'][] = $block;

							}

							$params['sim_json'] = json_encode($params['sims']);
						}
					} else
					if($this->vars['post']['method']=='messagingsave') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'SMS Commands successfully saved!';

						//pre(array('$vars'=>$this->vars));

						$content = array();

						for($i=0;$i<10;$i++) {
							$t = explode('|', $this->vars['post']['txt_smscommands_key'.$i]);
							$y = !empty($t[0]) ? trim($t[0]) : '';
							$content['smscommands_key'.$i] = '';
							if(!empty($y)) {
								$content['smscommands_key'.$i] = $y;
							}
						}

						for($i=0;$i<10;$i++) {
							$t = explode('|', $this->vars['post']['txt_smscommands_key'.$i.'_error']);
							$y = !empty($t[0]) ? trim($t[0]) : '';
							$content['smscommands_key'.$i.'_error'] = '';
							if(!empty($y)) {
								$content['smscommands_key'.$i.'_error'] = $y;
							}
						}

						for($i=1;$i<10;$i++) {
							$content['smscommands_key'.$i.'_optional'] = 0;
							if(!empty($this->vars['post']['txt_smscommands_key'.$i.'_optional'])) {
								$content['smscommands_key'.$i.'_optional'] = trim($this->vars['post']['txt_smscommands_key'.$i.'_optional']);
							}
						}

						for($i=0;$i<10;$i++) {
							$content['smscommands_action'.$i] = '';
							if(!empty($this->vars['post']['txt_smscommands_action'.$i])) {
								$content['smscommands_action'.$i] = trim($this->vars['post']['txt_smscommands_action'.$i]);
							}
						}

						for($i=0;$i<10;$i++) {
							$content['smscommands_sendsms'.$i] = '';
							if(!empty($this->vars['post']['txt_smscommands_sendsms'.$i])) {
								$content['smscommands_sendsms'.$i] = trim($this->vars['post']['txt_smscommands_sendsms'.$i]);
							}
						}

						$content['smscommands_active'] = 0;

						if(!empty($this->vars['post']['txt_smscommands_active'])) {
							$content['smscommands_active'] = $this->vars['post']['txt_smscommands_active'];
						}

						$content['smscommands_priority'] = 10;

						if(isset($this->vars['post']['txt_smscommands_priority'])) {
							$content['smscommands_priority'] = $this->vars['post']['txt_smscommands_priority'];
						}						

						if(!empty($this->vars['post']['rowid'])) {
							$retval['rowid'] = $this->vars['post']['rowid'];

							if(!($result = $appdb->update("tbl_smscommands",$content,"smscommands_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}
						} else {

							if(!($result = $appdb->insert("tbl_smscommands",$content,"smscommands_id"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['returning'][0]['smscommands_id'])) {
								$retval['rowid'] = $result['returning'][0]['smscommands_id'];
							}
						}

						//pre(array('$content1'=>$content));


						if(!($result = $appdb->query('delete from tbl_smsactions where smsactions_smscommandsid='.$retval['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						for($i=0;$i<10;$i++) {

							//pre(array('chk_sim_'.$i=>$this->vars['post']['chk_sim_'.$i],'txt_smsactions'.$i=>$this->vars['post']['txt_smsactions'.$i]));

							if(!empty($this->vars['post']['chk_sim_'.$i])&&!empty($this->vars['post']['txt_smsactions'.$i])) {
								$content = array();
								$content['smsactions_smscommandsid'] = $retval['rowid'];
								$content['smsactions_simnumber'] = trim($this->vars['post']['chk_sim_'.$i]);
								$content['smsactions_action'] = trim($this->vars['post']['txt_smsactions'.$i]);

								//pre(array('$content'=>$content));

								if(!($result = $appdb->insert("tbl_smsactions",$content,"smsactions_smscommandsid"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}
							}
						}

						//$networkname = !empty($this->vars['post']['txt_networkname']) ? substrNumChar(trim($this->vars['post']['txt_networkname']),30) : '';
						//$networknumber = !empty($this->vars['post']['txt_networknumber']) ? substrNumChar(trim($this->vars['post']['txt_networknumber']),30) : '';

						/*$content = array(
							'options_name'=>$this->vars['post']['txt_optionsname'],
							'options_type'=>$this->vars['post']['txt_optionstype'],
							'options_value'=>$this->vars['post']['txt_optionsvalue'],
						);

						if(!empty($this->vars['post']['rowid'])) {
							$retval['rowid'] = $this->vars['post']['rowid'];

							if(!($result = $appdb->update("tbl_options",$content,"options_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}
						} else {

							if(!($result = $appdb->insert("tbl_options",$content,"options_id"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['returning'][0]['options_id'])) {
								$retval['rowid'] = $result['returning'][0]['options_id'];
							}
						}*/

						json_encode_return($retval);
						die;

					} else
					if($this->vars['post']['method']=='messagingclone') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'SMS Commands successfully cloned!';

						if(!empty($this->vars['post']['rowid'])) {

							if(!($result = $appdb->query("select * from tbl_smscommands where smscommands_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							//pre(array('result'=>$result));

							if(!empty($result['rows'][0]['smscommands_id'])) {
								$content = $result['rows'][0];
								unset($content['smscommands_id']);
								unset($content['smscommands_createstamp']);
								unset($content['smscommands_updatestamp']);

								if(!($result = $appdb->insert("tbl_smscommands",$content,"smscommands_id"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								if(!empty($result['returning'][0]['smscommands_id'])) {
									//$retval['rowid'] = $result['returning'][0]['options_id'];
									$smscommands_id = $result['returning'][0]['smscommands_id'];
								}

							}
						}

						if(!empty($smscommands_id)) {

							if(!($result = $appdb->query("select * from tbl_smsactions where smsactions_smscommandsid=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['smsactions_id'])) {
								$smsactions = $result['rows'];
								foreach($smsactions as $k=>$content) {
									unset($content['smsactions_id']);
									unset($content['smsactions_createstamp']);
									unset($content['smsactions_updatestamp']);
									$content['smsactions_smscommandsid'] = $smscommands_id;

									if(!($result = $appdb->insert("tbl_smsactions",$content,"smsactions_id"))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}

								}

								$retval['rowid'] = $smscommands_id;
							}
						}

						json_encode_return($retval);
						die;

					} else 			
					if($this->vars['post']['method']=='messagingdelete') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'SMS Commands successfully deleted!';

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

								if(!($result = $appdb->query("delete from tbl_smscommands where smscommands_id in (".$rowids.")"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								if(!($result = $appdb->query("delete from tbl_smsactions where smsactions_smscommandsid in (".$rowids.")"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])) {
							if(!($result = $appdb->query("delete from tbl_smscommands where smscommands_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!($result = $appdb->query("delete from tbl_smsactions where smsactions_smscommandsid=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}
						}

						json_encode_return($retval);
						die;
					}					

				}

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;

		} // _form_messagingdetailssmscommands

		function _form_messagingdetailsmodemcommands($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$params = array();

				//$params = array('hello'=>'sherwin');

				if(!empty($this->vars['post']['method'])) {

					if($this->vars['post']['method']=='onrowselect'||$this->vars['post']['method']=='messagingedit'||$this->vars['post']['method']=='messagingnew') {

						$params['modemcommandsinfo'] = array();

						if(!empty($this->vars['post']['rowid'])&&is_numeric($this->vars['post']['rowid'])&&$this->vars['post']['rowid']>0) {
							if(!($result = $appdb->query("select * from tbl_modemcommands where modemcommands_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['modemcommands_id'])) {

								$params['modemcommandsinfo'] = $result['rows'][0];

								if(!($result = $appdb->query("select * from tbl_atcommands where atcommands_modemcommandsid=".$this->vars['post']['rowid']." order by atcommands_id asc"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								if(!empty($result['rows'][0]['atcommands_id'])) {
									$params['atcommandsinfo'] = $result['rows'];

									if(!empty($this->vars['post']['insertrow'])) {
										$insertrow = intval($this->vars['post']['insertrow']) - 1;

										$atcommandsinfo = array();

										foreach($params['atcommandsinfo'] as $k=>$v) {
											if($insertrow===$k) {
												$atcommandsinfo[] = array();
											}
											$atcommandsinfo[] =  $v;
										}

										$params['atcommandsinfo'] = $atcommandsinfo;
									}
								}

							}
						}

						$options = getAllOptionNames();

						$params['setting'] = $params['errormessage'] = $params['options'] = array();

						$params['errormessage'] = getOptionNamesWithType('ERRORMESSAGE');

						$params['setting'] = getOptionNamesWithType('SETTING');

						if(empty($params['setting'])) {
							$params['setting'] = array();
						}

						if(empty($params['errormessage'])) {
							$params['errormessage'] = array();
						}

						foreach($options as $v) {
							if(!in_array($v, $params['errormessage'])&&!in_array($v,$params['setting'])) {
								$params['options'][] = $v;
							}
						}

						if($this->vars['post']['method']=='messagingedit'||$this->vars['post']['method']=='messagingnew') {

							for($i=0;$i<20;$i++) { // 

								for($r=0;$r<3;$r++) {

									if(!empty($params['options'])) {
										$t = array(array('text'=>'','value'=>'|'));
										//$t = array();
										foreach($params['options'] as $k=>$v) {
											$selected = false;
											if(isset($params['atcommandsinfo'][$i]['atcommands_regx'.$r])&&$params['atcommandsinfo'][$i]['atcommands_regx'.$r]==$v) {
												$selected = true;
											}
											$t[] = array('text'=>$v,'value'=>$v.'|'.getOption($v),'selected'=>$selected);
										}
										$params['atcommandsinfo'][$i]['options'.$r.'_json'] = json_encode($t);
									}

								}
							}

						}

					} else
					if($this->vars['post']['method']=='messagingsave') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'Options successfully saved!';

						//pre(array('$vars'=>$this->vars));

						$content = array();

						$content['modemcommands_name'] = !empty($this->vars['post']['txt_modemcommandsname']) ? $this->vars['post']['txt_modemcommandsname'] : '';
						$content['modemcommands_desc'] = !empty($this->vars['post']['txt_modemcommandsdesc']) ? $this->vars['post']['txt_modemcommandsdesc'] : '';
						//$content['modemcommands_at'] = !empty($this->vars['post']['txt_modemcommandsat']) ? $this->vars['post']['txt_modemcommandsat'] : '';

						//$content['modemcommands_resultindex'] = isset($this->vars['post']['txt_modemcommandsresultindex']) ? $this->vars['post']['txt_modemcommandsresultindex'] : '';
						//$content['modemcommands_expectedresult'] = isset($this->vars['post']['txt_modemcommandsexpectedresult']) ? $this->vars['post']['txt_modemcommandsexpectedresult'] : '';
						//$content['modemcommands_repeat'] = isset($this->vars['post']['txt_modemcommandsrepeat']) ? $this->vars['post']['txt_modemcommandsrepeat'] : '';

						/*for($i=0;$i<10;$i++) {
							$t = explode('|', $this->vars['post']['txt_modemcommands_regx'.$i]);
							$y = !empty($t[0]) ? trim($t[0]) : '';
							$content['modemcommands_regx'.$i] = '';
							if(!empty($y)) {
								$content['modemcommands_regx'.$i] = $y;
							}
						}*/

						if(!empty($this->vars['post']['rowid'])) {
							$retval['rowid'] = $this->vars['post']['rowid'];

							if(!($result = $appdb->update("tbl_modemcommands",$content,"modemcommands_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

						} else {

							if(!($result = $appdb->insert("tbl_modemcommands",$content,"modemcommands_id"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['returning'][0]['modemcommands_id'])) {
								$retval['rowid'] = $result['returning'][0]['modemcommands_id'];
							}
						}


						if(!empty($retval['rowid'])&&!empty($this->vars['post']['txt_atcommands_at'])) {

							if(!($result = $appdb->query("delete from tbl_atcommands where atcommands_modemcommandsid=".$retval['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							foreach($this->vars['post']['txt_atcommands_at'] as $k=>$v) {
								if(trim($v)=='') continue;

								$content = array();
								$content['atcommands_modemcommandsid'] = $retval['rowid'];
								$content['atcommands_at'] = $v;

								if(!empty($this->vars['post']['txt_atcommands_regx0'][$k])) {
									$t = explode('|', $this->vars['post']['txt_atcommands_regx0'][$k]);
									$y = !empty($t[0]) ? trim($t[0]) : '';
									$content['atcommands_regx0'] = $y;
								}

								if(!empty($this->vars['post']['txt_atcommands_regx1'][$k])) {
									$t = explode('|', $this->vars['post']['txt_atcommands_regx1'][$k]);
									$y = !empty($t[0]) ? trim($t[0]) : '';
									$content['atcommands_regx1'] = $y;
								}

								if(!empty($this->vars['post']['txt_atcommands_regx2'][$k])) {
									$t = explode('|', $this->vars['post']['txt_atcommands_regx2'][$k]);
									$y = !empty($t[0]) ? trim($t[0]) : '';
									$content['atcommands_regx2'] = $y;
								}

								if(!empty($this->vars['post']['txt_atcommands_param0'][$k])) {
									$t = explode('|', $this->vars['post']['txt_atcommands_param0'][$k]);
									$y = !empty($t[0]) ? trim($t[0]) : '';
									$content['atcommands_param0'] = $y;
								}

								if(!empty($this->vars['post']['txt_atcommands_param1'][$k])) {
									$t = explode('|', $this->vars['post']['txt_atcommands_param1'][$k]);
									$y = !empty($t[0]) ? trim($t[0]) : '';
									$content['atcommands_param1'] = $y;
								}

								if(!empty($this->vars['post']['txt_atcommands_param2'][$k])) {
									$t = explode('|', $this->vars['post']['txt_atcommands_param2'][$k]);
									$y = !empty($t[0]) ? trim($t[0]) : '';
									$content['atcommands_param2'] = $y;
								}

								$content['atcommands_resultindex'] = !empty($this->vars['post']['txt_atcommands_resultindex'][$k]) ? $this->vars['post']['txt_atcommands_resultindex'][$k] : 0;
								$content['atcommands_expectedresult'] = !empty($this->vars['post']['txt_atcommands_expectedresult'][$k]) ? $this->vars['post']['txt_atcommands_expectedresult'][$k] : '';
								$content['atcommands_repeat'] = !empty($this->vars['post']['txt_atcommands_repeat'][$k]) ? $this->vars['post']['txt_atcommands_repeat'][$k] : 0;

								if(!($result = $appdb->insert("tbl_atcommands",$content,"atcommands_id"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								//$atcommands[] = $content;
								
							}
						}


						//$networkname = !empty($this->vars['post']['txt_networkname']) ? substrNumChar(trim($this->vars['post']['txt_networkname']),30) : '';
						//$networknumber = !empty($this->vars['post']['txt_networknumber']) ? substrNumChar(trim($this->vars['post']['txt_networknumber']),30) : '';

						/*$content = array(
							'options_name'=>$this->vars['post']['txt_optionsname'],
							'options_type'=>$this->vars['post']['txt_optionstype'],
							'options_value'=>$this->vars['post']['txt_optionsvalue'],
						);

						if(!empty($this->vars['post']['rowid'])) {
							$retval['rowid'] = $this->vars['post']['rowid'];

							if(!($result = $appdb->update("tbl_options",$content,"options_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}
						} else {

							if(!($result = $appdb->insert("tbl_options",$content,"options_id"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['returning'][0]['options_id'])) {
								$retval['rowid'] = $result['returning'][0]['options_id'];
							}
						}*/

						json_encode_return($retval);
						die;

					} else
					if($this->vars['post']['method']=='messagingdelete') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'MODEM Commands successfully deleted!';

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

								if(!($result = $appdb->query("delete from tbl_modemcommands where modemcommands_id in (".$rowids.")"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}


								if(!($result = $appdb->query("delete from tbl_atcommands where atcommands_modemcommandsid in (".$rowids.")"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								json_encode_return($retval);
								die;
							}

						}

						if(!empty($this->vars['post']['rowid'])) {
							if(!($result = $appdb->query("delete from tbl_modemcommands where modemcommands_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}


							if(!($result = $appdb->query("delete from tbl_atcommands where atcommands_modemcommandsid=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}
						}

						json_encode_return($retval);
						die;
					} else
					if($this->vars['post']['method']=='messagingclone') {

						$retval = array();
						$retval['return_code'] = 'SUCCESS';
						$retval['return_message'] = 'MODEM Commands successfully cloned!';

						if(!empty($this->vars['post']['rowid'])) {

							if(!($result = $appdb->query("select * from tbl_modemcommands where modemcommands_id=".$this->vars['post']['rowid']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['modemcommands_id'])) {

								$content = $result['rows'][0];
								$content['modemcommands_name'] = $content['modemcommands_name'] . '_clone_' . time();
								unset($content['modemcommands_id']);
								unset($content['modemcommands_createstamp']);
								unset($content['modemcommands_updatestamp']);

								if(!($result = $appdb->insert("tbl_modemcommands",$content,"modemcommands_id"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								if(!empty($result['returning'][0]['modemcommands_id'])) {
									$modemcommands_id = $result['returning'][0]['modemcommands_id'];
								}

							}

						}

						if(!empty($modemcommands_id)) {

							if(!($result = $appdb->query("select * from tbl_atcommands where atcommands_modemcommandsid=".$this->vars['post']['rowid']." order by atcommands_id asc"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['rows'][0]['atcommands_id'])) {

								foreach($result['rows'] as $k=>$content) {
									$content['atcommands_modemcommandsid'] = $modemcommands_id;
									unset($content['atcommands_id']);
									unset($content['atcommands_createstamp']);
									unset($content['atcommands_updatestamp']);

									if(!($result = $appdb->insert("tbl_atcommands",$content,"atcommands_id"))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;				
									}
								}

								$retval['rowid'] = $modemcommands_id;
							}

						}

						json_encode_return($retval);
						die;
					}					


				}

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;

		} // _form_messagingdetailsmodemcommands

		function _form_messagingdetailsautoreply($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;
			//pre(array('$this->vars'=>$this->vars));

			if(!empty($routerid)&&!empty($formid)) {

				$readonly = true;

				$post = $this->vars['post'];

				$params = array();

				//$params = array('hello'=>'sherwin');

				if(!empty($post['method'])&&($post['method']=='messagingedit'||$post['method']=='messagingnew')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&($post['method']=='onrowselect'||$post['method']=='messagingedit')) {
					if(!empty($post['rowid'])&&is_numeric($post['rowid'])&&$post['rowid']>0) {
						if(!($result = $appdb->query("select * from tbl_smscommands where smscommands_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['smscommands_id'])) {

							$params['autoreplyinfo'] = $result['rows'][0];

						}
					}
				} else
				if(!empty($post['method'])&&$post['method']=='messagingsave') {

					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Auto reply successfully saved!';

					//$networkname = !empty($this->vars['post']['txt_networkname']) ? substrNumChar(trim($this->vars['post']['txt_networkname']),30) : '';
					//$networknumber = !empty($this->vars['post']['txt_networknumber']) ? substrNumChar(trim($this->vars['post']['txt_networknumber']),30) : '';

					$content = array();
					$content['smscommands_priority'] = 10;
					$content['smscommands_action0'] = '_SendSMS';
					$content['smscommands_key0'] = !empty($post['smscommands_key0']) ? '$KEY_'.strtoupper($post['smscommands_key0']) : '';
					$content['smscommands_active'] = !empty($post['smscommands_active']) ? 1 : 0;
					$content['smscommands_type'] = 'autoreply';

					for($i=0;$i<10;$i++) {
						$content['smscommands_sendsms'.$i] = !empty($post['smscommands_sendsms'.$i]) ? $post['smscommands_sendsms'.$i] : '';
					}

					if(!empty($post['rowid'])) {
						$retval['rowid'] = $post['rowid'];
						$content['smscommands_updatestamp'] = 'now()';

						if(!($result = $appdb->update("tbl_smscommands",$content,"smscommands_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
					} else {

						if(!($result = $appdb->insert("tbl_smscommands",$content,"smscommands_id"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['returning'][0]['smscommands_id'])) {
							$retval['rowid'] = $result['returning'][0]['smscommands_id'];
						}
					}

					setOption($content['smscommands_key0'],strtoupper($post['smscommands_key0']),'KEYCODE',true);

					json_encode_return($retval);
					die;

				} else
				if(!empty($post['method'])&&$post['method']=='messagingclone') {

					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Auto reply successfully cloned!';

					if(!empty($post['rowid'])) {

						if(!($result = $appdb->query("select * from tbl_smscommands where smscommands_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						//pre(array('result'=>$result));

						if(!empty($result['rows'][0]['smscommands_id'])) {
							$content = $result['rows'][0];
							$content['smscommands_updatestamp'] = 'now()';
							unset($content['smscommands_id']);
							unset($content['smscommands_createstamp']);
							$content['smscommands_key0'] = $content['smscommands_key0'] . '_clone_' . time();

							if(!($result = $appdb->insert("tbl_smscommands",$content,"smscommands_id"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							if(!empty($result['returning'][0]['smscommands_id'])) {
								$retval['rowid'] = $result['returning'][0]['smscommands_id'];
							}

						}
					}

					json_encode_return($retval);
					die;

				} else 			
				if(!empty($post['method'])&&$post['method']=='messagingdelete') {

					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Auto reply successfully deleted!';

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

							if(!($result = $appdb->query("delete from tbl_smscommands where smscommands_id in (".$rowids.")"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							json_encode_return($retval);
							die;
						}

					}

					if(!empty($post['rowid'])) {
						if(!($result = $appdb->query("delete from tbl_smscommands where smscommands_id=".$post['rowid']))) {
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

				$block = array();

				$block[] = array(
					'type' => 'input',
					'label' => 'KEYWORD',
					'name' => 'smscommands_key0',
					'readonly' => $readonly,
					'required' => !$readonly,
					//'validate' => "NotEmpty",
					//'labelAlign' => $position,
					'value' => !empty($params['autoreplyinfo']['smscommands_key0']) ? getOption($params['autoreplyinfo']['smscommands_key0']) : '',
				);

				$block[] = array('type' => 'newcolumn','offset' => 20,);

				$block[] = array(
					'type' => 'checkbox',
					'label' => 'ACTIVE',
					'name' => 'smscommands_active',
					//'offsetTop' => 10,
					'position' => 'label-right',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'checked' => !empty($params['autoreplyinfo']['smscommands_active']) ? true : false,
				);

				$params['tbDetails'][] = array(
					'type' => 'block',
					'width' => 1500,
					'blockOffset' => 0,
					'offsetTop' => 0,
					'list' => $block,
				);

				$block = array();

				/*$block[] = array(
					'type' => 'input',
					'label' => 'SMS #1',
					'name' => 'smscommands_sendsms0',
					'readonly' => $readonly,
					'required' => !$readonly,
					//'validate' => "NotEmpty",
					'inputWidth' => 500,
					'rows' => 3,
					'value' => !empty($params['autoreplyinfo']['smscommands_sendsms0']) ? $params['autoreplyinfo']['smscommands_sendsms0'] : '',
				);*/

				for($i=0;$i<10;$i++) {
					$block[] = array(
						'type' => 'input',
						'label' => 'SMS #'.($i+1),
						'name' => 'smscommands_sendsms'.$i,
						'readonly' => $readonly,
						'required' => $i===0 ? !$readonly : false,
						//'validate' => "NotEmpty",
						'inputWidth' => 500,
						'rows' => 3,
						'value' => !empty($params['autoreplyinfo']['smscommands_sendsms'.$i]) ? $params['autoreplyinfo']['smscommands_sendsms'.$i] : '',
					);
				}

				$params['tbDetails'][] = array(
					'type' => 'block',
					'width' => 1500,
					'blockOffset' => 0,
					'offsetTop' => 0,
					'list' => $block,
				);

				$templatefile = $this->templatefile($routerid,$formid);

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}				
			}

			return false;

		} // _form_messagingdetailsautoreply

		function router() {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

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

					//if($this->post['formid']=='massagingone') {
					//	pre(array('$this->vars'=>$this->vars,'$this->post'=>$this->post));
					//}

					$form = $this->_form($this->post['routerid'], $this->post['formid']);

					$jsonxml = $this->_xml($this->post['routerid'], $this->post['formid']);

					if(!empty($this->post['formval'])) {
						$form = str_replace('%formval%',$this->post['formval'],$form);
					}

					$retval = array('html'=>$form);

					if(!empty($jsonxml)) {
						$retval['xml'] = $jsonxml;
					}

					//if($this->post['formid']=='massagingone') {
					//	pre(array('$retval'=>$retval));
					//}

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
					//pre($this->post);
					//die;

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
					if($this->post['table']=='smscommands') {
						if(!($result = $appdb->query("select * from tbl_smscommands order by smscommands_priority asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['smscommands_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['smscommands_id'],'data'=>array(0,$v['smscommands_id'],$v['smscommands_priority'],$v['smscommands_key0'],$v['smscommands_key1'],$v['smscommands_key2'],$v['smscommands_key3'],$v['smscommands_key4'],$v['smscommands_network'],$v['smscommands_active'],$v['smscommands_action0']));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='options') {

						if($applogin->isSystemAdministrator()) {
							if(!($result = $appdb->query("select options_id,options_name,options_value,options_type,options_priority from tbl_options order by options_id asc"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}
						} else {
							if(!($result = $appdb->query("select options_id,options_name,options_value,options_type,options_priority from tbl_options where options_hidden=0 order by options_id asc"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}							
						}

						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['options_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['options_id'],'data'=>array(0,$v['options_id'],$v['options_name'],$v['options_type'],htmlentities($v['options_value'])));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='network') {
						if(!($result = $appdb->query("select network_id,network_number,network_name from tbl_network order by network_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['network_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['network_id'],'data'=>array(0,$v['network_id'],$v['network_number'],$v['network_name']));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='port') {
						if(!($result = $appdb->query("select port_id,port_device,port_name,port_simnumber,port_network,port_desc,case when port_disabled=1 then 'disabled' when port_disabled=0 then 'enabled' end from tbl_port"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['port_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								//$rows[] = array('id'=>$v['port_id'],'data'=>array(0,$v['port_id'],$v['port_device'],$v['port_name'],$v['port_simnumber'],$v['port_network'],$v['port_desc'],$v['case']));
								$rows[] = array('id'=>$v['port_id'],'data'=>array(0,$v['port_id'],$v['port_device'],$v['port_simnumber'],$this->getNetworkName($v['port_simnumber'])));
							}

							$retval = array('rows'=>$rows);
						}
					} else
					if($this->post['table']=='sim') {
						if(!($result = $appdb->query("select sim_id,sim_device,sim_name,sim_number,sim_network,sim_desc,case when sim_online=0 then 'no' when sim_online=1 then 'yes' end as sim_online,sim_ip,case when sim_disabled=1 then 'disabled' when sim_disabled=0 then 'enabled' end as sim_disabled from tbl_sim"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['sim_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['sim_id'],'data'=>array(0,$v['sim_id'],$v['sim_name'],$v['sim_number'],$v['sim_network'],$v['sim_desc'],$v['sim_device'],$v['sim_ip'],$v['sim_disabled'],$v['sim_online']));
							}

							$retval = array('rows'=>$rows);
						}
					} else
					if($this->post['table']=='log') {
						if(!($result = $appdb->query("select log_id,log_text,log_result,log_portid from tbl_log"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['log_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['log_id'],'data'=>array($v['log_id'],$v['log_text'],$v['log_result'],$v['log_portid']));
							}

							$retval = array('rows'=>$rows);
						}
					} else
					if($this->post['table']=='inbox') {
						$smsinbox_count = 0;
						$smsoutbox_count = 0;
						$smssent_count = 0;
/////
						if(!($result = $appdb->query("select count(smsinbox_id) as count from tbl_smsinbox where smsinbox_unread=1 and smsinbox_deleted=0 and smsinbox_eload=0"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['count'])) {
							$smsinbox_count = $result['rows'][0]['count'];
						}
/////
						if(!($result = $appdb->query("select count(smsoutbox_id) as count from tbl_smsoutbox where smsoutbox_eload=0 and smsoutbox_sent=0 and smsoutbox_deleted=0 and smsoutbox_delay=0"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['count'])) {
							$smsoutbox_count = $result['rows'][0]['count'];
						}
/////
						if(!($result = $appdb->query("select count(smsoutbox_id) from tbl_smsoutbox where smsoutbox_sent!=0 and smsoutbox_deleted=0"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['count'])) {
							$smssent_count = $result['rows'][0]['count'];
						}
/////

						if(!($result = $appdb->query("select *,extract(epoch from smsinbox_timestamp) as unixstamp from tbl_smsinbox where smsinbox_deleted=0 and smsinbox_eload=0 order by smsinbox_id desc limit ".getOption('$INBOX_MAX_RESULT',1000)))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['smsinbox_id'])) {
							$rows = array();

							//$tmp = explode(',', getOption('$ELOAD_NUMBERS'));
							//$eloadnum = array();

							//foreach($tmp as $e=>$f) {
							//	if($mno=parseMobileNo($f)) {
							//		$eloadnum[] = $mno[2].$mno[3];
							//	}
							//}

							//pre(array('$eloadnum'=>$eloadnum,'$tmp'=>$tmp));

							foreach($result['rows'] as $k=>$v) {

								//if(($mno=parseMobileNo($v['smsinbox_contactnumber']))&&in_array($mno[2].$mno[3],$eloadnum)) {
								//	continue;
								//}

								$rows[] = array('id'=>$v['smsinbox_id'],'unread'=>$v['smsinbox_unread'],'data'=>array(0,$v['smsinbox_id'],getContactNickByID($v['smsinbox_contactsid']),$v['smsinbox_contactnumber'],$this->getSimNameByNumber($v['smsinbox_simnumber']),$v['smsinbox_message'],$this->getNetworkName($v['smsinbox_contactnumber']),pgDate($v['smsinbox_timestamp'])));
							}

							$retval = array('rows'=>$rows);

							if(!empty($smsinbox_count)) {
								$retval['inbox'] = number_format($smsinbox_count);
							}

							if(!empty($smsoutbox_count)) {
								$retval['outbox'] = number_format($smsoutbox_count);
							}

							if(!empty($smssent_count)) {
								$retval['sent'] = number_format($smssent_count);
							}
						}
					} else
					if($this->post['table']=='outbox') {
						$smsinbox_count = 0;
						$smsoutbox_count = 0;
						$smssent_count = 0;
/////
						if(!($result = $appdb->query("select count(smsinbox_id) as count from tbl_smsinbox where smsinbox_unread=1 and smsinbox_deleted=0 and smsinbox_eload=0"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['count'])) {
							$smsinbox_count = $result['rows'][0]['count'];
						}
/////
						if(!($result = $appdb->query("select count(smsoutbox_id) as count from tbl_smsoutbox where smsoutbox_eload=0 and smsoutbox_sent=0 and smsoutbox_deleted=0 and smsoutbox_delay=0"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['count'])) {
							$smsoutbox_count = $result['rows'][0]['count'];
						}
/////
						if(!($result = $appdb->query("select count(smsoutbox_id) from tbl_smsoutbox where smsoutbox_sent!=0 and smsoutbox_deleted=0"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['count'])) {
							$smssent_count = $result['rows'][0]['count'];
						}
/////

						if(!($result = $appdb->query("select smsoutbox_id,smsoutbox_contactid,smsoutbox_contactnumber,smsoutbox_simnumber,smsoutbox_part,smsoutbox_total,case when smsoutbox_type=0 then 'short' when smsoutbox_type=1 then 'long' end as smsoutbox_type,smsoutbox_message,case when smsoutbox_status=0 then 'queued' when smsoutbox_status=1 then 'waiting' when smsoutbox_status=3 then 'sending' when smsoutbox_status=4 then 'sent' when smsoutbox_status=5 then 'failed' end as smsoutbox_status,smsoutbox_createstamp,smsoutbox_sentstamp from tbl_smsoutbox where smsoutbox_eload=0 and smsoutbox_sent=0 and smsoutbox_deleted=0 and smsoutbox_delay=0 order by smsoutbox_id desc limit ".getOption('$OUTBOX_MAX_RESULT',1000)))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['smsoutbox_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['smsoutbox_id'],'data'=>array(0,$v['smsoutbox_id'],getContactNickByID($v['smsoutbox_contactid']),$v['smsoutbox_contactnumber'],$this->getSimNameByNumber($v['smsoutbox_simnumber']),$v['smsoutbox_total'],$v['smsoutbox_type'],$v['smsoutbox_message'],$v['smsoutbox_status'],pgDate($v['smsoutbox_createstamp']),pgDate($v['smsoutbox_sentstamp'])));
							}

							$retval = array('rows'=>$rows);

							if(!empty($smsinbox_count)) {
								$retval['inbox'] = number_format($smsinbox_count);
							}

							if(!empty($smsoutbox_count)) {
								$retval['outbox'] = number_format($smsoutbox_count);
							}

							if(!empty($smssent_count)) {
								$retval['sent'] = number_format($smssent_count);
							}
						}
					} else
					if($this->post['table']=='sent') {
						$smsinbox_count = 0;
						$smsoutbox_count = 0;
						$smssent_count = 0;
/////
						if(!($result = $appdb->query("select count(smsinbox_id) as count from tbl_smsinbox where smsinbox_unread=1 and smsinbox_deleted=0 and smsinbox_eload=0"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['count'])) {
							$smsinbox_count = $result['rows'][0]['count'];
						}
/////
						if(!($result = $appdb->query("select count(smsoutbox_id) as count from tbl_smsoutbox where smsoutbox_eload=0 and smsoutbox_sent=0 and smsoutbox_deleted=0 and smsoutbox_delay=0"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['count'])) {
							$smsoutbox_count = $result['rows'][0]['count'];
						}
/////
						if(!($result = $appdb->query("select count(smsoutbox_id) from tbl_smsoutbox where smsoutbox_sent!=0 and smsoutbox_deleted=0"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['count'])) {
							$smssent_count = $result['rows'][0]['count'];
						}
/////

						if(!($result = $appdb->query("select smsoutbox_id,smsoutbox_contactid,smsoutbox_contactnumber,smsoutbox_simnumber,smsoutbox_part,smsoutbox_total,case when smsoutbox_type=0 then 'short' when smsoutbox_type=1 then 'long' end as smsoutbox_type,smsoutbox_message,smsoutbox_status,smsoutbox_createstamp,smsoutbox_sentstamp from tbl_smsoutbox where smsoutbox_sent!=0 and smsoutbox_deleted=0 order by smsoutbox_id desc limit ".getOption('$SENT_MAX_RESULT',1000)))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['smsoutbox_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								//$rows[] = array('id'=>$v['smsoutbox_id'],'data'=>array($v['smsoutbox_id'],$v['smsoutbox_fromnumber'],$v['smsoutbox_tonumber'],$v['smsoutbox_message'],$v['smsoutbox_network'],$v['smsoutbox_sentstamp']));
								$rows[] = array('id'=>$v['smsoutbox_id'],'data'=>array(0,$v['smsoutbox_id'],getContactNickByID($v['smsoutbox_contactid']),$v['smsoutbox_contactnumber'],$this->getSimNameByNumber($v['smsoutbox_simnumber']),$v['smsoutbox_total'],$v['smsoutbox_type'],$v['smsoutbox_message'],'sent',pgDate($v['smsoutbox_createstamp']),pgDate($v['smsoutbox_sentstamp'])));
							}

							$retval = array('rows'=>$rows);

							if(!empty($smsinbox_count)) {
								$retval['inbox'] = number_format($smsinbox_count);
							}

							if(!empty($smsoutbox_count)) {
								$retval['outbox'] = number_format($smsoutbox_count);
							}

							if(!empty($smssent_count)) {
								$retval['sent'] = number_format($smssent_count);
							}
						}
					} else
					if($this->post['table']=='contact') {
						if(!($result = $appdb->query("select contact_id,contact_number,contact_network,contact_nick,contact_createstamp,contact_updatestamp from tbl_contact where contact_deleted=0 order by contact_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['contact_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {

								$groups = '';

								$grp = $this->getMembersGroups($v['contact_id']);

								if(!empty($grp)&&!empty($grp[0]['id'])) {
									$groups = array();
									foreach($grp as $j) {
										$groups[] = $j['name'];
									}
									$groups = implode(',', $groups);
								}

								
								//if(!($groupid = $this->getNetworkGroupID($v['contact_number']))) {
								//} else {
								//	$this->insertGroupContact($groupid,$v['contact_id']);
								//}

								//$rows[] = array('id'=>$v['contact_id'],'data'=>array($v['contact_id'],$v['contact_nick'],$v['contact_number'],$v['contact_network'],$groups,$v['contact_createstamp'],$v['contact_updatestamp']));

								$rows[] = array('id'=>$v['contact_id'],'data'=>array(0,$v['contact_id'],$v['contact_nick'],$v['contact_number'],$this->getNetworkName($v['contact_number']),$groups,pgDate($v['contact_createstamp']),pgDate($v['contact_updatestamp'])));
							}

							$retval = array('rows'=>$rows);
						}
					} else
					if($this->post['table']=='group') {
						if(!($result = $appdb->query("select group_id,group_name,group_desc,group_timestamp from tbl_group order by group_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						if(!empty($result['rows'][0]['group_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {

								/*if(!($result2 = $appdb->query("select count(groupcontact_id) from tbl_groupcontact where groupcontact_groupid=".$v['group_id']))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;				
								}

								$memberscount = 0;

								if(!empty($result2['rows'][0]['count'])) {
									$memberscount = $result2['rows'][0]['count'];
								}*/

								$rows[] = array('id'=>$v['group_id'],'data'=>array(0,$v['group_id'],$v['group_name'],$v['group_desc'],$this->getGroupMembersCount($v['group_id']),pgDate($v['group_timestamp'])));
							}

							$retval = array('rows'=>$rows);
						}
					} else
					if($this->post['table']=='autoreply') {
						if(!($result = $appdb->query("select *,case when smscommands_active=0 then 'no' when smscommands_active=1 then 'yes' end as smscommands_status from tbl_smscommands where smscommands_type='autoreply' order by smscommands_priority asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['smscommands_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['smscommands_id'],'data'=>array(0,$v['smscommands_id'],getOption($v['smscommands_key0']),$v['smscommands_status'],$v['smscommands_sendsms0']));
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

	$appappmessaging = new APP_app_messaging;
}

# eof modules/app.messaging