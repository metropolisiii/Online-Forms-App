<?php
	/**
	* If a form requires a payment, the user is brought to this page once the transaction is made.
	*
	* @author Jason Kirby <jkirby1325@gmail.com>
	*
	*/
	
	include("../includes/connect.php");
	
	if (!isset($_SESSION['conf_user']))
		exit;
	$_POST=sanitize($_POST);
	
	$headers = 'From: no-reply@mycompany.com'."\r\n" .
						'Reply-To: noreply@mycompany.com'. "\r\n" .
						'X-Mailer: PHP/' . phpversion();
	
	$result=mysql_query("SELECT fb_savedforms.id AS id, fb_savedforms.name, notifyees, fb_savedforms.url AS url, user_form.url AS link FROM user_form INNER JOIN fb_savedforms on user_form.formid=fb_savedforms.id where user_form.id=".$_POST['formid']);
	$form=mysql_fetch_array($result);
	$notifyees=explode("\n",$form['notifyees']);

	if (!empty($_POST['x_response_code']) && $_POST['x_response_code'] == 1){
		mysql_query("UPDATE user_form SET auth_code='".$_POST['x_trans_id']."', account_number='".$_POST['x_account_number']."', registration_sequence='SC2013".$_POST['formid']."', declined=0 WHERE id=".$_POST['formid']);
		$message="A user has filled out a form and has made a payment. \r\n \r\n ";
		foreach ($_POST as $key=>$value)
			$message.=$key." = ".$value."\r\n";
	
		
		foreach($notifyees as $notifyee)
			mail($notifyee,$_POST['x_first_name']." ".$_POST['x_last_name']." has made payment for ".$form['name'], $message, $headers);
	}
	else{
		
		$message="A user has filled out a form and has made a payment, but the payment was DECLINED. \r\n \r\n ";
		foreach ($_POST as $key=>$value)
			$message.=$key." = ".$value."\r\n";
	
		
		foreach($notifyees as $notifyee)
			mail($notifyee,$_POST['x_first_name']." ".$_POST['x_last_name']." has made payment for ".$form['name']." but it was DECLINED!", $message, $headers);
	}
	
	mysql_query("UPDATE form_answers SET response='".$_POST['x_trans_id']."' WHERE user_form_id=".$_POST['formid']." AND field_id='transaction_id'");
	mysql_query("UPDATE form_answers SET response='".$_POST['x_account_number']."' WHERE user_form_id=".$_POST['formid']." AND field_id='cc_number'");
	mysql_query("UPDATE form_answers SET response='".$_POST['billcode'].$_POST['formid']."' WHERE user_form_id=".$_POST['formid']." AND field_id='registration_sequence'");
	mysql_query("UPDATE form_answers SET response='".$_POST['x_card_type']."' WHERE user_form_id=".$_POST['formid']." AND field_id='cc_type'");
	mysql_query("UPDATE form_answers SET response='".$_POST['x_amount']."' WHERE user_form_id=".$_POST['formid']." AND field_id='authamount'");
	
?>
<meta http-equiv="refresh" content="0;url=https://www.mycompany.com/forms/finish.php?id=<?php echo $form['id']; ?>&amount=<?php echo $_POST['x_amount']; ?>&url=<?php echo $form['url']; ?>&email=<?php echo $_POST['x_email'];?>&link=<?php echo $form['link']; ?>&d=<?php if ($_POST['x_response_code'] != 1) echo "true"; else echo "false"; ?>">

