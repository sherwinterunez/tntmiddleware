<?php
/*
*  Author: jeE
* Contact: edang.jeorlie@gmail.com
* Description:
* Session class include file
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

/* INCLUDES_START */

if(!class_exists('APP_Download')) {

	class APP_Download {
				function dofiledownloadpath($file) {
				header('Pragma: public'); 	// required
				header('Expires: 0');		// no cache
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Cache-Control: private',false);				
				header('Content-Disposition: attachment; filename="'.basename($file).'"');
				header('Content-Transfer-Encoding: binary');
				header('Connection: close');
				readfile($file);
				}
	} // class	
	$appdownload = new APP_Download;
	
}

/* INCLUDES_END */
