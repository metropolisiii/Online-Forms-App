<?php 
	/**
	* The opening login screen.
	* 
	* Displays a login form.
	*
	* @author Jason Kirby <jkirby1325@gmail.com>
	*
	*/
	
	session_start();
	
	include_once("scripts/settings.php");	
	include_once("misc/functions.php");
	$_GET=sanitize($_GET);
	if (isset($_SESSION['userid']) && isset($_SESSION['membertype'])){
		header("Location: index.php");
		exit;
	}
	
	include("includes/header.php");

	
	debug("Session",$_SESSION, $_GET['debug']);
?>
<!--------------------------------------------------------------------Presentation--------------------------------------------------------------------------------------------------->


		<h1>Please Login</h1>
		<div id='error'><?php if (!empty($_SESSION['error'])){echo $_SESSION['error']; $_SESSION['error']=''; unset($_SESSION['error']);};?></div>
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
				<?php if ($forwarded_directory == 'forms_app' || $forwarded_directory == 'forms_app_test' ||  $forwarded_directory=='rfi_test' || $forwarded_directory == 'forms'):?>
				<div><b>Be sure to select the account for which you want to administer forms.</b></div>
				
				<?php endif ?>
				<div class="formControls">		
					<input class="context" id="form_submit" type="submit" value="log in" />
					<p> Please log out or exit your browser when you're done. Your session will time out after 24 hours. </p>
					<p>To register for access to Interops please go <a href="https://www.mycompany.com/forms/forms/Registration_12312027_533.html">here</a>.</p>
					<p>To reset your password, please submit the <a href="https://www.mycompany.com/IdM/PasswordReset.aspx">mycompany Password Reset Form</a>.</p>
				</div>
			</fieldset>
		</form>		
	</body>
</html>