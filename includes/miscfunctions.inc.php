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

function fixname2($q,$qq='',$num=true) {
	$q = trim(htmlspecialchars_decode($q));
	$q = trim(strip_tags($q));
	$q = trim(urldecode($q));
	$q = trim(strtolower($q));
	
	$q = str_replace('.jpg','',$q);
	$q = str_replace('.jpeg','',$q);
	$q = str_replace('.gif','',$q);
	$q = str_replace('.png','',$q);
	$q = str_replace('.bmp','',$q);
	$q = str_replace('-',' ',$q);
	$q = preg_replace("/&#([0-9]+);/","", $q);
	$q = preg_replace("/[\|]/","-",$q);
	$q = preg_replace("/\.\.\./","",$q);
	$q = preg_replace("/[\"\']/","",$q);
	$q = preg_replace("/[\—]/","-",$q);
	$q = preg_replace("/[\–]/","-",$q);
	$q = preg_replace("/---/","",$q);
	$q = preg_replace("/-/","",$q);
	$q = preg_replace("/»/","",$q);
	
	if(!$num) {
		$q = preg_replace("/[^a-zA-Z\s\$\%]/", " ", $q);
	} else {
		$q = preg_replace("/[^0-9a-zA-Z\s\$\%\.]/", " ", $q);
	}
	
	$q = trim($q);

	$aq = explode(" ", $q);
	$tq = array();

	if(trim($qq)!='') {
		$qq = urldecode($qq);
		$qq = explode(' ',$qq);
	
		$qt = array();
	
		foreach($qq as $qv) {
			$qt[] = trim($qv);
		}
	}
		
	foreach($aq as $tt) {
		if((trim($tt)!='')&&strlen(trim($tt))>1) {
			$tq[] = trim($tt);
		}
	}	
		
	$q = implode(' ',$tq);

	if(isset($qt)&&sizeof($qt)>0) {
		foreach($qt as $k) {
			//echo "$k => $q<br />";
			if(strpos($q, $k)===false) {
				return '';
			}
		}
	}
	
	return $q;
}

function alltrim($q) {
	if(empty($q)) return '';
	
	$qe = explode(' ',$q);
	
	foreach($qe as $k=>$v) {
		$qe[$k] = trim($qe[$k]);
		if(empty($qe[$k])) unset($qe[$k]);
	}
	
	$q = trim(implode(' ',$qe));
	
	return $q;
}

function fixname($q,$qq='',$num=true) {
	$q = alltrim(strip_tags($q));
	$q = alltrim(urldecode($q));
	
	$q = alltrim(stripslashes($q));

	$q = alltrim(preg_replace('#\.(jpg|html|php|jpeg|gif|png|bmp)#si','',$q));
	$q = alltrim(preg_replace('#(jpg|html|php|jpeg|gif|png|bmp)#si','',$q));
	
	$q = alltrim(str_replace('-',' ',$q));
	$q = alltrim(str_replace('_',' ',$q));
	
	//$q = urlencode($q);
	//$q = str_replace('+',' ',$q);
	//$q = str_replace('.',' ',$q);
		
	return $q;
}

function fixurldecode($str) {
	for($i=0;$i<20;$i++) {
		$str = urldecode($str);
	}
	
	return $str;
}

function makeurl($q) {
	if(empty($q)) return false;

	$q = strtolower(fixname($q));
	
	$qe = explode(' ',$q);
		
	$url = '/' . urlencode($qe[0]) . '/' . urlencode($q) . '.html';
	
	return array('category'=>$qe[0],'url'=>$url,'q'=>$q);
}

function redirect301($url='/') {
	header ('HTTP/1.1 301 Moved Permanently');
	header ('Location: '.$url);
	die;
}

/* INCLUDES_END */

#eof
