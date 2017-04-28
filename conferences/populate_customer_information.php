<?php

include_once("includes/connect.php");

$result = mysql_query("SELECT * FROM forms_app.form_answers WHERE field_id like 'Person_%_Paid' AND response = 'Paid' AND user_form_id>=13381") or die(mysql_error());
while ($row = mysql_fetch_array($result)){
	preg_match('!\d+!', $row['field_id'], $matches);
	$result2 = mysql_query("SELECT * FROM forms_app.form_answers WHERE field_id='Person_{$matches[0][0]}First_Name' AND user_form_id={$row['user_form_id']}");
	$form_answer = mysql_fetch_array($result2);
	$result2 = mysql_query("SELECT * FROM conferences.customer_information WHERE form_answer_id = {$form_answer['id']}") or die(mysql_error());
	if (mysql_num_rows($result2) == 0)
		mysql_query("INSERT INTO conferences.customer_information VALUES (null, {$form_answer['id']}, 0, '', 1, 0, '')") or die(mysql_error());
	else
		mysql_query("UPDATE conferences.customer_information SET paid=1 WHERE form_answer_id={$form_answer['id']}") or die(mysql_error());
}