<?php
$session_name="ELDERTECHVISINTERFACE";
session_name($session_name);
session_set_cookie_params('7600');
date_default_timezone_set('America/Chicago');
session_start();
if( (preg_match('^login^', $_SERVER['REQUEST_URI'])!=1&&preg_match('^hart^', $_SERVER['REQUEST_URI'])!=1 && preg_match('^oom^', $_SERVER['REQUEST_URI'])!=1 && preg_match('^density.php^', $_SERVER['REQUEST_URI'])!=1) && (preg_match('^image.php^', $_SERVER['REQUEST_URI'])!=1)|| preg_match('^all_charts.php^', $_SERVER['REQUEST_URI'])==1 ){
	session_regenerate_id(true);
//	print "Regenerating ID";
}
session_cache_limiter('private, must-revalidate');
//session_regenerate_id(true);
//$_SESSION['sessname']=$session_name;

?>