<?php 
	/**
	* Displays a menu of reports the user can view and shows the report.
	*
	* When a report is viewed, the form structure is retrieve from the fb_savedforms table in the database. The modified form structure is also retrieve from the user_reports table and that structure is displayed if it exists. If not, the values from fb_savedforms are used instead.
	* The original form structure is stored in the elements array. The modified form structure is stored in the modified_elements array. Next the two arrays are compared to see what elements have been added or deleted from the original array. The answers to the forms are then matched to the fields in the arrays.
	*
	* @author Jason Kirby <jkirby1325@gmail.com>
	*
	*/
	$selected='reports'; //For enabled tab
	include_once("scripts/settings.php");
	include("includes/header.php");
	$_GET=sanitize($_GET);
	
	$unencoded_form_structure=""; //Gets the JSON represented form structure of the form that is being reported
	$i=0;
	$csv=""; //csv representation of report
	$patterns[0] = '/[^a-zA-Z0-9_-]/'; 
	$replacements[0]='';
	if (empty($_SESSION['superadmin'])){
		$report_query=mysql_query("SELECT * FROM fb_savedforms LEFT JOIN permissions on formid=fb_savedforms.id WHERE sitename='".$forwarded_directory."' AND ((user='".$_SESSION['userid']."' AND (view_report=1 OR reports_no_restrictions=1)) OR userId='') order by name, LENGTH(name) asc "); //Gets current forms
	}
	else
		$report_query=mysql_query("SELECT * FROM fb_savedforms order by name, LENGTH(name) asc "); //Gets current forms
	//mail("jason.kirby@mycompany.com","report query","SELECT * FROM fb_savedforms LEFT JOIN permissions on formid=fb_savedforms.id WHERE sitename='".$forwarded_directory."' AND ((user='".$_SESSION['userid']."' AND (view_report=1 OR reports_no_restrictions=1)) OR userId='')".((!empty($_SESSION['account']))? " AND accountId=".$_SESSION['account']:" AND (accountId=1 OR accountId IS NULL OR accountId=0) ")." order by name, LENGTH(name) asc "); 
	$result=mysql_query("SELECT name, form_structure FROM fb_savedforms where id=".$_GET['formid']); //Get form structure of a specific form
	
	if ($result)
		$form=mysql_fetch_array($result);

	$formname=str_replace(" ","-",$form['name']); 
	$csv.='"'.$formname.'"'."\n";
	$form_structure=json_decode($form['form_structure']);
	
	$result=mysql_query("SELECT form_structure FROM users_reports WHERE formid=".$_GET['formid']." AND userId='".$_SESSION['userid']."'"); //Get the modified form structure if a user has modified it

	if ($result && mysql_num_rows($result)>0){ //If a modifed for structure exists, unserialize this structure.
		$unencoded_form_structure=mysql_fetch_array($result);
		$modified_form_structure=$unencoded_form_structure['form_structure'];
		$modified_form_structure=unserialize($modified_form_structure);
	}	

	if ($form_structure){
		$email_confirmation=0;
		foreach ($form_structure as $value){ //Get the original form structure and store it in $elements array. Different types of form elements are stored in the form_structure array differently.
			$element='';
			if ($value->cssClass=='checkbox' || $value->cssClass=='radio' || $value->cssClass=='select')
				$element=format_for_report($value->title);
			else if ($value->cssClass == 'payment_field'){
				$value->values='authamount';
				$element=format_for_report($value->values);
			}
			else if ($value->cssClass == 'discount_code'){
				$element=format_for_report("dc-".$value->values);
			}
			else if (property_exists($value, 'values') && $value->cssClass != 'input_block' ){
				$element=format_for_report($value->values);
			}
			if (!empty($element))
				$elements[] = $element;
		}
	}
	
	if (!empty($unencoded_form_structure)){ //Get the modified form structure and store in the $modified_elements array.
		foreach ($modified_form_structure as $value){
			$element=format_for_report($value);
			if (!empty($element))
				$modified_elements[] = $element;
		}
		/****Check to see if elements have been added ****/
		foreach($elements as $e){
			if (!in_array($e, $modified_elements)){
				$modified_elements[]=$e;
			}
		}
		
		/****Check to see if elements have been deleted****/
		foreach ($modified_elements as $key=>$me){
			if ($me !=="user" && $me !=="status" && strtolower($me) !=="date_created" && strtolower($me) !=="date_updated"){
				if(!in_array($me, $elements))
					unset($modified_elements[$key]);
			}
		}
		$elements=$modified_elements; //Reassign $elements to be $modified_elements

	}	
	/**********************************Get form answers and match them to our elements array ***************************************/
	$query="SELECT * FROM form_answers LEFT JOIN user_form on user_form_id=user_form.id WHERE formid=".$_GET['formid'];
	if (!empty($_GET['from_date']))
		$query.=" AND date_created>=".strtotime($_GET['from_date']);
	if (!empty($_GET['to_date']))
		$query.=" AND date_created<=".strtotime($_GET['to_date']);
	$query.=" ORDER by user_form.userid";
	
	$result=mysql_query($query);
	if ($result){
		while ($field=mysql_fetch_array($result)){  //Build the report and store it in an array to later be drawn
			$key='';		
			if ($field['field_id'] !="fid"){
				$f=$field['field_id'];
				$f=preg_replace("/htmlopen(.|\n)*?htmlclose/","",$f);
				$id=$field['user_form_id'];
				$table[$id][$f]=$field['response']; //This is where most of the fields and responses are stored.			
				/**********************Stores non-custom fields in array ****************************************************/
				if (is_null($field['accepted']) && is_null($field['submitted'])) //Store status
					$table[$id]['status']="Incomplete";
				else if (is_null($field['accepted']) && !is_null($field['submitted'])) //Stores submission status
					$table[$id]['status']="Pending";
				else if ($field['accepted']==1)  //Stores acceptance status
					$table[$id]['status']="Accepted";
				else if ($field['accepted']==0)
					$table[$id]['status']="Denied";
				$table[$id]['date_created']=date("m/d/Y",$field['date_created']); //Stores date_created
				if ($field['date_updated']) //Stores date updated
					$table[$id]['date_updated']=date("m/d/Y",$field['date_updated']);
				else 
					$table[$id]['date_updated']="";
			}
		}
	}
	
	$html.="<div id='reports_table'>";
	$html.= "<table border='0'><tr>";
		if ($user_login_required && empty($unencoded_form_structure)){
			$html.="<th><div><button type='button' style='display:none' class='moveleft'>&lt;</button> User<button type='button' class='moveright'>&gt;</button> </div><div class='include'><input type='checkbox' name='user' checked='checked'/> Include in CSV</div></th>";
			$csv.='"User",';
		}
	
	if ($elements){
		$last_key = end(array_keys($elements));
		
		/**********Creates the table header with the controls to move columns around. First and last columns do not have move left and move right controls respectively*************************************/
		foreach ($elements as $key=>$value){
			 if ($key == $last_key) 
				$last=true;
			$html.="<th><div><button type='button' class='moveleft' ";
			if (empty($first) && !empty($unencoded_form_structure)){ //First column controls
				$html.='style="display:none"';
				$first=true;
			}
			$html.=">&lt;</button> ".$value." <button type='button' class='moveright' ";
			if (!empty($last)) //Last column controls
				$html.='style="display:none" ';
			$html.=">&gt;</button></div><div class='include'><input type='checkbox' name='".$value."' checked='checked'/> Include in CSV</div></th>";
			$value=str_replace('"',"'",$value);
			$csv.='"'.$value.'",';
		}
	}
	if (empty($unencoded_form_structure)){ //Finish the original form structure if there is no modified form structure. The modified form structure already stores these elements.
		$html.="<th><button type='button' class='moveleft'>&lt;</button><div> Date Created </div><button type='button' class='moveright'>&gt;</button><div class='include'><input type='checkbox' name='date_created' checked='checked'/> Include in CSV</div></th><th><button type='button' class='moveleft'>&lt;</button><div> Date Updated </div><button type='button' style='display:none' class='moveright'>&gt;</button><div class='include'><input type='checkbox' name='date_uploaded' checked='checked'/> Include in CSV</div></th>";
		$csv.='"Date Created","Date Updated"';
	}
	else
		$csv=substr($csv, 0, -1);
	
	$html .= "</th>";
	$csv.="\n";
	
	/****************************************Draw the form answers into the $html variable.**********************************************/
	if (!empty($table)){
	
		foreach ($table as $key => $value){	
			if ($user_login_required){
				$userinfo=ldap_user_info($value['userid']); //Get the username and name of the user we're currently looping
				$value['user']=$userinfo[0]['givenname'][0]." ".$userinfo[0]['sn'][0];
			}
			$html.="<tr>";
			if ($user_login_required && empty($unencoded_form_structure)){
					$html.="<td><div>".$value['user']."</div></td>";
					$value['user']=str_replace('"',"'", $value['user']);
					$csv.='"'.$value['user'].'",';
				}
			foreach ($elements as $val){ //Get all of the answers of either the original or modified form and draw them into one row

				$v=$value[$val];
				//case insensitive array keys for legacy functionality
				if (empty($v))
					$v=$value[strtolower($val)];
				$html.="<td><div>".$v."</div></td>";
				$v=str_replace('"',"'", $v);
				$csv.='"'.$v.'",';
				
			}
			if (empty($unencoded_form_structure)){ //Finish drawing the remaining columns if we're using the original form structure. We don't need to worry about these in the modified form structure as they already are stored.
				$html.="<td><div>".$value['date_created']."</div></td><td><div>".$value['date_updated']."</div></td>";
				$csv.='"'.$value['date_created'].'","'.$value['date_updated'].'"';
			}
			$html.="</tr>";
			$csv.="\n";
		}
	}
	$html.="</table>";

	$fh=fopen("tmp/".$formname."_".date('mdy').".csv","w"); //Write information to csv
	fwrite($fh,$csv);
	fclose($fh);
?>
 <!----------------------------------------------------------------------Presentation------------------------------------------------------------------------------------->

<?php if (empty($_GET['formid'])): //If formid is empty, we're just going to display all of the reports that are available. ?>
	<p><b>If you want more flexible reporting functionality, please check out the "Reports Beta" tab. It allows you to create more comprehensive reports from scratch.</b></p>
	<div id='container' class='bordered' style='margin-top: 15px; width: 75%; margin-left: 0px; padding:10px;'>
		<div id='reports'>
			<div class='container_heading'><h2>Generate Reports</h2></div>
			<ul>
				<?php while ($form=mysql_fetch_array($report_query)):?>
					<li><a href='?formid=<?php echo $form[0]; ?>'><?php echo $form['name']; ?></a>
					<?php if ($form['date'] < date("U")): ?>
					ENDED
					<?php endif; ?>
					</li>
				<?php endwhile; ?>
			</ul>
		</div>
	</div>
	<?php exit; ?>
<?php endif; ?>
<?php
	if (empty($_SESSION['superadmin'])){
		$report_query=mysql_query("SELECT * FROM fb_savedforms LEFT JOIN permissions on formid=fb_savedforms.id WHERE sitename='".$forwarded_directory."' AND ((user='".$_SESSION['userid']."' AND (view_report=1 OR reports_no_restrictions=1)) OR userId='') AND fb_savedforms.id=".$_GET['formid']);
		debug('Report Query', "SELECT * FROM fb_savedforms LEFT JOIN permissions on formid=fb_savedforms.id WHERE sitename='".$forwarded_directory."' AND ((user='".$_SESSION['userid']."' AND (view_report=1 OR reports_no_restrictions=1)) OR userId='') AND id=".$_GET['formid'], $_GET['debug']);
		if (mysql_num_rows($report_query)==0)
			exit;
	}
?>
<h3><?php echo $formname; //If we have a formid, draw the table of data. ?></h3>
<div id='filter_date'>
	<form action='' method='GET'>
	<span>Enter a date range: </span> From: <input id="from_date"  type="text" name="from_date"> To: <input id="to_date"  type="text" name="to_date"> <input type='submit' value='Filter report'/><input type='hidden' name='formid' value='<?php echo $_GET['formid']; ?>'/>
    </form>
</div>
<form id='reports_form'>
<?php echo $html; //Output the table of data ?>
</form>
<br/>
<a id='rightclick' style='text-decoration:none; color:#000000;' href='scripts/downloadfile.php?file=<?php echo urlencode($formname);?>_<?php echo date('mdy');?>.csv'><img border='0' src='images/CSV.png'/> Click to Download csv.</a>
<br/>
<br/>
<a href='reports.php'><< Back</a>
<?php include("includes/footer.php"); ?>