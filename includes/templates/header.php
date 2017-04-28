<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1252">
		<meta http-equiv="Cache-control" content="no-cache">
		<title>mycompany forms</title>
		<link type="text/css" rel="stylesheet" href="css/styles.css">
		<link type="text/css" rel="stylesheet" href="themes/<?php echo $_SESSION['theme']; ?>/css/styles.css">
		<link href="//fonts.googleapis.com/css?family=Oswald" rel="stylesheet" type="text/css" />
		<link href="//fonts.googleapis.com/css?family=Roboto:400,400italic,700" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="css/jquery-ui-1.8.23.custom.css" type="text/css" media="all" />
		<link rel="stylesheet" href="css/ui.css" type="text/css" media="all" />
		<?php echo $styles; ?>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
		<script src="js/jquery-ui-1.8.23.custom.min.js"></script>
		<script src="js/jquery-ui-min.js"></script>
		<script type="text/javascript" src="js/scripts.js"></script>
		<?php echo $scripts;?>
		
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
								
				$('#rightclick').click(function(event){
					var href=$(this).attr('href');
					event.preventDefault();
					var serialstring=getSerializedData();
					var columns=$('#reports_form').serialize();
					$.post('scripts/write_report.php', {data:serialstring, columns:columns, formid:'<?php echo $_GET['formid']; ?>'},function(){window.location = href;});
				});
			
				$('.accept, .reject, .reset, .delete_answers').bind('click', function(event){
					var clicked=$(this);
					var conf=true;
					if ($(this).attr('class')=='delete_answers')
						conf=confirm("Are you sure you want to delete this user's answers?");
					if (conf){
						$.post('scripts/accept.php',{'status':$(this).val(), 'form_id':'<?php echo $_GET['id'];?>'}, function(data){
							var d = jQuery.parseJSON(data);
							if (d.status==='accept'){
								$('#'+d.userid+'_'+d.url+'_accept_status').html('<img src="images/accept.png">');
								$.post('scripts/reject.php',{'user':d.userid, 'id':'<?php echo $_GET['id']; ?>','status':'accepted'}, function(data2){
									clicked.parent().append(data2);
								});
							}
							else if (d.status ==='reject'){
								$('#'+d.userid+'_'+d.url+'_accept_status').html('<img src="images/reject.png">');
								$.post('scripts/reject.php',{'user':d.userid, 'id':'<?php echo $_GET['id']; ?>','status':'rejected'}, function(data2){
									clicked.parent().append(data2);
								});
							}
							else if(d.status=='reset'){
								$('#'+d.userid+'_'+d.url+'_accept_status').html('');
								$.post('scripts/reject.php',{'user':d.userid, 'id':'<?php echo $_GET['id']; ?>','status':'reset'}, function(data2){
									clicked.parent().append(data2);
								});
							}
							else if(d.status=='deleted')
								location.reload();
						});
					}
				});
			});			
		</script>
	</head>
	<body onload="window.parent.parent.scrollTo(0,0)">
		<?php  if (file_exists("themes/".$_SESSION['theme']."/header.php")) include("themes/".$_SESSION['theme']."/header.php"); ?>		
		<?php if ($_SESSION['membertype']=='admin'): ?>
		<div id="form_tabs2">
			<ul class='tabs'>
				<?php for ($i=0; $i<count($tabs); $i++): ?>
					<li <?php if ($selected==$tabs[$i]['name']) echo "class='selected'";?>><a href='<?php echo $tabs[$i]['url']; ?>'><?php echo $tabs[$i]['name']; ?></a></li>
				<?php endfor; ?>
			</ul>			
		</div>
		<?php endif; ?>
		<div id='form_container'>
			<?php if (isset($_SESSION['userid']) || $_SESSION['membertype']=='admin'): ?>
				<div id='logout'>
					<a href='logout.php'>Log out</a>
				</div>
			<?php endif;?>