<?php
/**
* File to hold all global functions
*
* @author Jason Kirby <jkirby1325@gmail.com>
*
*/

/**
* Sanitizes input for safe insertion into database
* 
* @param string $input
* @return string
*/
function cleanInput($input) {
	$search = array(
		'@<script[^>]*?>.*?</script>@si',   // Strip out javascript
		'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
		'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
		'@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
	);
	$output = preg_replace($search, '', $input);
    return $output;
}
/**
* Takes input, decides if it's an array. If it is, recursively call this function until input is not an array and sends it to the cleanInput function
* 
* @param string $input May be an array
* @return string
*/  
function sanitize($input) {
	if (is_array($input)) {
        foreach($input as $var=>$val) {
            $output[$var] = sanitize($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $output  = cleanInput($input);
      //  $output = mysql_real_escape_string($input);
    }
    return $output;
}
/**
* Cleans and formats input for output suitable for reports
* 
* @param string $element
* @return string
*/
function format_for_report($element){
	$fieldid = preg_replace("/&lt;.+?&gt;/is", "", str_replace(" ", "_", $element) );
	$fieldid=preg_replace("/<.+?>/is", "", str_replace(" ", "_", $fieldid) ) ;
	$fieldid=html_entity_decode($fieldid, ENT_QUOTES);
	$patterns = array();
	$patterns[0] = '/[^a-zA-Z0-9_-]/';
	$replacements = array();
    $replacements[0] = '';
    $fieldid = preg_replace($patterns, $replacements, trim($fieldid));
	return $fieldid;
}
/**
* Determins if a substring exists within any elements of an array
* 
* @param string $haystack The substring to search for
* @param array $needle The array to search through
* @return bool
*/
function substr_in_array($haystack, $needle){
	$found = ARRAY();
 	// cast to array 
    $needle = (ARRAY) $needle;
 
    // map with preg_quote 
    $needle = ARRAY_MAP('preg_quote', $needle);
 
    // loop over  array to get the search pattern 
    FOREACH ($needle AS $pattern)
    {
        IF (COUNT($found = PREG_GREP("/$pattern/", $haystack)) > 0) {
        	RETURN $found;
        }
    }
    // if not found 
    RETURN FALSE;
}
/**
* Sanitizes input but keep HTML tags but in escaped form
* 
* @param string $input
* @return string
*/
function sanitize_leave_html($input){
	if (is_array($input)) {
        foreach($input as $var=>$val) {
            $output[$var] = sanitize_leave_html($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $search = '@<script[^>]*?>.*?</script>@si';   // Strip out javascript
		$output = htmlspecialchars(preg_replace($search, '', $input), ENT_QUOTES);
    }

    return $output;
}
/**
* Search for user in Active Directory via LDAP and returns an array of user information. Returns false if no user found or bad binding.
* 
* @param string $user
* @return array
*/
function ldap_user_info($user){
	$ds=ldap_connect("ldap.mycompany.com");
	if ($ds) { 
		$ldapbind=ldap_bind($ds, 'CTLINT\zz_ldap', 'Q36buCA$');
        if ($ldapbind) {
			$filter="sAMAccountName=".$user;
			$dn = "OU=community,DC=mycompany,DC=com";
			$LDAPFieldsToFind = array("mail","givenname", "sn","company");
			$sr=ldap_search($ds, $dn, $filter, $LDAPFieldsToFind);
			$info = ldap_get_entries($ds, $sr);
			return $info;
		}
		else 
			return false;
	}
	else
		return false;
}
/**
* Swaps two keys in a multiple dimensional array
* 
* @param array $two_dimensional_array
* @return array
*/
function array_two_key_swap( $two_dimensional_array ) {
	$keys = array_keys( $two_dimensional_array );
	$array_swaped = array();
	foreach( $two_dimensional_array[$keys[0]] as $key_counter => $value1 ) {
		$temp_array = array();
		foreach( $keys as $key)
			$temp_array[$key] = $two_dimensional_array[$key][$key_counter];
		$array_swaped[] = $temp_array;
	}
	return $array_swaped;
}
/**
* Generates a random sequence of alphanumeric characters
* 
* @return string
*/
function generate_random_url($length=10){
	$valid_chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-';
	// start with an empty random string
    $random_string = "";

    // count the number of chars in the valid chars string so we know how many choices we have
    $num_valid_chars = strlen($valid_chars);

    // repeat the steps until we've created a string of the right length
    for ($i = 0; $i < $length; $i++)
    {
        // pick a random number from 1 up to the number of valid chars
        $random_pick = mt_rand(1, $num_valid_chars);

        // take the random character out of the string of valid chars
        // subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
        $random_char = $valid_chars[$random_pick-1];

        // add the randomly-chosen char onto the end of our string so far
        $random_string .= $random_char;
    }

    // return our finished random string
    return $random_string;
}

function debug($key, $value, $debug=false) {
	global $mode;
	if($debug && $mode=="test"){
		echo '<br/><b>'.$key . "</b> = ";
		switch (gettype($value)) {
			case 'string' :
			echo $value;
				break;
			case 'array' :
			case 'object' :
			default :
				echo '<pre>';
				print_r($value);
				echo '</pre>';
				break;
		}
	}
}

function checkSession(){
	session_regenerate_id(true);
	if (!empty($_SESSION['timeout']) && $_SESSION['timeout'] + 10 * 3600 < time()) {
		session_unset();
		session_destroy();
	}
	else
		$_SESSION['timeout']=time();
}

function checkHTTPS(){
	if(empty($_SERVER['HTTP_X_FORWARDED_HOST']) && $_SERVER['HTTPS']!="on")
		{
			$redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		//	header("Location:$redirect");
		}
}
function in_arrayi($needle, $haystack)
{
return in_array(strtolower($needle), array_map('strtolower', $haystack));
}

function prepareMessage($field, $altmessage, $field_list, $url=''){
	
	if ($field){
		$search=array("&lt;h&gt;","&lt;/h&gt;","&nbsp;", "&lt;","&gt;");
		$replace=array("",""," ","<",">");
		$field=str_replace($search, $replace, htmlspecialchars_decode($field));
		$field=str_replace(array("{random_url}","{form_id}"), $url, $field);
		preg_match_all('/{([^}]*)}/', $field, $matches);
		
		
			
		for ($i=0; $i<count($matches[1]); $i++){
			$match= $_POST[str_replace(" ","_",$matches[1][$i])];
			if (substr($matches[1][$i],0,3) == 'dc-'){
				//look up discount code
				
				$result=mysql_query("SELECT * FROM discounts WHERE code = '".$match."' AND form_id=".$_POST['fid']);
				//if code is valid, calculate the discount and apply to the total amount
				if (mysql_num_rows($result)>0){
					$discount=mysql_fetch_array($result);
					$replacement = "\$".$discount['amount'];
				}
			}
			else if ($matches[1][$i] == 'date'){
				$replacement = date("m/d/Y");
			}
			else{
				//Discount Codes
				
				$replacement='';
				if (is_array($match)){
					foreach ($match as $element)
						$replacement.=$element.", ";
					$replacement=rtrim($replacement,", ");
				}
				else
					$replacement=wordwrap($match,100,"\r\n");
			}	
		
			$field = str_replace('{'.$matches[1][$i].'}', $replacement, $field);
			
		}
		
		preg_match_all('/<eq>(.*?)<\/eq>/', $field, $matches);
		
		for ($i=0; $i<count($matches[1]); $i++){
			$mathString = trim($matches[1][$i]);
			$mathString = ereg_replace ('[^0-9\+-\*\/\(\) ]', '', $mathString);
			$replacement = create_function("", "return (" . $mathString . ");" );
			$replacement =  0+$replacement();
			$field = str_replace('<eq>'.$matches[1][$i].'</eq>', $replacement, $field);
		}
	}
	else
		$field = $altmessage;
		
//	$field=str_replace("\n","<br/>", $field);
//	$field=str_replace("\r\n","<br/>", $field);
	$field=str_replace(array("<br/>","<br />"),"<br/> \r\n", $field);
	$field=str_replace("[field list]", $field_list, $field);
	return $field;
}
function remove_non_alphanumeric($s){
	return preg_replace("/[^A-Za-z0-9]/", '', str_replace("#039;","'",$s));
}
function strip_encoded($value){
	return strip_tags(html_entity_decode($value));
}

function createFormArray($forms_result){
	
	return array('forms_array'=>$forms_array, 'form_ids'=>$form_ids);
}
function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
 function array_searchi($element, $array) {
	$array=array_map('strtolower', $array);
	return array_search(strtolower($element),$array); 
}
function CreateCondition($where){ 
	$operations = array("=","!=","<",">","<=",">="); 
	$condition="";
	$subject = new stdClass();
	$predicate = new stdClass(); //Constructing objects for the where clause to make lookup much easier 
	for ($i=0; $i<count($operations); $i++){ //go through each operation and split the 'where' expression
		$where_parts=explode($operations[$i], $where);
		if (count($where_parts)>1){ 
			$condition=$operations[$i];
			$leftside=explode(":",$where_parts[0]); //Get the form and the field of the lookup
			$subject->form = trim($leftside[0]);
			$subject->field = str_replace(" ","_",trim($leftside[1]));
			$rightside=explode(":",$where_parts[1]); //Get the form and the field of the predicate
			if (count($rightside)>1){ //If the where predicate is a form field as opposed to a simple value
				$predicate->form=trim($rightside[0]);
				$predicate->field=str_replace(" ","_",trim($rightside[1]));
			}
			else
				$predicate->value=$rightside[0];
			break;
		}
	}
	if ($condition != ""){
		$where_object =array();
		$where_object['subject'] = $subject;
		$where_object['predicate'] = $predicate;
		$where_object['condition'] = $condition;
		return $where_object;
	}
	else
		return false;
}
 function elemId($label, $prepend = false){
	if(is_string($label)){
		$prepend = is_string($prepend) ? $this->elemId($prepend).'-' : false;
		$fieldid = preg_replace("/&lt;.+?&gt;/is", "", str_replace(" ", "_", $label) );
		$fieldid= preg_replace("/<.+?>/is", "", str_replace(" ", "_", $fieldid) ) ;
		$fieldid=html_entity_decode($fieldid, ENT_QUOTES);
	
		$patterns = array(); //JK Mod
		$patterns[0] = '/[^a-zA-Z0-9\[\]_-]+/';//JK Mod
		$replacements = array(); //JK Mod
		$replacements[0] = ''; //JK Mod     
		$fieldid = preg_replace($patterns, $replacements, trim($fieldid));//JK Mod
		return $fieldid;
	}
	return false;
}

function createReport($id, $username, $to_date, $from_date){
	$report_query="SELECT permissions.id, reports.name, `match`, plugins FROM permissions INNER JOIN reports ON reports.id=permissions.reportid  WHERE user='{$username}' AND reportid={$id}";
	$report_result=mysql_query($report_query);
	if (mysql_num_rows($report_result) == 0){
		return false;
	}
	$report=mysql_fetch_object($report_result);
	$report_name = $report -> name;
	$match = $report -> match;
	$plugins = $report->plugins;
	
	
	mysql_free_result($report_result);	
	
	//Get columns
	$column_ids=array();
	$column_ids_js=array();//We need a column id to match the form answers and we need a column id to match the javascript id
	$checkboxes=array();
	$forms=array();
	$column_names=array();
	$date_created=$date_updated=$userid=false;
	$report_data_query="SELECT field_name, field_as, `index`, form_id from report_fields WHERE report_id={$id} ORDER BY `index`";

	$report_data_result=mysql_query($report_data_query);
	
	while ($column = mysql_fetch_object($report_data_result)){
		if ($column->field_name === 'Date_Created')
			$date_created=true;
		if ($column->field_name === 'Date_Updated')
			$date_updated=true;
		if ($column->field_name === 'userId')
			$userid=true;
		if (!in_array($column->field_as, $column_names)){
			$field=	elemId($column->field_name);	
			if (strpos($field, "_checkbox") !== false){
				$field=preg_split("/_-_[A-Za-z0-9_ ]+_checkbox/", $field);
				$field=$field[0];
				$checkboxes[]=$field;
			}			
			$column_ids[$column->index]=$field;
			$column_ids_js[$column->index]=elemId($column->field_name);
			$column_names[$column->index]=$column->field_as;
			$forms[$column->index]=$column->form_id;
		}
	}
	$unique_forms=array_unique($forms);
	mysql_free_result($report_data_result);
	
	//Get data
	$report_data=array();
	$data_query="SELECT fb_savedforms.name as name, field_id, response, formid, user_form.id, form_answers.id as answer_id, date_created, date_updated, user_form.userid FROM form_answers INNER JOIN user_form ON user_form_id=user_form.id INNER JOIN fb_savedforms ON fb_savedforms.id=user_form.formid WHERE ";
	$numItems = count($unique_forms);
	$i = 0;
	$data_query.="(";
	foreach ($unique_forms as $form){
		$data_query .= "formid={$form}";
		if (++$i !== $numItems)
			$data_query.=" OR ";
	}
	$data_query.=")";
	if ($to_date || $from_date){
		$data_query.=" AND (";
			if ($from_date){
				$data_query.="date_created >= ".strtotime($from_date);
				if ($to_date)
					$data_query.=" AND ";
			}
			if ($to_date)
				$data_query.="date_created <= ".(strtotime($to_date)+86000);
		$data_query.=")";
	}


	$data_results = mysql_query($data_query);
	

	while ($data=mysql_fetch_object($data_results)){
		
		//Remove discount code flag
		$field_id=str_replace(array("dc-"),array(""), $data->field_id);
		
		
		//If the field is part of the report, insert it into an array		
		if (in_array($field_id, $column_ids)){
			$response = $data->response;
			
			if (in_array($field_id, $checkboxes)){
				$responses=explode(";", $response);
				foreach ($responses as $r)
					$report_data[$data->id][$field_id."_-_".$r."_checkbox"]="X";
			}
			else
				$report_data[$data->id][$field_id]=str_replace(array("\r\n","\n",'"'),array("\\n","\\n",'\\"'),$response);
		}
		//else if (substr_in_array($column_ids, $field_id)){ //checkboxes
		//	$checkbox_options=explode(";", $data->response); //If more than one choice is selected, we need to get both choices since the report displays each choice as a separate column
		//	foreach ($checkbox_options as $checkbox_option)
		//		$report_data[$data->id][$field_id."___".$checkbox_option]="X";
		//}
		else if ($data->field_id==="authamount" && in_array("Authorization_Amount", $column_ids)){ //Authorization amount is a little different
			$report_data[$data->id]['Authorization_Amount']=str_replace('"',"'",$data->response);
		}
		else if ($data->field_id==="transaction_id" && in_array("Transaction_Id", $column_ids)){ //Authorization amount is a little different
			$report_data[$data->id]['Transaction_Id']=$data->response;
		}
		else if ($data->field_id==="cc_type" && in_array("CC_Type", $column_ids)){ //Authorization amount is a little different
			$report_data[$data->id]['CC_Type']=$data->response;
		}
		else if ($data->field_id==="registration_sequence" && in_array("Registration_Sequence", $column_ids)){ //Authorization amount is a little different
			$report_data[$data->id]['Registration_Sequence']=$data->response;
		}
		else if ($data->field_id==="cc_number" && in_array("CC_Number", $column_ids)){ //Authorization amount is a little different
			$report_data[$data->id]['CC_Number']=$data->response;
		}
		else if ($data->field_id==="auth_code" && in_array("Auth_Code", $column_ids)){ //Authorization amount is a little different
			$report_data[$data->id]['Auth_Code']=$data->response;
		}
		$report_data[$data->id]['form']=$data->name;
		//Date created and date uploaded are not part of the forms_answer table so we need to handle these separately
		if ($date_created && $data->date_created && !$report_data[$data->id]['Date_Created'])
			$report_data[$data->id]['Date_Created'] = date('m/d/Y',$data->date_created);
		if ($date_updated && $data->date_updated && !$report_data[$data->id]['Date_Updated'])
			$report_data[$data->id]['Date_Updated'] = date('m/d/Y',$data->date_updated);
		if ($userid && $data->userid && !$report_data[$data->id]['userId'])
			$report_data[$data->id]['userId'] = $data->userid;
	}

	
	if ($match){
		$matches = explode(",", $match);
		$matches = array_reverse($matches);
		foreach ($matches as $match){
			//match clause
			$match_array=array(); //Used to mark whether we have a match in our 'WHERE' clause
			$match_forms=array(); //Store the form ids in an array that match
			$i=0;
			$match_condition = CreateCondition($match);
			
			foreach ($report_data as $user_form => $fields){
				foreach ($fields as $field=>$value){
					if ($field != 'form'){ //We're ignoring the form name which is part of each row. We just want the responses
						//Build a structure
						$tempobj = new stdClass();
						$tempobj->form=$fields['form'];
						$tempobj->field = $field;
						if ($tempobj == $match_condition['subject'] || $tempobj == $match_condition['predicate']){
							$form_id=array_searchi($value, $match_array);
							if ($form_id !== false){
								//Merge rows
								foreach ($report_data[$user_form] as $f => $v){ //Go through all fields in the record and merge with match
									if (!array_key_exists($f, $report_data[$match_forms[$form_id]])){
										$report_data[$match_forms[$form_id]][$f] = $v;
										//pop the element out of the array since it is merged
										$report_data[$user_form] = false;
										$report_data=array_filter($report_data);									
									}
								}							
							}
							else{
								$match_array[$i]=$value;
								$match_forms[$i]=$user_form;
								$i++;
							}
							break;
						}
					}
				}
			}
		}
	}

	return array('report_name'=>$report_name, 'date_created'=>$date_created, 'column_names'=>$column_names, 'column_ids'=>$column_ids_js, 'forms'=>$forms, 'report_data'=>$report_data,'plugins'=>$plugins);
}
function flash($var){
	$response='';
	if (isset($_SESSION[$var]))
		$response = $_SESSION[$var];
	unset($_SESSION[$var]);
	return $response;
}

?>