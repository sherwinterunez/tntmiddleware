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

if(!class_exists('FileCache')) {
	class FileCache {
	
		public $cachepath = false;
		public $prefix = 'filecache_';
		public $postfix = '.cache';
		public $expire = 86400;
		public $redis = false;
		public $redis_prefix = 'redis4_';
		public $redis_ip = '127.0.0.1';
		public $redis_port = '6379';
		public $redis_expire = 3600; // seconds
		public $memcache = false;
		public $memcache_prefix = 'mc_';
		public $memcache_ip = '127.0.0.1';
		public $memcache_port = '11211';
		public $memcache_expire = 3600; // seconds
		public $debug = false;
		public $cachefile = false;
	
		function FileCache($cachepath='/tmp') {
		
			//echo "\n\n<!-- FileCache initialized -->\n\n";

			$this->cachepath = $cachepath;
		
			if(substr($cachepath,-1)!='/') {
				$this->cachepath = $cachepath . '/';
			}
			
			if(class_exists("Redis")) {
				$this->redis = new Redis();

				if(!$this->redis->connect($this->redis_ip, $this->redis_port)) {
					$this->redis = false;
					if(!empty($_GET['redisdebug'])) {
						echo "\n\n<!-- redis not initialized -->\n\n";
					}
				} else {
					if(!empty($_GET['redisdebug'])) {
						echo "\n\n<!-- redis initialized -->\n\n";
					}
				}
			}

			if(class_exists("Memcache")) {
				$this->memcache = new Memcache();
			
				if(!$this->memcache->connect($this->memcache_ip, $this->memcache_port)) {
					$this->memcache = false;
				}
			}

		}
		
		function savecache($cache='', $filename=false, $delete=false) {
			if(empty($cache)) {
				$this->cachefile = false;
				return false;
			}
			
			if(!$filename) {
				if(($filename = $this->_randomfile())&&is_readable($filename)&&is_writable($filename)) {
					if($hf=fopen($filename,'w')) {

						$this->cachefile = $filename;
					
						$gcache = gzcompress($cache,9);

						//$ret=fwrite($hf,$cache."\n");
					
						$ret=fwrite($hf,$gcache."\n");
						
						fclose($hf);
						
						if($this->redis) {
							$redis_key = $this->redis_prefix . $this->redis_ip . ':' . $this->redis_port . '_' . sha1($filename);
							$this->redis->setex($redis_key, $this->redis_expire, $gcache);
						} else
						if($this->memcache) {
							$mc_key = $this->memcache_prefix . $this->memcache_ip . ':' . $this->memcache_port . '_' . sha1($filename);
							$this->memcache->add($mc_key, $gcache, false, $this->memcache_expire );
						}
						
						if($ret) {
							return array(
										'totalbytes'=>$ret,
										'path'=>$this->cachepath,
										'filename'=>str_replace($this->cachepath,'',$filename),
										'fullpath'=>$filename,
										'gzip'=>true
									);
						}
					}
				}				
			} else {
				$filename = $this->prefix.$filename.$this->postfix;
								
				if($this->_createfile($filename, $delete)) {
					$filename = $this->cachepath . $filename;

					//if(isset($_GET['debug'])) {
					//	echo $filename;
					//}

					if(is_readable($filename)&&is_writable($filename)) {
						if($hf=fopen($filename,'w')) {

							//if(isset($_GET['debug'])) {
							//	echo $filename;
							//}

							$this->cachefile = $filename;

							$gcache = gzcompress($cache,9);

							//$ret=fwrite($hf,$cache."\n");
					
							$ret=fwrite($hf,$gcache."\n");

							fclose($hf);

							if($this->redis) {
								$redis_key = $this->redis_prefix . $this->redis_ip . ':' . $this->redis_port . '_' . sha1($filename);
								$this->redis->setex($redis_key, $this->redis_expire, $gcache);
							} else
							if($this->memcache) {
								$mc_key = $this->memcache_prefix . $this->memcache_ip . ':' . $this->memcache_port . '_' . sha1($filename);
								$this->memcache->add($mc_key, $gcache, false, $this->memcache_expire );
							}
						
							if($ret) {
								return array(
											'totalbytes'=>$ret,
											'path'=>$this->cachepath,
											'filename'=>str_replace($this->cachepath,'',$filename),
											'fullpath'=>$filename,
											'gzip'=>true
										);
							}
						}
					}
				}
			}

			$this->cachefile = false;
			
			return false;
		}
		
		function loadcache($file=false,$touch=false) {
			if(empty($file)) return false;
			
			//$cachefile = $this->cachepath . $file;
			
			$this->cachefile = $cachefile = $this->cachepath . $this->prefix.$file.$this->postfix;

			if($this->debug==true) {
				echo "\n\n<!-- cachefile: $cachefile -->\n\n";
			}
			
			if(!empty($_GET['redisdebug'])) {
				echo "\n\n<!-- loadcache: $cachefile -->\n\n";
			}
			
			if($this->redis) {
			
				$redis_key = $this->redis_prefix . $this->redis_ip . ':' . $this->redis_port . '_' . sha1($cachefile);

				if(!empty($_GET['redisdebug'])) {
					echo "\n\n<!-- loadcache redis: $redis_key -->\n\n";
				}

				$page = $this->redis->get($redis_key);
				
				if($page) {
					//if(!empty($_GET['redisdebug'])) {
					//	echo "\n\n<!-- redis cache page: $page -->\n\n";
					//}
					$gpage = gzuncompress($page);
					if(trim($gpage)!='') {
						if(!empty($_GET['redisdebug'])) {
							echo "\n\n<!-- redis cache file: $redis_key -->\n\n";
						}
						return $gpage;
					}
				}
			} else
			if($this->memcache) {
			
				$mc_key = $this->memcache_prefix . $this->memcache_ip . ':' . $this->memcache_port . '_' . sha1($cachefile);

				$page = $this->memcache->get($mc_key);

				//echo "\n\n<!-- memcache1: $mc_key -->\n\n";
				
				if($page) {
					$gpage = gzuncompress($page);
					if(trim($gpage)!='') {
						//echo "\n\n<!-- memcache2: $mc_key -->\n\n";
						return $gpage;
					}
				}
			}
			
			if(is_readable($cachefile)&&($hf=fopen($cachefile,'r'))) {
				$time0=time();
				
				if(is_numeric($this->expire)&&$this->expire>0) {
					if(($time0-filemtime($cachefile))>=$this->expire) { 
						fclose($hf);
						@unlink($cachefile);
						$this->cachefile = false;
						return false;
					}
				}
				
				if($touch) {
					touch($cachefile);
				}

				$page = $gcache = fread($hf,filesize($cachefile));
				fclose($hf);

				$gpage = gzuncompress($page);
		
				//if(trim($page)!='') return $page;

				if(trim($gpage)!='') {

					if($this->redis) {
						$redis_key = $this->redis_prefix . $this->redis_ip . ':' . $this->redis_port . '_' . sha1($cachefile);
						$this->redis->setex($redis_key, $this->redis_expire, $gcache);
						
						if(!empty($_GET['redisdebug'])) {
							echo "\n\n<!-- loadcache redis setex: $redis_key -->\n\n";
						}
					} else
					if($this->memcache) {
						$mc_key = $this->memcache_prefix . $this->memcache_ip . ':' . $this->memcache_port . '_' . sha1($cachefile);
						$this->memcache->add($mc_key, $gcache, false, $this->memcache_expire );
					}

					return $gpage;
				}
				
				@unlink($cachefile);
			}

			if(!empty($_GET['redisdebug'])) {
				echo "\n\n<!-- no loadcache cache page -->\n\n";
			}

			$this->cachefile = false;
			
			return false;
		}
		
		function _randomfile() {
			$ctr=0;
			while(true) {
				$file = $this->prefix.sha1(sha1((time() * rand(1000,9999))) . sha1((time() * rand(1000,9999)))) . $this->postfix;
				if($this->_createfile($file)) {
					return $this->cachepath . $file;
				}
				
				if(++$ctr>100) break;
			}
					
			return false;
		}

		function _createfile($file, $delete=false) {
			$cachefile = $this->cachepath . $file;
			
			if($delete) {
				@unlink($cachefile);
			}
			
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
	
	//if(0) {
	
	//	$fc = new FileCache();
		
		/*
		if(($ret = $fc->savecache('hello-'.time()))&&!empty($ret['totalbytes'])&&$ret['totalbytes']>0) {
			echo "<pre>";
			print_r($ret);
			echo "</pre>";
			$content = $fc->loadcache($ret['filename']);
			echo $content;
		}
		*/
		
	//	if(($ret = $fc->savecache('hello-'.time(),'cache-'.time()))&&!empty($ret['totalbytes'])&&$ret['totalbytes']>0) {
	//		echo "<pre>";
	//		print_r($ret);
	//		echo "</pre>";
	//		$content = $fc->loadcache($ret['filename']);
	//		echo $content;
	//	}
	
	//}
}

/* INCLUDES_END */
