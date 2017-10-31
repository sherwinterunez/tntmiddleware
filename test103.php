<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Modifications done for security purposes
*
*/

define('APPLICATION_RUNNING', true);

require_once('includes.inc.php');

$appsession->start();

$_SESSION['timestamp'] = time();

//if(!$applogin->is_loggedin()) {
//    die('access denied');
//}

$file = '/data/PH/Data/phmnlu01.13842.inprogress';

$filesize = filesize($file);

$filemtime = date ("F d Y H:i:s.", filemtime($file));

print_r(array('$file'=>$file,'$filesize'=>$filesize,'$filemtime'=>$filemtime));
