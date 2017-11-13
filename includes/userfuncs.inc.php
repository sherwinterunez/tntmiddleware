<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Misc functions include file
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

/* INCLUDES_START */

function getContactIDByNumber($number=false) {
	global $appdb;

	if(!empty($number)) {
	} else return false;

	$res = parseMobileNo($number);

	if($res) {

		$number = $res[2].$res[3];

		//$number = $parsedMobileNo['network'] . $parsedMobileNo['number'];

		//$sql = "select contact_id from tbl_contact where contact_number like '%".$number."'";

		$sql = "select studentprofile_id from tbl_studentprofile where studentprofile_guardianmobileno like '%".$number."'";

	} else {

		//$sql = "select contact_id from tbl_contact where contact_number='".$number."'";

		$sql = "select studentprofile_id from tbl_studentprofile where studentprofile_guardianmobileno='".$number."'";

	}

	//print_r(array('$sql'=>$sql));

	if(!($result = $appdb->query($sql))) {
		return false;
	}

	if(!empty($result['rows'][0]['studentprofile_id'])) {
		return $result['rows'][0]['studentprofile_id'];
	}
	return false;
}

function getContactByNumber($number=false) {
	global $appdb;

	if(!empty($number)) {
	} else return false;

	$res = parseMobileNo($number);

	if($res) {

		$number = $res[2].$res[3];

		//$number = $parsedMobileNo['network'] . $parsedMobileNo['number'];

		$sql = "select * from tbl_contact where contact_number like '%".$number."'";

	} else {

		$sql = "select * from tbl_contact where contact_number='".$number."'";

	}

	//print_r(array('$sql'=>$sql));

	if(!($result = $appdb->query($sql))) {
		return false;
	}

	if(!empty($result['rows'][0]['contact_id'])) {
		return $result['rows'][0];
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
		return false;
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

	$netname = getNetworkName($number);

	return getNetworkGroupIDFromName($netname);
}

function getNetworkCode($number=false) {

	if(!empty($number)) {
	} else return false;

	$number = trim($number);

	if(!($res=parseMobileNo($number))) {
		return false;
	}

	return $res[2];
}

function getAllNetworkName() {
	global $appdb;

	if(!($result = $appdb->query("select distinct network_name from tbl_network"))) {
		return false;
	}

	if(!empty($result['rows'][0]['network_name'])) {
		return $result['rows'];
	}

	return false;
}

function getGroupMembersCount($groupid=false) {
	global $appdb;

	if(!empty($groupid)&&is_numeric($groupid)) {
	} else return false;

	if(!($result = $appdb->query("select count(groupcontact_id) from tbl_groupcontact where groupcontact_groupid=".$groupid))) {
		return false;
	}

	if(!empty($result['rows'][0]['count'])) {
		return $result['rows'][0]['count'];
	}

	return false;
}

function getAllGroupsWithMembers() {

	$agroups = getAllGroups();

	$groups = array();

	if(is_array($agroups)&&!empty($agroups[0]['group_id'])) {

		foreach($agroups as $k=>$v) {
			if($groupid=getNetworkGroupIDFromName($v['group_name'])) {
				$memberscount = getGroupMembersCount($groupid);
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
			if(!empty($gname = getGroupNameByID($v))) {
				$groupNames[] = $gname;
			}
		}
	}

	if(!empty($groupNames)) {
		return $groupNames;
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

function getGroupMembersByName($groupname=false) {
	global $appdb;

	if(!empty($groupname)) {
	} else return false;

	//pre(array('$groupname'=>$groupname));

	$groupid = getGroupID($groupname);

	if(!$groupid) return false;

	//pre(array('$groupid'=>$groupid));

	$groupmembers = getGroupMembers($groupid);

	if(is_array($groupmembers)) {
		return $groupmembers;
	}

	return false;
}

function getMembersGroups($id=false) {
	global $appdb;

	if(!empty($id)&&is_numeric($id)) {
	} else return false;

	$sql = "select * from tbl_groupcontact where groupcontact_contactid=".$id;

	if(!($result = $appdb->query($sql))) {
		return false;
	}

	if(!empty($result['rows'][0]['groupcontact_id'])) {
		//return $result['rows'];

		$groups = array();

		foreach($result['rows'] as $k=>$v) {
			if(!($group = getGroup($v['groupcontact_groupid']))) {
				//return false;
				continue;
			}

			$groups[] = array('id'=>$group['group_id'],'name'=>$group['group_name'],'desc'=>$group['group_desc']);
		}

		if(!empty($groups)) {
			return $groups;
		}
	}

	return false;
}

function getContactNumber($contactid=false) {
	global $appdb;

	if(!empty($contactid)&&is_numeric($contactid)) {
	} else return false;

	//$sql = "select contact_number from tbl_contact where contact_id=".$contactid;

	$sql = "select studentprofile_guardianmobileno from tbl_studentprofile where studentprofile_id=".$contactid;

	// studentprofile_guardianmobileno

	//pre(array('$sql'=>$sql));

	if(!($result = $appdb->query($sql))) {
		return false;
	}

	if(!empty($result['rows'][0]['studentprofile_guardianmobileno'])) {
		return $result['rows'][0]['studentprofile_guardianmobileno'];
	}
	return false;
}

function getContactNickByNumber($number=false) {
	global $appdb;

	if(!empty($number)&&is_numeric($number)) {
	} else return false;

	if(($res=parseMobileNo($number))) {
		$number = $res[2].$res[3];
		$sql = "select contact_nick from tbl_contact where contact_number like '%$number'";
	} else {
		$sql = "select contact_nick from tbl_contact where contact_number='$number'";
	}

	if(!($result = $appdb->query($sql))) {
		return false;
	}

	if(!empty($result['rows'][0]['contact_nick'])) {
		return $result['rows'][0]['contact_nick'];
	}
	return 'Unregistered';
}

function getContactNickByID($id=false) {
	global $appdb;

	if(!empty($id)&&is_numeric($id)) {
	} else return false;

	$sql = "select contact_nick from tbl_contact where contact_id=".$id;

	if(!($result = $appdb->query($sql))) {
		return false;
	}

	if(!empty($result['rows'][0]['contact_nick'])) {
		return $result['rows'][0]['contact_nick'];
	}
	return 'Unregistered';
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

function getSimNumberUsingDev($dev=false) {
	global $appdb;

	if(empty($dev)) {
		return false;
	}

	if(!($result = $appdb->query("select * from tbl_sim where sim_device='$dev'"))) {
		return false;
	}

	if(!empty($result['rows'][0]['sim_number'])) {
		return $result['rows'][0]['sim_number'];
	}

	return false;
}

function getSimNameByNumber($number=false) {
	global $appdb, $simNameByNumberarr;

	if(empty($number)) {
		return false;
	}

	if(empty($simNameByNumberarr)) {

		if(!($result = $appdb->query("select * from tbl_sim"))) {
			return false;
		}

		if(!empty($result['rows'][0]['sim_name'])) {

			$simNameByNumberarr = array();

			foreach($result['rows'] as $v) {

				if(($res=parseMobileNo($v['sim_number']))) {
					$simnumber = $res[2].$res[3];
					$simNameByNumberarr[$simnumber]=$v['sim_name'];
				} else {
					$simNameByNumberarr[$v['sim_number']]=$v['sim_name'];
				}
			}
		}

	}

	if(($res=parseMobileNo($number))) {
		$number = $res[2].$res[3];

		if(!empty($simNameByNumberarr[$number])) {
			return $simNameByNumberarr[$number];
		}

		if(!($result = $appdb->query("select * from tbl_sim where sim_number like '%".$number."'"))) {
			return false;
		}
	} else {
		if(!empty($simNameByNumberarr[$number])) {
			return $simNameByNumberarr[$number];
		}

		if(!($result = $appdb->query("select * from tbl_sim where sim_number='$number'"))) {
			return false;
		}
	}

	if(!empty($result['rows'][0]['sim_name'])) {
		return $result['rows'][0]['sim_name'];
	}

	return false;
}

/*function getSimNameByNumber($number=false) {
	global $appdb;

	if(empty($number)) {
		return false;
	}

	if(($res=parseMobileNo($number))) {
		$number = $res[2].$res[3];

		if(!($result = $appdb->query("select * from tbl_sim where sim_number like '%".$number."'"))) {
			return false;
		}
	} else {
		if(!($result = $appdb->query("select * from tbl_sim where sim_number='$number'"))) {
			return false;
		}
	}

	if(!empty($result['rows'][0]['sim_name'])) {
		return $result['rows'][0]['sim_name'];
	}

	return false;
}*/

function getNetworkName($number=false) {
	global $appdb, $networkArr;

	if(empty($number)) {
		return false;
	}

	$number = trim($number);

	if(!($res=parseMobileNo($number))) {
		return 'Unknown';
	}

	$netnum = $res[2];

	if(!empty($networkArr)&&is_array($networkArr)&&!empty($networkArr[$netnum])) {
		return $networkArr[$netnum];
	}

	if(!($result = $appdb->query("select * from tbl_network where network_deleted=0"))) {
		return 'Unknown';
	}

	if(!empty($result['rows'][0]['network_id'])) {

		$networkArr = array();

		foreach($result['rows'] as $v) {
			$networkArr[$v['network_number']] = $v['network_name'];
		}

		if(!empty($networkArr[$netnum])) {
			return $networkArr[$netnum];
		}

	}

	return 'Unknown';
}

function getAllSims($mode=0) {
	global $appdb;

	if(!($result = $appdb->query("select * from tbl_sim where sim_disabled=0 and sim_deleted=0"))) {
		return false;
	}

	if(!empty($result['rows'][0]['sim_id'])) {

		$sims = array();

		if($mode==1) {
			foreach($result['rows'] as $v) {
				$sims[$v['sim_name']] = $v;
			}

			return $sims;
		} else
		if($mode==2) {
			foreach($result['rows'] as $v) {
				$sims[$v['sim_device']] = $v;
			}

			return $sims;
		} else
		if($mode==3) {
			foreach($result['rows'] as $v) {
				$sims[$v['sim_number']] = $v;
			}

			return $sims;
		} else
		if($mode==4) {
			foreach($result['rows'] as $v) {
				$sims[$v['sim_network']][] = $v;
			}

			return $sims;
		} else
		if($mode==5) { // online only
			foreach($result['rows'] as $v) {
				if(!empty($v['sim_online'])) {
					$sims[] = $v;
				}
			}

			return $sims;
		} else
		if($mode==6) { // offline only
			foreach($result['rows'] as $v) {
				if(empty($v['sim_online'])) {
					$sims[] = $v;
				}
			}

			return $sims;
		} else
		if($mode==7) {
			foreach($result['rows'] as $v) {
				if(!empty($v['sim_online'])) {
					$sims[$v['sim_name']] = $v;
				}
			}

			return $sims;
		} else
		if($mode==8) {
			foreach($result['rows'] as $v) {
				if(!empty($v['sim_online'])) {
					$sims[$v['sim_number']] = $v;
				}
			}

			return $sims;
		}


		return $result['rows'];
	}

	return false;
}

function getAllSimsName() {
	global $appdb;

	if(!($result = $appdb->query("select sim_name from tbl_sim where sim_disabled=0 and sim_deleted=0"))) {
		return false;
	}

	if(!empty($result['rows'][0]['sim_name'])) {
		$sims = array();

		foreach($result['rows'] as $v) {
			$sims[] = $v['sim_name'];
		}

		return $sims;
	}

	return false;
}

function generatePromoCode() {
	global $appdb;

	$size = mt_rand(5,getOption('$PROMOCODE_SIZE'));

	do {

		$promocode = '';

		for($i=0;$i<$size;$i++) {
			$promocode .= mt_rand(1,9);
		}

		$content = array();
		$content['promocodes_promocode'] = $promocode;

		if(!($result = $appdb->insert("tbl_promocodes", $content, "promocodes_id"))) {
			return false;
		}

		if(!empty($result['returning'][0]['promocodes_id'])) {
			return $promocode;
		}

	} while(1);

	return false;
}

function generateReferralCode() {
	global $appdb;

	$size = mt_rand(5,getOption('$REFERRALCODE_SIZE'));

	do {

		$referralcode = '';

		for($i=0;$i<$size;$i++) {
			$referralcode .= mt_rand(1,9);
		}

		$content = array();
		$content['referralcode_referralcode'] = $referralcode;

		if(!($result = $appdb->insert("tbl_referralcode", $content, "referralcode_id"))) {
			return false;
		}

		if(!empty($result['returning'][0]['referralcode_id'])) {
			return $referralcode;
		}

	} while(1);

	return false;
}

function parseMobileNo($mno=false,$regx = '^(\d+)(\d{3})(\d{7})$') {
	if(!empty($mno)) {
	} else return false;

	//pre(array('parseMobileNo'=>$mno));

	if(preg_match('#'.$regx.'#',$mno,$matches)) {
		//print_r(array('$mno'=>$mno,'$matches'=>$matches));

		return $matches;
	}

	return false;
}

function getMyLocalIP() {
	$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	@socket_connect($sock, "8.8.8.8", 53);
	socket_getsockname($sock, $name); // $name passed by reference

	return $name;
}

function isSimEnabled($number=false) {
	global $appdb;

	if(!empty($number)&&is_numeric($number)&&($res=parseMobileNo($number))) {
	} else return false;

	$mobileNo = '0' . $res[2] . $res[3];

	if(!($result = $appdb->query("select * from tbl_sim where sim_number='$mobileNo'"))) {
		return false;
	}

	if(!empty($result['rows'][0]['sim_number'])) {
		if(empty($result['rows'][0]['sim_disabled'])) {
			return true;
		}
	}

	return false;
}

function isSimOnline($number=false) {
	global $appdb;

	if(!empty($number)&&is_numeric($number)&&($res=parseMobileNo($number))) {
	} else return false;

	$mobileNo = '0' . $res[2] . $res[3];

	if(!($result = $appdb->query("select * from tbl_sim where sim_number='$mobileNo'"))) {
		return false;
	}

	if(!empty($result['rows'][0]['sim_number'])) {
		if(!empty($result['rows'][0]['sim_online'])) {
			return true;
		}
	}

	return false;
}

function getAllHotline($mode=0) {
	global $appdb;

	if(!($result = $appdb->query("select * from tbl_sim where sim_disabled=0 and sim_deleted=0 and sim_online>0 and sim_hotline>0"))) {
		return false;
	}

	if(!empty($result['rows'][0]['sim_id'])) {

		$sims = array();

		if($mode==1) {
			foreach($result['rows'] as $k=>$v) {
				$sims[] = $v['sim_number'];
			}
			return $sims;
		} else
		if($mode==2) {
			foreach($result['rows'] as $k=>$v) {
				$sims[$k] = $v['sim_number'];
			}
			return $sims;
		} else
		if($mode==3) {
			foreach($result['rows'] as $k=>$v) {
				$sims[$v['sim_number']] = $v;
			}
			return $sims;
		} else {
			return $result['rows'];
		}
	}

	return false;
}

function getLoader($mode=0) {
	global $appdb;

	if(!($result = $appdb->query("select * from tbl_sim where sim_disabled=0 and sim_deleted=0 and sim_online>0 and sim_eload>0"))) {
		return false;
	}

	if(!empty($result['rows'][0]['sim_id'])) {

		$sims = array();

		if($mode==1) {
			foreach($result['rows'] as $k=>$v) {
				$sims[] = $v['sim_number'];
			}
			return $sims;
		} else
		if($mode==2) {
			foreach($result['rows'] as $k=>$v) {
				$sims[$k] = $v['sim_number'];
			}
			return $sims;
		} else
		if($mode==3) {
			foreach($result['rows'] as $k=>$v) {
				$sims[$v['sim_number']] = $v;
			}
			return $sims;
		} else {
			return $result['rows'];
		}
	}

	return false;
}

function getLoadProducts($network=false) {
	global $appdb;

	if(!empty($network)) {
		$sql = "select * from tbl_eloadproduct where eloadproduct_disabled=0 and eloadproduct_provider='$network' order by eloadproduct_code asc";
	} else {
		$sql = "select * from tbl_eloadproduct where eloadproduct_disabled=0 order by eloadproduct_code asc";
	}

	//pre(array('$sql'=>$sql));

	if(!($result=$appdb->query($sql))) {
		return false;
	}

	// m-d-Y H:i

	if(!empty($result['rows'][0]['eloadproduct_id'])) {
		return $result['rows'];
	}

	return false;
}

function insertYearLevel($name=false) {
	global $appdb;

	if(!empty($name)) {
	} else {
		return false;
	}

	$name = trim($name);

	if(!empty($name)) {
	} else {
		return false;
	}

	$content = array();
	$content['groupref_name'] = $name;
	$content['groupref_type'] = 2;

	if(!($result = $appdb->insert("tbl_groupref",$content,"groupref_id"))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
		die;
	}

	if(!empty($result['returning'][0]['groupref_id'])) {
		return $result['returning'][0]['groupref_id'];
	}

	return false;
}

function insertSection($name=false,$yearlevel=false,$starttime=false,$endtime=false) {
	global $appdb;

	if(!empty($name)&&!empty($yearlevel)&&!empty($starttime)&&!empty($endtime)) {
	} else {
		return false;
	}

	$name = trim($name);

	if(!empty($name)) {
	} else {
		return false;
	}

	$content = array();
	$content['groupref_name'] = $name;
	$content['groupref_type'] = 1;
	$content['groupref_yearlevel'] = $yearlevel;
	$content['groupref_starttime'] = $starttime;
	$content['groupref_endtime'] = $endtime;

	if(!($result = $appdb->insert("tbl_groupref",$content,"groupref_id"))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
		die;
	}

	if(!empty($result['returning'][0]['groupref_id'])) {
		return $result['returning'][0]['groupref_id'];
	}

	return false;
}

function getGroupRefId($ref=false) {
	global $appdb;

	if(!empty($ref)) {
	} else {
		return false;
	}

	$sql = "select * from tbl_groupref where groupref_name='$ref'";

	if(!($result=$appdb->query($sql))) {
		return false;
	}

	//pre($result);

	if(!empty($result['rows'][0]['groupref_id'])) {
		return $result['rows'][0]['groupref_id'];
	}

	return false;
}

function getGroupRef($id=false,$mode=1) {
	global $appdb;

	if(!empty($id)&&is_numeric($id)&&$id>0) {
	} else {
		return false;
	}

	$sql = "select * from tbl_groupref where groupref_type=$id order by groupref_seq asc";

	if(!($result=$appdb->query($sql))) {
		return false;
	}

	//pre($result);

	if(!empty($result['rows'][0]['groupref_id'])) {
		return $result['rows'];
	}

	return false;
}

function getGroupRefName2($id=false) {
	global $appdb;

	if(!empty($id)&&is_numeric($id)&&$id>0) {
	} else {
		return false;
	}

	$sql = "select * from tbl_groupref where groupref_id=$id";

	if(!($result=$appdb->query($sql))) {
		return false;
	}

	//pre($result);

	if(!empty($result['rows'][0]['groupref_id'])) {
		return $result['rows'][0]['groupref_name'];
	}

	return false;
}

function getGroupRefName($id=false) {
	global $appdb, $arrGroupRefName;

	if(!empty($id)&&is_numeric($id)&&$id>0) {
	} else {
		return false;
	}

	if(!empty($arrGroupRefName)&&is_array($arrGroupRefName)) {
	} else {
		$sql = "select * from tbl_groupref";

		if(!($result=$appdb->query($sql))) {
			return false;
		}

		if(!empty($result['rows'][0]['groupref_id'])) {
			foreach($result['rows'] as $k=>$v) {
				$arrGroupRefName[$v['groupref_id']] = $v;
			}
		}
	}

	/*$sql = "select * from tbl_groupref where groupref_id=$id";

	if(!($result=$appdb->query($sql))) {
		return false;
	}

	//pre($result);

	if(!empty($result['rows'][0]['groupref_id'])) {
		return $result['rows'][0]['groupref_name'];
	}*/

	if(!empty($arrGroupRefName[$id]['groupref_name'])) {
		return $arrGroupRefName[$id]['groupref_name'];
	}

	return false;
}

function getSectionId($section=false,$yearlevel=false) {
	global $appdb;

	if(!empty($section)&&!empty($yearlevel)&&intval($yearlevel)>0) {
	} else {
		return false;
	}

	$sql = "select * from tbl_groupref where groupref_name='$section' and groupref_yearlevel='".intval($yearlevel)."'";

	if(!($result=$appdb->query($sql))) {
		return false;
	}

	//pre($result);

	if(!empty($result['rows'][0]['groupref_id'])) {
		return $result['rows'][0]['groupref_id'];
	}

	return false;
}

function getSectionStartTime($id=false) {
	global $appdb;

	if(!empty($id)&&is_numeric($id)&&$id>0) {
	} else {
		return false;
	}

	$sql = "select * from tbl_groupref where groupref_id=$id";

	if(!($result=$appdb->query($sql))) {
		return false;
	}

	//pre($result);

	if(!empty($result['rows'][0]['groupref_starttime'])) {
		return $result['rows'][0]['groupref_starttime'];
	}

	return false;
}

function getSectionEndTime($id=false) {
	global $appdb;

	if(!empty($id)&&is_numeric($id)&&$id>0) {
	} else {
		return false;
	}

	$sql = "select * from tbl_groupref where groupref_id=$id";

	if(!($result=$appdb->query($sql))) {
		return false;
	}

	//pre($result);

	if(!empty($result['rows'][0]['groupref_endtime'])) {
		return $result['rows'][0]['groupref_endtime'];
	}

	return false;
}

function getGuardianMobileNo($studentId) {
	global $appdb;

	if(!empty($studentId)&&is_numeric($studentId)&&$studentId>0) {
	} else {
		return false;
	}

	$sql = "select * from tbl_studentprofile where studentprofile_id=$studentId";

	if(!($result=$appdb->query($sql))) {
		return false;
	}

	//pre($result);

	if(!empty($result['rows'][0]['studentprofile_guardianmobileno'])) {
		return $result['rows'][0]['studentprofile_guardianmobileno'];
	}

	return false;
}

function getStudentFullName($studentId) {
	global $appdb;

	if(!empty($studentId)&&is_numeric($studentId)&&$studentId>0) {
	} else {
		return false;
	}

	$sql = "select * from tbl_studentprofile where studentprofile_id=$studentId";

	if(!($result=$appdb->query($sql))) {
		return false;
	}

	//pre($result);

	if(!empty($result['rows'][0]['studentprofile_id'])) {

		$sp = $result['rows'][0];

		$fullname = '';

		if(!empty($sp['studentprofile_firstname'])) {
			$fullname .= trim($sp['studentprofile_firstname']);
		}

		if(!empty($sp['studentprofile_middlename'])) {
			$fullname .= ' '.trim($sp['studentprofile_middlename']);
		}

		if(!empty($sp['studentprofile_lastname'])) {
			$fullname .= ' '.trim($sp['studentprofile_lastname']);
		}

		if(!empty($fullname)) {
			return $fullname;
		}
	}

	return false;
}

function getStudentProfile($studentId) {
	global $appdb;

	if(!empty($studentId)&&is_numeric($studentId)&&$studentId>0) {
	} else {
		return false;
	}

	$sql = "select * from tbl_studentprofile where studentprofile_id=$studentId";

	if(!($result=$appdb->query($sql))) {
		return false;
	}

	//pre($result);

	if(!empty($result['rows'][0]['studentprofile_id'])) {
		return $result['rows'][0];
	}

	return false;
}

function getCurrentSchoolYear() {
	$dbdate = intval(getDbUnixDate());

	$cyear = intval(date('Y',$dbdate));
	$nyear = $cyear++;

	$default_schoolyear = $cyear.'-'.$nyear;

	return getOption('$SETTINGS_SCHOOLYEAR',$default_schoolyear);
}

function getTotalStudentCurrentSchoolYear() {
	return getTotalStudent(getCurrentSchoolYear());
}

function getTotalStudent($schoolyear=false) {
	global $appdb;

	if(!empty($schoolyear)&&isValidSchoolYear($schoolyear)) {
	} else {
		return 0;
	}

	$sql = "select count(studentprofile_id) from tbl_studentprofile where studentprofile_schoolyear='$schoolyear'";

	if(!($result=$appdb->query($sql))) {
		return false;
	}

	//pre($result);

	if(!empty($result['rows'][0]['count'])&&intval($result['rows'][0]['count'])>0) {
		return intval($result['rows'][0]['count']);
	}

	return 0;
}

function isValidSchoolYear($schoolyear=false) {
	$validsy = false;

	if(!empty($schoolyear)) {
		$sy = explode('-',$schoolyear);

		if(!empty($sy[0])&&!empty($sy[1])&&intval($sy[0])>2000&&intval($sy[1])>2000&&intval($sy[1])>intval($sy[0])&&(intval($sy[1])-intval($sy[0]))==1) {
			$validsy = true;
		}
	}

	return $validsy;
}

function setSimNumber($dev,$mobileNo,$ip='') {

	if(!empty($dev)&&!empty($mobileNo)) {
	} else return false;

	$sms = new SMS;

	$sms->dev = $dev;
	$sms->mobileNo = $mobileNo;

	if(!($sms->deviceSet($dev)&&$sms->deviceOpen('w+')&&$sms->setBaudRate(115200))) {
		$em = 'Error initializing device!';
		trigger_error("$dev $mobileNo $ip $em",E_USER_NOTICE);
		return false;
	}

	if(!$sms->at()) {
		$em = 'Retrieve failed (AT)';
		trigger_error("$dev $mobileNo $ip $em",E_USER_NOTICE);

		$sms->deviceClose();
		return false;
	}

	if($sms->sendMessageReadPort("AT+CPBS=\"ON\"\r\n", "OK\r\n")&&$sms->sendMessageReadPort("AT+CPBW=1,\"$mobileNo\",129,\"\"\r\n", "OK\r\n")&&$sms->sendMessageReadPort("AT+CPBS=\"SM\"\r\n", "OK\r\n")) {

	}

	$history = $sms->getHistory();

	if(!empty($history)) {
		foreach($history as $a=>$b) {
			foreach($b as $k=>$v) {
				if($k=='timestamp') continue;
				$dt = logdt($b['timestamp']);
				trigger_error("$dev $mobileNo $ip $v",E_USER_NOTICE);
				doLog("$dt $dev $mobileNo $ip $v",$mobileNo);
				//atLog($v,'retrievesms',$dev,$mobileNo,$ip,logdt($b['timestamp']));
			}
		}
	}

	$sms->deviceClose();

	return true;
}

function doLog($str=false,$sim='') {
	if(empty($str)) {
		return false;
	}

	$logfile = 'log';

	if(!empty($sim)) {
		$logfile .= '-'.$sim;
	}

	$logfile .= '-'.date('Ymd');
	$logfile .= '.txt';

	$str = trim($str)."\n";

	//return error_log($str,3,ABS_PATH.'log/'.$logfile);
	return error_log($str,3,'/var/log/nginx/'.$logfile);
}

function sendSMS($sms=false,$number=false,$message=false) {

	$retval = false;

	if(!empty($sms)&&!empty($number)&&!empty($message)) {
	} else return false;

	$msg = array();

	$msg['message'] = $message;
	$msg['number'] = $number;
	//$msg['smsc'] = '+639180000101';
	$msg['class'] = -1;
	$msg['alphabetSize'] = 7;
	$msg['pdu'] = true;
	$msg['receiverFormat'] = '81';

	if(!empty($msg['pdu'])) {

		$sms->sendMessageOk("AT+CMGF=0\r\n");

		$pdu = new PduFactory();

		$x=1;

		$max=10;

		if(strlen($msg['message'])>160) {
			$dta=str_split($msg['message'],152);

			$ref=mt_rand(100,250);

			$sms->udh['msg_count']=$sms->dechex_str(count($dta));

			if(count($dta)>$max) {
				$sms->udh['msg_count']=$sms->dechex_str($max);

			}

			$sms->udh['reference']=$sms->dechex_str($ref);

			$ctr=1;

			$break = false;

			foreach($dta as $part) {
				$sms->udh['msg_part']=$sms->dechex_str($x);
				$msg['message'] = $part . ' ';
				$msg['udh'] = implode('', $sms->udh);
				$chop[] = $msg;
				$x++;

				$stra = $pdu->encode($msg,true);

				//print_r(array('$msg'=>$msg,'$stra'=>$stra));

				//print_r(array('$pdu->decode'=>$pdu->decode($stra['message'])));

				$cntr = 0;

				//at_cmgs($sms,$stra['byte_size'],$stra['message']);

				do {

					if(!$sms->cmgs($stra['byte_size'],$stra['message'])) {

						trigger_error($sms->dev.' '.$sms->mobileNo.' '.$sms->ip.' AT+CMGS Failed!',E_USER_NOTICE);

						sleep(5);
						if($cntr>2) {
							$break = true;
							break;
						}
					} else {
						break;
					}

					$cntr++;

				} while($cntr<2);

				$ctr++;

				if($ctr>$max) break;

				if($break) {
					break;
				}
			}

			if($break) {
				$retval = false;
			} else {
				$retval = ($ctr-1);
			}

		} else {

			$stra = $pdu->encode($msg);

			//print_r(array('$stra'=>$stra));

			//print_r(array('$pdu->decode'=>$pdu->decode($stra['message'])));

			$cntr = 0;

			$break = false;

			do {

				if(!$sms->cmgs($stra['byte_size'],$stra['message'])) {

					trigger_error($sms->dev.' '.$sms->mobileNo.' '.$sms->ip.' AT+CMGS Failed!',E_USER_NOTICE);

					sleep(5);
					if($cntr>2) {
						$break = true;
						break;
					}
				} else {
					break;
				}

				$cntr++;

			} while($cntr<5);

			if($break) {
				$retval = false;
			} else {
				$retval = 1;
			}

		}
	}

	return $retval;
}

function dechex_str($ref) {
	$hex = ($ref <= 15 )?'0'.dechex($ref):dechex($ref);
	return strtoupper($hex);
}

function pgDate($dt=false,$format=false) {
	if(!empty($dt)) {
	} else false;

	if(!empty($format)) {
	} else {
		$format = getOption('$DISPLAY_DATE_FORMAT','r');
	}

	$date = strtotime($dt);

	return date($format,$date);
}

function pgDateUnix($dt=false,$format=false) {
	if(!empty($dt)&&is_numeric($dt)) {
	} else false;

	if(!empty($format)) {
	} else {
		$format = getOption('$DISPLAY_DATE_FORMAT','r');
	}

	//$date = strtotime($dt);

	return date($format,$dt);
}

/*function getDbDate($mode=0,$f1='m-d-Y',$f2='H:i') {
	global $appdb;

	if(!($result=$appdb->query("select now()"))) {
		return false;
	}

	// m-d-Y H:i

	if(!empty($result['rows'][0]['now'])) {
		if($mode==1) {
			return array('date'=>pgDate($result['rows'][0]['now'],$f1),'time'=>pgDate($result['rows'][0]['now'],$f2));
		}

		return pgDate($result['rows'][0]['now'],"$f1 $f2");
	}

	//pre(array('$result'=>$result));
	return false;
}*/

function getDbDate($mode=0,$f1='m-d-Y',$f2='H:i') {
	global $appdb;

	if($mode==2) {
		$unixdate = intval(getDbUnixDate()) + 86400;

		return array('date'=>pgDateUnix($unixdate,$f1),'time'=>pgDateUnix($unixdate,$f2));
	}

	if(!($result=$appdb->query("select now()"))) {
		return false;
	}

	// m-d-Y H:i

	if(!empty($result['rows'][0]['now'])) {
		if($mode==1) {
			return array('date'=>pgDate($result['rows'][0]['now'],$f1),'time'=>pgDate($result['rows'][0]['now'],$f2));
		}

		return pgDate($result['rows'][0]['now'],"$f1 $f2");
	}

	//pre(array('$result'=>$result));
	return false;
}

function getDbUnixDate() {
	global $appdb;

	if(!($result=$appdb->query("select extract(epoch from now()) as unixstamp"))) {
		return false;
	}

	// m-d-Y H:i

	if(!empty($result['rows'][0]['unixstamp'])) {
		return $result['rows'][0]['unixstamp'];
	}

	//pre(array('$result'=>$result));
	return false;
}

function sendToOutBox($contactnumber=false,$simnumber=false,$message=false,$status=1,$delay=0,$eload=0,$push=0,$priority=0,$latenoti=0,$absentnoti=0,$thecontactid=0) {
	global $appdb;

	if(!empty($simnumber)) {
	} else {
		$simnumber = '09191234567';
	}

	if(!empty($contactnumber)&&!empty($simnumber)&&!empty($message)) {
	} else {
		return false;
	}

	//$message = trim(htmlspecialchars_decode(strip_tags($message,'<br><space>')));
	//$message = str_replace('&nbsp;',' ',$message);
	//$message = strip_tags($message, '<br><space>');
	//$message = str_replace('<br>',"\n",$message);
	//$message = str_replace('<br/>',"\n",$message);
	//$message = str_replace('<br />',"\n",$message);

	$message = trim($message);

	if(($res=parseMobileNo($contactnumber))) {
		$contactnumber = '0'.$res[2].$res[3];
	}

	if(($res=parseMobileNo($simnumber))) {
		$simnumber = '0'.$res[2].$res[3];
	}

	$contactid = getContactIDByNumber($contactnumber);

	if(!$contactid) {
		$contactid = 0;
	}

	if(!empty($thecontactid)) {
		$contactid = $thecontactid;
	}

	if(strlen($message)>160) {

		$smsparts = str_split($message,152);

		$smsoutbox_udhref = dechex_str(mt_rand(100,250));

		$smsoutbox_total = count($smsparts);

		$content = array();
		$content['smsoutbox_contactid'] = $contactid;
		$content['smsoutbox_contactnumber'] = $contactnumber;
		$content['smsoutbox_message'] = $message;
		$content['smsoutbox_udhref'] = $smsoutbox_udhref;
		$content['smsoutbox_part'] = $smsoutbox_total;
		$content['smsoutbox_total'] = $smsoutbox_total;
		$content['smsoutbox_simnumber'] = $simnumber;
		$content['smsoutbox_type'] = 1;
		$content['smsoutbox_status'] = $status;
		$content['smsoutbox_priority'] = $priority;

		if(!empty($delay)&&is_numeric($delay)&&intval($delay)>0) {
			$content['smsoutbox_delay'] = intval($delay);
			$content['smsoutbox_status'] = 1;
		}

		if(!empty($eload)) {
			$content['smsoutbox_eload'] = 1;
		}

		if(!empty($push)) {
			$content['smsoutbox_sendpush'] = 1;
			$content['smsoutbox_pushstatus'] = 1;
		}

		if(!empty($latenoti)) {
			$content['smsoutbox_latenoti'] = 1;
		}

		if(!empty($absentnoti)) {
			$content['smsoutbox_absentnoti'] = 1;
		}

	} else {

		$content = array();
		$content['smsoutbox_contactid'] = $contactid;
		$content['smsoutbox_contactnumber'] = $contactnumber;
		$content['smsoutbox_message'] = $message;
		$content['smsoutbox_simnumber'] = $simnumber;
		$content['smsoutbox_part'] = 1;
		$content['smsoutbox_total'] = 1;
		$content['smsoutbox_status'] = $status;
		$content['smsoutbox_priority'] = $priority;

		if(!empty($delay)&&is_numeric($delay)&&intval($delay)>0) {
			$content['smsoutbox_delay'] = intval($delay);
			$content['smsoutbox_status'] = 1;
		}

		if(!empty($eload)) {
			$content['smsoutbox_eload'] = 1;
		}

		if(!empty($push)) {
			$content['smsoutbox_sendpush'] = 1;
			$content['smsoutbox_pushstatus'] = 1;
		}

		if(!empty($latenoti)) {
			$content['smsoutbox_latenoti'] = 1;
		}

		if(!empty($absentnoti)) {
			$content['smsoutbox_absentnoti'] = 1;
		}

	}

	if(!($result = $appdb->insert("tbl_smsoutbox",$content,"smsoutbox_id"))) {
		return false;
	}

	if(!empty($result['returning'][0]['smsoutbox_id'])) {
		return $result['returning'][0]['smsoutbox_id'];
	}

	return false;
}

function sendToOutBoxPush($contactnumber=false,$simnumber=false,$message=false,$push=0) {
	return sendToOutBox($contactnumber,$simnumber,$message,1,0,0,$push);
}

function sendToOutBoxPriority($contactnumber=false,$simnumber=false,$message=false,$push=0,$priority=1,$status=1,$latenoti=0,$absentnoti=0,$contactid=0) {
	return sendToOutBox($contactnumber,$simnumber,$message,$status,0,0,$push,$priority,$latenoti,$absentnoti,$contactid);
}

function wLog($text='',$module='') {
	global $appdb;

	if(empty($text)) return false;

	//print_r(array('$text'=>$text));

	if(!($result=$appdb->insert("tbl_log",array('log_text'=>$text,'log_module'=>$module),"log_id"))) {
		return false;
	}

	if(!empty($result['returning'][0]['log_id'])) {
		return $result['returning'][0]['log_id'];
	}

	return false;
}

function logdt($timestamp=false) {
	if(!empty($timestamp)) {
	} else return date('M d Y H:i:s',time());

	return date('M d Y H:i:s',$timestamp);
}

function smsdt($timestamp=false) {
	if(!empty($timestamp)) {
	} else return date('j-M H:i:',time());

	return date('j-M H:i:',$timestamp);
}

/*
sherwint_sms101=# \d tbl_atlog
                                         Table "public.tbl_atlog"
     Column      |           Type           |                          Modifiers
-----------------+--------------------------+-------------------------------------------------------------
 atlog_id        | bigint                   | not null default nextval(('tbl_atlog_seq'::text)::regclass)
 atlog_text      | text                     | not null default ''::text
 atlog_module    | text                     | not null default ''::text
 atlog_device    | text                     | not null default ''::text
 atlog_sim       | text                     | not null default ''::text
 atlog_ip        | text                     | not null default ''::text
 atlog_deleted   | integer                  | not null default 0
 atlog_flag      | integer                  | not null default 0
 atlog_timestamp | timestamp with time zone | default now()
Indexes:
    "tbl_atlog_primary_key" PRIMARY KEY, btree (atlog_id)
*/

function atLog($text='',$module='',$device='',$sim='',$ip='',$date='') {
	global $appdb;

	if(empty($text)) return false;

	//print_r(array('$text'=>$text));

	/*$content = array();
	$content['atlog_text'] = $text;
	$content['atlog_module'] = $module;
	$content['atlog_device'] = $device;
	$content['atlog_sim'] = $sim;
	$content['atlog_ip'] = $ip;
	$content['atlog_date'] = $date;

	if(!($result=$appdb->insert("tbl_atlog",$content,"atlog_id"))) {
		return false;
	}

	if(!empty($result['returning'][0]['atlog_id'])) {
		return $result['returning'][0]['atlog_id'];
	}

	return false;*/

	return true;
}

function modemFunction2($sms=false,$simfunctions=false) {

	if(!empty($sms)&&!empty($simfunctions)) {
	} else {
		return false;
	}

	if(!empty($simfunctions)) {
		foreach($simfunctions as $func) {

			if(!empty($func['command'])) {

				$REGX = '';

				if(!empty($func['regx'])) {

					if(!empty($func['regx'][0])&&is_array($func['regx'])) {
						$REGX = $func['regx'][0];
					} else {
						$REGX = $func['regx'];
					}

					/*if(!empty($func['param'])) {
						if(is_array($func['param'])) {

						} else {
							$REGX = str_replace('<param>',$func['param'],$REGX);
						}
					}*/
				}

				$FUNC = trim($func['command']);

				//if(isset($gotresult)) {
				//	$FUNC = str_replace('%result%', $gotresult, $FUNC);
				//}

				if(isset($lastresult)&&is_array($lastresult)) {

					//print_r(array('$lastresult'=>$lastresult));

					foreach($lastresult as $k=>$v) {
						$FUNC = str_replace('$'.$k, $v, $FUNC);
					}
				}

				if(!empty($REGX)) {

					print_r(array('$FUNC'=>$FUNC));

					$flag = true;

					$repeatCtr = false;

					if(!empty($func['repeat'])) {
						$repeatCtr = intval($func['repeat']);
					}

					$break = false;

					do {

						if($repeatCtr) {
							$repeatCtr--;
						}

						if($sms->sendMessageReadPort($FUNC."\r\n", $REGX)) {
							$result = $sms->getResult();
							$result['flat'] = $sms->tocrlf($result[0]);

							print_r(array('$result'=>$result));

							if(!empty($func['regx'][1])&&is_array($func['regx'])) {
								for($i=1;$i<count($func['regx']);$i++) {
									print_r(array('regx'=>$func['regx'][$i]));
									if(preg_match('/'.$func['regx'][$i].'/s',$result[0],$result)) {
										print_r(array('regx'=>$func['regx'][$i],'$result'=>$result));
									} else {
										$flag = false;
										break;
									}
								}
							}

							if(!empty($flag)) {
								//print_r(array('$result'=>$result));
							} else {
								print_r(array('$repeatCtr'=>$repeatCtr));
								if(!$repeatCtr) {
									$break = true;
									break;
								}
							}

							$lastresult = $result;

							if(isset($func['resultindex'])&&is_numeric($func['resultindex'])) {
								$index = intval(trim($func['resultindex']));
								if(isset($result[$index])) {
									$gotresult = $result[$index];

									if(isset($func['expectedresult'])) {
										if(preg_match('/'.$func['expectedresult'].'/s',$gotresult,$match)) {
											print_r(array('$repeatCtr'=>$repeatCtr,'$match'=>$match));
											$repeatCtr = 0;
										} else {
											if(!$repeatCtr) {
												$break = true;
												break;
											}
										}
									}
								}
							}

							//print_r(array('current'=>$sms->getCurrent(),'result'=>$result,'gotresult'=>$gotresult));
						} else {
							//print_r(array('current'=>$sms->getCurrent()));
							$break = true;
							break;
						}

					} while($repeatCtr);

					if($break) break;

				} else {

				} // if(!empty($REGX)) {

			} // if(!empty($func['command'])) {

		} // foreach($simfunctions as $func) {

	} // if(!empty($simfunctions)) {

	//print_r(array('history'=>$sms->getHistory()));

	if($break) {
		return false;
	}

	return true;

} // function modemFunction2($sms=false,$simfunctions=false) {


function modemFunction($sms=false,$simfunctions=false,$debug=false) {

	if(!empty($sms)&&!empty($simfunctions)) {
	} else {
		return false;
	}

	if(!empty($simfunctions)) {
		foreach($simfunctions as $func) {

			if(!empty($func['command'])) {

				$REGX = '';

				if(!empty($func['regx'])) {

					if(!empty($func['regx'][0])&&is_array($func['regx'])) {
						$REGX = $func['regx'][0];
					} else {
						$REGX = $func['regx'];
					}

					/*if(!empty($func['param'])) {
						if(is_array($func['param'])) {

						} else {
							$REGX = str_replace('<param>',$func['param'],$REGX);
						}
					}*/
				}

				$FUNC = trim($func['command']);

				$TIMEOUT = 60;

				//if(isset($gotresult)) {
				//	$FUNC = str_replace('%result%', $gotresult, $FUNC);
				//}

				if(isset($lastresult)&&is_array($lastresult)) {

					//print_r(array('$lastresult'=>$lastresult));

					foreach($lastresult as $k=>$v) {
						$FUNC = str_replace('$'.$k, $v, $FUNC);
					}
				}

				if(!empty($REGX)) {

					$flag = true;

					$repeatCtr = false;

					if(!empty($func['repeat'])) {
						$repeatCtr = intval($func['repeat']);
					}

					if(!empty($func['timeout'])) {
						$TIMEOUT = intval($func['timeout']);
					}

					$break = false;

					do {

						if($repeatCtr) {
							$repeatCtr--;
						}

						if($debug) $sms->showBuffer();

						if($debug) $oFUNC = $sms->tocrlf($FUNC);

						$FUNC = str_replace('$CTRLZ',chr(26),$FUNC);
						$FUNC = str_replace('$CR',"\r",$FUNC);
						$FUNC = str_replace('$NL',"\n",$FUNC);
						$FUNC = str_replace('\r',"\r",$FUNC);
						$FUNC = str_replace('\n',"\n",$FUNC);

						if(preg_match("/\n/", $FUNC)) {
						} else
						if(preg_match("/\r/", $FUNC)) {
							//$sms->showBuffer();
						} else
						if(preg_match('/'.chr(26).'/', $FUNC)) {
							//$FUNC = "AT+STGR=3,1\r09493621618".chr(26);
							//$sms->showBuffer();
						} else {
							$FUNC = $FUNC."\r\n";
						}

						if($debug) print_r(array('$oFUNC'=>$oFUNC,'$FUNC'=>$FUNC,'flat'=>str_replace(chr(26),'(x26)',$sms->tocrlf($FUNC))));

						if($sms->sendMessageReadPort($FUNC, $REGX, $TIMEOUT)) {
							$result = $sms->getResult();
							$result['flat'] = $sms->tocrlf($result[0]);
							$sms->lastresult = $result;

							if($debug) print_r(array('$result'=>$result));

							if(!empty($func['regx'][1])&&is_array($func['regx'])) {
								for($i=1;$i<count($func['regx']);$i++) {

									if($debug) print_r(array('regx'=>$func['regx'][$i]));

									if(preg_match('/'.$func['regx'][$i].'/s',$result[0],$result)) {

										if($debug) print_r(array('regx'=>$func['regx'][$i],'$result'=>$result));

									} else {
										$flag = false;
										break;
									}
								}
							}

							if(!empty($flag)) {
								//print_r(array('$result'=>$result));
							} else {
								if($debug) print_r(array('$repeatCtr'=>$repeatCtr));
								if(!$repeatCtr) {
									$break = true;
									break;
								}
							}

							$result['flat'] = $sms->tocrlf($result[0]);

							$sms->lastresult = $lastresult = $result;

							if(isset($func['resultindex'])&&is_numeric($func['resultindex'])) {
								$index = intval(trim($func['resultindex']));
								if(isset($result[$index])) {
									$gotresult = $result[$index];

									if(isset($func['expectedresult'])) {
										if(preg_match('/'.$func['expectedresult'].'/s',$gotresult,$match)) {

											if($debug) print_r(array('$repeatCtr'=>$repeatCtr,'$match'=>$match));

											$repeatCtr = 0;
										} else {
											if(!$repeatCtr) {
												$break = true;
												break;
											}
										}
									}
								}
							}

							//print_r(array('current'=>$sms->getCurrent(),'result'=>$result,'gotresult'=>$gotresult));
						} else {
							//print_r(array('current'=>$sms->getCurrent()));
							$break = true;
							break;
						}

					} while($repeatCtr);

					if(!empty($break)) break;

				} else {

				} // if(!empty($REGX)) {

			} // if(!empty($func['command'])) {

		} // foreach($simfunctions as $func) {

	} // if(!empty($simfunctions)) {

	//print_r(array('history'=>$sms->getHistory()));

	if(!empty($break)) {

		//trigger_error($sms->dev.' '.$sms->mobileNo.' '.$sms->ip.' BREAK: '.$sms->getLastMessage(),E_USER_NOTICE);

		return false;
	}

	return true;

} // function modemFunction2($sms=false,$simfunctions=false) {

function  smsCommandMatched($content=false){
	global $appdb;

	if(empty($content)) {
		return false;
	}

	//$hotline = false;
	$smscommands = false;
	$allmatched = array();

	/*
	if(!($result=$appdb->query("select * from tbl_sim where sim_disabled=0 and sim_deleted=0 and sim_online=1 and sim_number='".$content['smsinbox_simnumber']."'"))) {
		return false;
	}

	if(!empty($result['rows'][0]['sim_id'])) {
		$hotline = !empty($result['rows'][0]['sim_hotline']) ? $result['rows'][0]['sim_hotline'] : false;
	} else {
		return false;
	}
	*/

	if(!($result=$appdb->query('select * from tbl_smscommands where smscommands_active=1 order by smscommands_priority'))) {
		return false;
	}

	if(!empty($result['rows'][0]['smscommands_id'])) {
		//print_r(array('$result'=>$result['rows']));
		$smscommands = $result['rows'];
	}

	if(!empty($smscommands)) {

		$str = trim($content['smsinbox_message']);

		//$smsinbox_id = $content['smsinbox_id'];
		//$smsinbox_contactnumber = $content['smsinbox_contactnumber'];

		//print_r(array('$str'=>$str));

		do {
			$str = str_replace('  ', ' ', trim($str));
			$str = str_replace("\n",' ', trim($str));
			$str = str_replace("\r",' ', trim($str));
			//echo '.';
		} while(preg_match('#\s\s#si', $str));

		//print_r(array('$content'=>$content,'str'=>$str));

		$matchedctr = 0;

		$error = array();

		foreach($smscommands as $smsc) {

			$allmatched = array();

			$smscommands_key0 = getOption($smsc['smscommands_key0']);

			$regstr = $smscommands_key0;

			$regx = '/'.$regstr.'/si';

			//print_r(array('regx'=>$regx));

			$matched = false;

			if(preg_match($regx,$str,$match)) {

				if($matchedctr<1) {
					$matchedctr = 1;
				}

				$matched = true;

				//print_r(array('$smscommands_key0'=>$smscommands_key0,'$match'=>$match));

				if(isset($match[1])) {
					$allmatched[$smsc['smscommands_key0']] = $match[1];
				} else {
					$allmatched[$smsc['smscommands_key0']] = $match[0];
				}

			} else {
				$matched = false;
				$error[0] = $smsc['smscommands_key0_error'];
			}

			if($matched&&!empty($smsc['smscommands_key1'])) {

				$smscommands_key1 = getOption($smsc['smscommands_key1']);

				$regstr .= '\s+'.$smscommands_key1;

				$regx = '/'.$regstr.'/si';

				//print_r(array('regx'=>$regx,'$str'=>$str));

				if(preg_match($regx,$str,$match)) {

					if($matchedctr<2) {
						$matchedctr = 2;
					}

					$matched = true;

					//print_r(array('$smscommands_key1'=>$smscommands_key1,'$match'=>$match));

					if(preg_match('/'.$smscommands_key1.'/si',$str,$match)) {
						if(isset($match[1])) {
							$allmatched[$smsc['smscommands_key1']] = $match[1];
						} else {
							$allmatched[$smsc['smscommands_key1']] = $match[0];
						}
					}

					//print_r(array('$smscommands_key1'=>$smscommands_key1,'$match'=>$match,'$smsc'=>$smsc));

				} else {
					$matched = false;
					$error[1] = $smsc['smscommands_key1_error'];
				}
			}

			if($matched&&!empty($smsc['smscommands_key2'])) {

				$smscommands_key2 = getOption($smsc['smscommands_key2']);

				$regstr .= '\s+'.$smscommands_key2;

				$regx = '/'.$regstr.'/si';

				//print_r(array('regx'=>$regx));

				if(preg_match($regx,$str,$match)) {

					if($matchedctr<3) {
						$matchedctr = 3;
					}

					$matched = true;

					//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match));

					//if(preg_match('/'.$smscommands_key2.'/si',$str,$match)) {
					//	$allmatched[$smsc['smscommands_key2']] = $match[1];
					//}

					if(preg_match('/'.$smscommands_key2.'/si',$str,$match)) {
						if(isset($match[1])) {
							$allmatched[$smsc['smscommands_key2']] = $match[1];
						} else {
							$allmatched[$smsc['smscommands_key2']] = $match[0];
						}
					}

					//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match,'$smsc'=>$smsc));

				} else {
					$matched = false;
					$error[2] = $smsc['smscommands_key2_error'];
				}
			}

			if($matched&&!empty($smsc['smscommands_key3'])) {

				$smscommands_key3 = getOption($smsc['smscommands_key3']);

				$regstr .= '\s+'.$smscommands_key3;

				$regx = '/'.$regstr.'/si';

				//print_r(array('regx'=>$regx));

				if(preg_match($regx,$str,$match)) {

					if($matchedctr<4) {
						$matchedctr = 4;
					}

					$matched = true;

					//if(preg_match('/'.$smscommands_key3.'/si',$str,$match)) {
					//	$allmatched[$smsc['smscommands_key3']] = $match[0];
					//}

					if(preg_match('/'.$smscommands_key3.'/si',$str,$match)) {
						if(isset($match[1])) {
							$allmatched[$smsc['smscommands_key3']] = $match[1];
						} else {
							$allmatched[$smsc['smscommands_key3']] = $match[0];
						}
					}

					//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match,'$smsc'=>$smsc));

				} else {
					$matched = false;
					$error[3] = $smsc['smscommands_key3_error'];
				}
			}

			if($matched&&!empty($smsc['smscommands_key4'])) {

				$smscommands_key4 = getOption($smsc['smscommands_key4']);

				$regstr .= '\s+'.$smscommands_key4;

				$regx = '/'.$regstr.'/si';

				//print_r(array('regx'=>$regx));

				if(preg_match($regx,$str,$match)) {

					if($matchedctr<5) {
						$matchedctr = 5;
					}

					$matched = true;

					//if(preg_match('/'.$smscommands_key4.'/si',$str,$match)) {
					//	$allmatched[$smsc['smscommands_key4']] = $match[0];
					//}

					if(preg_match('/'.$smscommands_key4.'/si',$str,$match)) {
						if(isset($match[1])) {
							$allmatched[$smsc['smscommands_key4']] = $match[1];
						} else {
							$allmatched[$smsc['smscommands_key4']] = $match[0];
						}
					}

					//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match,'$smsc'=>$smsc));

				} else {
					$matched = false;
					$error[4] = $smsc['smscommands_key4_error'];
				}
			}

			if($matched&&!empty($smsc['smscommands_key5'])) {

				$smscommands_key5 = getOption($smsc['smscommands_key5']);

				$regstr .= '\s+'.$smscommands_key5;

				$regx = '/'.$regstr.'/si';

				//print_r(array('regx'=>$regx));

				if(preg_match($regx,$str,$match)) {

					if($matchedctr<6) {
						$matchedctr = 6;
					}

					$matched = true;

					//if(preg_match('/'.$smscommands_key5.'/si',$str,$match)) {
					//	$allmatched[$smsc['smscommands_key5']] = $match[0];
					//}

					if(preg_match('/'.$smscommands_key5.'/si',$str,$match)) {
						if(isset($match[1])) {
							$allmatched[$smsc['smscommands_key5']] = $match[1];
						} else {
							$allmatched[$smsc['smscommands_key5']] = $match[0];
						}
					}

					//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match,'$smsc'=>$smsc));

				} else {
					$matched = false;
					$error[5] = $smsc['smscommands_key5_error'];
				}
			}

			if($matched&&!empty($smsc['smscommands_key6'])) {

				$smscommands_key6 = getOption($smsc['smscommands_key6']);

				$regstr .= '\s+'.$smscommands_key6;

				$regx = '/'.$regstr.'/si';

				//print_r(array('regx'=>$regx));

				if(preg_match($regx,$str,$match)) {

					if($matchedctr<7) {
						$matchedctr = 7;
					}

					$matched = true;

					//if(preg_match('/'.$smscommands_key6.'/si',$str,$match)) {
					//	$allmatched[$smsc['smscommands_key6']] = $match[0];
					//}

					if(preg_match('/'.$smscommands_key6.'/si',$str,$match)) {
						if(isset($match[1])) {
							$allmatched[$smsc['smscommands_key6']] = $match[1];
						} else {
							$allmatched[$smsc['smscommands_key6']] = $match[0];
						}
					}

					//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match,'$smsc'=>$smsc));

				} else {
					$matched = false;
					$error[6] = $smsc['smscommands_key6_error'];
				}
			}

			if($matched&&!empty($smsc['smscommands_key7'])) {

				$smscommands_key7 = getOption($smsc['smscommands_key7']);

				$regstr .= '\s+'.$smscommands_key7;

				$regx = '/'.$regstr.'/si';

				//print_r(array('regx'=>$regx));

				if(preg_match($regx,$str,$match)) {

					if($matchedctr<8) {
						$matchedctr = 8;
					}

					$matched = true;

					//if(preg_match('/'.$smscommands_key7.'/si',$str,$match)) {
					//	$allmatched[$smsc['smscommands_key7']] = $match[0];
					//}

					if(preg_match('/'.$smscommands_key7.'/si',$str,$match)) {
						if(isset($match[1])) {
							$allmatched[$smsc['smscommands_key7']] = $match[1];
						} else {
							$allmatched[$smsc['smscommands_key7']] = $match[0];
						}
					}

					//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match,'$smsc'=>$smsc));

				} else {
					$matched = false;
					$error[7] = $smsc['smscommands_key7_error'];
				}
			}

			if($matched&&!empty($smsc['smscommands_key8'])) {

				$smscommands_key8 = getOption($smsc['smscommands_key8']);

				$regstr .= '\s+'.$smscommands_key8;

				$regx = '/'.$regstr.'/si';

				//print_r(array('regx'=>$regx));

				if(preg_match($regx,$str,$match)) {

					if($matchedctr<9) {
						$matchedctr = 9;
					}

					$matched = true;

					//if(preg_match('/'.$smscommands_key8.'/si',$str,$match)) {
					//	$allmatched[$smsc['smscommands_key8']] = $match[0];
					//}

					if(preg_match('/'.$smscommands_key8.'/si',$str,$match)) {
						if(isset($match[1])) {
							$allmatched[$smsc['smscommands_key8']] = $match[1];
						} else {
							$allmatched[$smsc['smscommands_key8']] = $match[0];
						}
					}

					//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match,'$smsc'=>$smsc));

				} else {
					$matched = false;
					$error[8] = $smsc['smscommands_key8_error'];
				}
			}

			if($matched&&!empty($smsc['smscommands_key9'])) {

				$smscommands_key9 = getOption($smsc['smscommands_key9']);

				$regstr .= '\s+'.$smscommands_key9;

				$regx = '/'.$regstr.'/si';

				//print_r(array('regx'=>$regx));

				if(preg_match($regx,$str,$match)) {

					if($matchedctr<10) {
						$matchedctr = 10;
					}

					$matched = true;

					//if(preg_match('/'.$smscommands_key9.'/si',$str,$match)) {
					//	$allmatched[$smsc['smscommands_key9']] = $match[0];
					//}

					if(preg_match('/'.$smscommands_key9.'/si',$str,$match)) {
						if(isset($match[1])) {
							$allmatched[$smsc['smscommands_key9']] = $match[1];
						} else {
							$allmatched[$smsc['smscommands_key9']] = $match[0];
						}
					}

					//print_r(array('$smscommands_key2'=>$smscommands_key2,'$match'=>$match,'$smsc'=>$smsc));

				} else {
					$matched = false;
					$error[9] = $smsc['smscommands_key9_error'];
				}
			}

			if($matched) {
				return array('mobileNo'=>$content['smsinbox_simnumber'],'regx'=>$regstr,'smscommands'=>$smsc,'smsinbox'=>$content,'matched'=>$allmatched);
			}

		} // foreach($smscommands as $smsc) {

		if($matchedctr) {
			return array('error'=>true,'matchedctr'=>$matchedctr,'errmsg'=>$error[$matchedctr]);
		}

	} // if(!empty($smscommands)) {

	return false;
}

function processSMS($content=false) {

	//print_r(array('processSMS$content'=>$content));

	if(!empty($content)) {
	} else return false;

	$matched=smsCommandMatched($content);

	if($matched===false) {
		return false;
	}

	//print_r(array('$matched'=>$matched));

	if($matched&&is_array($matched)&&!empty($matched['error'])) {
		$errmsg = smsdt()." ".getOption($matched['errmsg']);
		sendToOutBox($content['smsinbox_contactnumber'],$content['smsinbox_simnumber'],$errmsg);
		return false;
	} else
	if($matched&&is_array($matched)) {
		if(!empty($matched['smscommands']['smscommands_action0'])&&is_callable($matched['smscommands']['smscommands_action0'],false,$callable_name)) {
			return $callable_name($matched);
		}
	}

	return false;
}

function doSMSCommands($sms=false,$mobileNo=false) {
	global $appdb;

	$validModemCommands = false;

	if(!empty($sms)&&!empty($mobileNo)) {
	} else return false;

	if(!($result=$appdb->query("select *,(extract(epoch from now()) - extract(epoch from loadtransaction_execstamp)) as elapsedtime from tbl_loadtransaction where loadtransaction_completed=1 and loadtransaction_simnumber='$mobileNo' order by loadtransaction_id asc limit 1"))) {
		return false;
	}

	if(!empty($result['rows'][0]['loadtransaction_id'])) {

		$content = array();
		$content['loadtransaction_attempt2'] = intval($result['rows'][0]['loadtransaction_attempt2']) + 1;
		$content['loadtransaction_updatestamp'] = 'now()';

		//if($content['loadtransaction_attempt2']>10||$result['rows'][0]['elapsedtime']>60) {  /// 10 attempts or 60 seconds

		if($result['rows'][0]['elapsedtime']>60) {  /// 60 seconds
			$content['loadtransaction_completed'] = 5; // pending

			print_r(array('$content'=>$content));
		}

		if(!($result = $appdb->update("tbl_loadtransaction",$content,"loadtransaction_id=".$result['rows'][0]['loadtransaction_id']))) {
			return false;
		}

		return false;
	}

	if(!($result=$appdb->query("select *,(extract(epoch from now()) - extract(epoch from loadtransaction_updatestamp)) as elapsedtime from tbl_loadtransaction where loadtransaction_completed=0 and loadtransaction_simnumber='$mobileNo' order by loadtransaction_id asc limit 1"))) {
		return false;
	}

	if(!empty($result['rows'][0]['loadtransaction_id'])) {

		$loadtransaction = $result['rows'][0];

		print_r(array('$loadtransaction'=>$loadtransaction));

		$content = array();
		$content['smsinbox_message'] = $loadtransaction['loadtransaction_keyword'];
		$content['smsinbox_contactnumber'] = $loadtransaction['loadtransaction_contactnumber'];
		$content['smsinbox_simnumber'] = $loadtransaction['loadtransaction_simnumber'];

		if(!($matched=smsCommandMatched($content))) {
			return false;
		}

		print_r(array('$matched'=>$matched));

		if($matched) {

			if(!($result = $appdb->query("select * from tbl_modemcommands where modemcommands_name='".$loadtransaction['loadtransaction_smsaction']."'"))) {
				return false;
			}

			if(!empty($result['rows'][0]['modemcommands_id'])) {
				//print_r(array('$result'=>$result['rows']));
				$validModemCommands = $result['rows'][0]['modemcommands_id'];
			} else {
				$content = array();
				$content['loadtransaction_completed'] = 4;
				$content['loadtransaction_invalid'] = 1;
				$content['loadtransaction_updatestamp'] = 'now()';

				if(!($result = $appdb->update("tbl_loadtransaction",$content,"loadtransaction_id=".$loadtransaction['loadtransaction_id']))) {
					return false;
				}
			}

		}

	}

/*
$simfunctions[] = array('command'=>'AT','regx'=>array("OK\r\n"),'resultindex'=>0);

$simfunctions[] = array('command'=>'AT+CUSD=1,0','regx'=>array("\+CUSD\:.+?\r\n","\+CUSD\:\s+(\d+)\r\n"),'resultindex'=>1,'expectedresult'=>4,'repeat'=>100);

$simfunctions[] = array('command'=>'AT+CUSD=1,*343#','regx'=>array("\+CUSD\:.+?\r\n","(\d+)\:Regular\s+Load"),'resultindex'=>1);

$simfunctions[] = array('command'=>'AT+CUSD=1,$1','regx'=>array("\+CUSD\:.+?\r\n","(Enter\s+number)"),'resultindex'=>0);

$simfunctions[] = array('command'=>'AT+CUSD=1,09493621618','regx'=>array("\+CUSD\:.+?\r\n","(Enter\s+Amount)"),'resultindex'=>0);

$simfunctions[] = array('command'=>'AT+CUSD=1,5','regx'=>array("\+CUSD\:.+?\r\n","(\d+)\:Load"),'resultindex'=>1,'expectedresult'=>1);

$simfunctions[] = array('command'=>'AT+CUSD=1,$1','regx'=>array("\+CUSD\:.+?\r\n"),'resultindex'=>0);
*/

	if($validModemCommands) {

		if(!($result = $appdb->query("select * from tbl_atcommands where atcommands_modemcommandsid='$validModemCommands' order by atcommands_id asc"))) {
			return false;
		}

		if(!empty($result['rows'][0]['atcommands_id'])) {

			//print_r(array('$result'=>$result['rows']));

			$atsc = array();

			foreach($result['rows'] as $row) {
				$t = array();

				$at = $row['atcommands_at'];

				foreach($matched['matched'] as $ak=>$am) {
					$at = str_replace($ak,$am,$at);
				}

				$t['command'] = $at;
				$t['resultindex'] = $row['atcommands_resultindex'];
				$t['expectedresult'] = !empty($row['atcommands_expectedresult']) ? $row['atcommands_expectedresult'] : false;
				$t['repeat'] = !empty($row['atcommands_repeat']) ? $row['atcommands_repeat'] : false;
				$t['regx'] = array();

				for($i=0;$i<10;$i++) {
					if(!empty($row['atcommands_regx'.$i])) {
						$o = getOption($row['atcommands_regx'.$i]);
						if(!empty($row['atcommands_param'.$i])) {
							$o = str_replace('%param%',$row['atcommands_param'.$i],$o);
						}
						$t['regx'][] = $o;
					}
				}

				$atsc[] = $t;
			}

			pre(array('$atsc'=>$atsc));

			$content = array();

			if(modemFunction($sms,$atsc)) {

				$content['loadtransaction_completed'] = 1; // at commands sent successfully

				$content['loadtransaction_execstamp'] = 'now()';

			} else {

				$content['loadtransaction_attempt'] = (intval($loadtransaction['loadtransaction_attempt']) + 1);

				if($content['loadtransaction_attempt']>2) {

					$content['loadtransaction_completed'] = 3; // not successful

				}

			}

			$content['loadtransaction_updatestamp'] = 'now()';

			if(!($result = $appdb->update("tbl_loadtransaction",$content,"loadtransaction_id=".$loadtransaction['loadtransaction_id']))) {
				return false;
			}

		}


	}

}

function doModemCommands($sms=false,$mobileNo=false) {
	global $appdb;

	if(!empty($sms)&&!empty($mobileNo)) {

	} else return false;

	//$loadtransaction_smsaction = '$STGI_TESTING';
	//$loadtransaction_smsaction = '$STGI_TESTING2';
	//$loadtransaction_smsaction = '$STGI_TESTING3';
	//$loadtransaction_smsaction = '$STGI_TESTING4';
	//$loadtransaction_smsaction = '$STGI_TESTING6';
	//$loadtransaction_smsaction = '$STGI_TESTING11';
	//$loadtransaction_smsaction = '$STGI_TEST_SMARTBRO';
	//$loadtransaction_smsaction = '$STGI_TEST_REGULARLOAD';
	//$loadtransaction_smsaction = '$STGI_TEST_PLDT';
	//$loadtransaction_smsaction = '$STGI_TEST_CIGNAL';
	//$loadtransaction_smsaction = '$STGI_TEST_MERALCO';
	$loadtransaction_smsaction = '$STGI_TEST_BALANCE';

	if(!($result = $appdb->query("select * from tbl_modemcommands where modemcommands_name='$loadtransaction_smsaction'"))) {
		return false;
	}

	print_r(array('$result'=>$result));

	if(!empty($result['rows'][0]['modemcommands_id'])) {
		$validModemCommands = $result['rows'][0]['modemcommands_id'];
	}

	if(!empty($validModemCommands)) {

		if(!($result = $appdb->query("select * from tbl_atcommands where atcommands_modemcommandsid='$validModemCommands' order by atcommands_id asc"))) {
			return false;
		}

		if(!empty($result['rows'][0]['atcommands_id'])) {

			//print_r(array('$result'=>$result['rows']));

			$params = array('$MOBILENUMBER'=>'09493621618');

			$atsc = array();

			foreach($result['rows'] as $row) {
				$t = array();

				$at = $row['atcommands_at'];

				foreach($params as $ak=>$am) {
					$at = str_replace($ak,$am,$at);
				}

				$t['command'] = $at;
				$t['resultindex'] = $row['atcommands_resultindex'];
				$t['expectedresult'] = !empty($row['atcommands_expectedresult']) ? $row['atcommands_expectedresult'] : false;
				$t['repeat'] = !empty($row['atcommands_repeat']) ? $row['atcommands_repeat'] : false;
				$t['regx'] = array();

				for($i=0;$i<10;$i++) {
					if(!empty($row['atcommands_regx'.$i])) {
						$o = getOption($row['atcommands_regx'.$i]);
						if(!empty($row['atcommands_param'.$i])) {
							$o = str_replace('%param%',$row['atcommands_param'.$i],$o);
						}
						$t['regx'][] = $o;
					}
				}

				$atsc[] = $t;
			}

			pre(array('$atsc'=>$atsc));

			$content = array();

			if(modemFunction($sms,$atsc)) {


			} else {


			}

			print_r(array('history'=>$sms->getHistory()));

		}


	}


	return true;
}

///-----------------------------------------------------------------------------

function getLoadTransactionStatusString($status=0) {
	global $_CONSTANTS;

	if(!empty($status)&&!empty($_CONSTANTS['STATUS'][$status])) {
		return $_CONSTANTS['STATUS'][$status];
	}

	/*
	if(!empty($status)) {
		if($status==TRN_APPROVED) {
			return TRNS_APPROVED;
		} else
		if($status==TRN_PROCESSING) {
			return TRNS_PROCESSING;
		} else
		if($status==TRN_SENT) {
			return TRNS_SENT;
		} else
		if($status==TRN_COMPLETED) {
			return TRNS_COMPLETED;
		} else
		if($status==TRN_PENDING) {
			return TRNS_PENDING;
		} else
		if($status==TRN_CANCELLED) {
			return TRNS_CANCELLED;
		} else
		if($status==TRN_COMPLETED_MANUALLY) {
			return TRNS_COMPLETED_MANUALLY;
		} else
		if($status==TRN_HOLD) {
			return TRNS_HOLD;
		} else
		if($status==TRN_FAILED) {
			return TRNS_FAILED;
		} else
		if($status==TRN_QUEUED) {
			return TRNS_QUEUED;
		} else
		if($status==TRN_INVALID_SIM_COMMANDS) {
			return TRNS_INVALID_SIM_COMMANDS;
		} else
		if($status==TRN_CLAIMED) {
			return TRNS_CLAIMED;
		} else
		if($status==TRN_DRAFT) {
			return TRNS_DRAFT;
		} else
		if($status==TRN_WAITING) {
			return TRNS_WAITING;
		} else
		if($status==TRN_POSTED) {
			return TRNS_POSTED;
		} else
		if($status==TRN_RECEIVED) {
			return TRNS_RECEIVED;
		}
	}
	*/

	return 'UNKNOWN';
}

function getAllStatus() {
	global $_CONSTANTS;

	return $_CONSTANTS['STATUS'];
}

function pacsDoSoapLogin() {
	global $PACS_URL, $PACS_IMAGEURL, $PACS_USER, $PACS_PASS;

	if(!empty($PACS_URL)&&!empty($PACS_IMAGEURL)&&!empty($PACS_USER)&&!empty($PACS_PASS)) {
	} else {
		die('pacsDoSoapLogin: Invalid Parameters!');
	}

	$start_time = time();

	print_r(array('pacsDoSoapLogin()'=>'Started.'));

  //Data, connection, auth
  //$dataFromTheForm = $_POST['fieldName']; // request data from the form
  $soapUrl = $PACS_URL; // asmx URL of WSDL
  $soapUser = $PACS_USER;  //  username
  $soapPassword = $PACS_PASS; // password

  // xml post structure

  $xml_post_string = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><ReqAuthenticate xmlns="http://www.meridio.com/meridio.xsd"><UserName>'.$soapUser.'</UserName><Password>'.$soapPassword.'</Password><ClientWorkStation>PACS Integration Library</ClientWorkStation></ReqAuthenticate></soap:Body></soap:Envelope>';   // data from the form, e.g. some ID number

 $headers = array(
              "Content-type: text/xml;charset=\"utf-8\"",
              "Accept: text/xml",
              "Cache-Control: no-cache",
              "Pragma: no-cache",
              "Content-length: ".strlen($xml_post_string),
          ); //SOAPAction: your op URL

  $url = $soapUrl;

  // PHP cURL  for https connection with auth
  $ch = curl_init();
  //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
  //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

  //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_VERBOSE, 1);
  //curl_setopt($ch, CURLOPT_HEADER, 1);

  curl_setopt($ch, CURLOPT_TIMEOUT, 360);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  // converting
  $response = curl_exec($ch);
  curl_close($ch);

  //pre(array('$response'=>$response));

	$end_time = time();

	$total_secs = $end_time - $start_time;

	print_r(array('pacsDoSoapLogin()'=>'Ended ('.$total_secs.'seconds).'));

  return $response;

  // converting
  //$response1 = str_replace("<soap:Body>","",$response);
  //$response2 = str_replace("</soap:Body>","",$response1);

  // convertingc to XML
  //$parser = simplexml_load_string($response2);
  // user $parser to get your data out of XML response and to display it.

} // function pacsDoSoapLogin() {

function pacsDoSoapSearch($token=false,$conn=false,$settime=false) {
	global $PACS_URL, $PACS_IMAGEURL, $PACS_USER, $PACS_PASS;

	if(!empty($PACS_URL)&&!empty($PACS_IMAGEURL)&&!empty($PACS_USER)&&!empty($PACS_PASS)) {
	} else {
		die('pacsDoSoapSearch: Invalid Parameters!');
	}

  if(!empty($token)&&!empty($conn)) {
  } else {
    return false;
  }

	$start_time = time();

	print_r(array('pacsDoSoapSearch('.$conn.','.$token.')'=>'Started.'));

  //Data, connection, auth
  //$dataFromTheForm = $_POST['fieldName']; // request data from the form
  $soapUrl = $PACS_URL; // asmx URL of WSDL
  $soapUser = $PACS_USER;  //  username
  $soapPassword = $PACS_PASS; // password
  //$conn = "130565321";
  //$conn = "482405265";
  // xml post structure

	if(!empty($settime)&&is_numeric($settime)) {
		$today = $settime;
	} else {
		$today = time();
	}

  $tmm = intval(date('m', $today));
  $tdd = intval(date('d', $today));
  $tyy = intval(date('Y', $today));

  $minusPeriod = 60 * 60 * 24 * 28;

  $period = $today - $minusPeriod;

  $pmm = intval(date('m', $period));
  $pdd = intval(date('d', $period));
  $pyy = intval(date('Y', $period));

  $xml_post_string = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Header><Authenticated Token="'.$token.'" xmlns="http://www.meridio.com/meridio.xsd" /></soap:Header><soap:Body><ReqSearch xmlns="http://www.meridio.com/meridio.xsd"><Search><SrcSpec Type="DOCUMENT" Permission="READ" MaxHitsToReturn="1800" StartPositionOfHits="0" SearchAll="false" Scope="BOTH" SrcChildren="false" /><SrcDef><PropertyRoot><PropertyAndOp><StrTerm StrRelation="IS"><KeyPropertyDef Object="DOCUMENT" Type="CTEXT" Id="1001" /><StrValue>'.$conn.'</StrValue></StrTerm><NumTerm NumRelation="EQUAL"><KeyPropertyDef Object="DOCUMENT" Type="CNUMBER" Id="1005" /><NumValue>1</NumValue></NumTerm><NumTerm NumRelation="EQUAL"><KeyPropertyDef Object="DOCUMENT" Type="CNUMBER" Id="1013" /><NumValue>0</NumValue></NumTerm><DateTerm DateRelation="dONORAFTER"><KeyPropertyDef Object="DOCUMENT" Type="CDATE" Id="1003" /><DateValue Year="'.$pyy.'" Month="'.$pmm.'" Day="'.$pdd.'" Hour="0" Minute="0" Second="0" /></DateTerm><DateTerm DateRelation="dBEFORE"><KeyPropertyDef Object="DOCUMENT" Type="CDATE" Id="1003" /><DateValue Year="'.$tyy.'" Month="'.$tmm.'" Day="'.$tdd.'" Hour="0" Minute="0" Second="0" /></DateTerm></PropertyAndOp></PropertyRoot></SrcDef><ResDef><KeyPropertyDef Object="DOCUMENT" Type="CTEXT" Id="1006" /></ResDef><ResDef><KeyPropertyDef Object="DOCUMENT" Type="CTEXT" Id="1002" /></ResDef><ResDef><KeyPropertyDef Object="DOCUMENT" Type="CNUMBER" Id="1012" /></ResDef><ResDef><KeyPropertyDef Object="DOCUMENT" Type="CNUMBER" Id="1014" /></ResDef><ResDef><KeyPropertyDef Object="DOCUMENT" Type="CDATE" Id="1007" /></ResDef><ResDef><KeyPropertyDef Object="DOCUMENT" Type="CDATE" Id="1003" /></ResDef><ResDef><KeyPropertyDef Object="VERSION" Type="FNUMBER" Id="11" /></ResDef></Search></ReqSearch></soap:Body></soap:Envelope>';

 $headers = array(
              "Content-type: text/xml;charset=\"utf-8\"",
              "Accept: text/xml",
              "Cache-Control: no-cache",
              "Pragma: no-cache",
              "Content-length: ".strlen($xml_post_string),
          ); //SOAPAction: your op URL

  $url = $soapUrl;

  // PHP cURL  for https connection with auth
  $ch = curl_init();
  //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
  //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

  //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_VERBOSE, 1);
  //curl_setopt($ch, CURLOPT_HEADER, 1);

  curl_setopt($ch, CURLOPT_TIMEOUT, 360);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  // converting
  $response = curl_exec($ch);
  curl_close($ch);

  //pre(array('$response'=>$response));

	$end_time = time();

	$total_secs = $end_time - $start_time;

	print_r(array('pacsDoSoapSearch()'=>'Ended ('.$total_secs.'seconds).'));

  return $response;

  // converting
  //$response1 = str_replace("<soap:Body>","",$response);
  //$response2 = str_replace("</soap:Body>","",$response1);

  // convertingc to XML
  //$parser = simplexml_load_string($response2);
  // user $parser to get your data out of XML response and to display it.

/*
<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Header><Authenticated Token="03f9072e-cccc-aa6d-d292-f44f642e57ca" xmlns="http://www.meridio.com/meridio.xsd" /></soap:Header><soap:Body><ReqSearch xmlns="http://www.meridio.com/meridio.xsd"><Search><SrcSpec Type="DOCUMENT" Permission="READ" MaxHitsToReturn="1800" StartPositionOfHits="0" SearchAll="false" Scope="BOTH" SrcChildren="false" /><SrcDef><PropertyRoot><PropertyAndOp><StrTerm StrRelation="IS"><KeyPropertyDef Object="DOCUMENT" Type="CTEXT" Id="1001" /><StrValue>832378282</StrValue></StrTerm><NumTerm NumRelation="EQUAL"><KeyPropertyDef Object="DOCUMENT" Type="CNUMBER" Id="1005" /><NumValue>1</NumValue></NumTerm><NumTerm NumRelation="EQUAL"><KeyPropertyDef Object="DOCUMENT" Type="CNUMBER" Id="1013" /><NumValue>0</NumValue></NumTerm><DateTerm DateRelation="dONORAFTER"><KeyPropertyDef Object="DOCUMENT" Type="CDATE" Id="1003" /><DateValue Year="2017" Month="8" Day="25" Hour="0" Minute="0" Second="0" /></DateTerm><DateTerm DateRelation="dBEFORE"><KeyPropertyDef Object="DOCUMENT" Type="CDATE" Id="1003" /><DateValue Year="2017" Month="9" Day="22" Hour="0" Minute="0" Second="0" /></DateTerm></PropertyAndOp></PropertyRoot></SrcDef><ResDef><KeyPropertyDef Object="DOCUMENT" Type="CTEXT" Id="1006" /></ResDef><ResDef><KeyPropertyDef Object="DOCUMENT" Type="CTEXT" Id="1002" /></ResDef><ResDef><KeyPropertyDef Object="DOCUMENT" Type="CNUMBER" Id="1012" /></ResDef><ResDef><KeyPropertyDef Object="DOCUMENT" Type="CNUMBER" Id="1014" /></ResDef><ResDef><KeyPropertyDef Object="DOCUMENT" Type="CDATE" Id="1007" /></ResDef><ResDef><KeyPropertyDef Object="DOCUMENT" Type="CDATE" Id="1003" /></ResDef><ResDef><KeyPropertyDef Object="VERSION" Type="FNUMBER" Id="11" /></ResDef></Search></ReqSearch></soap:Body></soap:Envelope>
*/

} // function pacsDoSoapSearch($token=false,$conn=false,$settime=false) {

function pacsDoSoapLogout($token=false) {
	global $PACS_URL, $PACS_IMAGEURL, $PACS_USER, $PACS_PASS;

	if(!empty($PACS_URL)&&!empty($PACS_IMAGEURL)&&!empty($PACS_USER)&&!empty($PACS_PASS)) {
	} else {
		die('pacsDoSoapLogout: Invalid Parameters!');
	}

/*
<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Header><Authenticated Token="04274864-1e20-c0cc-c742-46de9e913f64" xmlns="http://www.meridio.com/meridio.xsd" /></soap:Header><soap:Body><ReqCommit xmlns="http://www.meridio.com/meridio.xsd"><CommitSession><Remove /></CommitSession></ReqCommit></soap:Body></soap:Envelope>
*/


  if(!empty($token)) {
  } else {
    return false;
  }

	$start_time = time();

	print_r(array('pacsDoSoapLogout()'=>'Started.'));

  //Data, connection, auth
  //$dataFromTheForm = $_POST['fieldName']; // request data from the form
  $soapUrl = $PACS_URL; // asmx URL of WSDL
  $soapUser = $PACS_USER;  //  username
  $soapPassword = $PACS_PASS; // password
  //$conn = "797559179";

  // xml post structure

  $xml_post_string = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Header><Authenticated Token="'.$token.'" xmlns="http://www.meridio.com/meridio.xsd" /></soap:Header><soap:Body><ReqCommit xmlns="http://www.meridio.com/meridio.xsd"><CommitSession><Remove /></CommitSession></ReqCommit></soap:Body></soap:Envelope>';

 $headers = array(
              "Content-type: text/xml;charset=\"utf-8\"",
              "Accept: text/xml",
              "Cache-Control: no-cache",
              "Pragma: no-cache",
              "Content-length: ".strlen($xml_post_string),
          ); //SOAPAction: your op URL

  $url = $soapUrl;

  // PHP cURL  for https connection with auth
  $ch = curl_init();
  //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
  //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

  //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_VERBOSE, 1);
  //curl_setopt($ch, CURLOPT_HEADER, 1);

  curl_setopt($ch, CURLOPT_TIMEOUT, 360);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  // converting
  $response = curl_exec($ch);
  curl_close($ch);

  //pre(array('$response'=>$response));

	$end_time = time();

	$total_secs = $end_time - $start_time;

	print_r(array('pacsDoSoapLogout()'=>'Ended ('.$total_secs.'seconds).'));

  return $response;

} // function pacsDoSoapLogout($token=false) {

function pacsDoGetImage($url=false) {
	global $PACS_URL, $PACS_IMAGEURL, $PACS_USER, $PACS_PASS;

	if(!empty($PACS_URL)&&!empty($PACS_IMAGEURL)&&!empty($PACS_USER)&&!empty($PACS_PASS)) {
	} else {
		die('pacsDoSoapSearch: Invalid Parameters!');
	}

/*
<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Header><Authenticated Token="04274864-1e20-c0cc-c742-46de9e913f64" xmlns="http://www.meridio.com/meridio.xsd" /></soap:Header><soap:Body><ReqCommit xmlns="http://www.meridio.com/meridio.xsd"><CommitSession><Remove /></CommitSession></ReqCommit></soap:Body></soap:Envelope>
*/


  if(!empty($url)) {
  } else {
    return false;
  }

	$start_time = time();

	print_r(array('pacsDoGetImage()'=>'Started.'));

  // PHP cURL  for https connection with auth
  $ch = curl_init();
  //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
  //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

  //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_VERBOSE, 1);
  //curl_setopt($ch, CURLOPT_HEADER, 1);

  curl_setopt($ch, CURLOPT_TIMEOUT, 360);
  //curl_setopt($ch, CURLOPT_POST, true);
  //curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  // converting
  $response = curl_exec($ch);
  curl_close($ch);

  //pre(array('$response'=>$response));

	$end_time = time();

	$total_secs = $end_time - $start_time;

	print_r(array('pacsDoGetImage()'=>'Ended ('.$total_secs.'seconds).'));

  return $response;

} // function pacsDoGetImage($url=false) {

function pacsDoProcess($conn=false,$token=false) {
	global $PACS_URL, $PACS_IMAGEURL, $PACS_USER, $PACS_PASS;

	if(!empty($PACS_URL)&&!empty($PACS_IMAGEURL)&&!empty($PACS_USER)&&!empty($PACS_PASS)) {
	} else {
		die('pacsDoProcess: Invalid Parameters!');
	}

	if(!empty($conn)&&is_numeric($conn)) {
	} else {
		return false;
	}

	$start_time = time();

	print_r(array('pacsDoProcess('.$conn.','.$token.')'=>'Started.'));

	//$token = false;

	$success = false;

	if(!empty($token)) {
	} else {
		$res = pacsDoSoapLogin();

		if(preg_match('/Token="(.+?)"/si',$res,$match)) {
		  //print_r(array('$match'=>$match));

		  $token = $match[1];
		}
	}

	if(!empty($token)) {

	  $res = pacsDoSoapSearch($token,$conn);

	  if(preg_match('/\<SOAP\-ENV\:Envelope.+?\<\/SOAP\-ENV\:Envelope\>/si',$res,$match)) {

	    $xml = $match[0];

	    if(preg_match('/KeyVersion\s+VersionId\=\"(\d+)\"/si',$xml,$versionId)&&preg_match('/KeyDocument\s+Id\=\"(\d+)\"/si',$xml,$documentId)) {
	      //print_r(array('$versionId'=>$versionId,'$documentId'=>$documentId));

				$imageUrl = $PACS_IMAGEURL;

				$imageUrl = str_replace('%TOKEN%',$token,$imageUrl);
				$imageUrl = str_replace('%DOCID%',$documentId[1],$imageUrl);
				$imageUrl = str_replace('%VERSIONID%',$versionId[1],$imageUrl);

	      //$imageUrl = 'http://pacs.tntad.fedex.com/TNTCache/retrieve.asp?Token='.$token.'&DocId='.$documentId[1].'&VersionId='.$versionId[1];

	      print_r(array('$imageUrl'=>$imageUrl));

				$loopctr = 0;

				while(1) {

					$img = pacsDoGetImage($imageUrl);

		      if(preg_match('/\<ErrorCode\>(\d+)\<\/ErrorCode\>.+?\<Description\>(.+?)\<\/Description\>/si',$img,$mt)) {

		        pre(array('ERROR'=>$mt));

						$success = 3;

						break;

		      } else {

		        //$filenameonly = $documentId[1] . '.tiff';

		        $filenameonly = $conn . '-' . time() . '.tiff';

		        $connfilename = $conn . '.tiff';

		        $connfile = '/var/log/cache/'.$connfilename;

		        //print_r(array('$img'=>base64_encode($img)));

		        $filenamepath = '/var/log/cache/'.$filenameonly;

		        //print_r(array('$filename'=>$filename));

		        if($hf=fopen($filenamepath,'w')) {
		          $ret=fwrite($hf,$img);

		          fclose($hf);
		        }

		        $partial = false;

		        if(preg_match('/^(inside\s+retrieve)II/s',$img,$mt)) {
							continue;
		        } else {
		          rename($filenamepath,$connfile);
							$success = 1;
							break;
		        }

		      }

					$loopctr++;

					print_r(array('loop'=>$loopctr));

				} // while(1) {

	    } else {
	      print_r(array('ERROR'=>'No Image Found!'));
				$success = 2;
	    }

	    //$arr = xmlobj2array($match[0]);

	    //print_r(array('$arr'=>$arr));
	  }

	  //$res = pacsDoSoapLogout($token);

	}

	$end_time = time();

	$total_secs = $end_time - $start_time;

	print_r(array('pacsDoProcess()'=>'Ended ('.$total_secs.'seconds).'));

	return $success;

} // function pacsDoProcess($conn=false) {

/* INCLUDES_END */


#eof ./includes/functions/index.php
