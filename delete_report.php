<?php
	session_start();
	if ($_SESSION['membertype'] != "admin"){
		echo "You need to <a href='../login.php'> login </a> to enter this area";
		exit;
	}
	include_once("scripts/settings.php");	
	include("scripts/connect.php");
	include("misc/functions.php");
	include_once("/var/www/forms_app/classes/logController.php");
	
	if ($_SESSION['membertype'] != "admin"){
		echo "You need to <a href='login.php'> login </a> to enter this area";
		exit;
	}	
	$_GET=sanitize($_GET);
	$_POST=sanitize($_POST);
	$log=new logController();
	include('models/delete_report.php');
	header("location:reports.php");
	exit;
	

	