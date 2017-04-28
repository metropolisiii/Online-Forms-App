<?php 
	/* 
	* Administrative module for  sharing reports
	* @author Jason Kirby <jkirby1325@gmail.com>
	*/
	include_once("scripts/settings.php");
	include("includes/header.php");
	$_GET=sanitize($_GET);
		
	if ($_SESSION['membertype'] != "admin"){
		echo "You need to <a href='login.php'> login </a> to enter this area";
		exit;
	}	
	include('models/share_report.php');	
	
	include ('views/share_report.php');
	include("includes/footer.php");
?>
