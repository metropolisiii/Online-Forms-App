<?php
	session_start();
	include("../includes/settings.php");
	$_POST=sanitize($_POST);
	$result=mysql_query("SELECT form_structure FROM fb_savedforms where id=".$_POST['id']);
	if ($result){
			$structure = mysql_fetch_array($result);
			echo $structure['form_structure'];
			return true;
	}
	else
		return false;