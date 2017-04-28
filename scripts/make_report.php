<?php
	/**
	* Insert fields into database used to create report
	* @auther Jason Kirby <jkirby1325@gmail.com>
	*/
	session_start();
	
	function array_escape($input) {
		if (is_array($input)) {
			foreach($input as $var=>$val) {
				$output[$var] = array_escape($val);
			}
		}
		else {
			$output =htmlspecialchars(trim($input), ENT_QUOTES);
		}
		return $output;
	}
	function format_field($label){
		$fieldid = preg_replace("/&lt;.+?&gt;/is", "", str_replace(array(" ","."), "_", $label) );
		$fieldid= preg_replace("/<.+?>/is", "", str_replace(" ", "_", $fieldid) ) ;
		$fieldid=html_entity_decode($fieldid, ENT_QUOTES);
	
		$patterns = array(); //JK Mod
		$patterns[0] = '/[^a-zA-Z0-9_-]+/';//JK Mod
		$replacements = array(); //JK Mod
		$replacements[0] = ''; //JK Mod     
		$fieldid = preg_replace($patterns, $replacements, trim($fieldid));//JK Mod
		mail("jason.kirby@mycompany.com","test",$fieldid);
		return $fieldid;
	}
	
	if ($_SESSION['membertype'] != "admin"){
		echo "You need to <a href='../login.php'> login </a> to enter this area";
		exit;
	}

	include("settings.php");
	include("connect.php");
	include("../misc/functions.php");

	if (!empty($_POST)){
		$id="";
		$_POST=sanitize($_POST);
		$_POST=array_escape($_POST);
		$query="";
		if ($_POST['report_id'] != ''){ //If we're updating a report, update the static fields
			$query = "UPDATE reports SET `name`='{$_POST['name']}', `username`='{$_SESSION['userid']}', `match`='{$_POST['match']}' WHERE id={$_POST['report_id']}";
			$id=$_POST['report_id'];	
		}
		else{ //If we're creating a report, insert the static fields
			$query = "INSERT INTO reports (name, username, `match`) VALUES ('{$_POST['name']}', '{$_SESSION['userid']}','{$_POST['match']}')";
		}
		echo $query;
		mysql_query($query);
		if ($id == ""){
			$id=mysql_insert_id();
			//Give the main user permission to view and edit the report
			$permission_query = "INSERT INTO permissions VALUES (NULL, '{$_SESSION['userid']}', '','',1,1,{$id})";
			mysql_query($permission_query);
		}
		//Prepare the dynamic fields. Delete them and reinsert them to make changes easier
		mysql_query("DELETE FROM report_fields WHERE report_id={$id}");
	
		for ($i=0; $i<count($_POST['form_id']); $i++){
			$field_name = format_field($_POST['field'][$i]);
			$query="INSERT INTO report_fields VALUES (NULL, {$id}, {$_POST['form_id'][$i]}, '{$field_name}', '{$_POST['as'][$i]}', {$i}, '{$_POST['js_id'][$i]}')";
			mysql_query($query);
		}
	}
		
	$_SESSION['notification'] = "Your report has been updated.";
	
	header("Location:../make_report.php?id=".$id);
	exit;