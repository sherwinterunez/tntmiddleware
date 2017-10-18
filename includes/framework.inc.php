<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Framework Class
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

if(!class_exists('APP_SHERWIN_TERUNEZ_FRAMEWORK')) {

	class APP_SHERWIN_TERUNEZ_FRAMEWORK {
		
		function __construct() {
		}
		
		function __destruct() {
		}

		function pre($data) {
			echo "<pre>";
			print_r($data);
			echo "</pre>";
		}

		function prebuf($data) {
			ob_start();
			pre($data);
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}

	}
}

/* INCLUDES_END */
