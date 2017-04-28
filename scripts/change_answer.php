<?php
/**
* Changes a form answer via ajax
* @author Jason Kirby <jkirby1325@gmail.com>
*/
session_start();
if (empty($_SESSION['userid'])){
	echo "You need to <a href='../login.php'> login </a> to enter this area";
	exit;
}
include("../misc/functions.php");
include("settings.php");
include("connect.php");
include_once("/var/www/forms_app/classes/logController.php");
$log=new logController();
if (!empty($_POST)){
	//Make sure user has permission to change answers
	$permission_query="SELECT fb_savedforms.id FROM user_form INNER JOIN fb_savedforms on user_form.formid = fb_savedforms.id INNER JOIN permissions ON permissions.formid=fb_savedforms.id WHERE user_form.id={$_POST['form_id']} AND permissions.user='{$_SESSION['userid']}'";
	$permission_result=mysql_query($permission_query);
	if (mysql_num_rows($permission_result) == 0){
		print "no_permission";
		exit;
	}
	$_POST=sanitize($_POST);
	$result=mysql_query("SELECT id FROM form_answers where field_id='{$_POST['field_id']}' AND user_form_id={$_POST['form_id']}");
	if (mysql_num_rows($result) > 0){
		$rec=mysql_fetch_object($result);
		$query="UPDATE form_answers set response='{$_POST['answer']}' where id=".$rec->id;
	}
	else
		$query="INSERT INTO form_answers (field_id, response, user_form_id, custom) VALUES ('{$_POST['field_id']}', '{$_POST['answer']}', {$_POST['form_id']}, 1)";
	mysql_query($query) or die(mysql_error());	
}