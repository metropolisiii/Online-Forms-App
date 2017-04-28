<?php 
	include("settings.php");
	include("../includes/connect.php");
	if (!$_POST)
		exit;
	$_POST=sanitize($_POST);
	$email=$_POST['email'];
	$password=$_POST['password'];
	
	$result=mysql_query("SELECT salt FROM accounts WHERE email='{$email}'");
	$rec=mysql_fetch_object($result);
	$saltedPW=$password.$rec->salt;
	$hashedPW=hash('sha256', $saltedPW);
	$result=mysql_query("SELECT email, admin from accounts WHERE email='{$email}' AND password='{$hashedPW}'");
	if (mysql_num_rows($result)==0){
		$_SESSION['error']='Invalid email or password';
	}
	else{
		$rec = mysql_fetch_object($result);
		if ($rec->admin == 1){
			$_SESSION['type'] = 'admin';
			$_SESSION['conf_user']=$email;
		}
		if (IS_OPEN)
			$_SESSION['conf_user']=$email;
		if (in_array($email, $superadmins)){
			$_SESSION['superadmin']=$email;
			$_SESSION['conf_user']=$email;
		}
	}
	header("Location: ../index.php");