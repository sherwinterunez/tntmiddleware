<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Utilities Module Class
*
* Date: July 1, 2014 5:57PM +0800
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

if(!class_exists('APP_Index')) {

	class APP_Index extends APP_Base {

		var $pathid = 'index';
		var $desc = 'Index';
		var $post = false;
		var $vars = false;

		var $cls_ajax = false;

		function __construct() {
			parent::__construct();
		}

		function __destruct() {
			parent::__destruct();
		}

		function modulespath() {
			return str_replace(basename(__FILE__),'',__FILE__);
		}

		function action() {
			global $approuter;

			if($approuter->id==$this->pathid) {
				remove_action('default_css', 'action_default_css');
				remove_action('default_script', 'action_default_script');
				remove_action('default_setting','action_default_setting');
				add_action('default_css', array($this,'css'));
				add_action('default_script', array($this,'script'));
				//add_action('action_default_setting', array($this,'setting'));
				add_action('action_bottom_script', array($this,'bottom_script'));

			}
		}

		/*function add_css() {
			global $apptemplate;

			//$apptemplate->add_css('styles','http://fonts.googleapis.com/css?family=Open+Sans:400,700');
			//$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'css/login.css');
		}*/


/*
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" media="all">
        <link href="css/themify-icons.css" rel="stylesheet" type="text/css" media="all" />
        <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
        <link href="css/flexslider.css" rel="stylesheet" type="text/css" media="all" />
        <link href="css/theme.css" rel="stylesheet" type="text/css" media="all" />
        <link href="css/custom.css" rel="stylesheet" type="text/css" media="all" />

	<script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/flexslider.min.js"></script>
        <script src="js/smooth-scroll.min.js"></script>
        <script src="js/parallax.js"></script>
        <script src="js/scripts.js"></script>
    </body>

*/
		function css() {
			global $apptemplate;

			$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'frontend/css/font-awesome.min.css');
			$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'frontend/css/themify-icons.css');
			$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'frontend/css/bootstrap.css');
			$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'frontend/css/flexslider.css');
			$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'frontend/css/theme.css');
			$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'frontend/css/custom.css');
			$apptemplate->add_css('styles','http://fonts.googleapis.com/css?family=Lato:300,400%7CRaleway:100,400,300,500,600,700%7COpen+Sans:400,500,600');
		}

		function script() {
			global $apptemplate;

			//$apptemplate->add_script($apptemplate->templates_urlpath().'dhtmlx/codebase/dhtmlx.pro.js');
		}

		function bottom_script() {
			global $apptemplate;

			$apptemplate->add_bottom_script($apptemplate->templates_urlpath().'frontend/js/jquery.min.js');
			$apptemplate->add_bottom_script($apptemplate->templates_urlpath().'js/jquery.numeric.js');
			$apptemplate->add_bottom_script($apptemplate->templates_urlpath().'frontend/js/bootstrap.min.js');
			$apptemplate->add_bottom_script($apptemplate->templates_urlpath().'frontend/js/flexslider.min.js');
			$apptemplate->add_bottom_script($apptemplate->templates_urlpath().'frontend/js/smooth-scroll.min.js');
			$apptemplate->add_bottom_script($apptemplate->templates_urlpath().'frontend/js/parallax.js');
			$apptemplate->add_bottom_script($apptemplate->templates_urlpath().'frontend/js/scripts.js');
		}

		/*function add_script() {
			global $apptemplate;

			if(!is_bot()) {
				$apptemplate->add_script($apptemplate->templates_urlpath().'js/scripts.js');
			} else {
				$apptemplate->add_script($apptemplate->templates_urlpath().'js/scripts2.js');
			}
		}*/

		function setting() { }

		function is_loggedin() {

		}

		function add_route() {
			global $approuter;

			//$approuter->addroute(array('^/\?(.*)$' => array('id'=>'index','param'=>'action=index', 'callback'=>array($this,'render'))));

			//$approuter->addroute(array('^\/\?(.*)$' => array('id'=>'index','param'=>'action=notfound&param=$1', 'callback'=>array($this,'notfound'))));
			//$approuter->addroute(array('^\/(.+)\/(.+)\.html$' => array('id'=>'index','param'=>'action=notfound&category=$1&q=$2', 'callback'=>array($this,'notfound'))));
			//$approuter->addroute(array('^\/(.+)\.html$' => array('id'=>'index','param'=>'action=notfound&param=$1', 'callback'=>array($this,'notfound'))));

			//$approuter->addroute(array('^/(.*)' => array('id'=>'index','param'=>'action=notfound&param=$1', 'callback'=>array($appindex,'notfound'))));

			//$approuter->addroute(array('^/$' => array('id'=>'index','param'=>'action=index', 'callback'=>array($this,'render'))));

			$approuter->addroute(array('^/$' => array('id'=>'index','param'=>'action=index', 'callback'=>array($this,'render'))));
			//$approuter->addroute(array('^/p/(.+?)\/$' => array('id'=>'index','param'=>'action=index&param=$1', 'callback'=>array($this,'render'))));
		}

		function check_url() {
			if($_SERVER['REQUEST_URI']=='/'.$this->pathid.'/') {
				header("Location: /",TRUE,301);
				die;
			}
		} // check_url()

		function redis() {
			global $redis;

			$redis = false;

			/*if(class_exists('redis')) {
				$redis = new Redis();

				if(!$redis->connect('127.0.0.1', 6379)) {
					$redis = false;
				}
			}*/
		}

		function render($vars) {
			redirect301('/app/');
		}

		function render2($vars) {
			global $apptemplate, $appform, $current_page, $redis, $redisprefix, $default_keyword;

			/*if(!empty($default_keyword)) {
				$newurl = makeurl($default_keyword);
				if(!empty($newurl['url'])) {
					redirect301($newurl['url']);
				}
			}*/

			//pre(array('$vars'=>$vars));

			$page = 'register';

			if(!empty($vars['param'])&&preg_match('/^(promo|refer)$/si', $vars['param'])) {
				$page = $vars['param'];
			} else
			if(!empty($vars['param'])) {
				redirect301('/');
			}

			$this->check_url();

			//$this->redis();

			//$recents = $redis->lRange($redisprefix.':recent', 0, -1);

			$recents = array();

			$apptemplate->header(ucfirst(strtolower($page)).' | '.getOption('$APP_NAME',APP_NAME), 'frontendheader');

			$apptemplate->page($page);

			//$apptemplate->page('topnavbar');

			//$apptemplate->page('topmenu');

			//$apptemplate->page('workarea');

			//$apptemplate->page('index',array('vars'=>$vars,'recents'=>$recents));

			$apptemplate->footer();

		} // render()

		function render_result($vars,$results,$recents) {
			global $apptemplate, $appform, $current_page;

			//$this->check_url();

			$apptemplate->header($this->desc.' | '.APP_NAME);

			$apptemplate->page('topnavbar');

			//$apptemplate->page('topmenu');

			//$apptemplate->page('workarea');

			$apptemplate->page('result',array('vars'=>$vars,'results'=>$results,'recents'=>$recents));

			//pre(array($vars,$results));

			$apptemplate->footer();

		} // render()

		function params($vars) {
			if(!empty($vars['get']['q'])) {
				$newurl = makeurl($vars['get']['q']);

				$url = '/';

				if(!empty($newurl['url'])) {
					$url = $newurl['url'];
				}

				redirect301($url);
			}

			if(!empty($vars['category'])) {
				$vars['category'] = strtolower(fixname($vars['category']));
			}

			if(!empty($vars['q'])) {
				$vars['q'] = strtolower(fixname($vars['q']));

				$newurl = makeurl($vars['q']);
				if($newurl['url']!=$_SERVER['REQUEST_URI']) {
					redirect301($newurl['url']);
				}
			}

			if(!empty($vars['category'])&&!empty($vars['q'])) {
			} else {
				if(!empty($vars['param'])) {
					$q = strtolower(fixname($vars['param']));

					$newurl = makeurl($q);

					$url = '/';

					if(!empty($newurl['url'])) {
						$url = $newurl['url'];
					}

					redirect301($url);
				}
			}

			return $vars;
		} // params()

		function notfound($vars) {
			global $redis, $redisprefix, $cachedate, $default_keyword;

			$this->redis();

			$vars = $this->params($vars);

			if(!empty($vars)&&is_array($vars)) {
				//pre($vars);

				if(($results = retrieveImages($vars))&&is_array($results)&&!empty($results['images'])) {
					//pre($results);

					if(!empty($results['param']['datetimestr'])) {
						$cachedate = $results['param']['datetimestr'];
					}

					if($redis) {
						if(!empty($vars['q'])&&strlen($vars['q'])<60) {
							$redis->lRem($redisprefix.':recent', $vars['q'], 0);
							$redis->lPush($redisprefix.':recent', $vars['q']);
							$redis->lTrim($redisprefix.':recent', 0, 9);
						}

						$recents = $redis->lRange($redisprefix.':recent', 0, -1);

						//pre($recents);
					}

					$this->render_result($vars,$results,$recents);

				} else {

					if(!empty($default_keyword)) {
						$newurl = makeurl($default_keyword);
						if(!empty($newurl['url'])) {
							redirect301($newurl['url']);
						}
					}

				}
			}

		} // notfound()

	} // class APP_Index

	$appindex = new APP_Index;
}

# eof modules/index/index.php
