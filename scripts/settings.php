<?php
/**
* Global settings of app
* @author Jason Kirby <jkirby1325@gmail.com>
*/
	session_start();
	
	date_default_timezone_set('America/Denver'); 

	if (!empty($_GET['account']) && $_GET['account'] != 'generic')
		$forwarded_directory=$_GET['account'];
	else
		$forwarded_directory=$_SESSION['forwarded_directory'];
	
	if ($forwarded_directory=="forms_app_test" || $forwarded_directory=="rfi_test" ){
		$mode='test'; //Test or Production		
	}
	else
		$mode='production';
	if ($forwarded_directory=="forms_app_test" || $forwarded_directory=="forms_app")
		$generic_fd=true;
		
	$db_name=($mode=='test')?'forms_app_test':'forms_app';	
	$db_user='forms_app';
	$db_pass = trim(file_get_contents('/etc/forms_app_key.txt'));
	$db_host='localhost';
	$logfile="/var/log/forms_app/log.txt";
	$form_replacement='form';
	$form_email="no-reply@mycompany.com";
	$theme="standard";
	
	$superusers=array("jsmith","user1","user2");
	$conference_admin = array("name"=>"John Smith", "email"=>"j.smith@mycompany.com");
	
	
?>
