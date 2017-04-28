<?php include ("includes/reports_header.php"); ?>
<h1>Your Reports</h1>
<p>This is the new reporting engine that allows you to create reports from scratch. It is much more flexible and visually appealing than the old reporting system. Go ahead and give it a try. If you have any questions please see Jason Kirby or send him a question at jason.kirby@mycompany.com.</p>
<div class='notification'>
	<?php echo $_SESSION['notification']; unset($_SESSION['notification']);  ?>
</div>
<div id="reports_container" class="items_container">
	<?php while ($report=mysql_fetch_object($reports_result)): ?>
		<div class='reports_row'>
			<input type='radio' name='report_radio' <?php if ($_GET['id'] && $_GET['id'] == $report->id) echo "checked"; ?> value='<?php echo $report->id; ?>'/> <?php echo $report->name; ?></a>
		</div>
	<?php endwhile; ?>
</div>