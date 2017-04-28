<?php
	$title = "Reset Password";
	$head="
		<script>
		$(document).ready(function(){
			$.ketchup.validation('match_element', 'Password and verification must match', function(form, el, value, el2){
				if (el.val() === $('#'+el2).val())
					return true;
				else
					return false;					
			});
			$('#reset_password_form').ketchup();
		});
		</script>
	";
	include('scripts/settings.php');
	include_once("includes/connect.php");
	$_GET=sanitize($_GET);
	$link='';
	if ($_GET['link']){
		$link=$_GET['link'];
		//Get account
		$result=mysql_query("SELECT email FROM accounts WHERE reset_link='{$link}'");
		if (mysql_num_rows($result) == 0){
			//if account doesn't exist, set link='';
			$link='';
			$_SESSION['error'] = "This link is invalid. Please attempt to reset your password again.";
		}
		else{
			$rec=mysql_fetch_object($result);
			$email=$rec->email;
		}
			
	}	
?>
<body>

	<?php include('includes/nav-header.php'); ?>
	<div id="main" class="main_container">
		<div class="content">
			<div class="row">
				<div class="reset-password">
					<h2>Reset Your mycompany Password</h2>
					<?php if ($_SESSION['error']): ?>
						<p class='error'><?php echo $_SESSION['error']; ?></p>
						<?php unset($_SESSION['error']); ?>
					<?php endif; ?>
					<form id="reset_password_form" method="post" role="form" action='scripts/reset_password.php'>
						<div class="form-group">
							<?php if ($link): //If we're putting in a password ?>
								<p>Reset password for <?php echo $email; ?></p>
								<div class="form-group">
									<label for="InputPassword1">Password</label>
									<input data-validate="validate(required, minlength(6), maxlength(20))"  class="form-control" id="password" type="password" name="password"   />
								</div>
								<div class="form-group">
									<label for="InputPassword2">Verify Password</label>
									<input data-validate="validate(required, match_element(password))" class="form-control" data-match-error="Passwords do not match" type="password" id="verifypassword" name="verifypassword"  />
								</div>
								<input type="hidden" name="link" id="link" value="<?php echo $link ?>" />
							<?php else: //If we're putting in an email to reset password ?>
								<label for="email">Email Address of Account</label>
								<input data-validate="validate(required, email)"  class="form-control" id="email" type="text" name="email"   />
							<?php endif; ?>
						</div>
						<button class="btn btn-success" type="submit">Reset password</button>						
					</form>
				</div>
			</div>
		</div>
	</div>
</body>
</html>