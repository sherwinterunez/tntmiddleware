<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Misc functions include file
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

function is_bingbot() {
	global $bot_mode;
	
	if(isset($_GET['bot'])) return true;
	if(!empty($bot_mode)) return true;

	$iplong = floatval(sprintf("%u\n", ip2long($_SERVER['REMOTE_ADDR'])));

	if($iplong>=1152679936&&$iplong<=1152712703) { // 68.180.128.0 - 68.180.255.255 / 1152679936 - 1152712703 / yahoo slurp
		return true;
	} else
	if($iplong>=1093926912&&$iplong<=1094189055) { // 65.52.0.0 - 65.55.255.255 / 1093926912 - 1094189055
		return true;
	} else
	if($iplong>=3475898368&&$iplong<=3475963903) { // 207.46.0.0 - 207.46.255.255 / 3475898368 - 3475963903
		return true;
	} else
	if($iplong>=2637561856&&$iplong<=2638020607) { // 157.54.0.0 - 157.60.255.255 / 2637561856 - 2638020607
		return true;
	} else
	if($iplong>=2214401280&&$iplong<=2214408191) { // 131.253.21.0 - 131.253.47.255 / 2214401280 - 2214408191
		return true;
	} else
	if($iplong>=3340636160&&$iplong<=3340640255) { // 199.30.16.0 - 199.30.31.255 / 3340636160 - 3340640255
		return true;
	}

	return false;
}

function is_googlebot() {
	global $bot_mode;

	if(isset($_GET['bot'])) return true;
	if(!empty($bot_mode)) return true;
	
	$iplong = floatval(sprintf("%u\n", ip2long($_SERVER['REMOTE_ADDR'])));

	if($iplong>=1123631104&&$iplong<=1123639295) {
		return true;
	}
	
	if($iplong>=3419414528&&$iplong<=3419422719) {
		return true;
	}

	return false;
}

function is_bot() {
	global $bot_mode;

	if(isset($_GET['bot'])) return true;
	if(!empty($bot_mode)) return true;
	
	if(is_bingbot()) {
		return true;
	}
	
	if(is_googlebot()) {
		return true;
	}
	
	return false;
}

/* INCLUDES_END */
