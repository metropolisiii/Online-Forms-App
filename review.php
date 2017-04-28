<?php 
	/**
	* Administrative area to review forms.
	*
	* This area lists forms that the end users have either filled out or started to fill out. The administrator can view form answers and either accept, reject, or reset the forms with AJAX enabled buttons.
	*
	* @author Jason Kirby <jkirby1325@gmail.com>
	*
	*/
	include_once("scripts/settings.php");
	
	include("includes/header.php");
	if ($_SESSION['membertype'] != "admin"){
		echo "You need to <a href='login.php'> login </a> to enter this area";
		exit;
	}
	$_GET=sanitize($_GET);
	include('models/review.php');
	$form_counter=1;
	
	include ('views/review.php');
	include("includes/footer.php");
?>
