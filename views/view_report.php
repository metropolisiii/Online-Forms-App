 
 <link rel="stylesheet" href="css/slickgrid.css" type="text/css"/>
 <link rel="stylesheet" href="js/slickgrid/css/smoothness/jquery-ui-1.8.16.custom.css" type="text/css"/>
<?php include ("includes/reports_header.php"); ?>
 <div style="width:100%">
	<div class="grid-header" style="width:100%">
		<label><?php echo $report['report_name']; ?></label>
		<?php if ($report['date_created']): ?>
			<div id='report_filter'>
				<form method="GET" action="">
					<label>Enter a date range: </label>
					<label>From:</label>
					<input id="from_date" class="hasDatePicker" type="text" name="from_date"/>
					<label>To:</label>
					<input id="to_date" class="hasDatePicker" type="text" name="to_date"/>
					<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>"/>
					<input type="submit" value="Filter Report" />
				</form>
			</div>
			<?php endif; ?>
	</div>
	 <div id="myGrid" style="width:auto;min-height:200px;"></div>
</div>


<script src="js/jquery.event.drag-2.2.js"></script>

<script src="js/slickgrid/slick.core.js"></script>
<script src="js/slickgrid/plugins/slick.autotooltips.js"></script>
<script src="js/slickgrid/slick.formatters.js"></script>
<script src="js/slickgrid/slick.editors.js"></script>
<script src="js/slickgrid/slick.grid.js"></script>
<script src="js/slickgrid/controls/slick.columnpicker.js"></script>

<script>
	var data = [];
	function HTMLFormatter(row, cell, value, columnDef, dataContext) {
        return value;
	}
	var grid;	
	var columns = [
		<?php foreach ($report['column_names'] as $key=>$column_name): ?>
			<?php if ($column_name != ''): ?>
				{id: "<?php echo elemId($report['column_ids'][$key]); ?>", name: "<?php echo $column_name; ?>", field: "<?php echo $report['column_ids'][$key]; ?>", toolTip: "<?php echo $column_name; ?>", <?php if ($report['forms'][$key] == -1): ?> editor:Slick.Editors.LongText <?php else: ?>editor:Slick.Editors.LongText <?php endif; ?>, <?php if ($report['forms'][$key] == -1): ?>cssClass: "cell-title",<?php endif; ?> sortable:true},
			<?php endif; ?>
		<?php endforeach; ?>
	];
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
		<?php foreach ($report['report_data'] as $form=>$fields): ?>
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
		
		grid.onCellChange.subscribe(function(e, args) {
			var dta = data[args.row][grid.getColumns()[args.cell].field];
			var field_id=grid.getColumns()[args.cell].id;
			var cssClass = grid.getColumns()[args.cell].cssClass;
			var form_id = data[args.row].fid;
			if (cssClass === 'cell-title'){
				$.post('scripts/change_answer.php', {answer:dta, form_id:form_id, field_id:field_id}, function(dt){
					if (dt != ''){
						if (dt == 'no_permission')
							alert("You do not have privileges to change this value.");
						else
							alert("There was a problem saving this data. Perhaps it was formatted incorrectly");
					}
				
				});			
			}
		  });
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
