<?php
	header('Content-Type: application/json');
	include("../../scripts/settings.php");
	include("../../scripts/connect.php");
	mysql_select_db('conferences');
	$id = $_POST['value'];
	$data=array();
	$company = "";
	$userform = "";
	$url = "";
	$account="";
	$firstname = array();
	$lastname = array();
	$json_array = array();
	$people = array();
	$num_people = 0;
	$account_info_query = mysql_query("select accounts.id as account, forms.user_form_id as user_form, field_id, response, url from forms INNER JOIN forms_app.form_answers ON forms.user_form_id = forms_app.form_answers.user_form_id INNER JOIN forms_app.user_form ON forms.user_form_id = user_form.id INNER JOIN accounts on accounts.id=forms.user_id where forms.id={$id} AND (field_id = 'Company' OR (field_id LIKE 'Person\_%First\_Name' OR field_id LIKE 'Person\_%Last\_Name') AND response != '')");
	
	function parsePersonNumber($field_id){
		preg_match_all('!\d+!', $field_id, $matches);
		return $matches[0][0];
	}
	
	while ($rec = mysql_fetch_object($account_info_query)){
		if ($url == "")
			$url = $rec->url;
		if ($userform == "")
			$userform = $rec->user_form;
		if ($account == "")
			$account = $rec->account;
		if ($rec->field_id == 'Company')
			$company = $rec->response;
		else{
			/* Parse the person number */
			$personNumber=parsePersonNumber($rec->field_id);
			if ($personNumber > $num_people)
				$num_people = $personNumber;
			if (strpos($rec->field_id, "First_Name"))
				$firstname[$personNumber] = $rec->response;
			elseif (strpos($rec->field_id, "Last_Name"))
				$lastname[$personNumber] = $rec->response;
		}		
	}
	for ($i=1; $i<=$num_people; $i++){
		$json_array[]= array("id"=>$account, "company"=>$company, "name"=>$firstname[$i]." ".$lastname[$i], "url"=>$url, "userform"=>$userform, "person"=>$i);
	}
	echo json_encode($json_array);