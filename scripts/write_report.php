<?php
/**
* Writes modified form structure to database when a user changes report column configuration. Also saves form to CSV to be downloaded by user.
* @author Jason Kirby <jkirby1325@gmail.com>
*/
	session_start();
	include("settings.php");
	include("connect.php");
	include("../misc/functions.php");
	include_once("/var/www/forms_app/classes/logController.php");
	$_GET=sanitize($_GET);
	
	$user=(empty($_SESSION['userid'])?$_GET['user']:$_SESSION['userid']);
	$log=new logController();
	if (!empty($_POST)){
		$_POST=sanitize($_POST);
	
		foreach($_POST['data'][0] as $key=>$value)
			$_POST['data'][0][$key]=trim(str_replace("Include in CSV","",$value));
		
		//Parse columns to include in CSV
		$columns=explode("&", $_POST['columns']);
		foreach ($columns as $column){
			$c=explode("=", $column);
			$col[]=$c[0];
		}
		
		$result=mysql_query("SELECT name FROM fb_savedforms where id=".$_POST['formid']);
		$form=mysql_fetch_array($result);
		$name=str_replace(" ","-", $form['name']);
		if (!empty($user)){
			$result=mysql_query("SELECT id FROM users_reports WHERE userid='".$user."' AND formid=".$_POST['formid']);
			if (mysql_num_rows($result)>0){
				$rec=mysql_fetch_array($result);
				mysql_query("UPDATE users_reports set form_structure='".serialize($_POST['data'][0])."' WHERE id=".$rec['id']);
			}
			else
				mysql_query("INSERT INTO users_reports (userid, formid, form_structure) VALUES ('".$user."', ".$_POST['formid'].", '".serialize($_POST['data'][0])."')");
		}
		$csv='"'.$name.'"'."\n";
		$fh=fopen("../tmp/".$name."_".date('mdy').".csv","w");
	
		$datacount=count($_POST['data']);
		
		foreach ($_POST['data'][0] as $key=>$value){
			if (in_array(str_replace(" ", "_",strtolower($value)), $col) || in_array(str_replace(" ", "_",$value), $col )){
				for ($i=0; $i<$datacount; $i++)
					$data[$i][]=$_POST['data'][$i][$key];
			}
		}
		
		foreach ($data as $value){
			fputcsv($fh, $value);
		}
		$log->log("Saved report #".$_POST['formid']." to csv");
		fclose($fh);
	}
?>