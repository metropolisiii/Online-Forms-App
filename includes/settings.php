<?php
/**
* Global settings of app
* @author Jason Kirby <jkirby1325@gmail.com>
*/
	session_start();
	$_SESSION['redirectpage']=$_SERVER["REQUEST_URI"];
	$user_path = '/var/www/html/forms_app/';
	include_once($user_path."misc/functions.php");
	include_once("/var/www/forms_app/classes/logController.php");
	$mode='test';
	date_default_timezone_set('America/Denver'); 

	if (!empty($_GET['account']) && $_GET['account'] != 'generic')
		$forwarded_directory=$_GET['account'];
	else
		$forwarded_directory=$_SESSION['forwarded_directory'];
	
	if ($forwarded_directory=="forms_app_test" || $forwarded_directory=="rfi_test" ){
		ini_set("display_errors","1");
		$mode='test'; //Test or Production		
	}
	$db_name=($mode=='test')?'forms_app_test':'forms_app';	
	$db_user='forms_app';
	$db_pass='forms_app34343';
	$db_host='localhost';
	$logfile="/var/log/forms_app/log.txt";
	$form_replacement='form';
	$form_email="no-reply@mycompany.com";
	$theme="standard";
	$superusers=array("jsmith","user1","user2");
	if (strpos(htmlspecialchars($_SERVER['PHP_SELF']), 'login.php'))
		$loginpage=true;
	
	include_once($user_path."includes/connect.php");
	
	if (!isset($_SESSION['user_login_required']) || empty($_SESSION['user_login_required'])){
		$result=mysql_query("SELECT requires_login FROM accounts WHERE url LIKE '".$forwarded_directory."'");
		$user_login_requred_results=mysql_fetch_array($result);
		$_SESSION['user_login_required']=($user_login_requred_results['requires_login']==1)?true:false;
	}
	if (!isset($_SESSION['theme']) || $loginpage){
		$result=mysql_query("SELECT theme FROM accounts WHERE url LIKE '".$forwarded_directory."'");
		$theme_results=mysql_fetch_array($result);
		$_SESSION['theme']=($theme_results['theme'] != "")?$theme_results['theme']:'mycompany';
	}
	if (isset($_SESSION['user_login_required']) && empty($_SESSION['userid']) && !$loginpage){ //If the user is required to be logged in and he hasn't logged in, we need to direct him to the login page.
		header("Location: login.php");
		exit;
	}	
	$log=new logController();
	if ($_GET['forwarded_directory'])
		$_SESSION['forwarded_directory']=$_GET['forwarded_directory'];
	checkSession();
			
	
?>
