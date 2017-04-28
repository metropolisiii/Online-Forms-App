<?php
	/**
	* Header for script areas
	* @author Jason Kirby <jkirby1325@gmail.com>
	*/
	session_start();
	if ($_SESSION['membertype'] != "admin"){
		echo "You need to <a href='../login.php'> login </a> to enter this area";
		exit;
	}
	include("connect.php");
	include("../misc/functions.php");
	include ("../classes/logController.php");
	$log=new logController();
	$log->log("Accessed ".$_SESSION['forwarded_directory']."/".$_SERVER["SCRIPT_NAME"]);
?>