<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Language Class
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

if(!class_exists('APP_Language')) {

	class APP_Language {
	
		function __construct() {
		}
		
		function __destruct() {
		}
		
		function translate($data) {
			return $data;
		}
	}
	
	$applanguage = new APP_Language;
}

/* INCLUDES_END */


# eof includes/language/index.php