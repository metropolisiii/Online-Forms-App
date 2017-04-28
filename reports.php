<?php 
	/* 
	* Administrative module for reports
	* @author Jason Kirby <jkirby1325@gmail.com>
	*/
	$selected='reports_beta'; //For enabled tab
	include_once("scripts/settings.php");
	include("includes/header.php");
	$_GET=sanitize($_GET);
	$_POST=sanitize($_POST);
	include('models/reports.php');
	if ($_SESSION['membertype'] != "admin"){
		echo "You need to <a href='login.php'> login </a> to enter this area";
		exit;
	}		
	include ('views/reports.php');
	include("includes/footer.php");
	
	
?>
