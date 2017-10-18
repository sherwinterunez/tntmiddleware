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

function extractbingimages($content,$param) {
		global $banneddomain;

		//if(preg_match_all('#\<div\s+class\=\"dg\_u\"\s+([^\>]+)\>\<a\s+([^\>]+)\>\<img\s+([^\>]+)\>\<img\s+([^\>]+)\>\<img\s+([^\>]+)\>\<\/a\>\<\/div\>#',$content,$matches)) {

		if(preg_match_all('#\<div\s+class\=\"dg\_u\"\s+([^\>]+)\>\<a\s+([^\>]+)\>\<img\s+([^\>]+)\>\<\/a\>\<\/div\>#',$content,$matches)) {
				
			$tlinks = array();
	
			$tmp = array();

			foreach($matches[2] as $kindx=>$v) {
				$timg = array();

				$v = str_replace('&quot;','"',$v);

				if(preg_match('#ihk\=\"H\.(\d+)\"#',$v,$n)) {
					$timg['id'] = $n[1];
				} else
				if(preg_match('#ihk\=\"HN\.(\d+)\"#',$v,$n)) {
					$timg['id'] = $n[1];
				} else continue;

				if(preg_match('#surl\:\"([^\"]+)\"#',$v,$n)) {
					$timg['imgrefurl'] = $n[1];
				} else continue;

				if(preg_match('#imgurl\:\"([^\"]+)\"#',$v,$n)) {
					$timg['imgurl'] = $n[1];
				} else continue;
				
				if(preg_match('#t2\=\"(\d+)\s+x\s+(\d+)\s+([^\"]+)"#',$v,$n)) {
					$timg['w'] = intval($n[1]);
					$timg['h'] = intval($n[2]);
					$timg['dim'] = $timg['w'] * $timg['h'];
				} else continue;

				/*$banned = false;

				foreach($banneddomain as $k) {
					if(preg_match('#'.$k.'#si',strtolower($timg['imgurl']))) {
						$banned = true;
						break;
					}
				}
				
				if($banned) continue;*/

				if(preg_match('#\?.*#si',$timg['imgurl'],$m)) {
					$timg['imgurl'] = str_replace($m[0],'',$timg['imgurl']);
				} else
				if(preg_match('#\&.*#si',$timg['imgurl'],$m)) {
					$timg['imgurl'] = str_replace($m[0],'',$timg['imgurl']);
				}

				$timg['imgkeyword1'] = false;
				$timg['imgkeyword2'] = false;

				if(preg_match('#t1\=\"([^\"]+)\"#',$v,$n)) {
					$timg['imgkeyword1'] = fixname($n[1]);
				}

				if(!empty($timg['imgurl'])) {
					$keyword2 = fixname(basename($timg['imgurl']));		
					$timg['imgkeyword2'] = $keyword2;
				}

				if(!isset($tmp[$timg['imgurl']])) {
					$tmp[$timg['imgurl']] = true;
					$tlinks[] = $timg;
				}
				
			} // foreach
			
			shuffle($tlinks);
			shuffle($tlinks);
			
			$ret = array('bing'=>true,'images'=>$tlinks);
			
			$ret['param'] = $param;
			
			return $ret;
		}
		
		return false;
} // extractbingimages

function bingsearchimageall($params) {

	global $proxies1, $proxies2, $useproxy, $maxresultpage;

	shuffle($proxies1);

	shuffle($proxies2);

	$max = 150;
	
	if(!empty($_GET['bingmax'])&&is_numeric($_GET['bingmax'])) {
		$max = intval($_GET['bingmax']);
	}

	$aret = array();
	
	$aimg = array();
	
	$arelated = array();

	$params['ipage'] = 1;
	
	if($useproxy==1) {
		$ret = bingsearchimage($params,$proxies1[0]);
	} else
	if($useproxy==2) {
		$ret = bingsearchimage($params,$proxies2[0]);
	} else {
		$ret = bingsearchimage($params);
	}

	if(!empty($ret)&&is_array($ret)) {
	
		$aret[1] = $ret;
	
		$totalpages = ceil($ret['totalimage'] / $max);
		
		for($page=2;$page<=$totalpages;$page++) {
			$params['ipage'] = $page;

			if($useproxy==1) {
				$ret = bingsearchimage($params,$proxies1[0]);
			} else
			if($useproxy==2) {
				$ret = bingsearchimage($params,$proxies2[0]);
			} else {
				$ret = bingsearchimage($params);
			}

			if(!empty($ret)&&is_array($ret)) {
				$aret[$page] = $ret;
				$totalpages = ceil($ret['totalimage'] / $max);
			} else break;
		}
		
		if(!empty($aret)&&is_array($aret)) {
			//pre(array('aret'=>$aret)); die;
			foreach($aret as $k=>$v) {
				if(!empty($v['images'])) {
					//pre(array('$v[images]'=>$v));
					foreach($v['images'] as $g=>$h) {
						if(empty($aimg[$h['imgurl']])) {
							$aimg[$h['imgurl']] = $h;
						}
					}
				}
				if(!empty($v['relatedentities'])) {
					foreach($v['relatedentities'] as $g=>$h) {
						if(empty($arelated[$h])) $arelated[$h] = $h;
					}
				}
			}
		}
		
		//die;
	
		if(!empty($aimg)&&is_array($aimg)) {
			//pre(array('$aimg'=>$aimg));

			$dimg = array();

			foreach($aimg as $k=>$v) {
				$dimg[$v['dim']][] = $v;
			}
			
			krsort($dimg);
			
			$aimg = array();
			
			foreach($dimg as $k=>$v) {
				foreach($v as $g=>$h) {
					$aimg[] = $h;
				}
			}
			
			//pre($aimg); die;

			$xlinks = array();
			$tlinks = array();
			//$totalimage = 0;
			$page = 0;
			$ctr = 1;
			foreach($aimg as $k=>$v) {
				$xlinks[] = $tlinks[$page][] = $v;
				$ctr++;
				//$totalimage++;
				if($ctr>29) {
					$page++;
					$ctr = 0;
				}
				
				if(!empty($maxresultpage)&&$page>=$maxresultpage) break;
			}
			
			$tret = array('bing'=>true,'totalimage'=>count($xlinks),'images'=>$tlinks);
			
			$tret['aret'] = $aret;
			
			if(!empty($arelated)&&is_array($arelated)) {
				$trelated = array();
				foreach($arelated as $k=>$v) {
					$trelated[] = $v;
				}
				if(!empty($trelated)&&is_array($trelated)) $tret['relatedentities'] = $trelated;
			}
			
			return $tret;
		}
	}
	
	return false;
	
} // bingsearchimageall

function bingsearchimage($param) {
	$max = 20;
	$first = 0;

	$param['searchurl'] = $url = 'http://www.bing.com/images/async?async=content&first='.$first.'&count='.$max.'&q='.urlencode($param['q']);

	$header   = array();
	$header[] = "Cookie: SRCHHPGUSR=ADLT=OFF;";

	$ch = new MyCurl;
	
	$ch->setopt(CURLOPT_USERAGENT,ua());
	$ch->setopt(CURLOPT_HTTPHEADER,$header);
	$ch->setopt(CURLOPT_ENCODING,"gzip");
	
	if(($cont = $ch->get($url))&&!empty($cont['content'])) {
	
		//pre($cont['content']);
	
		$ret = extractbingimages($cont['content'],$param);
				
		if(!empty($ret)&&is_array($ret)) return $ret;
		
	}
	
	return false;

} // bingsearchimage

function bingrelated($pr) {
	//global $proxies3;
	
	if(empty($pr['iq'])) return false;
	
	$url = 'http://www.bing.com/images/search?q='.urlencode($pr['iq']);

	$header   = array();
	$header[] = "Cookie: SRCHHPGUSR=NEWWND=0&ADLT=OFF&NRSLT=10&NRSPH=2&SRCHLANG=&AS=1;";
	$header[] = "REMOTE_ADDR: " . $_SERVER['REMOTE_ADDR'];
	$header[] = "X_FORWARDED_FOR: " . $_SERVER['REMOTE_ADDR'];
	
	$related = array();

	$ua = ua();

	$ch = new MyCurl;

	$ch->setopt(CURLOPT_HTTPHEADER,$header);
	$ch->setopt(CURLOPT_ENCODING,"gzip");
	$ch->setopt(CURLOPT_USERAGENT,$ua);	

	//shuffle($proxies3);
	
	//$ch->setopt(CURLOPT_PROXY,$proxies3[0]);

	if(($cont = $ch->get($url))&&!empty($cont['content'])) {
		//pre(array('length'=>strlen($cont['content'])));
		//echo $cont['content']; die;
		
		// class="iol_rsi" title="Search for: Bill Gates and Steve Jobs"
	
		if(preg_match_all('#class\=\"iol_rsi\"\s+title\=\"Search\s+for\:\s+([^\"]+)"#',$cont['content'],$m)) {
			//pre($m);
			//return $m;
			$related = $m[1];
		}

		if(preg_match_all('#\<div\s+class\=\"it\"\>([^\<]+)\<\/div\>#',$cont['content'],$m)) {
			foreach($m[1] as $k=>$v) {
				$v = ucwords(strtolower($v));
				if(!in_array($v,$related)) $related[] = $v;
			}
		}
		
		//pre($related); die;
	}

	$url = 'http://api.bing.com/osjson.aspx?query='.urlencode($pr['iq']);

	$ua = ua();

	$ch = new MyCurl;

	$ch->setopt(CURLOPT_HTTPHEADER,$header);
	$ch->setopt(CURLOPT_ENCODING,"gzip");
	$ch->setopt(CURLOPT_USERAGENT,$ua);	

	//shuffle($proxies3);
	
	//$ch->setopt(CURLOPT_PROXY,$proxies3[0]);

	if(($cont = $ch->get($url))&&!empty($cont['content'])) {
		//pre(array('length'=>strlen($cont['content'])));
		//echo $cont['content'];
		
		$b = json_decode($cont['content'],true);
		//pre($b);
		
		if(!empty($b[1])&&is_array($b[1])) {
			foreach($b[1] as $k=>$v) {
				$v = ucwords(strtolower($v));
				if(!in_array($v,$related)) $related[] = $v;
			}
		}
	}

	$url = 'http://google.com/complete/search?output=toolbar&q='.urlencode($pr['iq']);

	$ua = ua();

	$ch = new MyCurl;

	$ch->setopt(CURLOPT_HTTPHEADER,$header);
	$ch->setopt(CURLOPT_ENCODING,"gzip");
	$ch->setopt(CURLOPT_USERAGENT,$ua);	

	//shuffle($proxies3);
	
	//$ch->setopt(CURLOPT_PROXY,$proxies3[0]);

	if(($cont = $ch->get($url))&&!empty($cont['content'])) {
		//pre(array('length'=>strlen($cont['content'])));
		//echo $cont['content'];

		$xml = @simplexml_load_string($cont['content']);

		if(!empty($xml)) {
			$arr = xmlobj2array($xml);
			if(!empty($arr)&&is_array($arr)&&!empty($arr['CompleteSuggestion'])&&is_array($arr['CompleteSuggestion'])) {
				//pre($arr);
				foreach($arr['CompleteSuggestion'] as $k=>$v) {
					if(!empty($v['suggestion']['@attributes']['data'])) {
						$v = ucwords(strtolower($v['suggestion']['@attributes']['data']));
						if(!in_array($v,$related)) $related[] = $v;
					}
				}	
			}
		}
	}
		
	if(!empty($related)&&is_array($related)) return $related; 

	return false;
} // bingrelated

function extractbingwww($content,$qq,$url) {
		global $banneddomain;

		$xml = @simplexml_load_string($content);

		$arr = xmlobj2array($xml);

		if(!empty($arr['channel']['item'][0])) {
		
			$arr['bingurl'] = $url;
			
			$tlinks = array();
			
			foreach($arr['channel']['item'] as $k=>$v) {
				unset($v['pubDate']);
				$tlinks[] = $v;
			}

			if(!empty($tlinks)&&is_array($tlinks)) {
				return $tlinks;
			}
		}
		
		return false;
} // extractbingwww

function bingsearchwww($params) {
	global $proxies3;
	
	$max = 10;

	$start = (($params['ipage']-1) * $max) + 1;

	$tsize = trim(strtolower($params['isize']));
	
	$qq = '';
		
	$url = "http://www.bing.com/search?q=";

	if(!empty($params['isite'])) {
		$site = 'site:'.$params['isite'];
		$site = urlencode($site);
		$url .= $site;
	}
	
	if(!empty($params['iq'])) {
		$qq = $q = trim(strtolower($params['iq']));
		$q = urlencode($q);
		
		if(!empty($site)) $url .= '+';
		
		$url .= $q;
	} else return false;
	
	$url .= "&format=rss&first=$start";
	
	$params['url'] = $url;

	$header   = array();
	$header[] = "Cookie: SRCHHPGUSR=NEWWND=0&ADLT=OFF&NRSLT=10&NRSPH=2&SRCHLANG=&AS=1;";
	$header[] = "REMOTE_ADDR: " . $_SERVER['REMOTE_ADDR'];
	$header[] = "X_FORWARDED_FOR: " . $_SERVER['REMOTE_ADDR'];
	
	$ua = !empty($_SERVER['HTTP_USER_AGENT']) ?  $_SERVER['HTTP_USER_AGENT'] : ua();
	
	$ch = new MyCurl;
	
	$ch->setopt(CURLOPT_USERAGENT,ua());
	
	$ch->setopt(CURLOPT_HTTPHEADER,$header);

	shuffle($proxies3);
	
	$ch->setopt(CURLOPT_PROXY,$proxies3[0]);
	
	if(($cont = $ch->get($url))&&!empty($cont['content'])) {
	
		$ret = extractbingwww($cont['content'],$qq,$url);
		
		if(!empty($ret)&&is_array($ret)) return $ret;
		
	}
	
	return false;
} // bingsearchwww

function bingsearchwwwall($params) {

	$max = 30;

	$maxpage = 5;

	$aret = array();
	
	$awww = array();
	
	$arelated = array();

	$params['ipage'] = 1;
	//$params['isite'] = 'youtube.com';

	$ret = bingsearchwww($params);
	
	if(!empty($ret)&&is_array($ret)) {
	
		$aret[1] = $ret;
	
		for($page=2;$page<=$maxpage;$page++) {
			$params['ipage'] = $page;
			$ret = bingsearchwww($params);
			if(!empty($ret)&&is_array($ret)) {
				$aret[$page] = $ret;
			} else break;
		}
		
		if(!empty($aret)&&is_array($aret)) {
			foreach($aret as $k=>$items) {
				foreach($items as $g=>$h ) {
					if(empty($awww[$h['link']])) {
						$awww[$h['link']] = $h;
					}
				}
			}
			
		}
				
		return $awww;
	}
	
	return false;
	
} // bingsearchwwwall

/* INCLUDES_END */

#eof