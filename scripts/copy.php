<?php
/**
* Makes a copy of form
* @author Jason Kirby <jkirby1325@gmail.com>
*/
	session_start();
	if ($_SESSION['membertype'] != "admin"){
		echo "You need to <a href='../login.php'> login </a> to enter this area";
		exit;
	}
	include("../misc/functions.php");
	include("settings.php");
	include("connect.php");
	include_once("/var/www/forms_app/classes/logController.php");
	$log=new logController();
	if (!empty($_POST)){
		$_POST=sanitize($_POST);
		$result=mysql_query("SELECT * FROM fb_savedforms where id=".$_POST['id']);
		if (mysql_num_rows($result)>0){
			$form=mysql_fetch_array($result);
			$form_structure = str_replace("'", "''", $form['form_structure']);
			$form_structure = str_replace("\\", "\\\\", $form_structure);
			mysql_query("INSERT INTO fb_savedforms (form_structure, userId, name, date, notifyees, accepted_email, declined_email, sitename, accountId, url, form_invisible_message, form_no_reg_message, theme, reports_no_restrictions, thank_you_page_message, num_times_filled_out, thankyou_url, email_confirmation_to_customer, notification_email, email_confirmation_to_administrator,email_confirmation_to_administrator_subject,email_confirmation_to_customer_subject  ) VALUES ('".$form_structure."', '".$form['userId']."', '".$form['name']."_copy', '".$form['date']."','".$form['notifyees']."','".$form['accepted_email']."','".$form['declined_email']."','".$form['sitename']."','".$form['accountId']."','".$form['url']."','".$form['form_invisible_message']."','".$form['form_no_reg_message']."','".$form['theme']."', '".$form['reports_no_restrictions']."','".$form['thank_you_page_message']."','".$form['num_times_filled_out']."','".$form['thankyou_url']."','".$form['email_confirmation_to_customer']."','".$form['notification_email']."','".$form['email_confirmation_to_administrator']."','".$form['email_confirmation_to_administrator_subject']."','".$form['email_confirmation_to_customer_subject']."')");
			//copy physical file and rename accordingly
			$id=mysql_insert_id();
			$f=preg_replace("/[^a-zA-Z0-9_\- ]/", "", $form['name']);
			$newfilename=str_replace("_".$form['id'], "_".$id, $form['filename']);
			$newfilename=str_replace($f."_", $f."_copy_", $newfilename);
			
			$oldfile=file_get_contents("../forms/".$form['filename']);
			$oldfile=preg_replace('/<input type="hidden" name="fid" value="\d+"\/>/', '<input type="hidden" name="fid" value="'.$id.'"/>', $oldfile);
			$fh=fopen("../forms/".$newfilename, "w");
			fwrite($fh, $oldfile);
			fclose($fh);			
			mysql_query("UPDATE fb_savedforms set filename='".$newfilename."' WHERE id=".$id);
			
			//copy permissions
			$result2=mysql_query("SELECT * FROM permissions where formid=".$_POST['id']);
			while ($permission=mysql_fetch_array($result2))
				mysql_query("INSERT INTO permissions (user, `group`, formid, edit, view_report) VALUES ('".$permission['user']."', '".$permission['group']."', ".$id.", ".$permission['edit'].", ".$permission['view_report'].")");
			$log->log("Copied form ".$form['name']);
		}
	}
?>