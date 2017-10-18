<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Footer template
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

global $cachedate, $cachepwd;

?>
<?php do_action('action_bottom_script'); ?>
</body>
</html>
<?php do_action('action_footer_bottom'); ?>

<?php if(!is_bot()&&!empty($cachedate)) { ?>
<!-- Result cache date: <?php echo $cachedate; ?> -->
<!-- Result cachefile: <?php echo $cachepwd; ?> -->
<?php } ?>
