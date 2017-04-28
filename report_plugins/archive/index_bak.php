<?php
	class DeleteAndArchive{
	
	}
	function addonRun(){
		global $report;
		$result = mysql_query("SELECT * from archives WHERE report_id={$_GET['id']}");
		$html = "
		<script>
			$(document).ready(function(){			
				$('#show_archives').live('click', function(){
					if ($(this).text() == 'Show Archives'){
						$('#archive_section').show();
						$(this).text('Hide Archives');
					}
					else{
						$('#archive_section').hide();
						$(this).text('Show Archives');
					}
				});
				var imageFormatter = function(row, cell, value, columnDef, dataContext){
					if (value)
						return '<img class=\"clickableImage\" src=\"images/tick.png\" />';
					else
						return ''
				};
				var archive_columns = clone(columns);
				columns.push({id: 'archive', name:'Archive', field: 'archive', width:10, maxwidth:20, cssClass:'cell-effort-drive', formatter:imageFormatter, editor:Slick.Editors.Checkbox});	
				$('#myGrid').wrapAll('<form id=\"report_form\" method=\"post\" action=\"report_plugins/delete and archive/index.php\" />');
				$('#report_form').append('<button id=\"submit_report\" type=\"button\">Archive Checked Records</button>');
				$('#submit_report').live('click', function(){
					$('#report_form').append('<input type=\"hidden\" name=\"report_id\" value=\"".$_GET['id']."\"/>');
					var d = [];
					$('input[type=checkbox]').each(function(){
						if ($(this).prop('checked')){
							var row = $(this).parent().parent().parent().children().index($(this).parent().parent());
							d.push({'form_id':data[row].fid, 'report_id':{$_GET['id']}});
						}
					});
					$('.clickableImage').each(function(){
						var row = $(this).parent().parent().parent().children().index($(this).parent().parent());
						d.push({'form_id':data[row].fid, 'report_id':{$_GET['id']}});
					});
					$.post('report_plugins/delete and archive/index.php', {data:d}, function(){
						location.reload();
					});
					
				});
				$(\"<div style='margin-top:40px' id='archive_section'><div class='grid_header'><label>Archives</label></div><div id='archiveGrid' style='width:auto;min-height:200px;'></div></div>\").insertAfter('#myGrid');
				var grid2;
				var data2 = [];
				var i=0;
				";
		while ($archive = mysql_fetch_array($result)){
			$result2 = mysql_query("SELECT * FROM form_answers WHERE user_form_id={$archive['user_form_id']}");
			$html.="			
				data2[i]={
			";
			while ($answer = mysql_fetch_object($result2)){
				$html.=json_encode($answer->field_id).":\"".$answer->response."\",";
			}
			$html.="}; 
			i++;";			
		}			
		$html.="grid2 = new Slick.Grid('#archiveGrid', data2, archive_columns, options);
		$('<button id=\"show_archives\" type=\"button\">Hide Archives</button>').insertAfter('#submit_report');
		});			
		</script>
		
		";
		echo $html;
	}
	
	
	
	
	

	if ($_POST){
		include("../../scripts/settings.php");
		include("../../includes/connect.php");
		foreach ($_POST['data'] as $data){
			$query = "DELETE FROM archives WHERE user_form_id = {$data['form_id']} AND report_id={$data['report_id']}";
			mysql_query($query);
			$query = "INSERT INTO archives (user_form_id, report_id) VALUES ({$data['form_id']},{$data['report_id']})";
			mysql_query($query);
			mysql_query("DELETE FROM user_form WHERE id={$data['form_id']}");	
		}

	}
?>