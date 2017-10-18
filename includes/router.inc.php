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

if(!class_exists('APP_Router')) {

	class APP_Router extends APP_SHERWIN_TERUNEZ_FRAMEWORK {
	
		var $parsed_url = false;
		
		var $id = false;
		
		var $routes = false;

		var $protocol = 'http';
	
		function __construct() {
			$this->routes = array();
			$this->init();
		}
		
		function __destruct() {
		}
				
		function init() {

			//pre($_SERVER); die;
		
			$baseurl = $this->protocol.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			
			$this->parsed_url = parse_url($baseurl);


			// .html
			// lmth.

			/*if(trim($this->parsed_url['path'])=='/') {
			} else
			if(strlen($this->parsed_url['path'])>1) {
				$st = strrev($this->parsed_url['path']);
				if($st[0]!='/') {

					if(preg_match('#\.(html|php)$#si',$this->parsed_url['path'])) {
						//pre("Hello!");
						//pre($this->parsed_url);
					} else {
						//pre($st); die;

						$pathinfo = $this->parsed_url['path'] . '/';
						if(isset($this->parsed_url['query'])) {
							$pathinfo .= '?'.$this->parsed_url['query'];
						}
						header("Location: $pathinfo",TRUE,301);
						die;
					}
				}
			}*/
		}
		
		function route() {
			//$debug = true;

			do_action('routes');
			
			if(!empty($debug)) $this->pre(array('$this->routes'=>$this->routes));
			
			if(is_array($this->routes)&&sizeof($this->routes)>0) {} else {
				die('invalid routes');
			}
			
			if(!empty($debug))$this->pre(array('$this->parsed_url'=>$this->parsed_url));

			if(!empty($_SERVER['REQUEST_URI'])&&preg_match('#\?(.+)#si',$_SERVER['REQUEST_URI'],$m)) {
				parse_str($m[1],$_GET);
				//$_SERVER['REQUEST_URI'] = str_replace('.html?'.$m[1],'.html',$_SERVER['REQUEST_URI']);
			}
			
			foreach($this->routes as $pattern=>$route) {
				if(!empty($debug)) $this->pre(array('$pattern'=>$pattern,'$route'=>$route));

				$pattern = '#'.$pattern.'#si';				

				if(preg_match($pattern,$_SERVER['REQUEST_URI'],$match)) {
				
					$str = preg_replace($pattern, $route['param'], $_SERVER['REQUEST_URI']);
					parse_str($str,$var);

					if(!empty($debug)) $this->pre(array('$pattern'=>$pattern,'$route'=>$route,'$match'=>$match,'$var'=>$var,'$str'=>$str));
					
					if(!empty($_GET)) {
						$var['get'] = $_GET;
					}

					if(!empty($_POST)) {
						$var['post'] = $_POST;
					}
					
					if(!isset($route['callback'])) {
						die('routes: invalid callback');
					}
					
					if(!is_callable($route['callback'])) {
						die('routes: invalid callback ('.$route['callback'].')');
					}
					
					$this->id = $route['id'];

					do_action('init');
					
					do_action('router');
					
					call_user_func($route['callback'], $var);
					
					break;
				}
			}
		} // routes
		
		function addroute($route=false) {
			if(is_array($route)&&!empty($route)) {
				foreach($route as $pattern=>$rt) {
					if(is_string($pattern)&&is_array($rt)&&!empty($rt['id'])&&!empty($rt['param'])&&!empty($rt['callback'])) {
						$this->routes[$pattern] = $rt;
					}
				}
			}
		} // addroute
				
	}
	
	$approuter = new APP_Router;
}

/* INCLUDES_END */

# eof includes/router/index.php