<?php
	session_start();
	include ("../misc/connect.php");
	include ("../misc/functions.php");
	
	$_POST = sanitize($_POST);
	
	//Check if user is logged in and has access to the form
	$result = mysql_query("SELECT id FROM permissions WHERE reportid = {$_POST['reportid']} AND user = '{$_SESSION['userid']}'");
	if (mysql_num_rows($result) == 0)
		exit;
	
	for ($i=0; $i<count($_POST['data']); $i++){
		mysql_query("DELETE from archives where report_id={$_POST['reportid']} AND user_form_id={$_POST['data'][$i]['form_id']}") or die(mysql_error());
	}