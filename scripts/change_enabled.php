<?php
/**
* Sets the enabled status of the form. If enabled, users can fill it out.
* @author Jason Kirby <jkirby1325@gmail.com>
*/
	session_start();
	if ($_SESSION['membertype'] != "admin"){
		echo "You need to <a href='../login.php'> login </a> to enter this area";
		exit;
	}
	include("../misc/functions.php");
	include("settings.php");
	include("connect.php");
	include_once("/var/www/forms_app/classes/logController.php");
	$log=new logController();
	
	if (!empty($_POST)){
		$_POST=sanitize($_POST);
		mysql_query("UPDATE fb_savedforms set enabled=".$_POST['status']." WHERE id=".$_POST['id']);
		$query=mysql_query("SELECT name from fb_savedforms WHERE id=".$_POST['id']);
		$form=mysql_fetch_array($query);
		$logstring=($_POST['status']==1)?"Enabled":"Disabled";
		$log->log($logstring." registration for form ".$form['name']);
	}
?>