<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1252">
		<meta http-equiv="Cache-control" content="no-cache">
		<link rel="stylesheet" href="../../css/slickgrid.css" type="text/css"/>
		<link rel="stylesheet" href="../../js/slickgrid/css/smoothness/jquery-ui-1.8.16.custom.css" type="text/css"/>
		<link rel="stylesheet" href="../../themes/mycompany/css/styles.css" type="text/css"/>
		<link type="text/css" rel="stylesheet" href="../../css/styles.css">
		<link type="text/css" rel="stylesheet" href="css/styles.css">
		<link href="//fonts.googleapis.com/css?family=Oswald" rel="stylesheet" type="text/css" />
		<link href="//fonts.googleapis.com/css?family=Roboto:400,400italic,700" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="../../css/jquery-ui-1.8.23.custom.css" type="text/css" media="all" />
		<link rel="stylesheet" href="../../css/ui.css" type="text/css" media="all" />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
		<script src="../../js/jquery-ui-1.8.23.custom.min.js"></script>
		<script src="../../js/jquery-ui-min.js"></script>
		<script src="../../js/tiny_mce2/tiny_mce.js"></script>
		<script src="../../js/jquery.event.drag-2.2.js"></script>
		<script src="../../js/slickgrid/slick.core.js"></script>
		<script src="../../js/slickgrid/plugins/slick.autotooltips.js"></script>
		<script type="text/javascript" src="../../js/scripts.js"></script>
		<script type="text/javascript" src="js/scripts.js"></script>
		<script src="../../js/slickgrid/slick.formatters.js"></script>
		<script src="../../js/slickgrid/slick.editors.js"></script>
		<script src="../../js/slickgrid/slick.grid.js"></script>
		<script src="../../js/slickgrid/controls/slick.columnpicker.js"></script>
		
		<script>
			var data = [];
			var grid;	
			var columns = [
				<?php foreach ($data['report_fields'] as $field=>$field_as): ?>
					{id: "<?php echo elemId($field); ?>", name: "<?php echo $field_as; ?>", field: "<?php echo $field; ?>", toolTip: "<?php echo $field_as; ?>", sortable:true},
				<?php endforeach; ?>
			];	
			var imageFormatter = function(row, cell, value, columnDef, dataContext){
				if (value)
					return '<img class=\"clickableImage\" src=\"../../images/tick.png\" />';
				else
					return ''
			};
			columns.push({id: 'delete', name:'Delete', field: 'delete', width:10, maxwidth:20, cssClass:'cell-effort-drive', formatter:imageFormatter,editor:Slick.Editors.Checkbox});				
			var options = {
				enableCellNavigation: true,
				enableColumnReorder: false,
				forceFitColumns: true,
				editable: true,
				topPanelHeight: 25,
				multiColumnSort: true
			};
			$(function(){
				var i=0;
				<?php foreach ($data['report_data'] as $form=>$fields): ?>
					data[i]={
						<?php foreach ($fields as $field => $value): ?>
							<?php echo json_encode($field); ?>:"<?php echo $value; ?>",
						<?php  endforeach; ?>	
						fid:"<?php echo $form; ?>",
					}
					i++;
				<?php endforeach; ?>
				grid = new Slick.Grid("#myGrid", data, columns, options);
				grid.registerPlugin(new Slick.AutoTooltips());
				grid.onSort.subscribe(function (e, args) {
					var cols = args.sortCols;
					data.sort(function (dataRow1, dataRow2) {
						for (var i = 0, l = cols.length; i < l; i++) {
						  var field = cols[i].sortCol.field;
						  var sign = cols[i].sortAsc ? 1 : -1;
						  var value1 = dataRow1[field], value2 = dataRow2[field];
						  var result = (value1 == value2 ? 0 : (value1 > value2 ? 1 : -1)) * sign;
						  if (result != 0) {
							return result;
						  }
						}
						return 0;
					});
					grid.invalidate();
					grid.render();
				});
			});
		</script>
	</head>
	<body>
		<div>
			<a class='button' style='left: 19px; position: relative; top: 11px;' href='/forms/view_report.php?id=<?php echo $reportid; ?>'>Back to reports</a>
			<h1>Archives</h1>
			<div id="myGrid" style="width:auto;min-height:200px;"></div>
			<input type="hidden" id="reportid" value="<?php echo $_GET['id']; ?>" />
			<button id="delete_archive" type="button">Delete Checked</button>
		
