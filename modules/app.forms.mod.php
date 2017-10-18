<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* App Module
*
* Date: November 13, 2015
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

//$forms['app']['newpatient'] = '<h1>New Patient</h1>';
//$forms['app']['patienthistory'] = '<h1>Patient History</h1>';
//$forms['app']['schedules'] = '<h1>Schedules</h1>';
//$forms['app']['patients'] = '<h1>Patients</h1>';
//$forms['app']['newopen'] = '<h1>New Open</h1>';
//$forms['app']['newfile'] = '<h1>New File</h1>';

$forms['app']['promotionmain'] = '<div id="promotionmain">#promotionmain</div>';
$forms['app']['promotiondetail'] = '<div id="promotiondetail">#promotiondetail</div>';

$forms['app']['eloadmain'] = '<div id="eloadmain">#eloadmain</div>';
$forms['app']['eloaddetail'] = '<div id="eloaddetail">#eloaddetail</div>';

$forms['app']['referralmain'] = '<div id="referralmain">#referralmain</div>';
$forms['app']['referraldetail'] = '<div id="referraldetail">#referraldetail</div>';

$forms['app']['schedulermain'] = '<div id="schedulermain">#schedulermain</div>';
$forms['app']['schedulerdetail'] = '<div id="schedulerdetail">#schedulerdetail</div>';

//$forms['app']['reportcontrol'] = '<div id="schedulermain">#schedulermain</div>';
$forms['app']['reportmain'] = '<div id="reportmain">#reportmain</div>';
//$forms['app']['reportdetails'] = '<div id="schedulerdetail">#schedulerdetail</div>';
//$forms['app']['reportmisc'] = '<div id="schedulerdetail">#schedulerdetail</div>';




//
