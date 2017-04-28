<?php 
	/************************************************************
	*  Main administrative area for standard users.
	*************************************************************/
	session_start();
	if ($_SESSION['membertype'] != "user"){
		header("Location: editform.php");
	}
	include("includes/header.php"); 
	
	
	$_GET=sanitize($_GET);
	$result=mysql_query("SELECT id, name, enabled, filename FROM fb_savedforms WHERE date>=".date('U')." AND visible=1 order by LENGTH(name), name asc");
	$result2=mysql_query("SELECT id, name, enabled, filename FROM fb_savedforms WHERE date<".date('U')." AND visible=1 order by LENGTH(name), name asc");
?>
<!----------------------------------------------------------------------Presentation------------------------------------------------------------------------------------->
<h2>RFIs - Please complete and submit no later than July 31st</h2>
<p class='bigger'>Please complete the form entitled <b>Parts 1 and 2 and 3 below and follow the instructions contained therein</b>. You may complete these forms in multiple sessions.</p>
<p class='bigger'>If you have any questions, please contact: <a href='mailto: legal@mycompany.com'>legal@mycompany.com</a>
<div id='container'>
	<table cellspacing='20'>
		<tr>
			<td class='bordered'>
				<div class='subcontainer'>
					<div class='container_heading'><h2>Current RFIs</h2></div>
					<div class='container'>
						<table id='table_rfis'>
							<?php while ($rfi=mysql_fetch_array($result)): ?>
							<tr>
								<td><div><?php echo $rfi['name']; ?></div></td>
								<td>
									<?php 
										$result3=mysql_query("SELECT submitted, accepted FROM user_rfi where formid=".$rfi['id']." AND userid='".$_SESSION['userid']."'");
										$sub=mysql_fetch_array($result3);
										if ($sub['submitted'] == 1 || $sub['accepted'] != NULL):
									?>
									Completed
									<?php elseif ($rfi['enabled']==1): ?>
									<div class='registration_button'><a  href='rfis/<?php echo $rfi['filename']; ?>'>Please complete info</a></div>
									<?php else: ?>
									RFI not open yet.
									<?php endif; ?>
								</td>
							</tr>
							<?php endwhile; ?>
						</table>
					</div>
				</div>
			</td>
			<!--
			<td class='bordered'>
				<div class='subcontainer'>
					<div class='container_heading'><h2>Closed RFIs</h2></div>
					<div class='container'>
						<table>
							<?php while ($rfi=mysql_fetch_array($result2)): ?>
							<tr><td><?php echo $rfi['name'];?></td></tr>
							<?php endwhile; ?>
						</table>
					</div>
				</div>
			</td>
			-->
		</tr>
	</table>
	<div style="font-size:1.2em"><a style='border-style:solid; border-width:thin; padding:5px; color:#000000; background-color: #DDDDDD; border-color: #BBBBBB; border-radius:5px; margin-left:20px;' href='rfi_list.php'>My RFIs</a></div>
</div>
<?php include('includes/footer.php'); ?>