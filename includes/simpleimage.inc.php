<?php
/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details:
* http://www.gnu.org/licenses/gpl.html
*

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

if(!class_exists('APP_SimpleImage')) {

	class APP_SimpleImage {

		var $image;
		var $image_type;

		function load($filename) {
			$image_info = getimagesize($filename);
			$this->image_type = $image_info[2];
			if( $this->image_type == IMAGETYPE_JPEG ) {
				$this->image = imagecreatefromjpeg($filename);
			} elseif( $this->image_type == IMAGETYPE_GIF ) {
				$this->image = imagecreatefromgif($filename);
			} elseif( $this->image_type == IMAGETYPE_PNG ) {
				$this->image = imagecreatefrompng($filename);
			}
		}

		function loadfromstring($imagedata) {
			$image_info = getimagesizefromstring($imagedata);
			//pre(array('$image_info'=>$image_info,'IMAGETYPE_JPEG'=>IMAGETYPE_JPEG));
			$this->image_type = $image_info[2];
			//if( $this->image_type == IMAGETYPE_JPEG ) {
				$this->image = imagecreatefromstring($imagedata);
			//} elseif( $this->image_type == IMAGETYPE_GIF ) {
				//$this->image = imagecreatefromgif($filename);
			//} elseif( $this->image_type == IMAGETYPE_PNG ) {
				//$this->image = imagecreatefrompng($filename);
			//}
		}

		function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
			if( $image_type == IMAGETYPE_JPEG ) {
				imagejpeg($this->image,$filename,$compression);
			} elseif( $image_type == IMAGETYPE_GIF ) {
				imagegif($this->image,$filename);
			} elseif( $image_type == IMAGETYPE_PNG ) {
				imagepng($this->image,$filename);
			}
			if( $permissions != null) {
				chmod($filename,$permissions);
			}
		}

		function output($image_type=IMAGETYPE_JPEG, $file=NULL) {
			if( $image_type == IMAGETYPE_JPEG ) {
				imagejpeg($this->image, $file);
			} elseif( $image_type == IMAGETYPE_GIF ) {
				imagegif($this->image, $file);
			} elseif( $image_type == IMAGETYPE_PNG ) {
				imagepng($this->image, $file);
			}
		}

		function getWidth() {
			return imagesx($this->image);
		}

		function getHeight() {
			return imagesy($this->image);
		}

		function crop($size) {
			$this->image = imagecrop($this->image, array('x' => 0, 'y' => 0, 'width' => $size, 'height' => $size));
		}

		function resizeToHeight($height) {
			$ratio = $height / $this->getHeight();
			$width = $this->getWidth() * $ratio;
			$this->resize($width,$height);
		}

		function resizeToWidth($width) {
			$ratio = $width / $this->getWidth();
			$height = $this->getheight() * $ratio;
			$this->resize($width,$height);
		}

		function scale($scale) {
			$width = $this->getWidth() * $scale/100;
			$height = $this->getheight() * $scale/100;
			$this->resize($width,$height);
		}

		function resize($width,$height) {
			$new_image = imagecreatetruecolor($width, $height);
			imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
			$this->image = $new_image;
		}

		function mimetype() {
			return image_type_to_mime_type($this->image_type);
		}
	}
}

/* INCLUDES_END */
