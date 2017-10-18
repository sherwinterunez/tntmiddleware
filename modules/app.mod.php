<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* App Module
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

if(!class_exists('APP_App')) {

	class APP_App extends APP_Base {

		var $pathid = 'app';
		var $desc = 'App';
		var $post = false;
		var $vars = false;

		var $cls_ajax = false;

		var $usermod = false;

		function __construct() {
			parent::__construct();
		}

		function __destruct() {
			parent::__destruct();
		}

		function modulespath() {
			return str_replace(basename(__FILE__),'',__FILE__);
		}

		function add_css() {
			global $apptemplate;
		}

		function add_script() {
			global $apptemplate;

			$apptemplate->add_script('/'.$this->pathid.'/js/');
		}

		function add_rules() {
			global $appaccess;
		}

		function add_route() {
			global $approuter;
		}

		function js($vars) {
			require_once('app.mod.inc.js');
		}

		function dosetsmsstatus($vars) {
			global $approuter;

			pre(array('$vars'=>$vars));
		}

		function dogetstatus($vars) {
			global $approuter, $applogin, $toolbars, $forms, $apptemplate, $appdb;

			$smsinbox_count = 0;
			$smsoutbox_count = 0;
			$smssent_count = 0;
			$contact_count = 0;
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

			pre(array('$vars'=>$vars,'$smsinbox_count'=>$smsinbox_count,'$smsoutbox_count'=>$smsoutbox_count,'$smssent_count'=>$smssent_count));
		}

		function dogetinbox($vars) {
			global $approuter;

			pre(array('$vars'=>$vars));
		}

		function dogetoutbox($vars) {
			global $approuter, $applogin, $toolbars, $forms, $apptemplate, $appdb;

			//pre(array('$vars'=>$vars));

			if(!empty($vars['params'])) {
				$params = explode('/',$vars['params']);

				if(!empty($params[0])&&preg_match('/^(all|queued|waiting|sending|sent|failed)$/si',$params[0],$match)&&!empty($match[0])) {

					$limit = '';

					if(!empty($params[1])&&is_numeric($params[1])&&intval(trim($params[1]))>0) {
						$limit = ' limit '.intval(trim($params[1]));
					}

					$where = '';

					if($match[0]=='all') {
					} else
					if($match[0]=='queued') {
						$where = 'smsoutbox_status=0 and ';
					} else
					if($match[0]=='waiting') {
						$where = 'smsoutbox_status=1 and ';
					} else
					if($match[0]=='sending') {
						$where = 'smsoutbox_status=3 and ';
					} else
					if($match[0]=='sent') {
						$where = 'smsoutbox_status=4 and ';
					} else
					if($match[0]=='failed') {
						$where = 'smsoutbox_status=5 and ';
					}

					//smsoutbox_status

					$sql = "select *,(extract(epoch from now()) - extract(epoch from smsoutbox_failedstamp)) as elapsedtime from tbl_smsoutbox where $where smsoutbox_deleted=0 and smsoutbox_delay=0 order by smsoutbox_id desc $limit";

					$sql = "select * from ($sql) as A order by elapsedtime desc";
					//pre(array('$params'=>$params,'$match'=>$match,'$sql'=>$sql));

					if(!($result = $appdb->query($sql))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						die;
					}

					if(!empty($result['rows'][0]['smsoutbox_id'])) {
						json_encode_return(array('rows'=>$result['rows'],'$_SERVER'=>$_SERVER,'$params'=>$params,'$match'=>$match,'$sql'=>$sql));
						die;
					}

				}
			}
		}

		function render($vars) {
			global $applogin, $apptemplate, $appform, $current_page;

			if(!$applogin->is_loggedin()) {
				redirect301('/'.$applogin->pathid.'/');
			}

			$this->check_url();

			$apptemplate->header($this->desc.' | '.getOption('$APP_NAME',APP_NAME),'appheader');

			//$apptemplate->page('topnavbar');

			//$apptemplate->page('topnav');

			//$apptemplate->page('topmenu');

			//$apptemplate->page('workarea');

			//$apptemplate->page('app');

			$apptemplate->footer();

		} // render

	} // class APP_App

	$appapp = new APP_App;
}

# eof modules/app
