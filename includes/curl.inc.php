<?php

/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
*
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

if(!class_exists('MyCURL')) {

	class MyCURL {
		public $ch = false;
		public $chproxy = false;
		public $useragent = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_5; en-us) AppleWebKit/525.26.2 (KHTML, like Gecko) Version/3.2 Safari/525.26.12";
		//public $useragent = "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)";
		public $timeout = 60;
		public $retry = 1;
		public $cookie = false;
		public $proxycookie = false;
		public $proxy = false;

		public function __construct() {
			$this->ch = curl_init();
			$this->setopt(CURLOPT_USERAGENT,$this->useragent);
			$this->setopt(CURLOPT_TIMEOUT,$this->timeout);
			$this->setopt(CURLOPT_RETURNTRANSFER,1);
			$this->setopt(CURLOPT_FOLLOWLOCATION,1);
		}
		public function __destruct()  { curl_close($this->ch); }

		public function setopt($opt=NULL, $val=NULL, $proxy=false) {
			if( $opt != NULL and $val != NULL ) {
				if(!$proxy) curl_setopt($this->ch, $opt, $val);
				else
				if($this->chproxy) {
					curl_setopt($this->chproxy, $opt, $val);
				}
			}
		}

		public function setcookie($cookie=NULL, $proxy=false) {
			if($cookie!=NULL) {
				if(!$proxy) {
					$this->setopt(CURLOPT_COOKIEJAR,$cookie);
					$this->setopt(CURLOPT_COOKIEFILE,$cookie);
					$this->cookie = $cookie;
				} else {
					$this->proxy_setopt(CURLOPT_COOKIEJAR,$cookie);
					$this->proxy_setopt(CURLOPT_COOKIEFILE,$cookie);
				}
			}
		}

		public function get($url=NULL,$debug=false,$proxy=false) {

			if($url!=NULL) {
				if(!$proxy) $this->setopt(CURLOPT_POST,0);
				else $this->proxy_setopt(CURLOPT_POST,0);

				$ctr = 0;
				while($ctr<$this->retry) {
					if(!$proxy) {
						$this->setopt(CURLOPT_URL,$url);
						$cont = curl_exec($this->ch);
					} else {
						$this->proxy_setopt(CURLOPT_URL,$url);
						$cont = curl_exec($this->chproxy);
					}

					if($debug) echo $cont;
					if( isset($cont) and trim($cont) != '' ) break;
					$ctr++;
				}

				if(isset($cont) and trim($cont) != '') {
					return array('content' => $cont);
				}
			}
			return false;
		}

		public function getdom($url=NULL,$debug=false,$proxy=false) {
			$cont = $this->get($url,$debug,$proxy);

			if($cont) {
				$dom = new DOMDocument;
				@$dom->loadHTML('<!DOCTYPE html>'.$cont['content']);

				$cont['dom'] = $dom;
			}

			return $cont;
		}

		public function post($url=NULL,$vars=NULL,$debug=false,$proxy=false) {

			if($url!=NULL and $vars!=NULL) {
				if(!$proxy) {
					$this->setopt(CURLOPT_POST,1);
					$this->setopt(CURLOPT_POSTFIELDS,$vars);
				} else {
					$this->proxy_setopt(CURLOPT_POST,1);
					$this->proxy_setopt(CURLOPT_POSTFIELDS,$vars);
				}
				$ctr = 0;
				while($ctr<$this->retry) {
					if(!$proxy) {
						$this->setopt(CURLOPT_URL,$url);
						$cont = curl_exec($this->ch);
					} else {
						$this->proxy_setopt(CURLOPT_URL,$url);
						$cont = curl_exec($this->chproxy);
					}

					if($debug) echo $cont;
					if( isset($cont) and trim($cont) != '' ) break;
					$ctr++;
				}

				if(!$proxy) $this->setopt(CURLOPT_POST,0);
				else $this->proxy_setopt(CURLOPT_POST,0);

				return array('content' => $cont);
			}
			return false;
		}

		// proxy support

		private function cookiecopy($file1,$file2) {
			  $contentx =@file_get_contents($file1);
					   $openedfile = fopen($file2, "w");
					   fwrite($openedfile, $contentx);
					   fclose($openedfile);
						if ($contentx === FALSE) {
						$status=false;
						}else $status=true;

						return $status;
		}

		public function proxy_init($proxy=NULL) {
			if($proxy!==NULL) {
				$this->chproxy = curl_init();
				$this->proxy_setopt(CURLOPT_USERAGENT,$this->useragent);
				$this->proxy_setopt(CURLOPT_TIMEOUT,$this->timeout);
				$this->proxy_setopt(CURLOPT_RETURNTRANSFER,1);
				$this->proxy_setopt(CURLOPT_FOLLOWLOCATION,1);
				$this->proxy_setopt(CURLOPT_PROXY,$proxy);
				$this->proxy = $proxy;
			}
		}

		public function proxy_close() {
			if($this->chproxy) {
				curl_close($this->chproxy);
				$this->chproxy = false;
				$this->proxy = false;
				$this->proxycookie = false;
			}
		}

		public function proxy_setopt($opt=NULL, $val=NULL, $proxy=true) {
			if($this->chproxy) $this->setopt($opt, $val, $proxy);
		}

		public function proxy_get($url=NULL,$debug=false,$proxy=true) {
			if($this->chproxy) return $this->get($url,$debug,$proxy);
		}

		public function proxy_post($url=NULL,$vars=NULL,$debug=false,$proxy=true) {
			if($this->chproxy) return $this->post($url,$vars,$debug,$proxy);
		}

		public function proxy_setcookie($cookie=NULL, $proxy=true) {
			if($this->chproxy) $this->setcookie($cookie,$proxy);
		}

		public function getinfo() {
			return curl_getinfo($this->ch);
		}
	}

}

/* INCLUDES_END */
