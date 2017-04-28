<?php
	include("../../scripts/settings.php");
	include("../../scripts/connect.php");
	include("../../misc/functions.php");
	
	$person = filter_var($_POST['person'], FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1, max_range=>15)));
	$userform = filter_var($_POST['userform'], FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1)));
	$value = sanitize($_POST['value']);
	if (!isset($_SESSION['type']) || $_SESSION['type'] != 'admin')
		exit;
	
	$form_answer_query = mysql_query("SELECT form_answers.id from forms_app.form_answers WHERE user_form_id={$userform} AND field_id = 'Person_{$person}First_Name'");
	$form_answer = mysql_fetch_object($form_answer_query);
	$form_answer_id = $form_answer->id;
	
	$customer_information_query=mysql_query("SELECT id FROM conferences.customer_information WHERE form_answer_id={$form_answer_id}");
	if (mysql_num_rows($customer_information_query)>0){
		$customer_information = mysql_fetch_object($customer_information_query);
		$customer_information_id = $customer_information->id;
		if (isset($_POST['isSquare']) && $_POST['isSquare'] == 'true')
			$query = "UPDATE conferences.customer_information set square_info = '{$value}' WHERE id = {$customer_information_id}";
		elseif (isset($_POST['isCCType']) && $_POST['isCCType'] == 'true')
			$query = "UPDATE conferences.customer_information set cc_type = '{$value}' WHERE id = {$customer_information_id}";		
		elseif (isset($_POST['isAmount']) && $_POST['isAmount'] == 'false')
			$query = "UPDATE conferences.customer_information set comments = '{$value}' WHERE id = {$customer_information_id}";		
	}
	else{
		if (isset($_POST['isSquare']) && $_POST['isSquare'] == 'true')
			$query = "INSERT INTO conferences.customer_information (form_answer_id, square_info) VALUES ({$form_answer_id}, '{$value}')";
		elseif (isset($_POST['isCCType']) && $_POST['isCCType'] == 'true')
			$query = "INSERT INTO conferences.customer_information (form_answer_id, cc_type) VALUES ({$form_answer_id}, '{$value}')";
		elseif (isset($_POST['isAmount']) && $_POST['isAmount'] == 'false')
			$query = "INSERT INTO conferences.customer_information (form_answer_id, comments) VALUES ({$form_answer_id}, '{$value}')";
	}	
	mysql_query($query);
	
	/* Update the total amount for the day collected */
	if (isset($_POST['isAmount']) && $_POST['isAmount'] == 'true'){
		$result = mysql_query("SELECT id FROM conferences.daily_stats WHERE customer_information_id={$customer_information_id} AND stat = 'paid'");
		if (mysql_num_rows($result) > 0){
			$rec = mysql_fetch_array($result);
			$id = $rec['id'];
			mysql_query("UPDATE conferences.daily_stats set quantity={$value} WHERE id={$id}");
		}
		else{
			mysql_query("INSERT INTO conferences.daily_stats (quantity, date, customer_information_id, stat) VALUES ({$value}, '{$_POST['date']}', {$customer_information_id}, 'paid')");
		}
	}
	/* Update the checked in amount for the day */
	if (isset($_POST['checked_in'])){
		$result = mysql_query("SELECT id FROM conferences.daily_stats WHERE customer_information_id={$customer_information_id} and stat = 'checked_in'");
		if (mysql_num_rows($result) > 0){
			$rec = mysql_fetch_array($result);
			$id = $rec['id'];
			if ($value == "true")
				mysql_query("UPDATE conferences.daily_stats set quantity=1 WHERE id={$id}");
			else
				mysql_query("UPDATE conferences.daily_stats set quantity=0 WHERE id={$id}");
		}
		else{
			mysql_query("INSERT INTO conferences.daily_stats (quantity, date, customer_information_id, stat) VALUES (1, '{$_POST['date']}', {$customer_information_id}, 'checked_in')");
		}
	}