<?php 
	
	include("../includes/connect.php");
	require_once __DIR__ . '/../../lib/recaptcha/src/autoload.php';
	$siteKey = '6Lc9VtwSAAAAAH_-JVWJ_SoS6GcXcPSj1EY9MfxS';
	$secret = '6LdYiOgSAAAAACrmawjcxWIB6_joX3ituZSrpQPw';
	$_POST=sanitize($_POST);
	$email=$_POST['email'];
	$headers = 'From: no-reply@mycompany.com' . "\r\n" .
    'Reply-To: no-reply@mycompany.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
	
	/* Verify Captcha */
	
	if (isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response'] != ''){
		$recaptcha = new \ReCaptcha\ReCaptcha($secret);
		$resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
		if (!$resp->isSuccess()){
			$_SESSION['error'] = "Captcha response incorrect. Please try again";
			header("Location: ../index.php?id={$_POST['url']}");
			exit;
		}
	}
	else{
		$_SESSION['error'] = "Please fill out the captcha";
		header("Location: ../index.php?id={$_POST['url']}");
		exit;
	}
	//Check for duplicate accounts
	$result=mysql_query("SELECT * FROM accounts WHERE email='{$email}'");
	
	if (mysql_num_rows($result) == 0){
		$blacklist = array('yahoo.com','gmail.com');
		//If no duplicate account, create the account
		//Makes sure this is not a gmail or yahoo account
		for ($i=0; $i<count($blacklist); $i++){
			if (strpos($_POST['email'], $blacklist[$i]) !== FALSE){
				$_SESSION['error'] = "You must use a company email address. Email from general domains are not allowed.";
				header("Location: ../index.php?id={$_POST['url']}");
				exit;
			}
		}
		$salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
		$saltedPW =  $_POST['password'] . $salt;
		$hashedPW = hash('sha256', $saltedPW);
		mysql_query("INSERT INTO accounts  (email, password, salt, reset_link, invoice_total,total_paid,admin, invoice_version) VALUES('{$email}', '{$hashedPW}','{$salt}','','','',0,0)");
		//mail("jason.kirby@mycompany.com","test","INSERT INTO accounts  (email, password, salt, reset_link, invoice_total,total_paid,admin) VALUES('{$email}', '{$hashedPW}','{$salt}','','','',0)");
		$account_id = mysql_insert_id();
		mysql_query("INSERT INTO forms VALUES(NULL, $id, $account_id)");
		$_SESSION['conf_user']=$email;
		mail("jason.kirby@mycompany.com", "User has created a Conferences Account", "{$email} has created a Conferences Account");
		$_SESSION['error']="Your account has been created! Now it's time to sign up for mycompany Summer Conference";
		header("Location: ../index.php");
	}
	else{
		//If there is a duplicate account, let user know account exists and provide a link to reset password
		$_SESSION['account_exists']=true;
		header("Location: ../index.php?id={$_POST['url']}");
	}