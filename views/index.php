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
							<?php echo $current_forms_html; ?>
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