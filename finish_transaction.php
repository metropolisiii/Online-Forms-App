<?php
	/**
	* If a form requires a payment, the user is brought to this page once the transaction is made.
	*
	* @author Jason Kirby <jkirby1325@gmail.com>
	*
	*/
	
	include_once("scripts/settings.php");
	include("includes/header.php");
	if ((!$user_login_required && empty($_POST)) || (empty($_POST) && empty($_SESSION['userid'])))
		exit;
	$_POST=sanitize($_POST);
	
	//Email headers
	$headers = 'From: no-reply@mycompany.com'."\r\n" .
						'Reply-To: noreply@mycompany.com'. "\r\n" .
						'X-Mailer: PHP/' . phpversion();
	
	//Get form information
	$result=mysql_query("SELECT fb_savedforms.id AS id, fb_savedforms.name, notifyees, fb_savedforms.url AS url, user_form.url AS link FROM user_form INNER JOIN fb_savedforms on user_form.formid=fb_savedforms.id where user_form.id=".$_POST['formid']);
	$form=mysql_fetch_array($result);
	$notifyees=explode("\n",$form['notifyees']);

	//If the transaction was accepted
	if (!empty($_POST['x_response_code']) && $_POST['x_response_code'] == 1){
		//Update the user's form information
		mysql_query("UPDATE user_form SET auth_code='".$_POST['x_trans_id']."', account_number='".$_POST['x_account_number']."', registration_sequence='{$_POST['billcode']}{$_POST['formid']}', declined=0 WHERE id=".$_POST['formid']);
		$message="A user has filled out a form and has made a payment. \r\n \r\n ";
		
		//Format the authorize notification email for the notifyees
		foreach ($_POST as $key=>$value)
			$message.=$key." = ".$value."\r\n";
			
		foreach($notifyees as $notifyee)
			mail($notifyee,$_POST['x_first_name']." ".$_POST['x_last_name']." has made payment for ".$form['name'], $message, $headers);
		
		//Code for conferences
		if (isset($_POST['conf_user'])){
			//Get conference account info
			$result=mysql_query("SELECT accounts.id, total_paid, invoice_total from conferences.accounts INNER JOIN conferences.forms ON user_id = accounts.id WHERE user_form_id='".$_POST['formid']."' limit 1");
			$total_paid=$invoice_total=0;
			//Calculate the total amount paid for the account and get the invoice total
			$rec=mysql_fetch_object($result);
			$accountid = $rec->id;
			$total_paid=$rec->total_paid;
			$invoice_total = $rec->invoice_total;
			$total_paid+=$_POST['x_amount'];	
			
			//Set the total paid to the new amount
			mysql_query("UPDATE conferences.accounts SET total_paid = ".$total_paid." WHERE id='".$accountid."'");
			//If the user pays is full, go through each person and mark them as paid if they are in the system. If they are not, create a record.
			if ($total_paid >= $invoice_total) { //User paid full bill
				//Go through each person
				$query = "SELECT form_answers.id as id, customer_information.id as cid, paid, forms.user_form_id from forms_app.form_answers LEFT JOIN conferences.customer_information on form_answer_id = form_answers.id INNER JOIN conferences.forms ON forms.user_form_id = form_answers.user_form_id WHERE user_id={$accountid} AND field_id LIKE 'Person\_%First\_Name' AND response != ''";
				$result = mysql_query($query);
				$user_form_id = 0;
				$person_comment = false;
				while ($attendee = mysql_fetch_object($result)){
					if (!is_null($attendee->cid)) //If attendee is already in the system
						$query = "UPDATE conferences.customer_information set paid = 1 WHERE id = ".$attendee->cid;
					else
						$query = "INSERT INTO conferences.customer_information (form_answer_id, onsite_payment, square_info, checkedin,comments, paid) VALUES (".$attendee->id.", 0, '', 0, '', 1)";
					mysql_query($query);
					if ($attendee->user_form_id != $user_form_id){
						mysql_query("DELETE from conferences.action_log WHERE user_form_id=".$attendee->user_form_id);
						mysql_query("INSERT INTO conferences.action_log (user_form_id) VALUES (".$attendee->user_form_id.")");	
						$user_form_id = $attendee->user_form_id;
					}
					if (!$person_comment){
						$person_comment = true;
						mysql_query("UPDATE conferences.customer_information set comments = CONCAT(comments,'\nCredit card payment on ".date("m/d").".') WHERE form_answer_id = ".$attendee->id);
					}
					$form_answer_result = mysql_query("SELECT * FROM form_answers WHERE id = ".$attendee->id);
					$rec = mysql_fetch_object($form_answer_result);
					preg_match_all('!\d+!', $rec->field_id, $matches);
					$form_answer_result = mysql_query("SELECT id FROM form_answers WHERE field_id='Person_{$matches[0][0]}_Paid' AND user_form_id=".$rec->user_form_id);
					if (mysql_num_rows($form_answer_result) == 0)
						mysql_query("INSERT INTO form_answers (field_id, response, user_form_id, custom) VALUES ('Person_{$matches[0][0]}_Paid', 'Paid',".$rec->user_form_id.",0)");
					else
						mysql_query("UPDATE form_answers set response = 'Paid' WHERE field_id='Person_{$matches[0][0]}_Paid' AND user_form_id=".$rec->user_form_id);
				}
				
			}				
		}
	
	}
	//Payment was declined. Send an email to the notifyees
	else{		
		$message="A user has filled out a form and has made a payment, but the payment was DECLINED. \r\n \r\n ";
		foreach ($_POST as $key=>$value)
			$message.=$key." = ".$value."\r\n";	
		
		foreach($notifyees as $notifyee)
			mail($notifyee,$_POST['x_first_name']." ".$_POST['x_last_name']." has made payment for ".$form['name']." but it was DECLINED!", $message, $headers);
	}
	//Add transaction information to the form answers
	mysql_query("UPDATE form_answers SET response='".$_POST['x_trans_id']."' WHERE user_form_id=".$_POST['formid']." AND field_id='transaction_id'");
	mysql_query("UPDATE form_answers SET response='".$_POST['x_auth_code']."' WHERE user_form_id=".$_POST['formid']." AND field_id='auth_code'");
	mysql_query("UPDATE form_answers SET response='".$_POST['x_account_number']."' WHERE user_form_id=".$_POST['formid']." AND field_id='cc_number'");
	mysql_query("UPDATE form_answers SET response='".$_POST['billcode'].$_POST['formid']."' WHERE user_form_id=".$_POST['formid']." AND field_id='registration_sequence'");
	mysql_query("UPDATE form_answers SET response='".$_POST['x_card_type']."' WHERE user_form_id=".$_POST['formid']." AND field_id='cc_type'");
	mysql_query("UPDATE form_answers SET response='".$_POST['x_amount']."' WHERE user_form_id=".$_POST['formid']." AND field_id='authamount'");
	mysql_query("UPDATE form_answers SET response='".date('m/d/Y')."' WHERE user_form_id=".$_POST['formid']." AND field_id='Date_Paid'");
	mysql_query("UPDATE form_answers SET response='".$_POST['x_amount']."' WHERE user_form_id=".$_POST['formid']." AND field_id='Amount_Paid'");
	mysql_close();
	//Add-ons
	mail("jason.kirby@mycompany.com","test", print_r($_POST, true));
	if (!empty($_POST['hidden_include'])){
		if (file_exists('addons/'.$_POST['hidden_include'].'.php'))
			include('addons/'.$_POST['hidden_include'].'.php');
		else if (file_exists('addons/'.$_POST['hidden_include'].'/index.php')){
		
			include('addons/'.$_POST['hidden_include'].'/index.php');				
		}
		if (function_exists('addonRunAfterPayment')){
			addonRunAfterPayment();
		}
	}

?>

<meta http-equiv="refresh" content="0;url=https://www.mycompany.com/forms/finish.php?id=<?php echo $form['id']; ?>&amount=<?php echo $_POST['x_amount']; ?>&url=<?php echo $form['url']; ?>&email=<?php echo $_POST['x_email'];?>&link=<?php echo $form['link']; ?>&d=<?php if ($_POST['x_response_code'] != 1) echo "true"; else echo "false"; ?>">

