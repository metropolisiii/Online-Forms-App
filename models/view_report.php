<?php
	/**
	*	
	*	@author Jason Kirby <jkirby1325@gmail.com>
	*/
	
	
	/**
	* Create a data structure of the where conditional to use to gather data
	*/
	
	
	//Get report
	//Check if user has permission to view report
	if (!$_GET['id']){
		$_SESSION['notification']="You have not selected a report to view.";
		echo '<script>window.location="reports.php"</script>';
		exit;
	}
	$permission_query="SELECT id FROM permissions WHERE user='{$_SESSION['userid']}' AND reportid={$_GET['id']}";
	$permission_result=mysql_query($permission_query);
	if (mysql_num_rows($permission_result) == 0){
		$_SESSION['notification']="You do not have permission to view this report.";
		echo '<script>window.location="reports.php"</script>';
		exit;
	}
	
	$report=createReport($_GET['id'], $_SESSION['userid'], $_GET['to_date'], $_GET['from_date']);
