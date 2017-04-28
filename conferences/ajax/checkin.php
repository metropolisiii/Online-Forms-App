<?php
	session_start();
	include_once("../includes/db_connect.php");
	
	//if (!isset($_SESSION['conf_user']) && $_SESSION['type'] != 'admin')
	//	exit;
	
	$response = array();
	//Remove all actions that are 1 minute old
	
	mysql_query("DELETE FROM action_log WHERE `timestamp` < (NOW() - INTERVAL 5 MINUTE)");

	//Check the database for an action
	$query = "SELECT action_log.user_form_id as id, field_id, response, checkedin, comments, paid, square_info, url, forms.user_id as account_id FROM action_log INNER JOIN forms_app.user_form ON user_form.id = action_log.user_form_id INNER JOIN forms ON forms.user_form_id = user_form.id INNER JOIN forms_app.form_answers ON action_log.user_form_id = form_answers.user_form_id LEFT JOIN customer_information ON form_answers.id = form_answer_id WHERE  (field_id LIKE 'Person\_%First\_Name' or field_id LIKE 'Person\_%Last\_Name' or field_id = 'Company' or field_id LIKE 'Person\_%_Paid') AND response != '';";

	$result = mysql_query($query);
	
	while ($action = mysql_fetch_array($result)){
		preg_match('!\d+!', $action['field_id'], $matches);
		if (strpos($action['field_id'], "First_Name") !== false || strpos($action['field_id'], "Last_Name") !== false || strpos($action['field_id'], "_Paid") !== false){
			$action['person'] = $matches[0];
		}
		if (!array_key_exists($action['id'],$response))
			$response[$action['id']] = array();
		$response[$action['id']][$action['field_id']] = $action;
	}
	
	//Return a json list corresponding to the actions
	print json_encode($response);
?>
