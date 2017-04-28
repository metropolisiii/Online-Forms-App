<?php
	/**
	* Stores the end-users' forms answers and displays a 'thank you' message.
	*
	* @author Jason Kirby <jkirby1325@gmail.com>
	*
	*/
	
	include_once("scripts/settings.php");
	include("includes/header.php");
	include("lib/phpmailer.php");
	require_once('lib/html2pdf/html2pdf.class.php');
	
	$url="";
	$badfile=false;
	$dontinsert=false;
	if ((!$user_login_required && empty($_POST)) || (empty($_POST) && empty($_SESSION['userid'])))
		exit;
	$_POST=sanitize($_POST);
	
	if ($user_login_required && empty($_POST['userid']))
		$userid=$_SESSION['userid'];
	else if ($user_login_required && !empty($_POST['userid']))
		$userid=$_POST['userid'];
	else{
		$userid="__none__";
		if (empty($_POST['url']))
			$url=generate_random_url();
		else
			$url=$_POST['url'];
	}
	
	$update=false; //Are we inserting or updating user's record? Initially set to insertion.	
	
	$fh=fopen("/var/log/forms_app/form_answers.txt", "a"); //A log for answers for safe keeping
	if ($user_login_required)
		fwrite($fh,"__________________________________________________________________".$userid."______________________________________");
	else
		fwrite($fh,"__________________________________________________________________".$url."______________________________________");
	$x=print_r($_POST, true);
	fwrite($fh,$x."\n");
	fclose($fh);
	/**
	*	If an admin is reviewing the form, we need to make sure that the user's form that is being reviewed is updated and not the admin's personal form. In the case where admin is reviewing a form, a hidden field in the form which denotes the end-user's id is passed into this script. Otherise the $_POST[id] is blank and the $_SESSION[userid] variable is used instead.
	*/
	
	$query = "SELECT filename from fb_savedforms WHERE id = ".$_POST['fid'];
	$refer_query = mysql_query($query);
	$refer_result = mysql_fetch_object($refer_query);
	$referer = str_replace(' ','%20',$refer_result->filename);
	
	$full_referer="";
	$full_referer.="https://www.mycompany.com/forms/forms/".$referer;
	
	$query="SELECT user_form.id, num_times_filled_out FROM user_form INNER JOIN fb_savedforms ON fb_savedforms.id=user_form.formid WHERE user_form.userid='".$userid."' AND formid=".$_POST['fid']." AND (enabled=0 OR accepted IS NOT NULL)  AND user_form.url='".$url."'";
	$result=mysql_query($query); //Make sure the form is elegible to be reviewed. If it's not enabled or already accepted, we dont want to re-store the form and confuse admins.
		
	if (mysql_num_rows($result)==0 || (!empty($userid) && $_SESSION['membertype']=='admin')){ //If the form is eligable to be stored
		$query="SELECT id FROM user_form WHERE userid='".$userid."' AND formid=".$_POST['fid']." AND url='".$url."'"; //Get the form information. If it exists, we're updating it. If not, we're inserting it.
		$query=mysql_query($query);
		if (mysql_num_rows($query)>0){
			$update=true;
			$id=mysql_fetch_array($query);
			$id=$id['id'];
			$form_answers_query=mysql_query("SELECT * FROM form_answers WHERE user_form_id=".$id." ORDER BY id");
			
			
			//mysql_query("UPDATE form_answers set response='' WHERE user_form_id=".$id." AND custom != 1");
			mysql_query("UPDATE user_form set date_updated=".date("U").", pagename='".$referer."' WHERE id=".$id); 
		}
		else{
			$query=mysql_query("SELECT id FROM user_form WHERE userid='".$userid."' AND formid=".$_POST['fid']);
			$num_times_filled_out=mysql_num_rows($query);
			$query=mysql_query("SELECT num_times_filled_out FROM fb_savedforms WHERE id=".$_POST['fid']);
			$n=mysql_fetch_array($query);
			$num_times_can_be_filled_out=$n['num_times_filled_out'];

			if ($num_times_filled_out<$num_times_can_be_filled_out || $num_times_can_be_filled_out==0 ){
				$query="INSERT INTO user_form (userid, formid, date_created, pagename, url) VALUES ('".$userid."', ".$_POST['fid'].", ".date("U").", '".$referer."','".$url."')";
				mysql_query($query);
				$id=mysql_insert_id();
			}
			else
				$dontinsert=true;
		}
	
		if (!$dontinsert){
			//Discount Codes
			
			foreach($_POST as $key=>$value){
				if (substr($key,0,3) == 'dc-'){
					//look up discount code
					$result=mysql_query("SELECT * FROM discounts WHERE code = '".trim($value)."' AND form_id=".$_POST['fid']);
					//if code is valid, calculate the discount and apply to the total amount
					if (mysql_num_rows($result)>0){
						$discount=mysql_fetch_array($result);
						$multiplier = str_replace(" ","_",$discount['multiplier']);
						$multiplier = $_POST[$multiplier];
						if (!$multiplier)
							$multiplier=1;
						$discount_amount=$discount['amount']*$multiplier;
						if ($discount_amount == -999)
							$_POST['authamount'] = 0;
						else
							$_POST['authamount']=$_POST['base_amount']-$discount_amount;
					}
				}
			}
			$form_answers=array();
			$result=mysql_query("SELECT field_id, response FROM form_answers WHERE user_form_id=".$id);
			while ($rec=mysql_fetch_object($result)){
				$form_answers[$rec->field_id]=$rec->response;
			}
			$changes = ""; //This will be used to notify the admin when a form is submitted with changes
			foreach ($_POST as $key=>$value){ //Updating or inserting form_answers
				$add_to_change_log = false;
				if ($key != "submit" && $key != "saveforlater"){
					if (is_array($value)) //Elements with multiple values such as checkboxes, radio buttons, etc
						$value=implode($value,";");
					$value=mysql_real_escape_string($value);					
					if (!isset($form_answers[$key])){ 
						mysql_query("INSERT INTO form_answers (field_id, response, user_form_id)  VALUES ('".$key."', '".$value."', ".$id.")");
						$add_to_change_log = true;
					}
					else if ($form_answers[$key] != $value){
						mysql_query("UPDATE form_answers set response = '".$value."' WHERE  field_id='".$key."' AND user_form_id=".$id);
						$add_to_change_log = true;
					}
					$changed_val = false;
					if ($key !== "url" && $update && $add_to_change_log){
						$changes.="<b>Field:</b> ".$key."&nbsp;&nbsp;&nbsp;<b>Previous answer:</b> ".$form_answers[$key]."&nbsp;&nbsp;&nbsp;<b>New Answer:</b> ".$value."<br/>";
						mysql_query("INSERT INTO change_log (form_answer_id, previous_answer, userid, date) VALUES (".$id.",'".$form_answers[$key]."', '".$_SESSION['userid']."',".date("U").")");
					}
					unset($form_answers[$key]);
				}
			}
			/* Take into account blank checkboxes */
			foreach ($form_answers as $key=>$value){
				mysql_query("UPDATE form_answers set response='' WHERE field_id='{$key}' AND user_form_id={$id} AND custom != 1");
				if ($value != "")
					$changes.="<b>Field:</b> ".$key."&nbsp;&nbsp;&nbsp;<b>Previous answer:</b> ".$value."&nbsp;&nbsp;&nbsp;<b>New Answer:</b> <br/>";
			}
				
				
			
			
			/*****************************************************************Take care of files if any **********************************************************************************/
	
			if (!empty($_FILES)){
				foreach ($_FILES as $key=>$value){
					$target_path = "files/";
					
					if (!empty($value['name']) && ($value['size']/1024)<6000096){
						$result=mysql_query("SELECT id FROM files where user_form_id=".$id." AND field='".$key."'");
						$target_path = $target_path . $id."_".basename($_FILES[$key]['name']); 
		
						if(move_uploaded_file($_FILES[$key]['tmp_name'], $target_path)) {
							echo "<br/>The file ".  basename( $_FILES[$key]['name'])." has been uploaded.<br/>";
							$filename[]=$target_path;
						} else{
							echo "<br/><h3>There was an error uploading the file ".basename( $_FILES[$key]['name']).", please try again!</h3>";
							mail("jason.kirby@mycompany.com","BAD FILE!!!", print_r($_POST, true)." <br/>".print_r($_FILES, true));
							$badfile=true;
						}
						if (mysql_num_rows($result)==0){
							mysql_query("INSERT INTO files (name, user_form_id, field) VALUES ('".$value['name']."', ".$id.", '".$key."')");
						}
						else{
							$f=mysql_fetch_array($result);
							mysql_query("UPDATE files set name='".$value['name']."' WHERE id=".$f['id']);
						}	
						$log->log(" Uploaded a file: ".basename($_FILES[$key]['name']));
					}
					else{
						if (!empty($value['name'])){
							
							if (!in_array($value['type'], $mimetypes)){
								echo "<br/><h3>File was not uploaded because it was not a supported type.</h3><br/>";
								mail("jason.kirby@mycompany.com","BAD FILE!!!", print_r($_POST, true)." <br/>".print_r($_FILES, true));
								$badfile=true;
								$log->log("Attempted to upload a file, but it was not a supported type");
							}
						}
					}
				}
			}
		}
		/**
		* The user can either submit the for or save it for later. If it is submitted, we need to notify the record that it is submitted, hence putting it in a pending status and sending an email to the form admin. If it is saved for later, it is put into an incomplete status and no email is sent.
		*/
		
		if (isset($_POST['submit']) || isset($_POST['authorizepayment']) || isset($_POST['vendorpayment']) || isset($_POST['authorizepaymenttest']) || isset($_POST['generateinvoice']) || isset($_POST['Conferences_Submit'])){
			$field_list="";
		
			$admin_field_list="";
			$excludefields=array('fid', 'url', 'userid', 'submit', 'recpatcha challenge field', 'recaptcha response field','g-recaptcha-response','pdf_filename_list');			
			
			foreach ($_POST as $key=>$value){
				
				if (!in_array($key, $excludefields)){
					$value=str_replace("\\r\\n", "<br/>", $value);
					$value=str_replace("\\'","'", $value);
					$value=str_replace('\\"','"', $value);
					if (strlen($field_list) > 700){
						$field_list.="\r\n";
					}
					if (is_array($value)){
						$v="";
						foreach ($value as $val)
							$v.=str_replace("_"," ",$val)."<br/>";
						$value=$v."<br/>";
					}
					$field_list.=($key == "static_text_field"?"":str_replace("_"," ",$key).": ").$value."<br/><br/>";				
				}
				/*
					if (is_array($value)){
					$v='';
					foreach ($value as $val)
						$v.=$val." ";
					$value=$v;
				}
				$admin_field_list.=str_replace("_"," ",$key)." = ".$value."<br/>";
				*/
				
			}
			
			$result=mysql_query("SELECT userId, name, notifyees, filename,email_confirmation_to_administrator, email_confirmation_to_customer, filename,email_confirmation_to_administrator_subject, email_confirmation_to_customer_subject, notification_email, invoice from fb_savedforms WHERE id=".$_POST['fid']); //Get notifyees to send emails to.
			$savedform=mysql_fetch_array($result);
			
			mysql_query("UPDATE user_form set submitted=1 WHERE id=".$id);
			if (($_POST['Conferences_Submit'] && $_POST['conferenceaccount']) || ($_POST['authorizepayment'] && $_POST['conferenceaccount'])){
				
				$result=mysql_query("SELECT forms.id, accounts.id as account from conferences.forms INNER JOIN conferences.accounts ON accounts.id=forms.user_id WHERE user_form_id=".$id);
				if (mysql_num_rows($result) == 0){
					$result=mysql_query("SELECT id from conferences.accounts where email='".$_POST['conferenceaccount']."'");
					if (mysql_num_rows($result)>0){
						$rec=mysql_fetch_object($result);
						mysql_query("INSERT INTO conferences.forms VALUES (NULL, ".$id.", '".$rec->id."')");
						$formid = mysql_insert_id();
					}
				}
				else{
					$formid = mysql_fetch_object($result);
					$formid = $formid->id;
				}
				
				if (strpos($savedform['name'], 'Exhibitor Registration')){
					mysql_query("DELETE FROM conferences.action_log WHERE user_form_id = {$id}");
					mysql_query("INSERT INTO conferences.action_log (user_form_id) VALUES (".$id.")");
					$redirect='conferences/payment.php';
					if (!isset($_POST['url']) || $_POST['url'] == '')
						$redirect .= '?action=generate_invoice';
					if (isset($_SESSION['type']) && $_SESSION['type'] == 'admin'){
						$result = mysql_query("SELECT a.id FROM conferences.accounts a INNER JOIN conferences.forms f ON a.id=f.user_id WHERE f.user_form_id={$id}");
						$rec = mysql_fetch_object($result);
						$redirect.='?id='.$rec->id;
					}
					//Update invoice version_compare
					mysql_query("UPDATE conferences.accounts a INNER JOIN conferences.forms f ON a.id=f.user_id SET invoice_version = invoice_version+1 WHERE f.user_form_id={$id}");
					echo "
						<script>
					//		var websocket = new WebSocket('ws://itweb.mycompany.com:8080');
					//		websocket.onopen = function(e) {
					//			console.log('test');
					//			websocket.send(JSON.stringify({'type':'update', 'id':$formid}));
								window.location='{$redirect}';
					//		};							
						</script>
					";
					
				}
				else
					echo "<script>window.location='conferences/';</script>";
			}
			if (isset($_POST['authorizepayment']))
				mysql_query("UPDATE user_form set declined=1 WHERE id=".$id); //Assume the user's card will decline until it actually gets accepted
			
			
			if ($_SESSION['membertype'] != 'admin'){
				
				$notifyees=explode("\n", $savedform['notifyees']);
				
				$headers = 'From: '.$form_email . "\r\n" .
							'Reply-To: '.$form_email. "\r\n" .
							"Content-type: text/html; charset=\"UTF-8\"; Content-Transfer-Encoding: quoted-printable;  format=flowed \r\n" .
							'X-Mailer: PHP/' . phpversion();
				$user=($user_login_required)?$_SESSION['userid']:"A user";
				$subject_field = prepareMessage($savedform['email_confirmation_to_administrator_subject'], 'Form for '.$savedform['name'], false);
				$message_field = htmlspecialchars_decode(prepareMessage($savedform['email_confirmation_to_administrator'], $user." has ".($update?'updated':'filled out')." a form for ".$savedform['name'].".<br/><br/>".$field_list, $field_list));
				if ($changes != "")
					$message_field = $message_field."<br/><br/><u>Updates</u><br/>".$changes;
				$message_field = wordwrap($message_field, 600,"\r\n");
				/*******************************************************************************************************************************************  CREATE PDF TO ATTACH TO NOTIFICATION EMAIL **************************************************************************************************************************************/
				
				$pdf_list="<h3>".$savedform['name']."</h3><div style='font-size:11px'>".$message_field."</div>";
				if (!isset($_POST['pdf_filename_list']))
					$file='files/pdf/'.$savedform['name']."_".$id.".pdf";
				else
					$file = 'files/pdf/'.$_POST['pdf_filename_list'];
				$html2pdf = new HTML2PDF('P','C4','fr');
				
				$html2pdf->WriteHTML($pdf_list);
				$html2pdf->Output($file, "F");
				/**************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
				
				$mail = new PHPMailer();
				$mail->CharSet = 'UTF-8';
				foreach ($notifyees as $email){
					 $mail->AddAddress($email);
				}
				if ($savedform['notification_email'])
					$form_email=$savedform['notification_email'];
				$mail->From         = $form_email;
				$mail->FromName     = $form_email;
				$mail->Subject      =  $subject_field;			
				$mail->Body         = $message_field;
				$mail->isHTML(true);    
				$mail->AddAttachment($file);
				if ($filename){
					foreach($filename as $key=>$value){
						if ($value)
							$mail->AddAttachment($value);
					}
				}
				if (isset($_POST['generateinvoice'])){
					
					$invoice = htmlspecialchars_decode(prepareMessage($savedform['invoice'], "", $field_list, $id));	
					$invoice_file='files/pdf/'.$savedform['name']."_".$id."_invoice.pdf";
					$html2pdf = new HTML2PDF('P','C4','fr');
					$html2pdf->WriteHTML($invoice);
					$html2pdf->Output($invoice_file, "F");
					$mail->AddAttachment($invoice_file);
				}
				$mail->send();
				
				//Get confirmation emails
				$query = mysql_query("SELECT value from confirmation_emails where form_id=".$_POST['fid']);
				
				if (mysql_num_rows($query)>0){
					$mail = new PHPMailer();
					$mail->CharSet = 'UTF-8';
					while ($email=mysql_fetch_array($query)){
						 $mail->AddAddress($_POST[str_replace(array(" ",":"),array("_",""),$email['value'])]);
					}
					$subject_field = prepareMessage($savedform['email_confirmation_to_customer_subject'], 'Email confirmation for '.$savedform['name'], false);
					
					$message_field = htmlspecialchars_decode(prepareMessage($savedform['email_confirmation_to_customer'], "Thank you for filling out the form for ".$savedform['name'].". If we have any questions, we will contact you shortly.", $field_list, $url));
					
						
					$mail->From         = $form_email;
					$mail->FromName     = $form_email;
					$mail->Subject      = $subject_field;		
					$mail->Body         = $message_field;
					$mail->isHTML(true);    
					
					if ($filename){
						foreach($filename as $key=>$value){
							if ($value)
								$mail->AddAttachment($value);
						}
					}
					if (isset($_POST['generateinvoice'])){
						$mail->AddAttachment($invoice_file);						
					}
					if (isset($_POST['Return_Path'])) //Return_Path needs to be set as a hidden field in order for the form notifyees to receive information on email bounces. Eventually, a widget will be created to capture these.
						$mail->Sender = $_POST['Return_Path'];
					$mail->send();
				}
				if ($invoice_file)
					unlink($invoice_file);
			}
			
		}
		if (isset($_POST['saveforlater']))
			mysql_query("UPDATE user_form set submitted=0 WHERE id=".$id);
		$result=mysql_query("SELECT url, thank_you_page_message, thankyou_url FROM fb_savedforms WHERE id=".$_POST['fid']);
		$url2=mysql_fetch_array($result);

		if (!empty($url2['thankyou_url']) && $url2['thankyou_url'] !=='http://'){
				$string = '<script type="text/javascript">';
				$string .= 'window.parent.location = "' . $url2['thankyou_url'] . '"';
				$string .= '</script>';
				echo $string;
		}
		if (!empty($url2['thank_you_page_message'])){ //If the admin has created a custom thank you message
			if (!empty($url2['url']) && $url2['url'] !=='http://'){
				$thank_you=str_replace("[form_link]", "<a href='".$url2['url']."?q=".$url."' target='_parent'>".$url2['url']."?q=".$url."</a>", htmlspecialchars_decode($url2['thank_you_page_message']));
			}
			else {
				$thank_you=str_replace("[form_link]",'<script>document.write("<a target=\'_parent\' href=\'"+window.location.protocol+"//"+window.location.host+"/forms/forms/'.$referer.'?q='.$url.'\'>"+window.location.protocol+"//"+window.location.host+"/forms/forms/'.$referer.'?q='.$url.'</a>")</script>', htmlspecialchars_decode($url2['thank_you_page_message']));
				$thank_you=str_replace("[random_url]", $url, $thank_you);
			}
		}	
		else{ //Else use a hard coded thank you message
			$thank_you="Thank you for filling out this form. Submission of this form has been successful. <br/><br/>You may continue this form by going to <br/><br/>";
			if (!empty($url2['url']) && $url2['url'] !=='http://' && $url2['url'] !=='undefined')
				$thank_you.= "<p><a href='".$url2['url']."?q=".$url."' target='_parent'>".$url2['url']."?q=".$url."</a></p>";			
			else{
				$thank_you.='<script>document.write("<a  target=\'_parent\' href=\''.$full_referer;
				if (empty($_POST['url'])) 
					$thank_you.="?q=".$url;
				$thank_you.='\'>'.$full_referer;
				if (empty($_POST['url'])) 
					$thank_you.="?q=".$url;
				$thank_you.='</a>")</script> </p><p>Please be sure to copy this link to a place where you can find it.</p><br/><br/>';
			} 
		}
		//If the 'save for later' button is pressed
		if (!empty($url2['save_for_later_message'])){
		
		}
		else{ //If no message specified, use a hard coded message
			$save_for_later_message = "Your answers have been saved but the form has not been submitted. You may continue filling out the form at a later time by going to <br/><br/>";
			if (!empty($url2['url']) && $url2['url'] !=='http://' && $url2['url'] !=='undefined')
				$save_for_later_message.= "<p><a href='".$url2['url']."?q=".$url."' target='_parent'>".$url2['url']."?q=".$url."</a></p>";			
			else{
				$save_for_later_message.='<script>document.write("<a  target=\'_parent\' href=\''.$full_referer;
				if (empty($_POST['url'])) 
					$save_for_later_message.="?q=".$url;
				$save_for_later_message.='\'>'.$full_referer;
				if (empty($_POST['url'])) 
					$save_for_later_message.="?q=".$url;
				$save_for_later_message.='</a>")</script> </p><p>Please be sure to copy this link to a place where you can find it.</p><br/><br/>';
			} 
		}
		if (!empty($url2['url']) && $url2['url'] != "http://" && $url2['url'] !== "undefined")
			$url2=$url2['url'];
		else 
			$url2="";	
	}
	if (!empty($_POST['first_name']))
		$firstname=$_POST['first_name'];
	else if (!empty($_POST['First_Name_of_Person_Being_Registered']))
		$firstname=$_POST['First_Name_of_Person_Being_Registered'];
	if (!empty($_POST['last_name']))
		$lastname=$_POST['last_name'];
	else if (!empty($_POST['Last_Name_of_Person_Being_Registered']))
		$lastname=$_POST['Last_Name_of_Person_Being_Registered'];

	//Add-ons
	if (!empty($_POST['hidden-include'])){
		if (file_exists('addons/'.$_POST['hidden-include'].'.php'))
			include('addons/'.$_POST['hidden-include'].'.php');
		else if (file_exists('addons/'.$_POST['hidden-include'].'/index.php')){
		
			include('addons/'.$_POST['hidden-include'].'/index.php');				
		}
		if (function_exists('addonRun')){
			addonRun();
		}
	}
?>
 <!----------------------------------------------------------------------Presentation------------------------------------------------------------------------------------->

 <?php if (!empty($stylesheet)): ?>
<link type="text/css" rel="stylesheet" href="css/<?php echo $stylesheet;?>.css">
<?php endif; ?>
 <div id='container'>
	<?php if(isset($_POST['submit'])): //Standard submit ?>
		<?php echo $thank_you; ?>
		<?php $log->log("Submitted a form for form ".$_POST['fid']); ?>
	<?php elseif (!$user_login_required && !empty($_POST['saveforlater'])): //User is saving forms for later and a user login is required to complete their form ?>
		<?php echo $save_for_later_message; ?>
		<?php $log->log("Saved a form for later for form ".$_POST['fid']); ?>
	<?php elseif (isset($_POST['authorizepayment'])): //If the user must move onto a payment screen for authorize.net ?>
		<?php $log->log("Submitted a form for form ".$_POST['fid']);?>
		<script>  
			window.location = 'scripts/authpayment.php?id=<?php echo $id; ?>&a=<?php echo $_POST['authamount']; ?>&p=<?php echo $firstname." ".$lastname; ?>&bc=<?php echo $_POST['billcode'];?>';  
		</script>  
	<?php elseif (isset($_POST['authorizepaymenttest'])): //If the user must move onto a payment screen for authorize.net ?>
		<?php $log->log("Submitted a form for form ".$_POST['fid']); ?>
		<script>  
			window.location = 'scripts/authpaymenttest.php?id=<?php echo $id; ?>&a=<?php echo $_POST['authamount']; ?>&p=<?php echo $firstname." ".$lastname; ?>&bc=<?php echo $_POST['billcode'];?>&hidden-include=<?php echo $_POST['hidden-include'];?>';  
		</script>  
	<?php elseif (isset($_POST['vendorpayment'])):?>
		<?php
			$count=0;
			foreach ($_POST as $key=>$value){
				if (strpos($key, 'first_name')>0 && $value != '')
				$count++;
			}
			$num_charged=$count-6;
			
			if ($num_charged >= 0)
				$amount=$num_charged*$_POST['baseamount'];
			else
				$amount=0;
			if ($amount==0){
				echo "
				<script>  
					window.location = 'finish.php?id=".$id."&amount=0&url=".$url2."&link=".$url."';  
				</script>
				";  
			}
		?>
		<?php $log->log("Submitted a form for form ".$_POST['fid']); ?>
		<script>  
			window.location = 'scripts/vendorpayment.php?id=<?php echo $id; ?>&a=<?php echo $amount?>&p=<?php echo $_POST['referrer']; ?>&bc=<?php echo $_POST['billcode'];?>';  
		</script>  
	<?php else:  //Standard save for later functionality. ?>
		<?php echo $thank_you; 	?>
	<?php endif; ?>
	<?php if (isset($_POST['subform']['form'])){
		$p="";
		if (isset($_POST['subform']['field1']))
			$p.=$_POST[$_POST['subform']['field1']];
		if (isset($_POST['subform']['field2']))
			$p.=" ".$_POST[$_POST['subform']['field2']];
	?>
		<script>
			window.location='forms/<?php echo $_POST['subform']['form']; ?>?id=<?php echo $id; ?>&p=<?php echo urlencode($p); ?>';
		</script>
	<?php } ?>
	<br/><br/>
	<?php if ($_SESSION['membertype'] == "admin"):?>
		<a href='admin.php'>Back to administration</a>
	<?php elseif ($user_login_required): ?>
		<a href='index.php'>Back to your account</a>
<?php endif; ?>
</div>
<?php include ("includes/footer.php"); ?>
