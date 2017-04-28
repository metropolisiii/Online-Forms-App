<?php
/**
* Determines whether the user's form was accepted or declined and gets the proper email to send to the user. The email body is returned.
* @author Jason Kirby <jkirby1325@gmail.com>
*/
	session_start();
	if ($_SESSION['membertype'] != "admin"){
		echo "You need to <a href='../login.php'> login </a> to enter this area";
		exit;
	}
	include("settings.php");
	include("connect.php");
	include("../misc/functions.php");
	if (empty($_POST))
		exit;
	$_POST=sanitize($_POST);
	$result=mysql_query("SELECT fb_savedforms.name, accepted_email, declined_email, notifyees FROM user_form INNER JOIN fb_savedforms on user_form.formid=fb_savedforms.id WHERE formid=".$_POST['id']." AND sitename='".$forwarded_directory."'");
	$form=mysql_fetch_array($result);
	$notifyees=explode("\n",$form['notifyees']);
	foreach($notifyees as $notifyee){
		$replyto.=$notifyee.";";
	}
	$replyto=substr($replyto, 0, -1);
	$info=ldap_user_info($_POST['user']);
?>
<!----------------------------------------------------------------------Presentation------------------------------------------------------------------------------------->
<style>
	td{
		align:left;
	}
</style>
<form class="emailform" name="contactform" method="post"  action="scripts/send_form_email.php">
	<div class='form_head'><img src='images/close.png'/></div>
	<table>
		<tr>
			<td>To:</td>
			<td><input type='text' name='email' value='<?php echo $info[0]['mail'][0]; ?>' size='50' /></td>
		</tr>
		<tr>
			<td>From:</td>
			<td><input type='text' name='replyto' value='<?php echo $replyto; ?>' size='50' /></td>
		</tr>
		<tr>
			<td>Subject:</td>
			<td><input type='text' name='subject' value='Information Regarding the <?php echo $form['name']; ?> form' size='50' /></td>
		</tr>
		<tr>
			<td>Message:</td>
			<td>
				<textarea rows='20' cols='47' name='message'>
<?php
	if ($_POST['status'] == 'rejected'):
		$email=str_replace("[user]",  $info[0]['givenname'][0], $form['declined_email']);
		$email=str_replace("[form]",  $form['name'], $email);
		echo trim($email);
	elseif ($_POST['status']=='accepted'):
		$email=str_replace("[user]",  $info[0]['givenname'][0], $form['accepted_email']);
		$email=str_replace("[form]",  $form['name'], $email);
		echo trim($email);
	endif;
?>
				</textarea>
			</td>
		</tr>
		<tr>
			<td><input type="submit" value="Send email"/></td>
		</tr>
	</table>
	<input type='hidden' name='user' value='<?php echo $_POST['user']; ?>' />
	<input type='hidden' name='id' value='<?php echo $_POST['id']; ?>' />
</form>
