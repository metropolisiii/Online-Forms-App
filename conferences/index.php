<?php

	$title='Conference User Account';
	$head="
		<script>
			$(document).ready(function(){
				if(window.location.href.indexOf('interops_forms') > -1)
					window.location='/forms/conferences/';
			});
		</script>
	";
	include_once("scripts/settings.php");
	include_once("includes/connect.php");
	
	//Get account's forms
	if ($_SESSION['conf_user']){
		$email=$_SESSION['conf_user'];
		$query="SELECT user_form.id as id, name, pagename, user_form.url FROM forms INNER JOIN accounts ON accounts.id=forms.user_id INNER JOIN forms_app.user_form ON user_form.id=forms.user_form_id INNER JOIN forms_app.fb_savedforms ON fb_savedforms.id=user_form.formid WHERE accounts.email='{$email}'";
		$result=mysql_query($query);	
		$html='';
		
		$vendor_registration=false;
		$has_private_room=false;
		while ($rec=mysql_fetch_object($result)){
			$html.='<div class="row">
				<div class="col-md-6 form_name"><a href="../forms/'.$rec->pagename.'?q='.$rec->url.'">'.$rec->name.'</a></div>
				<div class="col-md-6 form_controls">
					<div class="modify_form"><img class="delete_form" id="form_'.$rec->id.'" src="images/delete.png"/></div>
				</div>						
			</div>';
		
			if (strpos($rec->name,'Exhibitor Registration')){
				$vendor_registration=true;
				$query = "SELECT response from forms_app.form_answers WHERE user_form_id=".$rec->id." AND field_id = 'Demo_type' AND response like '%Demo_Room%'";
				$results2 = mysql_query($query) or die(mysql_error());
				if (mysql_num_rows($results2)>0)				
					$has_private_room = true;
			}
			if (strpos($rec->name, 'Additional Personnel') !== false)
				$has_private_room = false;
		}
		//Get administration user list to view invoices
		if (isset($_SESSION['type']) && $_SESSION['type'] == 'admin'){
			$admin_html='';
			$query = "SELECT accounts.id, form_answers.response FROM forms INNER JOIN accounts ON forms.user_id = accounts.id INNER JOIN forms_app.user_form ON forms.user_form_id=user_form.id INNER JOIN forms_app.form_answers ON user_form.id=form_answers.user_form_id WHERE field_id = 'Company' ORDER BY response";
			
			$result=mysql_query($query);
			while ($rec=mysql_fetch_object($result)){
				$admin_html.='
					<div class="col-md-6 account_company">'.$rec->response.'</div>
					<div class="col-md-6 view_invoice"><a href="payment.php?id='.$rec->id.'">View Invoice</a></div>
				';
			}
		}
		else if (mysql_num_rows($result) > 0)
			$html.='<div style="margin-top:35px" id="view_invoice_button"><a class="btn btn-success" href="payment.php" target="_blank">View Invoice</a></div>';
	}
?>

<body>
	<?php include('includes/nav-header.php'); ?>
	<div id="main" class="main_container">
		<div class="content">
			<div class="row">
				<?php if ($_SESSION['error']): ?>
					<p class='error'><?php echo $_SESSION['error']; ?></p>
					<?php unset($_SESSION['error']); ?>
				<?php endif; ?>
				<?php  if ($_SESSION['conf_user']): ?>
					<h3>Forms for <?php echo $_SESSION['conf_user']; ?></h3>
					<div id='form_list'>
						<?php echo $html; ?>
						<?php if (!$vendor_registration): ?>
							<div class="row">
								<div class="col-md-6 form_name"><a target="" href="<?php echo VENDOR_REG_URL; ?>"><?php echo EVENT_NAME;?></a> - <span class='needs_completion'>Needs Completion!</span></div>
							</div>
						<?php endif; ?>
						<?php if ($has_private_room): ?>
							<div class="row">
								<div class="col-md-6 form_name"><a target="" href="<?php echo VENDOR_REGISTRATION_ADDITIONAL_PERSONNSEL_URL; ?>">Additional Participants - $750/person (If you want to bring more than 6 people for your table)</a></div>
							</div>							
						<?php endif; ?>					
					</div>
					
					<?php if ($admin_html): ?>
						<hr/>
						<h1>Conferences Administration</h1>
						<div id='conference_checkin_link'><a class='small_button' href='checkin.php'>Conference Check-In</a></div>
						<div id='accountant_report' class='button'><a class='small_button' href='accounting_report.php'>Download Accounting Report</a></div>
						<div id='admin_user_list'>
							<?php echo $admin_html; ?>
						</div>
					<?php endif; ?>
				<?php else: ?>
					<div id="main" class="main_container">
						<?php if (!IS_OPEN):?>
							<h2>Advanced Registration has closed.</h2>
								<?php if (CLOSED_MESSAGE != ""): ?>
									<?php echo CLOSED_MESSAGE; ?>
								<?php else: ?>
									<p>Onsite registration will open 02/09 at 10 a.m.</p>
									<p>If you have any question, contact <a href='mailto:j.smith@mycompany.com'>John Smith</a></p>
								<?php endif; ?>
						<?php else: ?>				
							<div class='col-md-6 content home_container'>
								<h2>Log Into Your Conference Account</h2>
								<form method='post' action='scripts/login.php'>
									<div class="form-group">
										<input class='form-control' type='text' placeholder='Email' name='email'/>
									</div>
									<div class='form-group'>
										<input class='form-control' type='password' placeholder='Password' name='password'/>
									</div>
									<button class='btn btn-success' type='submit'>Sign in</button>
									<button class='btn btn-success' onclick='window.location="reset_password.php"' type='button'>Forgot Password</button>
								</form>
							</div>
							<div class='col-md-6 content home_container'>
								<h2>Create a New mycompany Conference Account</h2>
								<?php if ($_SESSION['account_exists']): ?>
									<p class='error'>This account already exists. If you have forgotten your password, you may reset your password by following <a href='reset_password.php'>this link</a></p>
									<?php unset($_SESSION['account_exists']); ?>
								<?php endif; ?>
								<form id="create_account_form" method="post" role="form" action='scripts/create_account.php'>
									<div class="form-group">
										<label for="InputPassword1">Company Email</label>
										<input data-validate="validate(required, email, maxlength(100))"  class="form-control" id="email" type="text" name="email"   />
									</div>
									<div class="form-group">
										<label for="InputPassword1">Password</label>
										<input data-validate="validate(required, minlength(6), maxlength(20))"  class="form-control" id="password" type="password" name="password"   />
									</div>
									<div class="form-group">
										<label for="InputPassword2">Verify Password</label>
										<input data-validate="validate(required, match_element(password))" class="form-control" data-match-error="Passwords do not match" type="password" id="verifypassword" name="verifypassword"  />
									</div>
									<div class="captcha" id="fld-"> <script src="https://www.google.com/recaptcha/api.js" async defer></script>
								<div class="g-recaptcha" data-sitekey="6LdYiOgSAAAAAMg2SxnD4D-ycaGm8Mf_npX3csuk"></div>
								</div>
									<button class="btn btn-success" type="submit">Create account</button>						
								</form>
							</div>
						<?php endif; ?>
						<p>Please contact John Smith <a href='mailto:j.smith@mycompany.com'>j.smith@mycompany.com</a> or 303-661-3331.</a></p>
						
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</body>
</html>