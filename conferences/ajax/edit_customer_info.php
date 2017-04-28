<?php
	include("../../scripts/settings.php");
	include("../../scripts/connect.php");
	include("../../misc/functions.php");
	
	$action = sanitize($_POST['action']);
	$person = filter_var($_POST['person'], FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1, max_range=>15)));
	$userform = filter_var($_POST['userform'], FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1)));
	$value = sanitize($_POST['value']);

	
	//Make sure an admin is performing these actions
	if (!isset($_SESSION['type']) || $_SESSION['type'] != 'admin')
		exit;	
	
	if ($action == "paid"){
		if ($value=="true"){
			$form_answer_action = "Paid";
			$boolean_action = 1;
			$multiplier = 1; //Used for calculating whether the amount paid on the invoice should go up or down
		}
		else if ($value == "false"){
			$form_answer_action = "";
			$boolean_action = 0;
			$multiplier = -1; //Used for calculating whether the amount paid on the invoice should go up or down
		}
		
		//Onsite payments are handled within the form app itself. All other actions are handled within the Conference checkin app.
		$result = mysql_query("SELECT id from forms_app.form_answers WHERE field_id='Person_{$person}First_Name' AND user_form_id={$userform}");
		
		//If the customer is not found, insert him/her and payment status into the database
		if (mysql_num_rows($result) == 0){
			mysql_query("INSERT INTO forms_app.form_answers (field_id, response, user_form_id, custom) VALUES ('Person_{$person}First_Name', '', {$userform},'0')");
			$id = mysql_insert_id();
			mysql_query("INSERT INTO forms_app.form_answers (field_id, response, user_form_id, custom) VALUES ('Person_{$person}_Paid', '{$form_answer_action}', {$userform},'0')");
		}
		//Otherwise, update the record
		else{
			$result2 = mysql_query("SELECT id from forms_app.form_answers WHERE field_id='Person_{$person}_Paid' AND user_form_id={$userform}");
			$row = mysql_fetch_object($result2);
			$id = $row->id;
			mysql_query("UPDATE forms_app.form_answers SET response='{$form_answer_action}' WHERE id={$id}");
			$row = mysql_fetch_object($result);
			$id = $row->id;
		}
		mysql_free_result($result);
		
		//Update the customer's information in the Conferences database
		$result = mysql_query("SELECT id FROM conferences.customer_information WHERE form_answer_id={$id}");

		if (mysql_num_rows($result) == 0){
			mysql_query("INSERT INTO conferences.customer_information (form_answer_id, checkedin, comments, paid, onsite_payment, square_info) VALUES ({$id},0,'',{$boolean_action},0,'')");
		}
		else{
			$row = mysql_fetch_object($result);
			mysql_query("UPDATE conferences.customer_information SET paid={$boolean_action} WHERE form_answer_id={$id}");
		}	
		mysql_free_result($result);
		
		//increment the version of the account
		mysql_query("UPDATE conferences.accounts a INNER JOIN conferences.forms f ON a.id=f.user_id SET invoice_version = invoice_version+1 WHERE f.user_form_id={$userform}");
	}
	else if ($action == "table_paid"){
		if ($value=="true")
			$boolean_action = 1;
		else
			$boolean_action = 0;
		mysql_query("UPDATE conferences.customer_information ci INNER JOIN forms_app.form_answers fa ON ci.form_answer_id = fa.id SET table_paid={$boolean_action} WHERE user_form_id={$userform}");
	}
	else{
		
		// Set the customer_information to the action and value of the response
		$form_answer_query = mysql_query("SELECT form_answers.id from forms_app.form_answers WHERE user_form_id={$userform} AND field_id = 'Person_{$person}First_Name'");
		$form_answer = mysql_fetch_object($form_answer_query);
		$form_answer_id = $form_answer->id;
		
		$customer_information_query=mysql_query("SELECT id FROM conferences.customer_information WHERE form_answer_id={$form_answer_id}");
		
		if (mysql_num_rows($customer_information_query)>0){
			$customer_information = mysql_fetch_object($customer_information_query);
			$customer_information_id = $customer_information->id;
			mysql_query("UPDATE conferences.customer_information set {$action} = {$value} WHERE id = {$customer_information_id}");	
		}
		else
			mysql_query("INSERT INTO conferences.customer_information (form_answer_id, {$action}) VALUES ({$form_answer_id}, {$value})");
	}
	mysql_query("DELETE FROM conferences.action_log WHERE user_form_id = {$userform}");
	mysql_query("INSERT INTO conferences.action_log (user_form_id) VALUES ({$userform})");