<?php
	if (!$_GET['id']){
		$_SESSION['notification']="You have not selected a report to delete.";
		echo '<script>window.location="reports.php"</script>';
		exit;
	}
	if (!empty($_GET)){
		$report_query="SELECT id FROM reports WHERE id={$_GET['id']} AND username = '{$_SESSION['userid']}'";
		$result=mysql_query($report_query);
		if (mysql_num_rows($result) > 0){
			mysql_query("DELETE FROM reports where id={$_GET['id']}");
			mysql_query("DELETE FROM report_fields where report_id={$_GET['id']}");
			$log->log("Deleted report ".$_GET['id']);
		}
		else
			$_SESSION['notification'] = "You are not allowed to delete this report!";
	}