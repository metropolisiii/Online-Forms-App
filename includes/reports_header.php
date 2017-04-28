<?php 
	$id = "";
	if (isset($_GET['id']))
		$id = $_GET['id'];
?>
<div id='reports_menu_bar'>
	<table width='100%'>
		<tr>
			<td width='5%'>
				<div class='report_icon'>
					<a href='make_report.php'><img src='images/new_report.png'/><div>New Report</div></a>
				</div>
			</td>
			<td width='5%'>
				<div class='report_icon'>
					<a class='report_header_link' href='make_report.php?id=<?php echo $id;?>'><img src='images/edit_report.png'/><div>Edit Report</div></a>
				</div>
			</td>
			<td width='5%'>
				<div class='report_icon'>
					<a class='report_header_link' href='view_report.php?id=<?php echo $id;?>'><img src='images/view_report.png'/><div>View Report</div></a>
				</div>
			</td>
			<td width='5%'>
				<div class='report_icon'>
					<a class='report_header_link' id='delete_report' href='delete_report.php?id=<?php echo $id;?>'><img src='images/delete-report.png'/><div>Delete Report</div></a>
				</div>
			</td>
			<td width='5%'>
				<div class='report_icon'>
					<a class='report_header_link' id='download_report' target="_blank" href='download_report.php?id=<?php echo $id;?>'><img src='images/download_report.png'/><div>Download Report</div></a>
				</div>
			</td>
			<td width='5%'>
				<div class='share_icon'>
					<a class='report_header_link' id='share_report' href='share_report.php?id=<?php echo $id;?>'><img src='images/share_icon.png'/><div>Share Report</div></a>
				</div>
			</td>
			<td width='5%'>
				<div class='report_icon'>
					<a href='reports.php'><img src='images/goback.png'/><div>Back to Reports</div></a>
				</div>
			</td>
			<td>
			
			</td>
			
		</tr>
	</table>
</div>