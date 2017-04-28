<?php
/**
* Writes modified form structure to database when a user changes report column configuration. Also saves form to CSV to be downloaded by user.
* @author Jason Kirby <jkirby1325@gmail.com>
*/
	include("../includes/settings.php");
	$_GET=sanitize($_GET);
	$user=(empty($_SESSION['userid'])?$_GET['user']:$_SESSION['userid']);
	
	if (!empty($_POST)){
		$_POST=sanitize($_POST);
		$result=mysql_query("SELECT name FROM fb_savedforms where id=".$_POST['formid']);
		$form=mysql_fetch_array($result);
		$name=$form['name'];
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
		foreach ($_POST['data'] as $value)
				fputcsv($fh, $value);
		$log->log("Saved report #".$_POST['formid']." to csv");
		fclose($fh);
	}
?>