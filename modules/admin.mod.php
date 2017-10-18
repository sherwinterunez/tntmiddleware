<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Utilities Module Class
*
* Date: July 1, 2014 5:57PM +0800
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

if(!class_exists('APP_Admin')) {

	class APP_Admin extends APP_Base {
	
		var $pathid = 'admin';
		var $desc = 'Admin';
		var $post = false;
		var $vars = false;
		
		var $cls_ajax = false;
	
		function __construct() {
			parent::__construct();
		}
		
		function __destruct() {
			parent::__destruct();
		}
		
		function modulespath() {
			return str_replace(basename(__FILE__),'',__FILE__);
		}

		function render($vars) {
			global $apptemplate, $appform, $current_page;
			
			$this->check_url();

			$apptemplate->header($this->desc.' | '.APP_NAME);
	
			//$apptemplate->page('topnav');
	
			//$apptemplate->page('topmenu');

			//$apptemplate->page('workarea');

			$apptemplate->footer();
			
		} // render
								
	} // class APP_Admin

	$appadmin = new APP_Admin;
}

# eof modules/admin/index.php