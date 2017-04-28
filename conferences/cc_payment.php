<?php
	$title='Conference Payment';
	$head="	
	";
	include_once("scripts/settings.php");
	include_once("includes/connect.php");
	
	//If user is already logged in, take him to his account
	//if (!isset($_SESSION['conf_user']))
	//	header('Location: index.php');
	$email=$_SESSION['conf_user'];
	
	//Make sure the company exists to submit a payment for. This can be checked via the vendor company sent in the POST request or the email/invoice number set in the GET request
	if (isset($_POST['vendor_company'])){
		$query="select accounts.email from forms_app.form_answers INNER JOIN forms_app.user_form on form_answers.user_form_id=user_form.id  INNER JOIN forms ON forms.user_form_id = user_form.id INNER JOIN accounts on user_id=accounts.id where field_id='Company' AND formid=".REGISTRATION_ID." AND response = '{$_POST['vendor_company']}'";
		$result = mysql_query($query);
		if (mysql_num_rows($result) == 0){
			echo "Invalid company. Please go <a href='cc_payment.php'>back</a> and try again.";
			exit;
		}
		$rec = mysql_fetch_object($result);
		$email = $rec->email;
	}
	else if (isset($_GET['email']))
		$email = $_GET['email'];
	else if (isset($_GET['invoice_id'])){
		$query = "SELECT email from accounts where id = {$_GET['invoice_id']}";
		$result = mysql_query($query);
		$rec = mysql_fetch_object($result);
		$email = $rec->email;
	}
	
	$company=$company_contact_last=$company_contact_first="";
	$query="SELECT accounts.id as id, accounts.total_paid, fb_savedforms.id as form_id, name, pagename, user_form.url, user_form.id as user_form_id FROM forms INNER JOIN accounts ON accounts.id=forms.user_id INNER JOIN forms_app.user_form ON user_form.id=forms.user_form_id INNER JOIN forms_app.fb_savedforms ON fb_savedforms.id=user_form.formid WHERE accounts.email='{$email}'";
	$result=mysql_query($query);
	while ($rec=mysql_fetch_object($result)){
		if (strpos($rec->name, 'Exhibitor Registration')){
			$form_id=$rec->user_form_id;
			$query="SELECT field_id, response from forms_app.form_answers WHERE user_form_id=".$rec->user_form_id;
			$result2=mysql_query($query);
			while ($rec2=mysql_fetch_object($result2)){
				if (!$company_contact_last && $rec2->field_id === 'Last_Name')
					$company_contact_last = $rec2->response;
				else if (!$company_contact_first && $rec2->field_id === 'First_Name')
					$company_contact_first = $rec2->response;
				else if ($rec2->field_id === 'Company')
					$company = $rec2->response;
			}
		}
	}
	
	//Get Total
	$result=mysql_query("SELECT * FROM accounts WHERE email='".$email."'");
	$rec=mysql_fetch_object($result);

	$total_due = $rec->invoice_total - $rec->total_paid;	
?>

<body>
	<?php include('includes/nav-header.php'); ?>
	<div id="main" class="main_container">
		<div class="content">
			<div class="row">
				<div id='cc_payment'>
					<?php if (!isset($_POST['vendor_company'])): ?>
					<form method='post' action='' >
					<?php else: ?>
					<form method='post' action='scripts/make_payment.php'>
					<?php endif; ?>
					<h1>Conference Payment Information</h1>
						<p>Payment information for <?php echo $email; ?></p>
						<?php if (isset($_POST['vendor_company'])): ?>
						<table width='100%'>
							<tr>
								<td id='amount_total'>
									Amount Total:
									<div class='cc_total'>
										$<?php echo number_format($total_due, 2); ?>
									</div>
								</td>
								<td id='amount_to_pay'>
									Amount to Pay:
									<div class='cc_total'>
										$<input type='type' name='amount' value='<?php echo $total_due; ?>' autofocus />
										<input type='hidden' name='person_being_registered' value="<?php echo $company_contact_first." ".$company_contact_last; ?>"/>
										<input type='hidden' name='payee' value="<?php echo $_POST['vendor_name']; ?>"/>
										<input type='hidden' name='company' value="<?php echo $_POST['vendor_company']; ?>"/>
										<input type='hidden' name='formid' value='<?php echo $form_id; ?>' />
									</div>
								</td>
							</tr>
						</table>
						<?php endif; ?>
						<?php if (!isset($_POST['vendor_company'])):?>
							Full name <input type='text' name='vendor_name' /><br/>
							Company <input type='text' name='vendor_company' value='<?php echo $company; ?>'/><br/>
							<input type='submit' value='submit'/>
						<?php else: ?>
							<input type='submit' value='Make Payment' />
						<?php endif; ?>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>
</html>