<?php
	/**
	* Backend script to save form structure to database.
	*
	* Intermediatery file that is called via AJAX to gather and format data about the form and save it to the fb_savedforms table. Most of the work is done in the save_form method which exists in both Formbuilder.php and Formbuilder_pdo.php
	*
	* @author Jason Kirby <jkirby1325@gmail.com>
	*
	*/
	
	session_start();
	if (empty($_POST) && ($_SESSION['membertype'] != "admin" && $_SESSION['membertype'] != 'superadmin'))
		exit;
	require_once("scripts/settings.php");
	require('Formbuilder/Formbuilder.php');
	require('Formbuilder/Formbuilder_pdo.php');
	require('misc/functions.php');
	include_once("/var/www/forms_app/classes/logController.php");
	$log=new logController();

	$_POST['savename'] = preg_replace("/[^a-zA-Z0-9_\-\*,\. ]/", "", $_POST['savename'] ); //Formats the name of the form which will also have a part in the html file and name that is created for this form.
	$_POST=sanitize_leave_html($_POST); //Sanitizes the $_POST variable but leaves in html tags in escaped form.
	$form_data = isset($_POST['frmb']) ? $_POST : false;
    $form = new Formbuilder_pdo($form_data); //Builds the form structure
   	if ($mode=="test")
		$form->connect("mysql:host=127.0.0.1; dbname=forms_app_test");
	else
		$form->connect();
	$groups=str_replace('&quot;','"', $_POST['groupperms']);
	$users=str_replace('&quot;','"', $_POST['userperms']);	
	if (!$_POST['notification_email'])
		$_POST['notification_email'] = $form_email;
	
	$save=$form->save_form($_POST['savename'], $_POST['date'], $_POST['userId'], $_POST['enabled'], $_POST['notifyees'], $_POST['notification_email'],  $_POST['visible'], $_POST['accepted_email'], $_POST['declined_email'], $_POST['sitename'], $_SESSION['account'], $_POST['url'], $_POST['form_invisible_message'], $_POST['form_no_reg_message'], $_POST['thank_you_page_message'],json_decode($groups), json_decode($users),$_POST['no_restrictions'], $_POST['num_times_filled_out'], $_POST['thankyou_url'], $_POST['email_confirmation_to_administrator'], $_POST['email_confirmation_to_customer'],$_POST['email_confirmation_to_administrator_subject'],$_POST['email_confirmation_to_customer_subject'], $_POST['invoice'], $_POST['theme']);	
	$log->log("Saved a form named ".$_POST['savename']);
	print($save); //Returns the id of the database entry in fb_savedforms	
?>