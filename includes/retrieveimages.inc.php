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

function retrieveImages($param) {
	global $appdb, $website, $cachepath, $cachepwd, $relatedlimit;

	$fc = new FileCache($cachepath);

	$fc->expire = 0; // no expiration
		
	$fc->redis = $fc->memcache = false;

	$q = $param['q'];
						
	$hash = sha1($website.'_'.$q);
	
	if($cache=$fc->loadcache($hash)) {
		$cache = unserialize($cache);
	
		if(!empty($cache)&&is_array($cache)) {
			//pre('this is a cache version');
			
			if(empty($cache['relatedkeys'])&&!empty($cache['related'])) {
			
				if(empty($appdb)&&!is_a($appdb,'APP_Db')) {
					$appdb = new APP_Db(DB_HOST, DB_NAME, DB_USER, DB_PASS);
				}
				
				foreach($cache['related'] as $k=>$v) {
					$v = strtolower(fixname($v));
					$thash = sha1($website.'_'.$v);

					$sql = "SELECT id FROM tbl_keywords WHERE hash='$thash'";

					if(!empty($relatedlimit)) {
						$sql .= " AND flag=10";
					}

					if(!($rets = $appdb->query($sql))) {
					} else {
						if(!empty($rets['rows'][0]['id'])) {
							$cache['relatedkeys'][] = $v;
						}
					}

				}
				
				//if(!empty($appdb)) {
				//	$appdb->close();
				//}
				
				if(!empty($cache['relatedkeys'])) {
					$serial = serialize($cache);
					$fc->savecache($serial,$hash,true);
				}
			}

			//pre($cache);
			
			if(!empty($fc->cachefile)) $cachepwd = $fc->cachefile;
			
			return $cache;
		}
	}

	if(empty($appdb)) {
		$appdb = new APP_Db(DB_HOST, DB_NAME, DB_USER, DB_PASS);
	}
	
	$v = strtolower(fixname($param['q']));

	$ct = explode(' ',$v);
				
	$ct[0] = strtolower(fixname($ct[0]));

	$retid = false;

	if(!($rets = $appdb->insert('tbl_keywords',array('hash'=>$hash,'website'=>$website,'keyword'=>urlencode($q),'category'=>$ct[0],'flag'=>0),'id'))) {
	}	

	if(!empty($rets['returning'][0]['id'])&&is_numeric($rets['returning'][0]['id'])&&$rets['returning'][0]['id']>0) {
		$retid = $rets['returning'][0]['id'];
	} else {
		if(!($rets = $appdb->query("SELECT id,flag FROM tbl_keywords WHERE hash='$hash'"))) {
		} else {
			if(!empty($rets['rows'][0]['id'])&&$rets['rows'][0]['flag']!=10) {
				$retid = $rets['rows'][0]['id'];
				//pre($rets);
			}
		}
	}

	//pre($rets);				

	$ret = bingsearchimage($param);
	
	if(!empty($ret['images'])) {
		$related = bingrelated(array('iq'=>$param['q']));
	
		if(!empty($related)&&is_array($related)) {
			shuffle($related);
			shuffle($related);
			
			$trel = array();
			
			$ctr=0;
			foreach($related as $k=>$v) {
				$v = strtolower(fixname($v));
				
				$trel[] = $v;

				if(++$ctr>9) break;
			}
			
			if(!empty($param['category'])&&!in_array($param['category'],$trel)) {
				$trel[] = strtolower(fixname($param['category']));
			}
									
			$tmp = array();
			
			$ctr=0;
			foreach($trel as $k=>$v) {
				$v = strtolower(fixname($v));
				
				$tmp[] = $v;
				
				$thash = sha1($website.'_'.$v);
				
				$cat = 0;
				
				if($v==strtolower(fixname($param['category']))) {
					$cat = 1;
				}
				
				$ct = explode(' ',$v);
				
				$ct[0] = strtolower(fixname($ct[0]));
				
				$bypass = false;
				
				if($cat) {
					if(!($rets = $appdb->query("SELECT id FROM tbl_keywords WHERE hash='$thash'"))) {
					} else {
						if(!empty($rets['rows'][0]['id'])) {
							$appdb->query("UPDATE tbl_keywords SET catflag=1 WHERE id=".$rets['rows'][0]['id']);
							$bypass=true;
						}
					}
					
				}

				if(!$bypass) {
					if(!($drets = $appdb->insert('tbl_keywords',array('hash'=>$thash,'website'=>$website,'keyword'=>urlencode($v),'category'=>$ct[0],'catflag'=>$cat,'flag'=>0),'id'))) {
					}					
				}

				//if(++$ctr>9) break;
			}
			
			//if(!empty($appdb)) {
			//	$appdb->close();
			//}
			
			$ret['related'] = $tmp;
		}
		
		$ret['param']['datetime'] = time();
		$ret['param']['datetimestr'] = date('l jS \of F Y h:i:s A',$ret['param']['datetime']);

		//pre($ret);
		
		$serial = serialize($ret);
		
		if(!empty($serial)) {
			$sret = $fc->savecache($serial,$hash,false);

			if(!empty($fc->cachefile)) $cachepwd = $fc->cachefile;

			if(!empty($sret['totalbytes'])&&is_numeric($sret['totalbytes'])&&$sret['totalbytes']>0) {
				//pre($sret);
				if(!empty($retid)) {
					$appdb->query("UPDATE tbl_keywords SET flag=10 WHERE id=".$retid);
				}
			}
						
			return $ret;
		}

	}

	//if(!empty($appdb)) {
	//	$appdb->close();
	//}
	
	return false;
}

/* INCLUDES_END */


#eof
