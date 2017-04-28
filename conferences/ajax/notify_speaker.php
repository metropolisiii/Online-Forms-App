<?php
	include("../../lib/phpmailer.php");
	include("../../scripts/settings.php");
	include("../../scripts/connect.php");
	include("../../misc/functions.php");
	
	if (!$_POST['fid']){
		exit;
	}
	$field_list="";
	foreach ($_POST as $key=>$value){				
		if (!in_array($key, $excludefields)){
			$value=str_replace("\\r\\n", "<br/>", $value);
			$value=str_replace("\\'","'", $value);
			$value=str_replace('\\"','"', $value);
			if (strlen($field_list) > 700){
				$field_list.="\r\n";
			}
			if (is_array($value)){
				$v="";
				foreach ($value as $val)
					$v.=$val." ";
				$value=$v;
			}
			$field_list.=str_replace("_"," ",$key).": ".$value."<br/><br/>";				
		}
		
	}
	$result=mysql_query("SELECT email_confirmation_to_administrator_subject, email_confirmation_to_administrator, name, notification_email WHERE id=".$_POST['fid']); //Get notifyees to send emails to.
	$savedform=mysql_fetch_array($result);
	$subject_field = prepareMessage($savedform['email_confirmation_to_administrator_subject'], 'Form for '.$savedform['name'], false);
	$message_field = htmlspecialchars_decode(prepareMessage($savedform['email_confirmation_to_administrator'], "", $field_list));
	$message_field = wordwrap($message_field, 600,"\r\n");
	$mail = new PHPMailer();
	$mail->CharSet = 'UTF-8';
	$mail->AddAddress($_POST[$_POST['Title_of_the_Session_You_Are_Speaking_On']]);

	if ($savedform['notification_email'])
		$form_email=$savedform['notification_email'];
	$mail->From         = $form_email;
	$mail->FromName     = $form_email;
	$mail->Subject      =  $subject_field;			
	$mail->Body         = $message_field;
	$mail->isHTML(true);    
	
	if ($filename){
		foreach($filename as $key=>$value){
			if ($value)
				$mail->AddAttachment($value);
		}
	}
	$mail->send();

?>