<?php 
	$data=array();
	$data['report_fields'] = array();
	$data['report_data'] = array();
	$result = mysql_query("SELECT id from permissions WHERE user = '{$userid}' AND reportid={$reportid}");
	if (mysql_num_rows($result) == 0)
		exit;
	
	//Get all of the forms in the archive that belong to this report
	$result = mysql_query("SELECT * from archives a INNER JOIN form_answers f on a.user_form_id = f.user_form_id WHERE report_id={$reportid}");
	
	//Get the fields in the report
	$result2 = mysql_query("SELECT field_name, field_as FROM report_fields where report_id={$reportid}");
	
	while ($field = mysql_fetch_object($result2)){
		$data['report_fields'][$field->field_name]=$field->field_as;
	}	
	
	while ($archive = mysql_fetch_object($result)){
		//Check if field in report fields
		if (array_key_exists($archive->field_id, $data['report_fields'])){
			$data['report_data'][$archive->user_form_id][$archive->field_id] = $archive->response;
		}
	}
