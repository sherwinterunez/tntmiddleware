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

date_default_timezone_set('Asia/Manila');

setlocale(LC_ALL,'en_US.UTF-8');

require_once(ABS_PATH.'includes/index.php');
//require_once(ABS_PATH.'includes.min/includes.inc.php');
//require_once(ABS_PATH.'includes.min/includes.encoded.php');
require_once(ABS_PATH.'modules/index.php');
require_once(ABS_PATH.'templates/default/index.php');

/*function check_login() {
	global $applogin;

	//pre($_SERVER);


	//if(!preg_match("#\/login(.+?)#si",$_SERVER['REQUEST_URI'])&&!$applogin->is_loggedin()) {

	if(preg_match("#\/app(.+?)#si",$_SERVER['REQUEST_URI'])&&!$applogin->is_loggedin()) {

		//if(!empty($_POST)) {
		//	json_return_error(255);
		//}

		redirect301('/'.$applogin->pathid.'/');
	}
}*/

function index() {
	echo "Hello World!";
}

function defaultroute() {
	global $approuter, $appindex;

	//$approuter->addroute(array('^/logout/$' => array('id'=>'logout','param'=>'action=logout', 'callback'=>'logout')));
	//$approuter->addroute(array('^/\?(.*)$' => array('id'=>'index','param'=>'action=index', 'callback'=>'index')));
	//$approuter->addroute(array('^/$' => array('id'=>'index','param'=>'action=index', 'callback'=>'index')));
	//$approuter->addroute(array('^/(.*)' => array('id'=>'defaultpage','param'=>'action=error&param=$1', 'callback'=>'notfound')));

	//$approuter->addroute(array('^/(.*)' => array('id'=>'index','param'=>'action=notfound&param=$1', 'callback'=>array($appindex,'notfound'))));
}

function notfound() {
	//header('Location: /');
	die('Not Found!');
}

function logout() {
	die("Logout!");
}

//add_action('router','check_login');

$appsession->start();

//$_SESSION['timestamp'] = time();

$_SESSION['timestamp'] = getTimeFromServer();

$_SESSION['datestamp'] = date('l jS \of F Y h:i:s A',$_SESSION['timestamp']);

add_action('routes','defaultroute',999);

$approuter->route();
