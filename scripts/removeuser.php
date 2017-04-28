<?php
/**
* Removes an administrator
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
if (!empty($_POST)){
	$_POST=sanitize($_POST);
	if ($_POST['operation']==='edit'){
		foreach($_POST['record'] as $key=>$value){
			$requires_login=($_POST['requires_login'][$key]?1:0);
			mysql_query("UPDATE accounts set name='".$_POST['account'][$key]."', url='".$_POST['url'][$key]."', requires_login=".$requires_login.", groups='".$_POST['groups'][$key]."' WHERE id=".$key); 
			$log->log($_SESSION['userid']." changed ".$_POST['name'][$key]."'s account information.");
		}
		
	}
	else if ($_POST['operation']==='delete'){
		foreach($_POST['record'] as $key=>$value){
			mysql_query("DELETE FROM accounts WHERE id='".$key."'"); 
			$log->log($_SESSION['userid']." removed ".$_POST['name'][$key]."'s account information.");
		}
		foreach($_POST['user'] as $key=>$value){
			mysql_query("DELETE FROM admins WHERE id='".$key."'"); 
			$log->log($_SESSION['userid']." removed ".$_POST['name'][$key]."'s admin information.");
		}
		
	}
}
header("Location: ../users.php");