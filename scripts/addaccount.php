<?php
/**
* Add an account
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
if (!empty($_POST) && !empty($_POST['account']) && !empty($_POST['url']) && !empty($_POST['requireslogin'])){
	$_POST=sanitize($_POST);
	$result=mysql_query("SELECT id FROM accounts where name='".$_POST['account']."'");
	if (mysql_num_rows($result)==0){
		//Get directory of URL
		$url=explode("/", rtrim($_POST['url'],'/'));
		$url=$url[count($url)-1];
		
		//Format requires_login
		$requires_login=($_POST['requireslogin']==='y'?1:0);
		mysql_query("INSERT INTO accounts (name, url, requires_login, groups) VALUES ('".$_POST['account']."','".$url."', ".$requires_login.", '".$_POST['groups']."')");
		$log->log($_SESSION['userid']." added ".$_POST['account']." as an account.");
	}
}
header("Location: ../users.php");