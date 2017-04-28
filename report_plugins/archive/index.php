<?php
	class DeleteAndArchive{
	
	}
	function addonRun(){
		$html = "
		<script>
			$(document).ready(function(){	
				$('#view_archives').live('click', function(){
					window.location = 'report_plugins/archive/archives.php?id={$_GET['id']}';
				});
				var imageFormatter = function(row, cell, value, columnDef, dataContext){
					if (value)
						return '<img class=\"clickableImage\" src=\"images/tick.png\" />';
					else
						return ''
				};
				columns.push({id: 'ignore', name:'Ignore', field: 'ignore', width:10, maxwidth:20, cssClass:'cell-effort-drive ignore', formatter:imageFormatter, editor:Slick.Editors.Checkbox}, {id: 'archive', name:'Archive', field: 'archive', width:10, maxwidth:20, cssClass:'cell-effort-drive archive', formatter:imageFormatter, editor:Slick.Editors.Checkbox});	
				$('#myGrid').wrapAll('<form id=\"report_form\" method=\"post\" action=\"report_plugins/delete and archive/index.php\" />');
				$('#report_form').append('<button id=\"submit_report\" type=\"button\">Process Checked Records</button>');
				$('#submit_report').live('click', function(){
					$('#report_form').append('<input type=\"hidden\" name=\"report_id\" value=\"".$_GET['id']."\"/>');
					var d = [];
					$('input[type=checkbox]').each(function(){
						var row = $(this).parent().parent().parent().children().index($(this).parent().parent());
						console.log(row);
						if ($(this).prop('checked')){
							json = {'form_id':data[row].fid}
							if ($(this).parent().hasClass('ignore')){
								json['type'] = 'ignore';
								json['value'] = 1
							}
							if ($(this).parent().hasClass('archive'))
								json['type'] = 'archive';
							d.push(json);
						}
						else{
							if ($(this).parent().hasClass('ignore')){
								json = {'form_id':data[row].fid, 'type':'ignore', 'value':0}
								d.push(json);
							}
						}
					});
					$('.clickableImage').each(function(){
						var row = $(this).parent().parent().parent().children().index($(this).parent().parent());
						json = {'form_id':data[row].fid}
						if ($(this).parent().hasClass('ignore')){
							json['type'] = 'ignore';
							json['value'] = 1
						}
						if ($(this).parent().hasClass('archive'))
							json['type'] = 'archive';
						d.push(json);
					});
					console.log(d);
					$.post('report_plugins/archive/index.php', {data:d, reportid:{$_GET['id']}}, function(){
						location.reload();
					});
					
				});
				$('<button id=\"view_archives\" type=\"button\">View Archives</button>').insertAfter('#submit_report');
			});			
		</script>
		";
		echo $html;
	
	}	
	
	function postView(){
		$result = mysql_query("SELECT user_form_id, `ignore` FROM VPN.vpn_form WHERE `ignore` = 1");
		$html = "
		<script>
			$(function(){ 
		";
		$loopString="";
		$counter = 0;
		$numrows=mysql_num_rows($result);
		while ($rec = mysql_fetch_object($result)){
			$counter++;
			$loopString.="data[i].fid == ".$rec->user_form_id." ";
			if ($counter<$numrows)
				$loopString.="|| ";
			
		}
		if ($loopString != ""){
			$html.="for (var i=0; i<data.length; i++){					
						if ({$loopString}){
							data[i].ignore = true;
						}
					}
					grid.invalidate();
					grid.render();	
				";
		}
		
		$html.="   });
		</script>";
		echo $html;
	}
	
	if ($_POST){
		include("../../scripts/settings.php");
		include("../../scripts/connect.php");
		include("../../misc/functions.php");
		$userid = sanitize($_SESSION['userid']);
		$reportid = sanitize($_POST['reportid']);
		//Check if user is allowed to post
		$result = mysql_query("SELECT id from permissions WHERE user = '{$userid}' AND reportid={$reportid}");
		//echo "SELECT id from permissions WHERE user = '{$userid}' AND reportid={$reportid}";
		if (mysql_num_rows($result) > 0){
			foreach ($_POST['data'] as $data){	
				if ($data['type'] == "ignore"){
					$query = "UPDATE VPN.vpn_form SET `ignore` = {$data['value']} WHERE user_form_id = {$data['form_id']}";
				}
				else if ($data['type'] == "archive"){
					$query = "DELETE FROM archives WHERE user_form_id = {$data['form_id']} AND report_id={$reportid}";
					$query2 = "INSERT INTO archives (user_form_id, report_id) VALUES ({$data['form_id']},{$reportid})";
					mysql_query($query2);
					mysql_query("DELETE FROM user_form WHERE id={$data['form_id']}");
					mysql_query("DELETE FROM VPN.vpn_form WHERE user_form_id={$data['form_id']}") or die(mysql_error());
				}				
				mysql_query($query);			
			}
		}
	}
?>