<?php 
	/**
	* Header for all files that include it. Takes care of a lot of event handling, importation of functions, importing of global variables, and database connections.
	*
	* @author Jason Kirby <jkirby1325@gmail.com>
	*
	*/

	$_SESSION['redirectpage']=$_SERVER["REQUEST_URI"];
	if (strpos(htmlspecialchars($_SERVER['PHP_SELF']), 'login.php'))
		$loginpage=true;
	include_once("misc/functions.php");
	include_once("scripts/connect.php");
	include_once("/var/www/forms_app/classes/logController.php");
	
	$result=mysql_query("SELECT requires_login, theme FROM accounts WHERE url LIKE '".$forwarded_directory."'");

	$ulr=mysql_fetch_array($result);

	if ($ulr['requires_login']==1)
		$user_login_required=true;
	if (!empty($ulr['theme']))
		$theme=$ulr['theme'];
	
	if (!empty($_POST['fid'])){
		$query=mysql_query("SELECT  fb_savedforms.theme AS theme, sitename from fb_savedforms WHERE fb_savedforms.id=".$_POST['fid']);
		$rec=mysql_fetch_array($query);
		if ($rec['theme'])
			$theme=$rec['theme'];
	}
	if (isset($user_login_required) && empty($_SESSION['userid']) && !$loginpage){ //If the user is required to be logged in and he hasn't logged in, we need to direct him to the login page.
		header("Location: login.php");
		exit;
	}
	
	$log=new logController();
	$log->log("Accessed ".$_SESSION['redirectpage']);
	$_GET=sanitize($_GET);
	if (array_key_exists('forwarded_directory', $_GET))
		$_SESSION['forwarded_directory']=$_GET['forwarded_directory'];
	checkSession();
	//checkHTTPS();
	

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1252">
		<meta http-equiv="Cache-control" content="no-cache">
		<title>mycompany forms</title>
		<link type="text/css" rel="stylesheet" href="themes/<?php echo $theme; ?>/css/styles.css">
		<link type="text/css" rel="stylesheet" href="css/styles.css">
		<link href="//fonts.googleapis.com/css?family=Oswald" rel="stylesheet" type="text/css" />
		<link href="//fonts.googleapis.com/css?family=Roboto:400,400italic,700" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="css/jquery-ui-1.8.23.custom.css" type="text/css" media="all" />
		<link rel="stylesheet" href="css/ui.css" type="text/css" media="all" />
		<link href="css/jquery.formbuilder.css" media="screen" rel="stylesheet" />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
		<script src="js/jquery-ui-1.8.23.custom.min.js"></script>
		<script src="js/jquery-ui-min.js"></script>
		<script src="js/tiny_mce2/tiny_mce.js"></script>
		<script src="js/jquery.formbuilder2.js"></script>
		<script type="text/javascript" src="js/scripts.js"></script>
		
		<script>
			$(document).ready(function(){
				$.ajax({
					type: "POST",
					url: "scripts/get_forwarded_directory.php",
					data: { forwarded_url: window.location.pathname},
					success: function(){
						<?php if (empty($_SESSION['forwarded_directory'])): ?>
							location.reload();
						<?php endif; ?>
					}
				});
				$('#form-builder').formbuilder({
					'save_url': 'save.php',
					'load_url': 'load2.php<?php if (array_key_exists('id', $_GET)) echo "?id=".$_GET['id']; ?>'
				});
				$( "#form_date, #from_date, #to_date" ).datepicker();
				$("#notify_button").click(function(){
					var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
					if ($('#notify_name').val().search(emailRegEx) != -1)
						$('#notify_textarea').val($('#notify_name').val()+"\n"+$('#notify_textarea').val());
					else
						alert("This is not a valid email address.");
				});
				$('#tab1').click(function(){
					$('#tab-1').show();
					$('.tab').not('#tab-1').hide();
					$(this).addClass('selected');
					$('.formtabs').not('#tab1').removeClass('selected');
				});
				$('#tab2').click(function(){
					$('#tab-2').show();
					$('.tab').not('#tab-2').hide();
					$(this).addClass('selected');
					$('.formtabs').not('#tab2').removeClass('selected');
				});	
				$('#tab3').click(function(){
					$('#tab-3').show();
					$('.tab').not('#tab-3').hide();
					$(this).addClass('selected');
					$('.formtabs').not('#tab3').removeClass('selected');
				});	
				$('#groupadd').click(function(){
					if ($('#groupfield').val())
						$('#grouptable tr:last').after('<tr><td><input type="text" class="permissiontext group" value="'+$('#groupfield').val()+'"/></td><td><input type="checkbox" id="groupedit_'+$('#groupfield').val()+'" /></td><td><input type="checkbox" id="groupreport_'+$('#groupfield').val()+'" /></td></tr>');
					$('#groupfield').val('');
				});
				$('#useradd').click(function(){
					if ($('#userfield').val())
						$('#usertable tr:last').after('<tr><td><input type="text" class="permissiontext user" value="'+$('#userfield').val()+'"/></td><td><input type="checkbox" id="useredit_'+$('#userfield').val()+'" /></td><td><input type="checkbox" id="userreport_'+$('#userfield').val()+'" /></td></tr>');
					$('#userfield').val('');
				});
				$('.delete').click(function(){
					return window.confirm(this.title || 'Are you sure you want to delete this form?');
				});
				$('.enable_button').live('click', function(){
					$.post('scripts/change_enabled.php', {id:$(this).attr('id'), status:'0'});
					$(this).html('Enable Registration');
					$(this).addClass('disable_button');
					$(this).removeClass('enable_button');
				});
				$('.disable_button').live('click', function(){
					$.post('scripts/change_enabled.php', {id:$(this).attr('id'), status:'1'});
					$(this).html('Disable Registration');
					$(this).addClass('enable_button');
					$(this).removeClass('disable_button');
				});
				$('.visible_button').live('click', function(){
					$.post('scripts/change_visibility.php', {id:$(this).attr('id'), status:'0'});
					$(this).html('Make Visible to Public');
					$(this).addClass('invisible_button');
					$(this).removeClass('visible_button');
				});
				$('.invisible_button').live('click', function(){
					$.post('scripts/change_visibility.php', {id:$(this).attr('id'), status:'1'});
					$(this).html('Make Invisible to Public');
					$(this).addClass('visible_button');
					$(this).removeClass('invisible_button');
				});
				
				jQuery.columnCount = function (){
					var numCols = $("#reports_table").find('tr')[0].cells.length;
					return numCols;
				}
				jQuery.moveColumn = function (table, from, to) {
					var rows = jQuery('tr', table);
					var cols;
					rows.each(function() {
						cols = jQuery(this).children('th, td');
						if (from>to)
							cols.eq(from).detach().insertBefore(cols.eq(to));
						else{
							if (cols.eq(to+1)) 
							cols.eq(from).detach().insertAfter(cols.eq(to));
						}
					});
				}
				$('.moveleft').live('click',function(){
					var tbl=$('#reports_table');
					var th=$(this).parents("th:first").index();
					if (!$(this).next('.moveright').is(':visible')){
						$(this).closest('th').prev().find('.moveright').hide();
						$(this).next('.moveright').show();				
					}
					$.moveColumn(tbl, th, th-1);
					if (th-1==0){
						$(this).hide();
						$('.moveleft').not(this).show();
					}		
				});
				$('.moveright').click(function(){
					var tblln=$("#reports_table").find("tr:first td").length;
					var tbl=$('#reports_table');
					var th=$(this).parents("th:first").index();
					if (!$(this).prev('.moveleft').is(':visible')){
						$(this).prev('.moveleft').show();
							$(this).closest('th').next().find('.moveleft').hide();
					}
					var numCols=$.columnCount();
				
					if (th+1==numCols-1){
						$(this).hide();
						$('.moveright').not(this).show();
					}
					$.moveColumn(tbl, th, th+1);
				});
				$('#rightclick').click(function(event){
					var href=$(this).attr('href');
					event.preventDefault();
					var serialstring=getSerializedData();
					var columns=$('#reports_form').serialize();
					$.post('scripts/write_report.php', {data:serialstring, columns:columns, formid:'<?php echo $_GET['formid']; ?>'},function(){window.location = href;});
				});
				$('#requiresloginy').click(function(){
					$('#logingroups').show();
				});
				$('#requiresloginn').click(function(){
					$('#logingroups').hide();
				});
				$('.accept, .reject, .reset, .delete_answers').bind('click', function(event){
					var clicked=$(this);
					var conf=true;
					if ($(this).attr('class')=='delete_answers')
						conf=confirm("Are you sure you want to delete this user's answers?");
					if (conf){
						$.post('scripts/accept.php',{'status':$(this).val(), 'form_id':'<?php if (array_key_exists('id', $_GET)) echo $_GET['id'];?>'}, function(data){
							var d = jQuery.parseJSON(data);
							if (d.status==='accept'){
								$('#'+d.userid+'_'+d.url+'_accept_status').html('<img src="images/accept.png">');
								$.post('scripts/reject.php',{'user':d.userid, 'id':'<?php if (array_key_exists('id', $_GET)) echo $_GET['id']; ?>','status':'accepted'}, function(data2){
									clicked.parent().append(data2);
								});
							}
							else if (d.status ==='reject'){
								$('#'+d.userid+'_'+d.url+'_accept_status').html('<img src="images/reject.png">');
								$.post('scripts/reject.php',{'user':d.userid, 'id':'<?php if (array_key_exists('id', $_GET)) echo $_GET['id']; ?>','status':'rejected'}, function(data2){
									clicked.parent().append(data2);
								});
							}
							else if(d.status=='reset'){
								$('#'+d.userid+'_'+d.url+'_accept_status').html('');
								$.post('scripts/reject.php',{'user':d.userid, 'id':'<?php if (array_key_exists('id', $_GET)) echo $_GET['id']; ?>','status':'reset'}, function(data2){
									clicked.parent().append(data2);
								});
							}
							else if(d.status=='deleted')
								location.reload();
						});
					}
				});
				$('.copy_form').click(function(){
					$.post('scripts/copy.php', {'id':$(this).val()}, function(){
						location.reload();
					});
				});
				$('.delete_form').click(function(){
					var c = confirm("Are you sure you want to delete this form?");
					if (c){
						$.post('scripts/delete.php', {'id':$(this).val()}, function(){
							location.reload();
						});
					}
				});
				$('.form_head img').live('click', function(){
					$(this).parent().parent().hide();
				});
				$('.files').live('click', function(){
				    $('#files_'+$(this).attr('id')).toggle();
				});
				$('#iframe_code_button').click(function(){
					$('#iframe_code').toggle();
				});
				
				$('#operationform').submit(function(e){
					if ($('#useroperation').val()=='delete'){
						var sure=confirm('Are you sure you want to delete?');
						if (!sure){
							e.preventDefault();
							return false;
						}
					}					
				});
				$('#usertable input:not(.noclickevent)').click(function(){
					var id=$(this).attr('name');
					id=id.split("[");
					id=id[1].split("]");
					id=id[0];
					$('input[name=record\\['+id+'\\]]').attr("checked","true");
				});
				$('#show_field').change(function(){
					$('#show_fields_form').submit();
				});
			});
			function getSerializedData(){
				var data = new Array();
				var i=0;
				var row=0;
				data[0]= new Array();
				$('#reports_table tr').each(function(){
					var j=0;
					data[row]=new Array();
					$('th, td', this).each(function(){
						data[row][j]=$.trim($(this).html().replace("&lt;","").replace("&gt;","").replace(/<(?:.|\n)*?>/gm, ''));
						j++;
					});
					row++;
				});
				return data;
			}
			tinyMCE.init({
				mode : "textareas",
				theme : "advanced",
				editor_deselector : "mceNoEditor",
				theme_advanced_text_colors : "FF00FF,FFFF00,000000",
				theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,fontselect,fontsizeselect,formatselect",
				theme_advanced_buttons2 : "cut,copy,paste,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,|,code,preview,|,forecolor,backcolor",
				theme_advanced_buttons3 : "insertdate,inserttime,|,spellchecker,advhr,,removeformat,|,sub,sup,|,charmap,emotions, hr",      
				style_formats : [
				{title : 'teensy', inline : 'span', styles:{'font-size':'.8em'}},
				{title : 'small', inline : 'span', styles:{'font-size':'.9em'}},
				{title : 'bigger', inline : 'span', styles:{'font-size':'1.5em'}},
				{title : 'LARGER', inline : 'span', styles:{'font-size':'2.0em'}},
				{title : 'HUGE!', inline : 'span', styles:{'font-size':'2.5em'}}
				],
				relative_urls: false,
			});
			
		</script>
	</head>
	<body onload="window.parent.parent.scrollTo(0,0)">
		<?php  if (file_exists("themes/".$theme."/header.php")) include("themes/".$theme."/header.php"); ?>
		
		<?php if ($_SESSION['membertype']=='admin'): ?>
		<div id="form_tabs2">
			<ul class='tabs'>
				<li <?php if ($selected=='home') echo "class='selected'";?>><a href='admin.php'>Home</a></li>
				<li <?php if ($selected=='new_form') echo "class='selected'";?>><a href='createform.php'>New form</a></li>
				<li class='<?php if ($selected=='reports') echo "selected ";?> last'><a href='reports2.php'>Reports</a></li>
				<li class='<?php if ($selected=='reports_beta') echo "selected ";?> last'><a href='reports.php'>Reports Beta</a></li>
			</ul>
			
		</div>
		<?php endif; ?>
		<div id='form_container'>

		<?php if ((isset($user_login_required) && isset($_SESSION['userid'])) || $_SESSION['membertype']=='admin'): ?>
		<div id='logout'>
			<a href='logout.php'>Log out</a>
		</div>
		<?php endif;?>