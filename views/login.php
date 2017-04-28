<h1>Please Login</h1>
<div class='error'><?php echo flash('error'); ?></div>
<form method="POST" id='login_form' action="scripts/login.php">
	<fieldset>
		<legend>Account</legend>
		<div class="field">
			<label>Login Name</label>
			<div class="formHelp"> Login names are case sensitive, make sure the caps lock key is not enabled. </div>
			<input type="text" name="username"/>
		</div>
		<div class="field">
			<label>Password</label>
			<div class="formHelp">Case sensitive, make sure caps lock is not enabled. </div>
			<input type="password" name="password"/>
		</div>
		<div class="formControls">		
			<input class="context" id="form_submit" type="submit" value="log in" />
			<p> Please log out or exit your browser when you're done. Your session will time out after 24 hours. </p>
			<p>To reset your password, please submit the <a href="https://www.mycompany.com/IdM/PasswordReset.aspx">mycompany Password Reset Form</a>.</p>
		</div>
	</fieldset>
</form>		