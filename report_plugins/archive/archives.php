<?php
	session_start();
	include('misc/functions.php');
	include('misc/connect.php');
	
	if ($_SESSION['membertype'] != "admin"){
		echo "You need to <a href='login.php'> login </a> to enter this area";
		exit;
	}
	$_GET=sanitize($_GET);
	$_POST=sanitize($_POST);
	$reportid = $_GET['id'];
	$userid = $_SESSION['userid'];
	
	include('models/archives.php');
	
	include ('views/archives.php');
	include("../../includes/footer.php");	
?>
