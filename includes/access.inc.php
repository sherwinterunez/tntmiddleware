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

if(!class_exists('APP_Access')) {

	class APP_Access {
	
		var $access = array();

		function __construct() {
		}
		
		function __destruct() {
		}
		
		function rules($id=false,$rule=false,$desc=false) {
			if(!empty($id)&&!empty($rule)&&!empty($desc)) {
				$this->access[$id][$rule] = $desc;
				return true;
			} else
			if(!empty($id)&&!empty($rule)) {
				$this->access[$id][str_replace(' ','',trim(strtolower($rule)))] = $rule;
				return true;
			}
			return false;
		}
		
		function showrules() {
			global $applogin;
			
			//pre(array('userid'=>$applogin->userid(),'username'=>$applogin->username()));
			//pre($_SESSION);
			pre($this->access);
			//die;
		}

		function getRules($id=false) {
			if(!empty($id)&&!empty($this->access[$id])) {
				return $this->access[$id];
			}
			return false;
		}

		function getAllRules() {
			return $this->access;
		}
		
		function isAllowed($routerid=false,$vars=array()) {
			global $approuter;
			
			if($_SESSION['login']=='ADMIN') return true;
			
			if(empty($routerid)) return false;
			
			$p = 'userprivileges' . $routerid;

			//pre(array('userprivileges'=>$p)); pre($_SESSION);
			
			if(empty($_SESSION['privileges'][$p])) return false;
			
			//die;
			return true;
		}

	}
	
	$appaccess = new APP_Access;

}

/* INCLUDES_END */

#eof ./includes/access/index.php