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

if(!class_exists('APP_FileCache')) {

	class APP_FileCache {
	
		public $cachepath = false;
		public $prefix = 'filecache_';
		public $postfix = '.cache';
		public $expire = 86400;
		public $gzip = true;
	
		function APP_FileCache($cachepath='/tmp') {

			$this->cachepath = $cachepath;
		
			if(substr($cachepath,-1)!='/') {
				$this->cachepath = $cachepath . '/';
			} 
		}
		
		function savecache($cache='', $filename=false, $debug=false) {
			if(empty($cache)) return false;
			
			if(!$filename) {
			
				//if($debug) {
				//	pre(array($filename));
				//}
			
				if(($filename = $this->_randomfile($debug))&&is_readable($filename)&&is_writable($filename)) {
					if($hf=fopen($filename,'w')) {
					
						if($this->gzip) {
							$gcache = gzcompress($cache,9);
						} else {
							$gcache = $cache;
						}

						//$ret=fwrite($hf,$cache."\n");
					
						$ret=fwrite($hf,$gcache."\n");
						
						fclose($hf);
						
						if($ret) {
							return array(
										'totalbytes'=>$ret,
										'path'=>$this->cachepath,
										'filename'=>str_replace($this->cachepath,'',$filename),
										'fullpath'=>$filename,
										'gzip'=>$this->gzip
									);
						}
					}
				}				
			} else {
				$filename = $this->prefix.$filename.$this->postfix;
								
				if($this->_createfile($filename)) {
					$filename = $this->cachepath . $filename;

					//if(isset($_GET['debug'])) {
					//	echo $filename;
					//}

					if(is_readable($filename)&&is_writable($filename)) {
						if($hf=fopen($filename,'w')) {

							//if(isset($_GET['debug'])) {
							//	echo $filename;
							//}

							if($this->gzip) {
								$gcache = gzcompress($cache,9);
							} else {
								$gcache = $cache;
							}

							//$ret=fwrite($hf,$cache."\n");
					
							$ret=fwrite($hf,$gcache."\n");

							fclose($hf);
							
							if($ret) {
								return array(
											'totalbytes'=>$ret,
											'path'=>$this->cachepath,
											'filename'=>str_replace($this->cachepath,'',$filename),
											'fullpath'=>$filename,
											'gzip'=>$this->gzip
										);
							}
						}
					}
				}
			}
			
			return false;
		}
		
		function loadcache($file=false) {
			if(empty($file)) return false;
			
			//$cachefile = $this->cachepath . $file;
			
			if(preg_match("#^".$this->prefix."(.+?)".$this->postfix."$#si",$file)) {
				$cachefile = $this->cachepath . $file;
			} else {
				$cachefile = $this->cachepath . $this->prefix.$file.$this->postfix;
			}
			
			if(is_readable($cachefile)&&($hf=fopen($cachefile,'r'))) {
				$page = fread($hf,filesize($cachefile));
				fclose($hf);

				$time0=time();
				$unlinked=false;
				
				if(is_numeric($this->expire)&&$this->expire>0) {
					if(($time0-filemtime($cachefile))>=$this->expire) { 
						@unlink($cachefile);
						$unlinked=true;
					}
				}
				
				if(!$unlinked) {
					touch($cachefile);
				}

				if($this->gzip) {
					$gpage = gzuncompress($page);
				} else {
					$gpage = $page;
				}

				if(trim($gpage)!='') return $gpage;
			}
			
			return false;
		}
		
		function _randomfile($debug=false) {
			$ctr=0;
			while(true) {
				$file = $this->prefix.sha1(sha1((microtime() * rand(1000,9999))) . sha1((microtime() * rand(1000,9999)))) . $this->postfix;
				if($this->_createfile($file,$debug)) {
					return $this->cachepath . $file;
				}
				
				if(++$ctr>100) break;
			}
					
			return false;
		}

		function _createfile($file,$debug=false) {
			$cachefile = $this->cachepath . $file;
			
			//if($debug) {
			//	pre(array($cachefile));
			//}
			
			if(!file_exists($cachefile)) {

				//if(isset($_GET['debug'])) {
				//	echo $cachefile;
				//}
			
				if($hf=@fopen($cachefile,'x')) {
					fclose($hf);
					return true;
				}
			}

			return false;
		}
	}
	
}

/* INCLUDES_END */


#eof ./includes/filecache/index.php