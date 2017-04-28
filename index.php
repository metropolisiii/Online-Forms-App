<?php 
	/**
	* Main administration for the end-user
	*
	* Displays to a user a form administration area that shows the current forms and closed forms. The user can pick a form that he or she has not already filled out to fill it out. 
	* Shows two containers. One that lists the open forms (forms that haven't been completed) that belongs to the URL being used to access this page. The other container displays the closed forms. 
	* @authrr Jason Kirby <jkirby1325@gmail.com>
	*/
	
	include_once("scripts/settings.php");	
	
	include("includes/header.php"); 
	if (!$user_login_required)
		exit;
	if ($_SESSION['membertype'] !== "user" || $_SESSION['membertype'] === "admin" ){
		header("Location: admin.php");
		exit;
	}
	$_GET=sanitize($_GET);
	
	debug("forwarded_directory", $forwarded_directory, $_GET['debug']);
	
	$result=mysql_query("SELECT id, name, enabled, filename, num_times_filled_out FROM fb_savedforms WHERE date>=".date('U')." AND visible=1 AND sitename='".$forwarded_directory."' AND (accountId=1 OR accountId IS NULL OR accountId=0) order by LENGTH(name), name asc"); //Gets current forms
	debug("Query", "SELECT id, name, enabled, filename FROM fb_savedforms WHERE date>=".date('U')." AND visible=1 AND sitename='".$forwarded_directory."' AND (accountId=1 OR accountId IS NULL OR accountId=0) order by LENGTH(name), name asc", $_GET['debug']);
	$result2=mysql_query("SELECT id, name, enabled, filename FROM fb_savedforms WHERE date<".date('U')." AND visible=1 AND sitename='".$forwarded_directory."' AND (accountId=1 OR accountId IS NULL OR accountId=0) order by LENGTH(name), name asc"); //Gets closed forms
	$submitted=True;
	$accepted=True;
?>
<!----------------------------------------------------------------------Presentation------------------------------------------------------------------------------------->


<h2><?php echo ucfirst($form_replacement); ?> Administration</h2>

<div id='container'>
	<table cellspacing='20'>
		<tr>
			<!--Current forms -->
			<td class='bordered'>
				<div class='subcontainer'>
					<div class='container_heading'><h2>Current <?php echo $form_replacement; ?>s</h2></div>
					<div class='container'>
						<table id='table_forms'>
							<?php while ($form=mysql_fetch_array($result)): ?>
							<tr>
								<td><div><?php echo $form['name']; ?></div></td>
								<td>
									<?php 
										/**
										* <p>Gets completed status to display to the end-user.</p>
										* <p>If the form is marked as submitted (submit button has been pressed in the form) or form has already been accepted or declined, the status will be marked as completed. Otherwise, it will be marked as incomplete.</p>
										* <p>Also, if the form is enabled, a 'please complete info' message is shown to the user. Otherwise, the user is let known that the form is not open yet. </p>
										*/
										$result3=mysql_query("SELECT submitted, accepted FROM user_form where formid=".$form['id']." AND userid='".$_SESSION['userid']."'"); 
										$num_times_can_be_filled_out=$form['num_times_filled_out'];
										$num_times_filled_out=mysql_num_rows($result3);
		
										while ($sub=mysql_fetch_array($result3)){
											if ($submitted){
												if ($sub['submitted']==0)
													$submitted=False;
											}
											if ($accepted){
												if ($sub['accepted']==0)
													$accepted=False;
											}
										}
										if ($submitted && $accepted && $num_times_filled_out >= $num_times_can_be_filled_out && $num_times_can_be_filled_out != 0):
									?>
									Completed
									<?php elseif ($num_times_filled_out >= $num_times_can_be_filled_out && $num_times_can_be_filled_out != 0): ?>
									Incomplete (See your forms)
									<?php elseif ($form['enabled']==1): ?>
									<div class='registration_button'><a  href='forms/<?php echo $form['filename']; ?>'>Please complete info</a></div>
									<?php else: ?>
									<?php echo $form_replacement; ?> not open yet.
									<?php endif; ?>
								</td>
							</tr>
							<?php endwhile; ?>
						</table>
					</div>
				</div>
			</td>
			<!--End current forms-->
			<!--Closed forms-->
			<td class='bordered'>
				<div class='subcontainer'>
					<div class='container_heading'><h2>Closed <?php echo $form_replacement; ?>s</h2></div>
					<div class='container'>
						<table>
							<?php while ($form=mysql_fetch_array($result2)): ?>
							<tr><td><?php echo $form['name'];?></td></tr>
							<?php endwhile; ?>
						</table>
					</div>
				</div>
			</td>
			<!--End closed forms-->
		</tr>
	</table>
	<?php if ($user_login_required): ?>
	<div style="font-size:1.2em"><a style='border-style:solid; border-width:thin; padding:5px; color:#000000; background-color: #DDDDDD; border-color: #BBBBBB; border-radius:5px; margin-left:20px;' href='form_list.php'>My <?php echo $form_replacement; ?>s</a></div>
	<?php endif; ?>
</div>
<?php include('includes/footer.php'); ?>