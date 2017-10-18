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

if(!class_exists('APP_Test')) {

	class APP_Test extends APP_Base {
	
		var $pathid = 'test';
		var $desc = 'Testing';
		var $post = false;
		var $vars = false;
		
		var $cls_ajax = false;

		var $usermod = false;
	
		function __construct() {
			parent::__construct();

			$this->app_init();
		}
		
		function __destruct() {
			parent::__destruct();
		}

		function app_init() {
			$this->usermod = new APP_App_User($this->pathid,$this);
		}
		
		function modulespath() {
			return str_replace(basename(__FILE__),'',__FILE__);
		}

		function add_css() {
			global $apptemplate;

			//$apptemplate->add_css('styles','http://fonts.googleapis.com/css?family=Open+Sans:400,700');
			//$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'css/login.css');
		}

		function add_script() {
			global $apptemplate;

			$apptemplate->add_script($apptemplate->templates_urlpath().'js/testing.js');
		}

		function add_rules() {
			global $appaccess;

			//$appaccess->rules($this->pathid,'add-users','Add users');
			//$appaccess->rules($this->pathid,'update-users','Update users');
			//$appaccess->rules($this->pathid,'view-users','View users');
			//$appaccess->rules($this->pathid,'delete-users','Delete users');
		}

		function is_loggedin() {

		}

		function render($vars) {
			global $apptemplate, $appform, $current_page;
			
			$this->check_url();

			$apptemplate->header($this->desc.' | '.APP_NAME);

			//$apptemplate->page('topnavbar');
	
			//$apptemplate->page('topnav');
	
			//$apptemplate->page('topmenu');

			//$apptemplate->page('workarea');

			$apptemplate->page('test');

			$apptemplate->footer();
			
		} // render
								
	} // class APP_Test

	$apptest = new APP_Test;
}

# eof modules/app