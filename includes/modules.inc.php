<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Template include file
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

if(!class_exists('APP_Modules')) {


	class APP_Modules {

		var $amodules = array();

		function __construct() {
		}
		
		function __destruct() {
		}

		function register($modulename) {

		}

	}

	$appmodules = new APP_Modules;
}

/* INCLUDES_END */


#eof