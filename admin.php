<?php 
	/**
	* Main administrative area as well as the area to edit forms.
	* 
	* If $_GET[id] is set, a form with the given id is being edited. Otherwise, the main administrative screen is shown. 
	* Most of this functionality is jQuery driven. When the user hits 'Save' or 'Done', the save_form function is called in the save.php which is called via a POST AJAX event. Formbuilder/formbuilder_pdo.php and Formbuilder/formuilder.php (parent class) take care of building the form and putting it into a MySQL database.
	* The instantiation for the formbuilder_pdo class causes the form to be drawn with values from the fb_savedforms table in the database.
	* @author Jason Kirby <jkirby1325@gmail.com>
	*
	*/
	
	$selected='home'; //For enabled tab
	include_once("scripts/settings.php");
	if ($_SESSION['membertype'] !== "admin"){
		header("Location: login.php");
		exit;
	}
	
	include("includes/header.php");
	
	$_GET=sanitize($_GET);
	
	require('Formbuilder/Formbuilder.php');
	require('Formbuilder/Formbuilder_pdo.php');
	if (empty($_GET['id']))
		$result=false;
	if ($result){
		if (empty($_SESSION['superadmin']))
			$result=mysql_query("SELECT * FROM fb_savedforms WHERE id=".$_GET['id']." AND sitename='".$forwarded_directory."'"); //Get form information
		else 
			$result=mysql_query("SELECT * FROM fb_savedforms WHERE id=".$_GET['id']); //Get form information
	}
	
	if ($result){
		$form=mysql_fetch_array($result);
	}	
	//Get current forms belonging to form creator
	//If user is a superadmin, get all forms, else get user's forms

	if (isset($_SESSION['superadmin']) && empty($_GET['superadmin']))
		$result=mysql_query("SELECT * FROM fb_savedforms WHERE date>=".date('U')." order by name, LENGTH(name) asc");
	else	
		$result=mysql_query("SELECT * FROM fb_savedforms LEFT JOIN permissions on formid=fb_savedforms.id WHERE date>=".date('U')." AND sitename='".$forwarded_directory."' AND ((user='".$_SESSION['userid']."' AND edit=1) OR userId='') order by name, LENGTH(name) asc "); //Gets current forms
	
	
		
	//Get closed forms belonging to form creator
	//If user is a superadmin, get all forms, else get user's forms
	if (isset($_SESSION['superadmin']) && empty($_GET['superadmin']))
		$result2=mysql_query("SELECT * FROM fb_savedforms WHERE date<".date('U')." order by name, LENGTH(name) asc");
	else	
		$result2=mysql_query("SELECT * FROM fb_savedforms LEFT JOIN permissions on formid=fb_savedforms.id WHERE date<".date('U')." AND sitename='".$forwarded_directory."' AND ((user='".$_SESSION['userid']."' AND edit=1) OR userId='') order by name, LENGTH(name) asc "); //Gets closed forms
	
	//Get permissions
	if (isset($_GET['id']))
		$permquery=mysql_query("SELECT * FROM permissions where formid=".$_GET['id']);
	
	//Get themes
	$themes_query = mysql_query("SELECT id, name FROM themes");
	
	$i=0;
?>

<!----------------------------------------------------------------------Presentation------------------------------------------------------------------------------------->
<?php if (!empty($_GET['id'])): //if a form is chosen ?>
<?php 
	if (!isset($_SESSION['superadmin'])){
		$result=mysql_query("SELECT * FROM fb_savedforms LEFT JOIN permissions on formid=fb_savedforms.id WHERE ((user='".$_SESSION['userid']."' AND edit=1) OR userId='') AND fb_savedforms.id=".$_GET['id']); 
		
		if (mysql_num_rows($result)==0)
			exit;
	}

?>
<div id='createform'>
	<div id="form-status">
		<?php if(!empty($_GET['created'])): //Just a flag to indicate the form has been submitted. ?>
		Form successfully saved
		<?php endif; ?>
	</div>
	
	<div id="form_tabs">
		<ul>
			<li id='tab1' class='formtabs selected'>Form Information</li>
			<li id='tab2' class='formtabs'>Form Design</li>
			<li id='tab3' class='formtabs'>Form Permissions</li>
		</ul>
	</div>
	
	<!--Form informatopn-->
	<div id='tab-1' class='tab'>
		<table width='100%'>
			<tr>
				<td align='center' style=' border-right-style: solid; border-width: thin; border-color:#CCCCCC'>
					<table width="100%">
						<tr>
							<td align="left">Form name:</td>
							<td align="left"><input type="text" id="savename" value="<?php echo $form['name']; ?>" name="savename"/></td>
						</tr>
						<tr>
							<td align="left">Close registration date (mm/dd/yyyy):</td>
							<td align="left"><input type='text' id='form_date' value="<?php echo date('m/d/Y', $form['date']); ?>" name='date'/></td>
						</tr>
						<tr>
							<td align="left">Enable registration:</td>
							<td align="left"><input id='enabled_trued' <?php if ($form['enabled']==1) echo "checked='checked'"; ?> type='radio' name='enabled' value='1'/> Yes <input type='radio' id='enabled_false' <?php if ($form['enabled']==0) echo "checked='checked'"; ?> name='enabled' value='0'/> No</td>
						</tr>
						<tr>
							<td align="left">Visible to public:</td>
							<td align="left"><input type='radio' name='visible' value='1' <?php if ($form['visible']==1) echo "checked='checked'"; ?> id='visible_true'/> Yes <input type='radio' name='visible' id='visible_false' <?php if ($form['visible']==0) echo "checked='checked'";?> value='0'/> No</td>
						</tr>
						
						<tr>
							<td align="left">URL where form will exist (optional):</td>
							<td align="left"><input id="url"  type='text' name='url' value="<?php if (!empty($form['url'])) echo $form['url']; else echo "http://"; ?>" />
						</tr>
						<tr>
							<td align="left">URL of thank you page (optional):</td>
							<td align="left"><input id="thankyou_url"  type='text' name='thankyou_url' value="<?php if (!empty($form['thankyou_url'])) echo $form['thankyou_url']; else echo "http://"; ?>" />
						</tr>
						<?php if ($user_login_required): ?>
						<tr>
							<td align="left">Number of times form can be filled out (zero or leave blank for no limit):</td>
							<td align="left"><input id="num_times_filled_out" type='text' name='num_times_filled_out' size='4' value="<?php echo $form['num_times_filled_out']; ?>" />
						</tr>
						<?php endif; ?>
						<tr>
							<td align="left">Theme:</td>
							<td align="left">
								<select name="theme" id="theme">
									<option value=""></option>
									<?php while ($theme = mysql_fetch_object($themes_query)): ?>
										<option <?php if ($theme->id == $form['theme']) echo "selected=selected"; ?> value="<?php echo $theme->id; ?>"><?php echo $theme->name; ?></option>
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
								<button type='button' id='notify_button'>Add Email Address for Notifications</button>
								<br/>
							</td>
						</tr>
						<tr>		
							<td>
								<textarea class="mceNoEditor" cols="34" rows="10" name='notifyees' id="notify_textarea" style=" border-color: #CCCCCC;border-style: solid;"><?php echo $form['notifyees']; ?></textarea>
								<p>Email address used to send notifications: <input type='text' name='notification_email' id="notification_email" value="<?php echo $form['notification_email']; ?>" /> </p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td  style=' border-top-style:solid;border-right-style: solid; border-width: thin; border-color:#CCCCCC' valign='top' align='center'>
					<h3>Email to send users when form is accepted.</h3>
					<textarea  class="mceNoEditor" cols="55" rows="10" name='accepted_email' id='accepted_email'>
<?php echo trim($form['accepted_email']); ?> 
					</textarea>
				</td>
				<td valign='top' align='center'>
					<h3>Email to send users when form is incomplete.</h3>
					<textarea  class="mceNoEditor" cols="55" rows="10" name='declined_email' id='declined_email'>
<?php echo trim($form['declined_email']); ?> 
					</textarea>
				</td> 
			</tr>
			<tr>
				<td align="center">
					<h3>Message shown when a form is not visible.</h3>
					<textarea cols="55" rows="5" id='form_invisible_message' name='form_invisible_message'><?php echo trim($form['form_invisible_message']); ?></textarea>
				</td>
				<td align="center">
					<h3>Message shown when registration is not enabled.</h3>
					<textarea  cols="55" rows="5" id='form_no_reg_message' name='form_no_reg_message'><?php echo trim($form['form_no_reg_message']); ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<h3>"Thank you page" message</h3>
					<textarea  cols="55" rows="5" id='thank_you_page_message' name='thank_you_page_message'><?php echo trim($form['thank_you_page_message']);?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2" >
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
					<input type='text' name='email_confirmation_to_administrator_subject' id='email_confirmation_to_administrator_subject' value="<?php echo $form['email_confirmation_to_administrator_subject']; ?>"/>
					<p><b>Message:</b></p>
					<textarea cols="55" rows="5" id='email_confirmation_to_administrator' name='email_confirmation_to_administrator'><?php if ($form['email_confirmation_to_administrator']) echo trim($form['email_confirmation_to_administrator']); ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<h3>Email confirmation message to customer (optional)</h3>
					<p><b>Subject:</b></p>
					<input type='text' name='email_confirmation_to_customer_subject' id='email_confirmation_to_customer_subject' value="<?php echo $form['email_confirmation_to_customer_subject']; ?>"/>				
					<p><b>Message:</b></p>
					<textarea cols="55" rows="5" id='email_confirmation_to_customer' name='email_confirmation_to_customer'><?php if ($form['email_confirmation_to_customer']) echo trim($form['email_confirmation_to_customer']); ?></textarea>
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
					<textarea cols="55" rows="5" id='invoice' name='invoice'><?php if ($form['invoice']) echo trim($form['invoice']); ?></textarea>
				</td>
			</tr>
		</table>
		<input type='hidden' id='userId' name='userId' value='<?php if (empty($form['userId'])) echo $_SESSION['userid']; else echo $form['userId']; ?>' />
		<input type='hidden' id='sitename' name='sitename' value='<?php if (empty($form['sitename'])) echo $forwarded_directory; else echo $form['sitename']; ?>' />
	</div>
	<!--Form Designer-->
	<div id="tab-2" class='tab'>
		<div id="form-builder">
			<?php
				$formed = new Formbuilder_pdo();
			?>
		</div>
	</div>
	<!--Form Permissions -->
	<div id='tab-3' class='tab permissions'>
		<h3>Users</h3>
		<p>These users are allowed to administer this form.</p>
		<table id='usertable' class='formpermissions'>
			<tbody>
				<tr>
					<td></td>
					<th>Edit Form</th>
					<th>View Reports <div><input type='checkbox' id='no_restrictions' <?php if ($form['reports_no_restrictions']==1) echo "checked='checked'"; ?> /><span style='font-size:.85em'>No restrictions</span></div></th>
				</tr>
				<?php while ($permission=mysql_fetch_array($permquery)): ?>
				<tr>
					<td><input type="text" class="permissiontext user" value="<?php echo $permission['user']; ?>" readonly/></td>
					<td><input type="checkbox" id="useredit_<?php echo $permission['user'];?>" <?php if ($permission['edit']==1) echo "checked='checked'"; ?> /></td>
					<td><input type="checkbox" id="userreport_<?php echo $permission['user']; ?>" <?php if ($permission['view_report']==1) echo "checked='checked'"; ?> /></td>
				</tr>
				<?php endwhile; ?>
			</tbody>
		</table>
		<div class='inputcontainer'>
			<p><input type='text' name='user' id='userfield' class='searchtext'/> <button id='useradd' type='button'>Add</button></p>
		</div>
	</div>
</div>
<?php else: //if no form is picked (we're in the admin screen) ?>
<div id='container'>
	<div class="notice">
		<p>02/22/2013 - <b>Notice! </b> - Changes have been made to form permissions. Now, when you create a new form, only you have access to that form. A permissions tab has been added to the form editor. If you need to give someone permission to edit forms or view reports, please perform these actions in the "permissions" tab.</p>
	</div>
	
	<?php if (isset($_SESSION['superadmin'])): ?>
		<div class="notice">
			<h5>You are logged in as a super administrator which gives you the ability to handle all forms. </h5>
			<a href='?superadmin=true'>Click here to manage your forms</a><br/>
			<a href='users.php'>Click here to add administrators.</a>
		</div>
	<?php endif; ?>
	
	<p>In order to edit a form, click the name of the form. To preview a form, click the "preview" link next to the form. To review forms, click the "review" link that corresponds with the form you want to review.</p>
	
	<table cellspacing='20'>
		<tr>
			<td class='bordered' width='70%'>
				<div class='subcontainer'>
					<div class='container_heading'><h2>Current forms</h2></div>
					<div class='container'>
						<div id='saveforms'>
							<table>
								<?php while ($form=mysql_fetch_array($result)): ?>
								
								<tr <?php if ($i%2==1): ?> class='odd' <?php endif; ?> >
									<td align='left'>
										<a href='?id=<?php echo $form[0];?>'><?php echo $form['name']; ?></a>
										<a href='forms/<?php echo $form['filename'];?>'>(Preview)</a> 
									</td>
									<td align='left'>
										<?php if ($form['enabled']==1): ?>
											<button id='<?php echo $form[0];?>' class='enable_button'>Disable Registration</button>
										<?php elseif ($form['enabled']==0): ?>
											<button id='<?php echo $form[0];?>' class='disable_button'>Enable Registration</button>
										<?php endif; ?>
										<?php if ($form['visible']==1): ?>
											<button id='<?php echo $form[0];?>' class='visible_button'>Make Invisible to Public</button>
										<?php elseif ($form['visible']==0): ?>
										<button id='<?php echo $form[0]; ?>' class='invisible_button'>Make Visible to Public</button>
										<?php endif; ?>
									</td>
									<td align='right'>
										<button value='<?php echo $form[0]; ?>' class='copy_form'>Copy</button>
										<button class='delete_form' value='<?php echo $form[0]; ?>'>Delete</button>
										<a href='review.php?id=<?php echo $form[0];?>'>Review</a>
									</td>
								</tr>
								<?php $i++; endwhile; ?>
							</table>
						</div>
					</div>
				</div>
			</td>
			<td class='bordered'>
				<div class='subcontainer'>
					<div class='container_heading'><h2>Closed forms</h2></div>
					<div class='container'>
						<table>
							<?php $i=0;	while ($form=mysql_fetch_array($result2)):?>
								<tr <?php if ($i%2==1): ?> class='odd' <?php endif; ?> >
									<td align='left'><a href='?id=<?php echo $form[0]; ?>'><?php echo $form['name']; ?></a></td>
									<td align='right'>
										<button value='<?php echo $form[0]; ?>' class='copy_form'>Copy</button>
										<button class='delete_form' value='<?php echo $form[0]; ?>'>Delete</button>
										<a href='review.php?id=<?php echo $form[0]; ?>'>Review</a>
									</td>
								</tr>
							<?php $i++; endwhile; ?>
						</table>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
<?php endif; ?>

<div id="form-status">
	<?php if(!empty($_GET['created'])): //If 'created' is in query string, print a success message. ?>
	Form successfully saved
	<?php endif; ?>
</div>
<?php include("includes/footer.php"); ?>
