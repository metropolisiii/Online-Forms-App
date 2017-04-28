<?php
	/**
	*	
	*	@author Jason Kirby <jkirby1325@gmail.com>
	*/

	$report_name=$report_match=$report_plugins='';
	$fields=array();
		
	if (isset($_GET['id'])){ //If we're editing a report, get the report information and put it into appropriate variable/arrays for use in the view
		$reports_query="SELECT `reports`.`name`, plugins, username, `match`, js_id, form_id, fb_savedforms.name as form_name, field_name, field_as FROM reports LEFT JOIN report_fields ON report_id = reports.id LEFT JOIN fb_savedforms on fb_savedforms.id = form_id WHERE reports.id={$_GET['id']} ORDER BY `index`;";
		$reports_result=mysql_query($reports_query);
		if ($reports_result && mysql_num_rows($reports_result) > 0){
			while ($report= mysql_fetch_object($reports_result)){
				if ($report->username !== $_SESSION['userid']){
					$_SESSION['notification']="You do not have permission to edit this report.";
					echo "<script>window.location='reports.php'</script>";
					exit;
				}
				if ($report_name == '' ){ //Get the statically generated fields
					$report_name=html_entity_decode($report->name);
					$report_match=html_entity_decode($report->match);
					$report_plugins=html_entity_decode($report->plugins);
				}
				//Get the dynamically generated fields
				if (!is_null($report->js_id))
					$fields[]=array('id'=>$report->js_id, 'form_id'=>$report->form_id, 'form_name'=>$report->form_name, 'field_name'=>$report->field_name, 'field_as'=>$report->field_as);			
			}
			mysql_free_result($reports_result);
		}
	}
	
	//Get the forms belonging to the current user. Parse it's fields and use it to populate the form fields container
	$forms_array=$form_ids=array();
	$forms_query="SELECT fb_savedforms.id, name, form_structure FROM fb_savedforms INNER JOIN permissions on permissions.formid = fb_savedforms.id WHERE user='{$_SESSION['userid']}' ORDER BY name";
	$forms_result=mysql_query($forms_query);
	
	//Get valid form types
	$field_types=array();
	$field_types_query="SELECT type FROM form_types";
	$field_types_result=mysql_query($field_types_query);
	while ($field_type=mysql_fetch_object($field_types_result)) //Fill array with the type of INPUT form widgets. We will use this to compare to each form widget to make sure it is a proper input type and not just a static widget
		$field_types[]=$field_type->type;	
	while ($form = mysql_fetch_object($forms_result)){ // for each form, create an array with the key being the form name
		$forms_array[$form->name]=array();
		$form_ids[$form->name]=$form->id;
		//Get the fields in each form
		$form_json = json_decode($form->form_structure);
		$form_field_counter=0;
		for ($i=0; $i<count($form_json); $i++){ // for each field, store with form
			if (in_array($form_json[$i]->cssClass, $field_types)){ //We only want to store the proper field as there are a few non-input fields that should never appear on a report
				if (isset($form_json[$i]->title)){ //Fields with multiple values (checkboxes, radios, selects) use title to define their labels, other wise they use 'values'
					$title=strip_encoded($form_json[$i]->title); //Prepare the field and insert into the array
					if ($form_json[$i]->cssClass == 'checkbox'){
						foreach ($form_json[$i]->values as $value){ //Insert the multiple values into the array
							$forms_array[$form->name][$form_field_counter] = strip_encoded($title." - ".$value->value." (checkbox)");
							$form_field_counter++;
						}
					}
					else
						$forms_array[$form->name][$form_field_counter]=$title;
				}
				else{
					if ($form_json[$i]->cssClass == 'payment_field'){ //We want to give payment fields a static label
						$forms_array[$form->name][$form_field_counter]="Authorization Amount";
					}
					else
						$forms_array[$form->name][$form_field_counter]=strip_encoded($form_json[$i]->values);
				}
				$form_field_counter++;
			}
		}
			$forms_array[$form->name][$form_field_counter] = "Date Created";
			$form_field_counter++;
			$forms_array[$form->name][$form_field_counter] = "Date Updated";
			$form_field_counter++;
			$forms_array[$form->name][$form_field_counter] = "userId";
	}
	
	mysql_free_result($forms_result);