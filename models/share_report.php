<?php
	/**
	*	
	*	@author Jason Kirby <jkirby1325@gmail.com>
	*/

	
	//If no id is provided, send the user back to the reports page with notification
	if (!$_GET['id']){
		$_SESSION['notification'] = "Please select a report to share. ";
		echo "<script>window.location='reports.php';</script>";
		exit;
	}
	
	//Get permissions
	$permission_query = "SELECT id FROM reports WHERE username='{$_SESSION['userid']}' AND id={$_GET['id']}";
	$permission_result = mysql_query($permission_query);
	
	//If user does not have permission to make modifications to a report, send back to reports page with notification
	if (mysql_num_rows($permission_result) == 0){
		$_SESSION['notification'] = "You do not have the permissions to share this report. ";
		echo "<script>window.location='reports.php';</script>";
		exit;
	}
	
	//Get report name
	$report_query = "SELECT name FROM reports where id={$_GET['id']}";
	$report_result = mysql_query($report_query);
	$report = mysql_fetch_object($report_result);
	$report_name=$report->name;
	
	//Get permissions for the current form
	$permission_query = "SELECT id, user FROM permissions WHERE reportid={$_GET['id']}";
	$permission_result = mysql_query($permission_query);
	