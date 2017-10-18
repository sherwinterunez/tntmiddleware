<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Router Class
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

$filters = array();

function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {	
	global $filters;

	$idx = _filter_build_unique_id($tag, $function_to_add, $priority);
	$filters[$tag][$priority][$idx] = array('function' => $function_to_add, 'accepted_args' => $accepted_args);
	return true;
}

function apply_filters($tag, $value) {
	global $filters;
	
	if(!isset($filters[$tag])) return $value;
	
	$args = func_get_args();
	
	ksort($filters[$tag]);
	
	foreach($filters[$tag] as $filter) {
		foreach($filter as $the_) {
			//pre(array('$tag'=>$tag,$the_));
			if(!is_null($the_['function'])&&is_callable($the_['function'])) {
				if($the_['accepted_args']>0) {
					$args[1] = $value;
					$value = call_user_func_array($the_['function'], array_slice($args, 1, (int) $the_['accepted_args']));
				} else {
					$value = call_user_func($the_['function']);
				}
			}
		}
	}
	
	return $value;
}

function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 0) {
	return add_filter($tag, $function_to_add, $priority, $accepted_args);
}

function do_action($tag) {
	apply_filters($tag, false);
}

function remove_action($tag, $function_to_remove, $priority = 10, $accepted_args = 0) {
	global $filters;
	
	if(!isset($filters[$tag][$priority])) return false;
	
	foreach($filters[$tag][$priority] as $idx=>$the_) {
		if($the_['function']==$function_to_remove) {
			unset($filters[$tag][$priority][$idx]);
		}
	}
}

function _filter_build_unique_id($tag, $function_to_add, $priority) {
	global $filters, $filterctr;
	
	if(empty($filterctr)) $filterctr = 0;
	
	do {
		$idx = @sha1($tag.$function_to_add.$priority.$filterctr);
		
		if(!isset($filters[$tag][$priority][$idx])) break;
		
		$filterctr++;
	} while(true);
	
	return $idx;
}

/* INCLUDES_END */


# eof includes/hooks/index.php