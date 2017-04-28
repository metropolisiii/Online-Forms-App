<?php
/**
* Add permissions to a report
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
if ($_POST['action'] == 'delete'){
	//Make sure the user has the permissions to delete permissions	
	$permission_query="SELECT reports.username from permissions INNER JOIN reports ON reports.id=permissions.reportid WHERE permissions.id={$_POST['id']}";
	$result=mysql_query($permission_query);
	$report=mysql_fetch_object($result);	
	if ($report->username == $_SESSION['userid']){
		$delete_query="DELETE FROM permissions WHERE id={$_POST['id']}";
		mysql_query($delete_query);
	}
	mysql_free_result($result);
}
else{
	if (!empty($_POST['user'])){
		$_POST=sanitize($_POST);
		$result=mysql_query("SELECT id FROM permissions where user='{$_POST['user']}' AND reportid={$_POST['reportid']}");
		if (mysql_num_rows($result)==0){
			$insert_report_query = "INSERT into permissions VALUES (NULL, '{$_POST['user']}', '', 0, 0, 1, {$_POST['reportid']})";
			mysql_query($insert_report_query);
			echo mysql_insert_id();
		}
		mysql_free_result($result);
	}
}

