<?php
	//Get current forms belonging to form creator
	//If user is a superadmin, get all forms, else get user's forms
	$query="";
	$current_forms=array();
	$closed_forms=array();
	if ($_SESSION['membertype'] == "superadmin" && empty($_GET['superadmin'])){
		$current_forms_query="SELECT * FROM fb_savedforms WHERE date>=".date('U')." order by name, LENGTH(name) asc";
		$closed_forms_query="SELECT * FROM fb_savedforms WHERE date<".date('U')." order by name, LENGTH(name) asc";
	}
	else{	
		$current_forms_query="SELECT * FROM fb_savedforms LEFT JOIN permissions on formid=fb_savedforms.id WHERE date>=".date('U')." AND sitename='".$forwarded_directory."' AND (user='".$_SESSION['userid']."' AND edit=1) order by name, LENGTH(name) asc "; 
		$closed_forms_query="SELECT * FROM fb_savedforms LEFT JOIN permissions on formid=fb_savedforms.id WHERE date<".date('U')." AND sitename='".$forwarded_directory."' AND (user='".$_SESSION['userid']."' AND edit=1) order by name, LENGTH(name) asc ";
	}
	$current_forms_result=mysql_query($current_forms_query);
	$closed_forms_result=mysql_query($closed_forms_query);
		
	while ($row=mysql_fetch_array($current_forms_result))
		$current_forms[]=$row;
	while ($row=mysql_fetch_array($closed_forms_result))
		$closed_forms[]=$row;
	
	mysql_free_result($current_forms_result);
	mysql_free_result($closed_forms_result);
	