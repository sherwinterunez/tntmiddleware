<?php
/*
* Author: Mervin Separa
* Description: Class to convert PDF file to IMG using IMAGICK PHP extension
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

/* INCLUDES_START */

if(!class_exists('APP_PDF2IMG')) {
	class APP_PDF2IMG {
		private $format;
		private $ext;
		private $xresolution;
		private $yresolution;
		private $imgk;
		private $filename;
		
		public function __construct($args = null) {
			$result = false;
			if ($this->init()) {
				$this->config();
				$this->setImg();
				$result = $this->processFile($args);
				
				$this->clean();
			}
			
			return $result;
		}
		
		private function init() {
			$result = false;
			$this->imgk = new Imagick();
			
			if ($this->imgk) $result = true;
			
			return $result;
		}
		
		private function config() {
			$this->format = 'jpeg';
			$this->ext = '.jpeg';
			$this->xresolution = '150';
			$this->yresolution = '150';
		}
		
		private function setImg() {
			$this->imgk->setresolution($this->xresolution, $this->yresolution);
		}
		
		private function processFile($args) {
			$result = false;
			$newfile = str_replace('.pdf',$this->ext,$args->file);
			
			$this->imgk->readimage($args->loc.$args->file);
			$this->imgk->resetIterator();
			
			$newimg = $this->imgk->appendImages(true);
    		$newimg->setImageFormat($this->format);
			if($newimg->writeimage($args->loc.$newfile)) {
				$this->filename = $newfile;
				$result = true;
			}
			
			return $result;
    	}
		
		private function clean(){
			$this->imgk->clear();
			$this->imgk->destroy();
		}
		
		public function getFilename() {
			return $this->filename;
		}
		
		public function getExt() {
			return $this->ext;
		}
	}
}

/* INCLUDES_END */
