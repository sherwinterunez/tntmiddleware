<?php

/* INCLUDES_START */

$redis = new Redis();

if(!$redis->connect('127.0.0.1', 6379)) {
	if($debug) {
		preh('error connecting to redis:6379');
	}
	$redis = false;
}

//if($debug&&$redis) {
//	preh('connected to redis:6379');
//}

/* INCLUDES_END */
