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

if(!class_exists('APP_Template')) {

	class APP_Template {
	
		var $template = 'default';
		var $stylesheets = false;
		var $scripts = false;
		var $site_title = 'Untitled';
		var $settings = false;
		var $bottom_scripts = false;
	
		function __construct() {
			$this->init();
			$this->stylesheets = array();
			$this->scripts = array();
			$this->settings = array();
			$this->bottom_scripts = array();
		}
		
		function __destruct() {
		}
		
		function init() {
			//echo "template class";
		}
		
		function title() {
			echo apply_filters('site_title', $this->site_title);
		}
		
		function meta_description() {
			$cont = apply_filters('meta_description', false);
			
			if($cont) {
				echo $cont;
			}
		}
		
		function meta_content_type() {
			$cont = apply_filters('meta_content_type', false);
			
			if($cont) {
				echo $cont;
			}			
		}
		
		function meta_robots() {
			$cont = apply_filters('meta_robots', false);
			
			if($cont) {
				echo $cont;
			}			
		}

		function meta_author() {
			$cont = apply_filters('meta_author', false);
			
			if($cont) {
				echo $cont;
			}			
		}
		
		function stylesheets() {
			if(is_array($this->stylesheets)&&sizeof($this->stylesheets)>0) {
				$output = '';
				foreach($this->stylesheets as $style) {
					$output .= '<link rel="stylesheet" id="'.$style['name'].'-css" href="'.$style['location'].'" type="text/css" />'."\n";
				}
				$output = apply_filters('stylesheets', $output);
				if($output) {
					echo $output;
				}
			}
		}
		
		function scripts() {
			if(is_array($this->scripts)&&sizeof($this->scripts)>0) {
				$output = '';
				foreach($this->scripts as $script) {
					$output .= '<script type="text/javascript" src="'.$script.'"></script>'."\n";
				}
				$output = apply_filters('scripts', $output);
				if($output) {
					echo $output;
				}
			}
		}
		
		function settings() {
			if(is_array($this->settings)&&!empty($this->settings)) {
				$output  = "\n".'<script type="text/javascript">'."\n".'<!--//--><![CDATA[//><!--'."\n";
				$output .= "var settings = {};\n";
				$output .= 'jQuery.extend(settings,'.json_encode($this->settings).');';
				$output .= "\n".'//--><!]]>'."\n".'</script>'."\n";
				$output = apply_filters('settings', $output);
				if(!empty($output)) {
					echo $output;
				}
			}
		}

		function bottom_scripts() {
			if(is_array($this->bottom_scripts)&&sizeof($this->bottom_scripts)>0) {
				$output = '';
				foreach($this->bottom_scripts as $script) {
					$output .= '<script type="text/javascript" src="'.$script.'"></script>'."\n";
				}
				$output = apply_filters('bottom_scripts', $output);
				if($output) {
					echo $output;
				}
			}
		}

		function add_css($name=false,$location=false) {
			if($name&&$location) {
				$this->stylesheets[] = array('name'=>$name,'location'=>$location);
			}
		}

		function add_script($location=false) {
			//<script type='text/javascript' src='http://www.thefemalecelebrity.info/wp-includes/js/jquery/jquery.js?ver=1.4.2'></script>
			if($location) {
				$this->scripts[] = $location;
			}
		}

		function add_bottom_script($location=false) {
			//<script type='text/javascript' src='http://www.thefemalecelebrity.info/wp-includes/js/jquery/jquery.js?ver=1.4.2'></script>
			if($location) {
				$this->bottom_scripts[] = $location;
			}
		}
		
		function add_settings($setting=false) {
			if(is_array($setting)&&!empty($setting)) {
				foreach($setting as $k=>$v) {
					$this->settings[$k] = $v;
				}
			}
		}
		
		function flush_scripts() {
			$this->scripts = array();
		}

		function flush_css() {
			$this->stylesheets = array();
		}
			
		function header($title=false,$name='header',$vars=false, $ret=false) {
			if($title) {
				$this->site_title = $title;
			}
			
			$file = $this->templates_path().$name.'.tpl.php';
			
			if(file_exists($file)) {
				if($ret) {
					ob_start();

					require_once($file);

					$output = ob_get_contents();
					ob_end_clean();
					
					return $output;
				} else {
					require_once($file);
				}
			} else {
			
				if(BACKTRACE) {
					$dback = debug_backtrace();
					pre($dback);
				}

				die('ERROR: APP_Template::header: Missing template ('.$file.')');
			}
		}

		function page($name='page',$vars=false, $ret=false) {
			$file = $this->templates_path().$name.'.tpl.php';
			
			if(file_exists($file)) {				
				if($ret) {
					ob_start();

					require_once($file);

					$output = ob_get_contents();
					ob_end_clean();
					
					return $output;
				} else {
					require_once($file);
				}
			} else {

				if(BACKTRACE) {
					$dback = debug_backtrace();
					pre($dback);
				}

				die('ERROR: APP_Template::page: Missing template ('.$file.')');
			}
		}
		
		function footer($name='footer', $vars=false, $ret=false) {
			$file = $this->templates_path().$name.'.tpl.php';
			
			if(file_exists($file)) {
				if($ret) {
					ob_start();

					require_once($file);

					$output = ob_get_contents();
					ob_end_clean();
					
					return $output;
				} else {
					require_once($file);
				}
			} else {

				if(BACKTRACE) {
					$dback = debug_backtrace();
					pre($dback);
				}

				die('ERROR: APP_Template::footer: Missing template ('.$file.')');
			}
		}

		function load_template($name='', $vars=false, $ret=false) {
			if(!empty($name)) {
				$file = $this->templates_path().$name.'.tpl.php';
				
				if(file_exists($file)) {
					if($ret) {
						ob_start();

						require_once($file);

						$output = ob_get_contents();
						ob_end_clean();
					
						return $output;
					} else {
						require_once($file);
					}
				} else {

					if(BACKTRACE) {
						$dback = debug_backtrace();
						pre($dback);
					}

					die('ERROR: APP_Template::load_template: Missing template ('.$file.')');
				}
			} else {

				if(BACKTRACE) {
					$dback = debug_backtrace();
					pre($dback);
				}

				die('ERROR: APP_Template::load_template: Invalid template name');
			}
		}
		
		function template_exists($name='') {
			if(!empty($name)) {
				$file = $this->templates_path().$name.'.tpl.php';
				
				if(file_exists($file)) {
					return true;
				}
			}
			
			return false;
		}
		
		function templates_path() {
			return templates_path().$this->template.'/';
		}
		
		function templates_urlpath() {
			$path = '/'.str_replace(ABS_PATH,'',$this->templates_path());
			return $path;
		}
		
	}
	
	$apptemplate = new APP_Template;

	/*
	function page_header() {
		global $apptemplate;
		
		require_once(templates_path().$apptemplate->template.'/header.tpl.php');
	}
	*/
		
}

/* INCLUDES_END */

#eof ./includes/template/index.php