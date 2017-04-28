<?php 
	include("../includes/connect.php");
	if (!$_POST)
		exit;
	$_POST=sanitize($_POST);
	
	if ($_POST['link']){
		//If we're resetting password
		$link=$_POST['link'];
		//Get account associated with link
		$result=mysql_query("SELECT id, email, salt FROM accounts WHERE reset_link='{$link}'");
		if (mysql_num_rows($result) > 0){
			$rec=mysql_fetch_object($result);
			$saltedPW =  $_POST['password'].$rec->salt;
			$hashedPW = hash('sha256', $saltedPW);
			mysql_query("UPDATE accounts SET password='{$hashedPW}', reset_link='' WHERE id=".$rec->id);		
			$_SESSION['conf_user']=$rec->email;
			$_SESSION['error']="Your password has been reset and you have been logged in!";
			header("Location: ../index.php");
		}
		else
			exit;
	}
	else{
		//If we're sending the email to reset the password
		$email=$_POST['email'];
		
		$result=mysql_query("SELECT id from accounts where email='{$email}'");
		if (mysql_num_rows($result)==0){
			$_SESSION['error']='This email does not exist in our system. Please fill out and save your Conference registration to obtain access to an account.';
			header("Location: ../reset_password.php");
			exit;
		}		
		else{
			$rec=mysql_fetch_object($result);
			$id=$rec->id;
		}	
		//Generate a random link and store in user's table
		$random_url=generate_random_url();

		
		mysql_query("UPDATE accounts SET reset_link='{$random_url}' WHERE id='{$id}'");
		
		//Create email and send
		include("../../lib/phpmailer.php");
		
		$message_field ="
		Greetings, <br/>
		<br/>
		We are e-mailing in response to your request to reset your password for your mycompany Conference Account. If you did not request a password reset, please ignore this e-mail.<br/>
		<br/>
		In order to reset your account, please click or copy and paste the following link into your browser: {$website}reset_password.php?link={$random_url}<br/>
		<br/>
		Thank you,<br/>
		mycompany
		";
		
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8';
		$mail->AddAddress($email);
		$mail->From = "j.smith@mycompany.com";
		$mail->FromName = "John Smith";
		$mail->Subject = "Your request to reset your password for your mycompany Conference Account";			
		$mail->Body         = $message_field;
		$mail->isHTML(true);    
		$mail->send();
		$_SESSION['error'] = 'An email has been sent to '.$email.'. This email will provide instructions to continue resetting your password.';
		header("Location: ../reset_password.php");
	}
	