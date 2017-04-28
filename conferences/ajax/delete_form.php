<?php 
	$title="";
	include("../includes/connect.php");
	if (!$_POST)
		exit;
	$_POST=sanitize($_POST);
	mysql_query("DELETE from forms_app.form_answers WHERE user_form_id=".$_POST['id']);
	mysql_query("DELETE from forms_app.user_form WHERE id=".$_POST['id']);
	mysql_query("DELETE from forms WHERE user_form_id=".$_POST['id']);
	
	
	
	