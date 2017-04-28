<?php include ("includes/reports_header.php"); ?>
<h1><?php echo $report_name; ?> </h1>
<div id="reports_container" class="items_container">
	<div id="report_permissions">
		<div class="reports_row main_row">
			<div class="reports_column_name">
				User
			</div>
			
		</div>
		<?php while ($permission = mysql_fetch_object($permission_result)): ?>			
				<div class="reports_row" id="<?php echo $permission->id; ?>">
					<div class="reports_column_name">
						<?php echo $permission -> user; ?>
					</div>
					<div class="reports_column">
						<?php if ($permission->user != $_SESSION['userid']): ?>
							<button class='delete_permission'>Remove</button>
						<?php endif; ?>
					</div>
				</div>
			
		<?php endwhile; ?>
	</div>
	<button id='report_add_user'>Add User</button>
</div>
<input type='hidden' name='reportid' value='<?php echo $_GET['id']; ?>' />
