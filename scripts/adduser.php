<?php
/**
* Add an administrator
* @author Jason Kirby <jkirby1325@gmail.com>
*/
session_start();
if (empty($_SESSION['superadmin'])){
	echo "You need to <a href='../login.php'> login </a> to enter this area";
	exit;
}
include("../misc/functions.php");
include("settings.php");
include("connect.php");
include_once("/var/www/forms_app/classes/logController.php");
$log=new logController();
if (!empty($_POST) && !empty($_POST['user'])){
	$_POST=sanitize($_POST);
	$result=mysql_query("SELECT id FROM admins where user='".$_POST['user']."' AND accountId=".$_POST['adminaccount']);
	if (mysql_num_rows($result)==0){
		mysql_query("INSERT INTO admins (user, accountId) VALUES ('".$_POST['user']."', ".$_POST['adminaccount'].")");
		$log->log($_SESSION['userid']." add ".$_POST['user']." as a form administrator.");
	}
}
header("Location: ../users.php");