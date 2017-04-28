<?php
include("../includes/connect.php");
$result=mysql_query("select user_form_id, forms.user_id from forms_app.user_form INNER JOIN conferences.forms on user_form_id = user_form.id WHERE formid=599");

while ($rec = mysql_fetch_array($result)){
	
  mysql_query("update forms_app.form_answers set response = 'SC2014-".$rec['user_id']."' where field_id='Invoice_Number' and user_form_id = ".$rec['user_form_id']);
  echo "update form_answers set response = 'SC2014-".$rec['user_id']."' where field_id='Invoice_Number' and user_form_id = ".$rec['user_form_id'].'<br/>';
}