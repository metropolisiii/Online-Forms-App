<?php
/**
* Updates user's form record as to whether it is accepted or declined. Returns the field the name and acceptance status of the user
* @author Jason Kirby <jkirby1325@gmail.com>
*/
	session_start();
	if ($_SESSION['membertype'] != "admin"){
		echo "You need to <a href='../login.php'> login </a> to enter this area";
		exit;
	}
	include("settings.php");
	include("connect.php");
	include("../misc/functions.php");
	include_once("/var/www/forms_app/classes/logController.php");
	$log=new logController();
	
	if (!empty($_POST)){
		$_POST=sanitize($_POST);
		$field=explode(",", $_POST['status']);
		foreach($field as $value){
			$fields=explode(":", $value);
			$status[trim($fields[0])]=trim($fields[1]);
		}
		if ($status['status']=='accept'){
			$accepted=1;
			$log->log("Accepted form ".$status['url']." which was filled out by ".$status['userid']);
		}
		else if ($status['status']=='reject'){
			$accepted=0;
			$log->log("Declined form ".$status['url']."  which was filled out by ".$status['userid']);
		}
		else if ($status['status']=='reset'){
			$accepted='NULL';
			$log->log("Reset form ".$status['url']."  which was filled out by ".$status['userid']);
			mysql_query("UPDATE user_form set submitted=".$accepted." WHERE userid='".$status['userid']."' AND formid=".$_POST['form_id']." AND url='".$status['url']."'");
		}
		else if ($status['status']=='delete'){
			$log->log("Deleted form answers from form ".$status['url']."  which was filled out by ".$status['userid']);
			mysql_query("DELETE FROM user_form WHERE userid='".$status['userid']."' AND formid=".$_POST['form_id']." AND url='".$status['url']."'");
			print('{"status":"deleted"}');
			exit;
		}
		mysql_query("UPDATE user_form set accepted=".$accepted." WHERE userid='".$status['userid']."' AND formid=".$_POST['form_id']." AND url='".$status['url']."'");
		print('{"userid":"'.$status['userid'].'","status":"'.$status['status'].'","url":"'.$status['url'].'"}');
	}
?>