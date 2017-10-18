<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Header template
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"/>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<title><?php $this->title(); ?></title>
<?php 
do_action('action_meta_content_type');
do_action('action_meta_description');
do_action('action_meta_author');
do_action('action_meta_robots');
do_action('action_stylesheets');
do_action('action_scripts');
do_action('action_settings');
do_action('action_header_bottom');
?>
<?php /*
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
*/ ?>
</head>
<body class="scroll-assist btn-rounded">

		<div class="nav-container">

		    <nav class="absolute transparent bg-dark">
		        <div class="nav-bar">
		            <div class="module left">
		                <a href="/">
		                    <img class="logo logo-light" alt="Foundry" src="/templates/default/frontend/img/logo-light.png">
		                    <img class="logo logo-dark" alt="Foundry" src="/templates/default/frontend/img/logo-dark.png">
		                </a>
		            </div>
		            <div class="module widget-handle mobile-toggle right visible-sm visible-xs">
		                <i class="ti-menu"></i>
		            </div>
		            <div class="module-group right">
		                <div class="module left">
		                    <ul class="menu">
		                        <li>
			                        <a target="_self" href="/">Register</a>
		                        </li>
		                        <li>
			                        <a target="_self" href="/p/promo/">Claim Promo</a>
		                        </li>
		                        <li>
			                        <a target="_self" href="/p/refer/">Referral</a>
		                        </li>
		                        <li>
			                        <a target="_self" href="/login/">Admin</a>
		                        </li>
<?php /*
		                        <li class="has-dropdown">
		                            <a href="#">
		                                Mega Menu
		                            </a>
		                            <ul class="mega-menu">
		                                <li>
		                                    <ul>
		                                        <li>
		                                            <span class="title">Column 1</span>
		                                        </li>
		                                        <li>
		                                            <a href="#">Single</a>
		                                        </li>
		                                    </ul>
		                                </li>
		                                <li>
		                                    <ul>
		                                        <li>
		                                            <span class="title">Column 2</span>
		                                        </li>
		                                        <li>
		                                            <a href="#">Single</a>
		                                        </li>
		                                    </ul>
		                                </li>
		                            </ul>
		                        </li>
		                        <li class="has-dropdown">
		                            <a href="#">
		                                Single Dropdown
		                            </a>
		                            <ul>
		                                <li class="has-dropdown">
		                                    <a href="#">
		                                        Second Level
		                                    </a>
		                                    <ul>
		                                        <li>
		                                            <a href="#">
		                                                Single
		                                            </a>
		                                        </li>
		                                    </ul>
		                                </li>
		                            </ul>
		                        </li>
*/ ?>
		                    </ul>
		                </div>
<?php /*
		                <div class="module widget-handle language left">
		                    <ul class="menu">
		                        <li class="has-dropdown">
		                            <a href="#">ENG</a>
		                            <ul>
		                                <li>
		                                    <a href="#">French</a>
		                                </li>
		                                <li>
		                                    <a href="#">Deutsch</a>
		                                </li>
		                            </ul>
		                        </li>
		                    </ul>
		                </div>
*/ ?>
		            </div>
		            
		        </div>
		    </nav>

		</div>
	