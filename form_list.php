<?php
	/**
	* End user's personalized form list
	* 
	* Shows information about the end user's forms that he or she has started and completed filling out.
	*
	* @author Jason Kirby <jkirby1325@gmail.com>
	*
	*/

	include_once("scripts/settings.php");
	if ($_SESSION['membertype'] != "user"){
		header("Location: admin.php");
	}
	include("includes/header.php");
	
	$result=mysql_query("SELECT * FROM user_form INNER JOIN fb_savedforms on formid=fb_savedforms.id WHERE user_form.userid='".$_SESSION['userid']."' AND date>=".date('U')." AND visible=1 AND sitename='".$forwarded_directory."' order by date asc"); //Gets end-user's open forms
	$result2=mysql_query("SELECT * FROM user_form INNER JOIN fb_savedforms on formid=fb_savedforms.id WHERE user_form.userid='".$_SESSION['userid']."' AND date<".date('U')." AND visible=1 AND sitename='".$forwarded_directory."' order by date asc"); //Gets end-user's closed forms

?>
<!----------------------------------------------------------------------Presentation------------------------------------------------------------------------------------->
<div id='container'>
	<a href='index.php'><< Back to forms</a><br/>
	<table cellspacing='20'>
		<tr>
			<td class='bordered'>
				<div class='subcontainer'>
					<div class='container_heading'><h2>Current forms</h2></div>
					<div class='container'>
						<table>
							<?php while ($form=mysql_fetch_array($result)): ?>
								<tr>
									<td>
										<?php if (is_null($form['accepted'])): ?>
										<a href='forms/<?php echo $form['pagename']; ?>?q=<?php echo $form[8];?>'>
										<?php endif; ?>
										<?php echo $form['name']; ?>
										<?php if (is_null($form['accepted'])): ?>
										</a>
										<?php endif; ?>
									</td>
									<td>
										<?php if (is_null($form['accepted']) && $form['submitted'] == 1): ?>
										Pending
										<?php elseif ($form['submitted']==0 && $form['accepted']==NULL): ?>
										Incomplete
										<?php elseif ($form['accepted']==1): ?>
										<span class='accepted'>Accepted!</span>
										<?php else: ?>
										<span class='rejected'>Need more information</span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endwhile; ?>
						</table>
					</div>
				</div>
			</td>
			<td class='bordered'>
				<div class='subcontainer'>
					<div class='container_heading'><h2>Closed forms</h2></div>
					<div class='container'>
						<table>
							<?php while ($form=mysql_fetch_array($result)): ?>
							<tr>
								<td><?php echo $form['name'];?> </td>
								<td>
									<?php if ($form['accepted']==1): ?>
									<span class='accepted'>Accepted!</span>
									<?php elseif ($form['submitted']==0): ?>
									Incomplete
									<?php else: ?>
									<span class='rejected'>Needed more information</span>
									<?php endif; ?>
								</td>
							</tr>
							<?php endwhile; ?>
						</table>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
