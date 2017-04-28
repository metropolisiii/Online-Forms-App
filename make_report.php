<?php 
	/* 
	* Administrative module for creating reports
	* @author Jason Kirby <jkirby1325@gmail.com>
	*/
	include_once("scripts/settings.php");
	
	include("includes/header.php");
	if ($_SESSION['membertype'] != "admin"){
		echo "You need to <a href='login.php'> login </a> to enter this area";
		exit;
	}
	$_GET=sanitize($_GET);
	$_POST=sanitize($_POST);
	include('models/make_report.php');
	$notification="";
	if ($_SESSION['notification']){
		$notification=$_SESSION['notification'];
		unset($_SESSION['notification']);
	}
	include ('views/make_report.php');
	include("includes/footer.php");
?>
