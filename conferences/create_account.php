<?php
	$title='Conference User Account Creation';
	$head="
		<script>
			$(document).ready(function(){
				$.ketchup.validation('match_element', 'Password and verification must match', function(form, el, value, el2){
					if (el.val() === $('#'+el2).val())
						return true;
					else
						return false;					
				});
				$('#create_account_form').ketchup();
			});
		</script>
	";
	include_once("scripts/settings.php");
	include_once("includes/connect.php");
	
	//If user is already logged in, take him to his account
	if (isset($_SESSION['conf_user']))
		header('Location: index.php');
		
?>
<body>
	<?php include('includes/nav-header.php'); ?>
	<div id="main" class="main_container">
		<div class="content">
			<div class="row">
				<div class="create-form">
					<h2>Create a mycompany Conference Account</h2>
					<?php if ($_SESSION['error']): ?>
						<p class='error'><?php echo $_SESSION['error']; ?></a></p>
						<?php unset($_SESSION['error']); ?>
					<?php endif; ?>
					<?php if ($_SESSION['account_exists']): ?>
						<p class='error'>This account already exists. If you have forgotten your password, you may reset your password by following <a href='reset_password.php'>this link</a></p>
						<?php unset($_SESSION['account_exists']); ?>
					<?php endif; ?>
					<form id="create_account_form" method="post" role="form" action='scripts/create_account.php'>
						<p><a href='index.php'>I already have an account</a></p>
						<p>Create a password for <span id='create_account_email'><?php echo $email; ?></span></p>	
						<div class="form-group">
							<label for="InputPassword1">Email</label>
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
							<div id="termsckb-wrap"><p class="bold">Type in the words below (separated by a space):</p></div>
							<div class="g-recaptcha" data-sitekey="6LdYiOgSAAAAAMg2SxnD4D-ycaGm8Mf_npX3csuk"></div>
						</div>
						<button class="btn btn-success" type="submit">Create account</button>						
					</form>
				</div>
			</div>
		</div>
	</div>
</body>
</html>