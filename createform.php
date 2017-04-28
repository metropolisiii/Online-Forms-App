<?php
    /**
	* Administrative area to create a form
	* 
	* Shows the form builder tool that allows the administrator the ability to add form information in one tab and build the form in another tab.
	* Most of this functionality is jQuery driven. When the user hits 'Save' or 'Done', the save_form function is called in the save.php which is called via a POST AJAX event. Formbuilder/formbuilder_pdo.php and Formbuilder/formuilder.php (parent class) take care of building the form and putting it into a MySQL database.
	*
	* @author Jason Kirby <jkirby1325@gmail.com>
	*
	*/
	include_once("scripts/settings.php");
	$selected='new_form'; //For enabled tab
	include("includes/header.php");
	if ($_SESSION['membertype'] !== "admin" && $_SESSION['membertype'] !=='superadmin'){
		echo "You need to <a href='login.php'> login </a> to enter this area";
		exit;
	}	
	$userinfo=ldap_user_info($_SESSION['userid']); //User information form LDAP
	$email=$userinfo[0]['mail'][0]; //Email address of user
	//Get themes
	$themes_query = mysql_query("SELECT id, name FROM themes");
?>
 <!----------------------------------------------------------------------Presentation------------------------------------------------------------------------------------->
<div id='createform'>
	<!--Form tabs-->
	<div id="form_tabs">
		<ul>
			<li id='tab1' class='formtabs selected'>Form Information</li>
			<li id='tab2' class='formtabs'>Form Design</li>
			<li id='tab3' class='formtabs'>Form Permissions</li>
		</ul>
	</div>
	<!--End form tabs-->
	<!--Form Information-->
	<div id='tab-1' class='tab'>
		<table width='100%'>
			<tr>
				<td align='center' style=' border-right-style: solid; border-width: thin; border-color:#CCCCCC'>
					<table width="100%">
						<tr>
							<td align="left">Form name:</td>
							<td align="left"><input type="text" id="savename" name="savename"/></td>
						</tr>
						<tr>
							<td align="left">Close registration date (mm/dd/yyyy):</td>
							<td align="left"><input type='text' id='form_date' name='date'/></td>
						</tr>
						<tr>
							<td align="left">Enable registration:</td>
							<td align="left"><input type='radio' name='enabled' value='1'/> Yes <input type='radio' name='enabled' checked='checked' value='0'/> No</td>
						</tr>
						<tr>
							<td align="left">Visible to public:</td>
							<td align="left"><input type='radio' name='visible' value='1'/> Yes <input type='radio' name='visible' checked='checked' value='0'/> No</td>
						</tr>
						<tr>
							<td align="left">URL where form will exist:</td>
							<td align="left"><input id="url" type='text' name='url' value="http://" /></td>
						</tr>
						<tr>
							<td align="left">URL of thank you page (optional):</td>
							<td align="left"><input id="thankyou_url"  type='text' name='thankyou_url' value="http://" /></td>
						</tr>
						<?php if ($user_login_required): ?>
						<tr>
							<td align="left">Number of times form can be filled out (zero or leave blank for no limit):</td>
							<td align="left"><input id="num_times_filled_out" type='text' name='num_times_filled_out' size='4' value="" /></td>
						</tr>
						<?php endif; ?>
						<tr>
							<td align="left">Theme:</td>
							<td align="left">
								<select name="theme" id="theme">
									<option value=""></option>
									<?php while ($theme = mysql_fetch_object($themes_query)): ?>
										<option value="<?php echo $theme->id; ?>"><?php echo $theme->name; ?></option>
									<?php endwhile; ?>
								</select>
							</td>
						</tr>
						</table>
				</td>
				<td valign='top' align='center'>
					<table>
						<tr>
							<td>
								<input type='text' id='notify_name'/> 
								<button type='button' id='notify_button'>Add Email Address for Notifications</button><br/>
							</td>
						</tr>
						<tr>
							<td>
								<textarea class="mceNoEditor" cols="34" rows="10" name='notifyees' style=" border-color: #CCCCCC;border-style: solid;" id="notify_textarea"><?php echo $email; ?></textarea>
								<p>Email address used to send notifications: <input type='text' name='notification_email'  id="notification_email" /> </p>
								
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td  style=' border-top-style:solid;border-right-style: solid; border-width: thin; border-color:#CCCCCC' valign='top' align='center'>
					<h3>Email to send users when form is accepted.</h3>
					<textarea  class="mceNoEditor" cols="55" rows="10" name='accepted_email' id='accepted_email'>
[user],

We are pleased to inform you that your registration for this form has been accepted. If you have any further questions, please respond to this email and we'll be happy to assist you.

Thank you,
mycompany form Team
					</textarea>
				</td>
				<td valign='top' align='center'>
					<h3>Email to send users when form is incomplete.</h3>
					<textarea  class="mceNoEditor" cols="55" rows="10" name='declined_email' id='declined_email'>
[user],

In order to approve you for [form], we will need some more information. Please respond to this email for further information.

Thank you,
mycompany
					</textarea>
				</td>
			</tr>
			<tr>
				<td align="center">
					<h3>Message shown when a form is not visible.</h3>
					<textarea  cols="55" rows="5" id='form_invisible_message' name='form_invisible_message'></textarea>
				</td>
				<td align="center">
					<h3>Message shown when registration is not enabled.</h3>
					<textarea  cols="55" rows="5" id='form_no_reg_message' name='form_no_reg_message'></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<h3>"Thank you page" message</h3>
					<textarea  cols="55" rows="5" id='thank_you_page_message' name='thank_you_page_message'>Thank you for filling out this form. Submission of this form has been successful. If there are questions about your responses, we will email you. You may return to fill out your form at [form_link]</textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<fieldset>
						<legend>Email Confirmation Legend</legend>
						<b>[field list]</b> - Displays all fields in vertical list
						<b>{field}</b> - Display the field specified in the brackets
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<h3>Email confirmation message to administrator (optional)</h3>
					<p><b>Subject:</b></p>
					<input type='text' name='email_confirmation_to_administrator_subject' id='email_confirmation_to_administrator_subject'/>
					<p><b>Message:</b></p>
					<textarea cols="55" rows="5" id='email_confirmation_to_administrator' name='email_confirmation_to_administrator'>[field list]</textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<h3>Email confirmation message to customer (optional)</h3>
					<p><b>Subject:</b></p>
					<input type='text' name='email_confirmation_to_customer_subject' id='email_confirmation_to_customer_subject'/>
					<p><b>Message:</b></p>
					<textarea cols="55" rows="5" id='email_confirmation_to_customer' name='email_confirmation_to_customer'>[field list]</textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<h3>Payment Invoice (for forms that use the payment widget)</h3>
					<fieldset>
						<legend>Invoice Legend</legend>
						<b>{field}</b> - Display the field specified in the brackets <br/>
						<b>&lt;eq&gt;...&lt;/eq&gt;</b> - Evaluates a mathematical formula <br/>
						<b>{base_amount}</b> - Displays the base amount before discounts<br/>
						<b>{dc-discount_code_field}</b> - Displays the amount of discounts <br/>
						<b>{authamount}</b> - Displays the net total amount to pay <br/>
					</fieldset>
					<p><b>Message:</b></p>
					<textarea cols="55" rows="5" id='invoice' name='invoice'></textarea>
				</td>
			</tr>
		</table>
		<input type='hidden' id='userId' name='userId' value='<?php echo $_SESSION['userid']; ?>' />
		<input type='hidden' id='sitename' name='sitename' value='<?php echo $_SESSION['forwarded_directory']; ?>' />
	</div>

	<!--Form Design-->
	
	<!--Design tools-->
	<div id='tab-2' class='tab'><div id="form-builder"></div></div>
	<!--Form Permissions-->
	<div id='tab-3' class='tab permissions'>
		<h3>Users</h3>
		<p>These users are allowed to administer this form.</p>
		<table id='usertable' class='formpermissions'>
			<tbody>
				<tr>
					<td></td>
					<th>Edit Form</th>
					<th>View Reports <div><input type='checkbox' id='no_restrictions'/><span style='font-size:.85em'>No restrictions</span></div></th>
				</tr>
				<tr>
					<td><input type="text" class="permissiontext user" readonly value="<?php echo $_SESSION['userid']; ?>" disabled='disabled' /></td>
					<td><input type="checkbox" id="useredit_<?php echo $_SESSION['userid'];?>" checked='checked' disabled='disabled' /></td>
					<td><input type="checkbox" id="userreport_<?php echo $_SESSION['userid']; ?>" checked='checked' disabled='disabled' /></td>
				</tr>
			</tbody>
		</table>
		<div class='inputcontainer'>
			<p><input type='text' name='user' id='userfield' class='searchtext'/> <button id='useradd' type='button'>Add</button></p>
		</div>
	</div>
	<div id="form-status"></div>
</div>
<?php include("includes/footer.php"); ?>
