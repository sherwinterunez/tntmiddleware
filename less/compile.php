<?php
require_once('../min/lib/Minify/CSS/Compressor.php');
require_once('lessc.inc.php');
$compressor = new Minify_CSS_Compressor(array());
$parser = new Less_Parser();
$parser->parseFile( '../templates/default/less/styles.less' );
$css = $parser->getCss();
$cssmin = $compressor->process($css);


//$filename = "styles-".date("m-d-Y").".css";

$filename = "../templates/default/css/styles.css";
$filenamemin = "../templates/default/css/styles.min.css";

if(!empty($css)&&($hf=fopen($filename,'w+'))) {
	$ret=fwrite($hf,$css."\n");
	fclose($hf);
	if(file_exists($filename)) {

		if(!empty($cssmin)&&($hf=fopen($filenamemin,'w+'))) {
			$ret=fwrite($hf,$cssmin."\n");
			fclose($hf);
		}

		if(file_exists($filenamemin)) {
			die("success!");
		}

	}
}

echo "an error has occured!";