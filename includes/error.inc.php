<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Error include file
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

if(!class_exists('APP_Error')) {

	//require_once('errors.inc.php');

	class APP_Error {
	
		function __construct() {
		}
		
		function __destruct() {
		}
		
		function pre($data) {
			echo "<pre>";
			print_r($data);
			echo "</pre>";
		}
		
		function show_error($msg="\n\n<strong>an error has occured</strong>\n\n",$show_backtrace=false,$die=false) {
			echo $msg;
			if($show_backtrace) $this->pre(debug_backtrace());
			if($die) die;
		}
		
		function log_error($msg="\n\n<strong>an error has occured</strong>\n\n",$show_backtrace=false,$die=false) {
		}
		
		function error_message($code=254) {
			global $error_codes;
			
			if(!isset($error_codes[$code])) {
				return $error_codes[254];
			}
			
			return $error_codes[$code];
		}
		
	}
	
	$apperror = new APP_Error;
	
}

/* INCLUDES_END */


#eof ./includes/error/index.php