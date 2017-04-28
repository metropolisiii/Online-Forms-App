<div id='container'>
	<?php if ($_SESSION['membertype'] == "superadmin"): ?>
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
								<?php foreach ($current_forms as $form): ?>
									<tr>
										<td align='left'>
											<a href='createform.php?id=<?php echo $form[0];?>'><?php echo $form['name']; ?></a>
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
								<?php endforeach; ?>
							</table>
						</div>
					</div>
				</div>
			</td>
			<td class='bordered' width='30%'>
				<div class='subcontainer'>
					<div class='container_heading'><h2>Closed forms</h2></div>
					<div class='container'>
						<table>
							<?php foreach ($closed_forms as $form):?>
								<tr>
									<td align='left'>
										<a href='createform.php?id=<?php echo $form[0]; ?>'><?php echo $form['name']; ?></a>
										<a href='forms/<?php echo $form['filename'];?>'>(Preview)</a> 
									</td>									
									<td align='right'>
										<button value='<?php echo $form[0]; ?>' class='copy_form'>Copy</button>
										<button class='delete_form' value='<?php echo $form[0]; ?>'>Delete</button>
										<a href='review.php?id=<?php echo $form[0]; ?>'>Review</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
<?php include("includes/footer.php"); ?>