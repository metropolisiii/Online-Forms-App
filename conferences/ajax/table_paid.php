<?php
	include("../../scripts/settings.php");
	include("../../scripts/connect.php");
	
	$action = $_POST['action'];
	$userform = $_POST['userform'];
	
	if ($action == "paid"){
		$form_answer_action = "Paid";
		$boolean_action = 1;
		$table_paid_action =  "Table_is_paid";
	}
	else{
		$form_answer_action = "";
		$boolean_action = 0;
		$table_paid_action = "";
	}
	#If paid go through customer information and either update or insert paid column for 1st 6 people
	for ($i=1; $i<=6; $i++){
		$result = mysql_query("SELECT id from forms_app.form_answers WHERE field_id='Person_{$i}First_Name' AND user_form_id={$userform}");
		if (mysql_num_rows($result) == 0){
			mysql_query("INSERT INTO forms_app.form_answers (field_id, response, user_form_id, custom) VALUES ('Person_{$i}First_Name', '', {$userform},'0')");
			$id = mysql_insert_id();
			mysql_query("INSERT INTO forms_app.form_answers (field_id, response, user_form_id, custom) VALUES ('Person_{$i}_Paid', '{$form_answer_action}', {$userform},'0')");
		}
		else{
			$result2 = mysql_query("SELECT id from forms_app.form_answers WHERE field_id='Person_{$i}_Paid' AND user_form_id={$userform}");
			$row = mysql_fetch_object($result2);
			$id = $row->id;
			mysql_query("UPDATE forms_app.form_answers SET response='{$form_answer_action}' WHERE id={$id}");
			$row = mysql_fetch_object($result);
			$id = $row->id;
		}
		mysql_free_result($result);
		$result = mysql_query("SELECT id FROM conferences.customer_information WHERE form_answer_id={$id}");

		if (mysql_num_rows($result) == 0){
			mysql_query("INSERT INTO conferences.customer_information (form_answer_id, checkedin, comments, paid, onsite_payment, square_info, table_paid) VALUES ({$id},0,'',{$boolean_action},0,'',{$boolean_action})");
		}
		else{
			$row = mysql_fetch_object($result);
			mysql_query("UPDATE conferences.customer_information SET paid={$boolean_action} WHERE form_answer_id={$id}");
		}	
		mysql_free_result($result);
		$result = mysql_query("SELECT id FROM form_answers WHERE field_id='Person_{$i}_Paid' AND user_form_id={$userform}");
		if (mysql_num_rows($result) == 0)
			mysql_query("INSERT INTO form_answers (field_id, response, user_form_id, custom) VALUES ('Person_{$i}_Paid', '{$form_answer_action}',{$userform},0)");
		else
			mysql_query("UPDATE form_answers set response = '{$form_answer_action}' WHERE field_id='Person_{$i}_Paid' AND user_form_id={$userform}");
	}
	mysql_free_result($result);
	$result = mysql_query("SELECT id from forms_app.form_answers WHERE field_id='Table_Paid' AND user_form_id={$userform}");
	if (mysql_num_rows($result) == 0){
		mysql_query("INSERT INTO forms_app.form_answers (field_id, response, user_form_id, custom) VALUES ('Table_Paid', '{$table_paid_action}', {$userform},'0')");
	}
	else{
		$row = mysql_fetch_object($result);
		mysql_query("UPDATE forms_app.form_answers SET response='{$table_paid_action}' WHERE id=".$row->id);
	}
	mysql_free_result($result);
	
	
	#Get the price paid for table
	$result = mysql_query("SELECT field_id,response, amount, multiplier from forms_app.form_answers LEFT JOIN forms_app.discounts ON form_answers.response = discounts.code WHERE (field_id='Demo_Type' OR field_id='dc-Promotional_Code')  AND user_form_id={$userform}");
	$amount_paid = 0;
	while ($rec = mysql_fetch_object($result)){
		if ($rec->field_id == "Demo_Type"){
			preg_match_all('!\d+!', $rec->response, $matches);
			$amount_paid+=$matches[0][0];
		}
		else if ($rec->response != ''){ //Promotional Code
			if ($rec->amount == -999){
				$amount_paid = 0;
				break;
			}
			else{
				$amount_paid -= $rec->amount;
			}
		}
	}
	echo $amount_paid;
	mysql_free_result($result);
	#Select account
	$result = mysql_query("SELECT user_id FROM conferences.forms WHERE user_form_id = {$userform}");
	$rec = mysql_fetch_array($result);
	
	//Update the invoice version number
	mysql_query("UPDATE conferences.accounts a INNER JOIN conferences.forms f ON a.id=f.user_id SET invoice_version = invoice_version+1 WHERE f.user_form_id={$userform}");
	mysql_close();