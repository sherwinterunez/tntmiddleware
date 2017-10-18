<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Defaults include file
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

function default_meta_content_type($cont) {
	$cont = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'."\n";
	return $cont;
}
add_filter('meta_content_type', 'default_meta_content_type');

function default_meta_robots($cont) {
	$cont = '<meta name="robots" content="index,follow" />'."\n";
	return $cont;
}
add_filter('meta_robots', 'default_meta_robots');

function default_meta_author($cont) {
	$cont = '<meta name="author" content="Sherwin R. Terunez / sherwinterunez@yahoo.com" />'."\n";
	return $cont;
}
add_filter('meta_author', 'default_meta_author');

function action_meta_content_type() {
	global $apptemplate;
	
	$apptemplate->meta_content_type();
}
add_action('action_meta_content_type', 'action_meta_content_type');

function action_meta_description() {
	global $apptemplate;
	
	$apptemplate->meta_description();
}
add_action('action_meta_description', 'action_meta_description');

function action_meta_robots() {
	global $apptemplate;
	
	$apptemplate->meta_robots();
}
add_action('action_meta_robots', 'action_meta_robots');

function action_meta_author() {
	global $apptemplate;
	
	$apptemplate->meta_author();
}
add_action('action_meta_author', 'action_meta_author');

function action_stylesheets() {
	global $apptemplate;
	
	$apptemplate->stylesheets();
}
add_action('action_stylesheets', 'action_stylesheets');

function action_scripts() {
	global $apptemplate;
	
	$apptemplate->scripts();
}
add_action('action_scripts', 'action_scripts');

function action_bottom_script() {
	global $apptemplate;
	
	$apptemplate->bottom_scripts();
}
add_action('action_bottom_script', 'action_bottom_script',99999);

function action_settings() {
	global $apptemplate;
	
	$apptemplate->settings();
}
add_action('action_settings', 'action_settings');

function action_timer_footer() {
	$tstop = timer_stop();
	
	echo "<!-- Page generated in ".$tstop." sec(s) -->";
}
add_action('action_footer_bottom', 'action_timer_footer',99999);

function action_default_css() {
	global $apptemplate;
	
	$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'css/styles.css');
	
	do_action('action_default_css');
}
add_action('default_css', 'action_default_css');

function action_default_script() {
	global $apptemplate;
	
	$apptemplate->add_script($apptemplate->templates_urlpath().'js/scripts.js');
	
	do_action('action_default_script');
}
add_action('default_script', 'action_default_script');

function action_default_setting() {
	global $apptemplate, $approuter;

	$apptemplate->add_settings(array('site'=>'http://'.host()));	
	$apptemplate->add_settings(array('template_path'=>$apptemplate->templates_urlpath()));
	$apptemplate->add_settings(array('router_id'=>$approuter->id));	
	$apptemplate->add_settings(array('windows'=>false));
	$apptemplate->add_settings(array('execute'=>false));
	
	do_action('action_default_setting');
}
add_action('default_setting', 'action_default_setting');

function action_default_init() {
	do_action('default_css');
	do_action('default_script');
	do_action('default_setting');
	do_action('action_default_init');
}
add_action('init', 'action_default_init');

/* INCLUDES_END */

#eof ./includes/defaults/index.php