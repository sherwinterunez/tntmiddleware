<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: February 23, 2011
*
* Description:
*
* Application entry point.
*
*/

//define('ANNOUNCE', true);

define('APPLICATION_RUNNING', true);

define('ABS_PATH', dirname(__FILE__) . '/');

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

require_once(ABS_PATH.'includes/index.php');
require_once(ABS_PATH.'modules/index.php');

function check_login() {
	global $applogin;

	if(!preg_match("#\/login(.+?)#si",$_SERVER['REQUEST_URI'])&&!$applogin->is_loggedin()) {
	
		if(!empty($_POST)) {
			json_return_error(255);
		}
	
		header("Location: /".$applogin->pathid."/",TRUE,301);
		die;
	}
}
//check_login();
add_action('router','check_login');

function defaultroute() {
	global $approuter;
	
	$approuter->addroute(array('^/\?(.*)$' => array('id'=>'index','param'=>'action=index', 'callback'=>'index')));
	$approuter->addroute(array('^/$' => array('id'=>'index','param'=>'action=index', 'callback'=>'index')));
	$approuter->addroute(array('^/(.*)' => array('id'=>'defaultpage','param'=>'action=error&param=$1', 'callback'=>'notfound')));
}

function index() {
	global $applogin, $apptemplate, $approuter;
	
	if(!$applogin->is_loggedin()) {
		header('Location: /login/');
		exit;
	}
	
	$apptemplate->flush_scripts();
	$apptemplate->flush_css();

	//$apptemplate->add_css($approuter->id,$apptemplate->templates_urlpath().'css/reset.css');
	$apptemplate->add_css($approuter->id,$apptemplate->templates_urlpath().'dhtmlx/dhtmlxwindows.css');
	$apptemplate->add_css($approuter->id,$apptemplate->templates_urlpath().'dhtmlx/skins/dhtmlxwindows_dhx_skyblue.css');
	$apptemplate->add_css($approuter->id,$apptemplate->templates_urlpath().'dhtmlx/skins/dhtmlxtoolbar_dhx_skyblue.css');
	$apptemplate->add_css($approuter->id,$apptemplate->templates_urlpath().'css/styles.css');

	$apptemplate->add_script($apptemplate->templates_urlpath().'js/jquery-1.7.min.js');
	//$apptemplate->add_script($apptemplate->templates_urlpath().'js/jquery-1.5.min.js');
	$apptemplate->add_script($apptemplate->templates_urlpath().'js/jquery.ba-resize.min.js');
	$apptemplate->add_script($apptemplate->templates_urlpath().'js/jquery.prettyLoader.js');
	$apptemplate->add_script($apptemplate->templates_urlpath().'dhtmlx/dhtmlxcommon.js');
	$apptemplate->add_script($apptemplate->templates_urlpath().'dhtmlx/dhtmlxtoolbar.js');
	$apptemplate->add_script($apptemplate->templates_urlpath().'dhtmlx/dhtmlxwindows.js');
	$apptemplate->add_script($apptemplate->templates_urlpath().'dhtmlx/dhtmlxcontainer.js');
	$apptemplate->add_script($apptemplate->templates_urlpath().'dhtmlx/dhtmlxcombo.js');
	//$apptemplate->add_script($apptemplate->templates_urlpath().'js/utils.min.js');
	$apptemplate->add_script($apptemplate->templates_urlpath().'js/utils.js');
	$apptemplate->add_script($apptemplate->templates_urlpath().'js/scripts.js');
	
	$apptemplate->header("Health Care | Aeomatrix");
	
	$apptemplate->page('topnav');
	
	$apptemplate->page('topmenu');

	$apptemplate->page('workarea');

	//$apptemplate->page();
			
	$apptemplate->footer();
}

function notfound() {
	header('Location: /');
	exit;
}

//pre($_SERVER);
//exit;

$appsession->start();

$_SESSION['timestamp'] = time();

add_action('routes','defaultroute',999);

//pre($appaccess->access);

$approuter->route();