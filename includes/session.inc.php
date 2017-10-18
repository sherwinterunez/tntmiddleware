<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Session class include file
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

if(!class_exists('APP_Session')) {

	class APP_Session {
	
		function __construct() {
			$this->init();
		}
		
		function __destruct() {
		}
		
		function init() {
			//echo 'session class';
			$this->start();
		}
		
		function start() {
			session_start();
		}
		
		function destroy() {
			session_destroy();
		}
	
	} // class
	
	$appsession = new APP_Session;
	
}

/* INCLUDES_END */
