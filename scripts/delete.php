<?php
/**
* Deletes a form
* @author Jason Kirby <jkirby1325@gmail.com>
*/
	session_start();
	if ($_SESSION['membertype'] != "admin"){
		echo "You need to <a href='../login.php'> login </a> to enter this area";
		exit;
	}
	include("settings.php");
	include("../misc/functions.php");
	include("connect.php");
	include_once("/var/www/forms_app/classes/logController.php");
	$log=new logController();
	if (!empty($_POST)){
		$_POST=sanitize($_POST);
		$query=mysql_query("SELECT name, filename from fb_savedforms WHERE id=".$_POST['id']);
		$form=mysql_fetch_array($query);
		mysql_query("DELETE FROM fb_savedforms where id=".$_POST['id']);
		//Delete permissions associated with form
		mysql_query("DELETE FROM permissions WHERE formid=".$_POST['id']);
		//Delete physical file
		unlink("../forms/".$form['filename']);
		$log->log("Deleted form ".$form['name']);
	}
	header("Location: ../admin.php");
?>