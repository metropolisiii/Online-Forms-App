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
		ini_set("display_errors","1");
		$mode='test'; //Test or Production		
	}
	else
		$mode='production';
	if ($forwarded_directory=="forms_app_test" || $forwarded_directory=="forms_app")
		$generic_fd=true;
		
	$db_name=($mode=='test')?'forms_app_test':'forms_app';	
	$db_user='forms_app';
	$db_pass='AtMzhttUUfzffvRm';
	$db_host='localhost';
	$logfile="/var/log/forms_app/log.txt";
	$form_replacement='form';
	$form_email="no-reply@mycompany.com";
	$theme="standard";
	switch ($forwarded_directory){
		case 'forms_app':
			$admins=array('jsmith','user1','rzigrino');
			$groups=array('cl-employees');
			$user_login_required=false;			
			break;
		case 'minerals':
			$admins=array('jsmith','user1','cpoland', 'khiatt', 'skrauss', 'jbrock','kkramer');
			$groups=array('ConflictMineralsRFI');
			$user_login_required=false;
			$form_replacement='RFI';
			break;
		case 'mycompany':
			$admins=array('jsmith');
			$user_login_required=false;
			$stylesheet="mycompany";
			break;
		case 'rfi_test':
			$admins=array('jsmith');
			$user_login_required=false;
			$theme="mycompany";
			break;
		case 'forms':
			$admins=array('jsmith','rzigrino','ewinters', 'user1', 'psesock','bthao');
			$user_login_required=false;
			$theme="mycompany";
			break;
		case 'cablenet':
			$admins=array('jsmith', 'asmith', 'khiatt');
			$user_login_required=false;
			$theme='cablenet';
			break;
		case 'surveys':
			$admins=array('jsmith', 'user1');
			$user_login_required=false;
			$theme='mycompany';
			break;
		default:
			$admins=array('jsmith','user1');
			$user_login_required=false;
			break;
	}

?>