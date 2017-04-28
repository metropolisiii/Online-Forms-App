<?php 
	include("../includes/connect.php");
	if (!$_POST)
		exit;
	$_POST=sanitize($_POST);
	
	if ($_POST['link']){
		//If we're resetting password
	}
	else{
		//If we're sending the reset email
	//Get user's email by URL number
	$result=mysql_query("SELECT response from forms_app.form_answers INNER JOIN forms_app.user_form ON user_form_id = user_form.id  WHERE url='".$_POST['id']."' and field_id='email_address'");
	if (mysql_num_rows($result)==0)
		exit;
	else{
		$rec=mysql_fetch_object($result);
		$email=$rec->response;
		
	}	
	//Generate a random link and store in user's table
	$random_url=generate_random_url();

	
	mysql_query("UPDATE accounts SET reset_link='{$random_url}' WHERE email='{$email}'");
	
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
	
	
	