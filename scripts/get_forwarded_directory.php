<?php
/**
* Determines which site this app is being accessed from (if reverse proxied).
* @author Jason Kirby <jkirby1325@gmail.com>
*/	
	session_start();
	include("../misc/functions.php");
	$_POST=sanitize($_POST);
	$path_parts=explode("/",$_POST['forwarded_url']);
	if ($_SESSION['forwarded_directory'] != $path_parts[1])
		$_SESSION['forwarded_directory'] = $path_parts[1];
	

?>