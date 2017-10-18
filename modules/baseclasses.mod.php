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
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

///////

if(!class_exists('APP_Base_Ajax')) {

	class APP_Base_Ajax {

		var $pathid = false;
		var $vars = false;
		var $post = false;
		var $modpath = false;

		function modulespath() {
			return str_replace(basename(__FILE__),'',__FILE__);
		}

		function _form($routerid=false,$formid=false) {
			global $forms, $apptemplate;

			$templatefile = $this->templatefile($routerid,$formid);

			//pre(array($routerid,$formid,$templatefile));

			if(!empty($routerid)&&!empty($formid)) {
				if(!empty($forms[$routerid][$formid])) {
					return $forms[$routerid][$formid];
				} else
				if(method_exists($this,'_form_'.$formid)) {
					//pre(array($routerid,$formid,$templatefile));
					return call_user_func(array($this, '_form_'.$formid),$routerid,$formid);
				} else
				if(file_exists($templatefile)){
					return $this->_form_load_template($templatefile);
				}
			}

			return false;
		}

		function _toolbar($routerid=false,$toolbarid=false) {
			global $toolbars, $applogin;

			$tpath = $this->modulespath().$routerid.'.toolbars.mod.php';

			require_once($tpath);

			if(!empty($routerid)&&!empty($toolbarid)) {
				if(!empty($toolbars[$routerid][$toolbarid])) {
					return $toolbars[$routerid][$toolbarid];
				}
			}

			return false;
		}

		function _layout($routerid=false,$layoutid=false) {
			global $layouts, $applogin;

			$lpath = $this->modulespath().$routerid.'.layouts.mod.php';

			require_once($lpath);

			if(!empty($routerid)&&!empty($layoutid)) {
				if(!empty($layouts[$routerid][$layoutid])) {
					return $layouts[$routerid][$layoutid];
				}
			}

			return false;
		}

		function _xml($routerid=false,$xmlid=false) {
			//if($xmlid=='messaging') {
			//} else {
			//	pre(array('_xml()'=>array('$routerid'=>$routerid,'$xmlid'=>$xmlid)));
			//}

			if(!empty($routerid)&&!empty($xmlid)) {

				//if($xmlid=='messaging') {
				//} else {
				//	pre(array('_xml()'=>array('$routerid'=>$routerid,'$xmlid'=>$xmlid,'method'=>'_xml_'.$xmlid)));
				//}

				if(method_exists($this,'_xml_'.$xmlid)) {
					//if($xmlid=='messaging') {
					//} else {
					//	pre(array('_xml()'=>array('$routerid'=>$routerid,'$xmlid'=>$xmlid)));
					//}
					return call_user_func(array($this, '_xml_'.$xmlid),$routerid,$xmlid);
				}
			}

			return false;
		}

		function _form_load_template($templatefile=false,$params=false) {
			if(!empty($templatefile)&&file_exists($templatefile)){
				$post = $this->post;
				$vars = $this->vars;

				if(!empty($params)) {
					$vars['params'] = $params;
				}

				ob_start();

				require_once($templatefile);

				$output = ob_get_contents();
				ob_end_clean();

				//$output = preg_replace('!/\*.*?\*/!s', '', $output);
				//$output = preg_replace('/\n\s*\n/', "\n", $output);
				//$output = preg_replace('#^\s*//.+$#m', "", $output);

				//$output = str_replace("\t", '', $output);
				//$output = str_replace("\n\n", "\n", $output);
				//$output = str_replace("\n", "", $output);


				return $output;
			}
			return false;
		}

		function _form_validate() {
			if(empty($this->vars['post']['formval'])) {
				//json_encode_return(array('error_code'=>123,'error_message'=>'Invalid form.'));
				json_error_return(253); // 253 => 'Invalid form.',
			}
			if(!empty($_SESSION['FORMS'][$this->vars['post']['formval']])) {
				/*if(time()>($_SESSION['FORMS'][$this->vars['post']['formval']]['since']+1800)) {
					unset($_SESSION['FORMS'][$this->vars['post']['formval']]);
					//json_encode_return(array('error_code'=>123,'error_message'=>'Expired form.'));
					json_error_return(254); // 254 => 'Expired form.',
				}*/
			} else {
				//json_encode_return(array('error_code'=>123,'error_message'=>'Invalid form.'));
				json_error_return(253); // 253 => 'Invalid form.',
			}

			return true;
		}

		function templatefile($routerid=false,$formid=false) {
			if(!empty($routerid)&&!empty($formid)) {
				return TEMPLATE_PATH . "$routerid/$formid.tpl.php";
			}
			return false;
		}

	}

}

///////

if(!class_exists('APP_Base')) {

	class APP_Base {

		var $pathid = false;
		var $post = false;
		var $vars = false;

		var $cls_ajax = false;

		function __construct() {
			$this->init();
		}

		function __destruct() {
		}

		function init() {
			$this->rules();

			add_action('init',array($this,'action'),9);
			add_action('routes',array($this,'route'));
		}

		function route() {
			global $approuter;

			$approuter->addroute(array('^/'.$this->pathid.'/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'render'))));
			$approuter->addroute(array('^/'.$this->pathid.'/\?(.*)$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'render'))));
			$approuter->addroute(array('^/'.$this->pathid.'/session/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'session'))));
			$approuter->addroute(array('^/'.$this->pathid.'/js/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'dojs'))));
			$approuter->addroute(array('^/'.$this->pathid.'/js/\?(.*)$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'dojs'))));
			$approuter->addroute(array('^/'.$this->pathid.'/json/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'postjson'))));
			//$approuter->addroute(array('^/'.$this->pathid.'/api/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid.'&params=', 'callback'=>array($this,'doapi'))));
			$approuter->addroute(array('^/'.$this->pathid.'/api/(.+?)\/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid.'&params=$1', 'callback'=>array($this,'doapi2'))));
			$approuter->addroute(array('^/'.$this->pathid.'/api/(.+?)\/\?(.+?)$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid.'&params=$1&params2=$2', 'callback'=>array($this,'doapi2'))));
			$approuter->addroute(array('^/'.$this->pathid.'/export/(.+?)$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid.'&params=$1', 'callback'=>array($this,'export'))));
			$approuter->addroute(array('^/'.$this->pathid.'/print/(.+?)$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid.'&params=$1', 'callback'=>array($this,'doprint'))));
			$approuter->addroute(array('^/'.$this->pathid.'/setsmsstatus/(.+?)$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid.'&params=$1', 'callback'=>array($this,'dosetsmsstatus'))));
			$approuter->addroute(array('^/'.$this->pathid.'/getstatus/(.+?)$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid.'&params=$1', 'callback'=>array($this,'dogetstatus'))));
			$approuter->addroute(array('^/'.$this->pathid.'/getinbox/(.+?)$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid.'&params=$1', 'callback'=>array($this,'dogetinbox'))));
			$approuter->addroute(array('^/'.$this->pathid.'/getoutbox/(.+?)$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid.'&params=$1', 'callback'=>array($this,'dogetoutbox'))));
			$approuter->addroute(array('^/'.$this->pathid.'/(.+?)\/(.+?)\/(.+?)$' => array('id'=>$this->pathid,'param'=>'routerid='.$this->pathid.'&module=$1&action=$2&params=$3', 'callback'=>array($this,'doapi'))));

			//$approuter->addroute(array('^/'.$this->pathid.'/rules/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'show_rules'))));
			//$approuter->addroute(array('^/'.$this->pathid.'/js/(.+)/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid.'&path=$1', 'callback'=>array($this,'dojs'))));
			//$approuter->addroute(array('^/'.$this->pathid.'/json/(.+)/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid.'&path=$1', 'callback'=>array($this,'dojson'))));
			//$approuter->addroute(array('^/'.$this->pathid.'/json/(.+)/\?(.*)$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid.'&path=$1', 'callback'=>array($this,'dojson'))));
			//$approuter->addroute(array('^/'.$this->pathid.'/xml/(.+)/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid.'&path=$1', 'callback'=>array($this,'doxml'))));

			$this->add_route();
		}

		function add_route() {
			global $approuter;

		}

		function action() {
			global $approuter;

			if($approuter->id==$this->pathid) {
				remove_action('default_css', 'action_default_css');
				remove_action('default_script', 'action_default_script');
				add_action('default_css', array($this,'css'));
				add_action('default_script', array($this,'script'));
				add_action('action_default_setting', array($this,'setting'));
				add_action('action_bottom_script', array($this,'bottom_script'));

			}
		}

		function css() {
			global $apptemplate;

			//$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'dhtmlx/skins/skyblue/dhtmlx.css');
			$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'dhtmlx/skins/web/dhtmlx.css');
			$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'css/styles.min.css');
			//$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'dhtmlx/skins/skyblue/dhtmlx.css');
			//$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'css/test.css');
			//$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'css/bootstrap.compiled.css');
			//$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'css/font-awesome.min.css');

			$this->add_css();
		}

		function add_css() {

		}

		function script() {
			global $apptemplate;

			$apptemplate->add_script($apptemplate->templates_urlpath().'dhtmlx/codebase/dhtmlx.pro.js');
			//$apptemplate->add_script($apptemplate->templates_urlpath().'dhtmlx/codebase/dhtmlx.pro.js');
			//$apptemplate->add_script($apptemplate->templates_urlpath().'dhtmlx/codebase/dhtmlx.std.js');
			$apptemplate->add_script($apptemplate->templates_urlpath().'js/jquery-1.11.3.min.js');
			$apptemplate->add_script($apptemplate->templates_urlpath().'js/jquery-ui.min.js');
			$apptemplate->add_script($apptemplate->templates_urlpath().'js/jquery.form.js');
			$apptemplate->add_script($apptemplate->templates_urlpath().'js/jquery.numeric.js');
			$apptemplate->add_script($apptemplate->templates_urlpath().'js/jquery.scrollTo.min.js');
			$apptemplate->add_script($apptemplate->templates_urlpath().'js/jquery.inputmask.js');
			$apptemplate->add_script($apptemplate->templates_urlpath().'js/php.js');
			$apptemplate->add_script($apptemplate->templates_urlpath().'js/utils.js');

			//$apptemplate->add_script($apptemplate->templates_urlpath().'js/utils.js?t='.time());

			//$apptemplate->add_script($apptemplate->templates_urlpath().'js/jquery-1.11.1.min.js');
			//$apptemplate->add_script($apptemplate->templates_urlpath().'js/bootstrap.min.js');

			$this->add_script();
		}

		function bottom_script() {
			global $apptemplate;

			//$apptemplate->add_bottom_script($apptemplate->templates_urlpath().'dhtmlx/codebase/dhtmlx.pro.js');
		}

		function add_script() {

		}

		function setting() {
			global $apptemplate, $applogin;

			$apptemplate->add_settings(array('template_assets'=>$apptemplate->templates_urlpath().'assets/'));
			$apptemplate->add_settings(array('id'=>$_SESSION['id']));

			$apptemplate->add_settings(array('debug'=>true));

			if($applogin->isSystemAdministrator()) {
				$apptemplate->add_settings(array('isSystemAdministrator'=>true));
			} else {
				$apptemplate->add_settings(array('isSystemAdministrator'=>false));
			}
		}

		function check_url() {
			if($_SERVER['REQUEST_URI']!='/'.$this->pathid.'/') {
				header("Location: /".$this->pathid."/",TRUE,301);
				die;
			}
		}

		function rules() {
			global $appaccess;

			$this->add_rules();

			//$appaccess->rules($this->pathid,'Create and setup system users');
			//$appaccess->showrules();
		}

		function add_rules() {

		}

		function show_rules() {
			global $appaccess;

			$appaccess->showrules();
		}

		/*function api($vars) {
			//global $appaccess;

			//if(!$this->ajax_valid_params($vars)) {
			//	json_return_error(1);
			//}

			$this->vars = $vars;
			$this->post = $vars['post'];

			$modpath = $this->modulespath();
			$parentid = $this->post['parentid'];
			$id = $this->post['id'];

			if(empty($this->post['routerid'])) {
				json_return_error(32,array('vars'=>$vars));
			}

			$routerid = $this->post['routerid'];

			//if(!$appaccess->isAllowed($routerid,$this->vars)) {
			//	json_return_error(32,array('vars'=>$vars));
			//}

			$classfile = $modpath."$parentid.$id.mod.inc.php";

			if(!file_exists($classfile)) {
				//json_return_error(8);
				$this->cls_ajax = new APP_Base_Ajax;
			} else {
				require_once($classfile);
				$this->cls_ajax = new APP_Ajax;
			}

			$this->cls_ajax->vars = $this->vars;
			$this->cls_ajax->post = $this->post;
			$this->cls_ajax->pathid = $this->pathid;
			$this->cls_ajax->modpath = $modpath;

			if(method_exists($this->cls_ajax,$this->post['action'])) {
				call_user_func(array($this->cls_ajax, $this->post['action']));
				return;
			}

			json_return_error(4,array('vars'=>$vars));

		} // ajax*/

		function dosetsmsstatus($vars) {
			global $approuter;
		}

		function dogetstatus($vars) {
			global $approuter;
		}

		function dogetinbox($vars) {
			global $approuter;
		}

		function dogetoutbox($vars) {
			global $approuter;
		}

		function dojs($vars) {
			header('Content-type: application/javascript');

			//echo '/*'."\n";
			//echo '*'."\n";
			//echo '* Author: Sherwin R. Terunez'."\n";
			//echo '* Contact: sherwinterunez@yahoo.com'."\n";
			//echo '*'."\n";
			//echo '* Description:'."\n";
			//echo '*'."\n";
			//echo '* Javascript Utilities'."\n";
			//echo '*'."\n";
			//echo '* Created: November 12, 2015'."\n";
			//echo '*'."\n";
			//echo '*/'."\n\n";

			$this->js($vars);
			die;
		}

		function js($vars) { }

		function doxml($vars) {
			header('Content-type: text/xml');

			$this->xml($vars);
			die;
		}

		function xml($vars) { }

		function dojson($vars) {
			global $toolbars;

			header_json();

			if(!empty($vars['path'])) {
				$paths = explode('/',$vars['path']);
			}

			if(!empty($paths)&&$paths[0]=='toolbar'&&!empty($toolbars[$paths[1]][$paths[2]])) {
				die(json_encode($toolbars[$paths[1]][$paths[2]],JSON_OBJECT_AS_ARRAY));
			} else {
				$this->json($vars);
			}

			die;
		}

		function doapi2($vars=false,$retflag=false) {
			//pre($vars); die;
			if(!empty($vars['params'])) {
				$vars['post'] = json_decode(gzuncompress(base64_decode($vars['params'])),true);
				if($vars['post']) {
					//pre($vars); die;
					$this->postjson($vars,$retflag);
				}
			}
			//pre($vars);
		}

		function doapi($vars=false,$retflag=false) {

			pre($vars);

			if(!empty($vars)) {
				$this->vars = $vars;
			}

			if(!empty($vars['post'])) {
				$this->post = $vars['post'];
			}

		} // postapi

		function postjson($vars=false,$retflag=false) {
			global $applogin;

			$bypass = false;

			if(!empty($vars['post']['method'])&&preg_match('/^(messagingregister|messagingclaimpromo|messagingreferral)$/si',$vars['post']['method'])) {
				$bypass = true;
			}

			if(!$bypass&&!$applogin->is_loggedin()) {
				json_error_return(255); // 255 => 'Session has expired.'
			}

			//pre(array('$this->vars'=>$this->vars,'$vars'=>$vars));

			if(!empty($vars)&&!empty($vars['post'])) {
				$this->vars = $vars;
				$this->post = $vars['post'];
			}

			if(empty($this->post['routerid'])) {
				//json_encode_return(array('error_code'=>123,'error_message'=>'Invalid Router ID.'));
				json_error_return(252); // 252 => 'Invalid Router ID.',
			}

			if(empty($this->post['module'])) {
				//json_encode_return(array('error_code'=>123,'error_message'=>'Invalid Module ID.'));
				json_error_return(251); // 251 => 'Invalid Module ID.',
			}

			if(empty($this->post['action'])) {
				//json_encode_return(array('error_code'=>123,'error_message'=>'Invalid Action ID.'));
				json_error_return(250); // 250 => 'Invalid Action ID.',
			}

			$classname = 'APP_'.$this->post['routerid'].'_'.$this->post['module'];
			$classfile = $this->modulespath().$this->post['routerid'].'.'.$this->post['module'].'.mod.php';

			//pre(array('$this->vars'=>$this->vars,'$vars'=>$vars,'$classname'=>$classname,'$classfile'=>$classfile));

			$bypass = true;

			if(class_exists($classname)) {
				$this->cls_ajax = new $classname();
				$this->cls_ajax->vars = $this->vars;
				$this->cls_ajax->post = $this->post;

				//pre(array('$this->vars'=>$this->vars,'$vars'=>$vars,'$classname'=>$classname,'$classfile'=>$classfile));

				if(method_exists($this->cls_ajax,'router')) {
					$this->cls_ajax->router();
				} else {
					$bypass = false;
				}
			} else {
				$bypass = false;
			}

			if(!$bypass&&file_exists($classfile)) {

				//pre(array('$this->vars'=>$this->vars,'$vars'=>$vars,'$classname'=>$classname,'$classfile'=>$classfile));

				require_once($classfile);

				if(class_exists($classname)) {
					$this->cls_ajax = new $classname();
					$this->cls_ajax->vars = $this->vars;
					$this->cls_ajax->post = $this->post;

					if(method_exists($this->cls_ajax,'router')) {
						return $this->cls_ajax->router($retflag);
					} else {
						$bypass = false;
					}
				} else {
					$bypass = false;
				}
			} else {
				$bypass = false;
			}

			if(!$bypass&&method_exists($this,'router')) {
				$this->router();
			}

		} // postjson($vars=false,$retflag=false)

		function export($vars) {
			//$file = ABS_PATH . 'lipsum.txt';

			//pre(array('$vars'=>$vars));

			//if(!empty($vars['params'])) {
			//	$_SESSION['EXPORT'][$vars['params']] = time() . ': the quick brown fox jump over the lazy dog';
			//}

			if(!empty($vars['params'])&&!empty($_SESSION['EXPORT'][$vars['params']])) {
				$file = 'export-' . date('Y-m-d') . '.tsv';

			    header('Content-Description: File Transfer');
			    header('Content-Type: application/octet-stream');
			    header('Content-Disposition: attachment; filename="'.strtoupper($file).'"');
			    header('Expires: 0');
			    header('Cache-Control: must-revalidate');
			    header('Pragma: public');
			   // header('Content-Length: ' . filesize($_SESSION['EXPORT'][$vars['params']]));
			    //print_r($_SESSION['EXPORT'][$vars['params']]);

			    echo readFromFile($_SESSION['EXPORT'][$vars['params']]);

			    //print_r(readFromFile($_SESSION['EXPORT'][$vars['params']]));
			    //print_r($_SESSION);

			    exit;

			}

			/*if (file_exists($file)) {
			    header('Content-Description: File Transfer');
			    header('Content-Type: application/octet-stream');
			    header('Content-Disposition: attachment; filename="'.basename($file).'"');
			    header('Expires: 0');
			    header('Cache-Control: must-revalidate');
			    header('Pragma: public');
			    header('Content-Length: ' . filesize($file));
			    readfile($file);
			    exit;
			}*/
		}

		function doprint($vars) {
			//pre(array('$vars'=>$vars));

			global $apptemplate, $appform, $current_page;

			//$this->check_url();

			//pre(array('$this->scripts'=>$apptemplate->scripts));

			foreach($apptemplate->scripts as $k=>$v) {
				if($v=='/app/js/') {
					unset($apptemplate->scripts[$k]);
				}
			}

			// header($title=false,$name='header',$vars=false, $ret=false) {

			$header = $apptemplate->header($this->desc.' | '.getOption('$APP_NAME',APP_NAME),'appheader');

			//$header = $apptemplate->header($this->desc.' | '.APP_NAME, 'appheader', false, true);

			//echo $header;

			//$apptemplate->page('topnav');

			//$apptemplate->page('topmenu');

			//$apptemplate->page('workarea');

			// footer($name='footer', $vars=false, $ret=false) {

			$page = '';

			if(!empty($vars['params'])) {
				$vars['params'] = str_replace(' ','+',$vars['params']);
				$vars['post'] = json_decode(gzuncompress(base64_decode($vars['params'])),true);
				if($vars['post']) {
					$vars['post']['method'] = 'generatereportprint';
					$tvars = array();
					$tvars['json'] = $this->postjson($vars,1);
					$tvars['formval'] = $vars['post']['formval'];
					//echo 'hello, sherwin!';

					//pre(array('$tvars'=>$tvars));

					$page = $apptemplate->page('reportmainmonthlyattendance',$tvars,true);

					$page = str_replace('%formval%',$vars['post']['formval'],$page);

					//echo $page;
				}
			}


			$footer = $apptemplate->footer('footer', false, true);

			//echo $footer;

			header_html();

			die($header.$page.$footer);

		}

		function json($vars) { }

		function logout($vars) {
			global $applogin, $appsession;

			$appsession->destroy();

			//header('Location: /');
			redirect301('/login/');
			//die;
		}

		function session($vars) {
			global $appaccess, $applogin;

			pre(array('isSystemAdministrator'=>$applogin->isSystemAdministrator(),'getTimeFromServer'=>getTimeFromServer(),'$_SESSION'=>$_SESSION,'$vars'=>$vars,'$_SERVER'=>$_SERVER,'$appaccess'=>$appaccess->getAllRules()));
		} // session

		function ajax_valid_params($vars) {
			if(isset($vars['post'])&&is_array($vars['post'])&&!empty($vars['post'])) {
				$post = $vars['post'];
				if(!empty($post['action'])&&!empty($post['routerid'])&&!empty($post['parentid'])&&!empty($post['id'])) {
					return true;
				}
			}

			return false;
		} // ajax_valid_params

		function audit_trail() { }

		function render($vars) {
			global $apptemplate, $appform, $current_page;

			$this->check_url();

			$apptemplate->header($this->desc.' | '.APP_NAME);

			$apptemplate->page('topnav');

			$apptemplate->page('topmenu');

			$apptemplate->page('workarea');

			$apptemplate->footer();

		} // render

		function modulespath() {
			return str_replace(basename(__FILE__),'',__FILE__);
		}

	} // class APP_Base

}

# eof modules/baseclasses/index.php
