<?php session_start(); include("../scripts/settings.php"); include("../scripts/connect.php"); include("../misc/functions.php"); if (empty($_POST["userid"])) $_POST["userid"]=$_SESSION["userid"]; if (!empty($_GET['account'])) $_SESSION['user_account'] = $_GET['account']; ?>
<?php
	$_GET=sanitize($_GET);
	$_POST=sanitize($_POST);
	$result=mysql_query("SELECT requires_login, theme FROM accounts WHERE url LIKE '".$forwarded_directory."'");
	$ulr=mysql_fetch_array($result);
	if ($ulr['requires_login']==1)
		$user_login_required=true;
	if (!empty($ulr['theme']))
		$theme=$ulr['theme'];

	if ($user_login_required && empty($_SESSION['userid']) && !$loginpage){ //If the user is required to be logged in and he hasn't logged in, we need to direct him to the login page.
		header("Location: ../login.php");
		exit;
	}
	
	$uri=explode("&account=", $_SERVER['REQUEST_URI']);
	$uri=$uri[0];
	$formid=explode(".html", $uri);
	$formid=explode("_",$formid[0]);
	$formid=$formid[count($formid)-1];
	$formid=explode(".html", $formid);
	$formid=$formid[0];
	$query=mysql_query("SELECT enabled, visible, date, form_invisible_message, form_no_reg_message, fb_savedforms.theme AS spectheme, fb_savedforms.theme AS theme, sitename from fb_savedforms INNER JOIN accounts on accounts.url=fb_savedforms.sitename WHERE fb_savedforms.id=".$formid);
	$rec=mysql_fetch_array($query);
	if (!empty($rec['theme']) && $rec['theme'] != "standard"){
		$theme_query = mysql_query("SELECT folder from themes where id = ".$rec['theme']);
		$theme = mysql_fetch_object($theme_query);
		$theme=$theme->folder;
	}
	else
		$theme="mycompany";
	if (!empty($_GET['q'])){
		$query2=mysql_query("SELECT * FROM user_form WHERE formid=".$formid." AND url='".$_GET['q']."'");
		$userinfo=mysql_fetch_array($query2);
	}
	
	$visible=$rec['visible'];
	$enabled=$rec['enabled'];
	include_once("/var/www/forms_app/classes/logController.php");
	$log=new logController();
	$log->log("Accessed ".$uri);
	
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
		<link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css" rel="stylesheet">
		<meta name="viewport" content="width=960, user-scalable=yes"/>
		<title>[[title]]</title>
		
		<script src='../js/jquery-1.3.2.min.js'></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script>
			document.domain = "mycompany.com"
			/* function getQueryString() {
                var queryStringKeyValue = window.parent.location.search.replace('?', '').split('&');
				
                var qsJsonObject = {};
                if (queryStringKeyValue != '') {
                    for (i = 0; i < queryStringKeyValue.length; i++) {
                        qsJsonObject[queryStringKeyValue[i].split('=')[0]] = queryStringKeyValue[i].split('=')[1];
                    }
                }
                return qsJsonObject;
            }
			*/
			function getStates(country, state){
				if ($('.states_list').length){
					$.ajax({
						url: "../scripts/get_states.php",
						context: $('.states_list'),
						async:false,
						type:'GET',
						data:{'country':country},
						dataType: "html"
					}).done(function(html){
						$(this).append(html);
						$(this).val(state);
					});
				}
			}
			function getCountries(){				
				if ($('.countries_list').length){
					$.ajax({
						url: "../scripts/get_countries.php",
						context: $('.countries_list'),
						async:false,
						dataType: "html"
					}).done(function(html){
						$(this).append(html);
					});
				}
			}
			$(document).ready(function() {
				<?php if ($userinfo['submitted'] == 1): ?>
					$('[id=saveform]').attr("disabled","disabled");
				<?php endif; ?>
				$.ajax({
					type: "POST",
					url: "../scripts/get_forwarded_directory.php",
					data: { forwarded_url: window.location.pathname},
					success: function(){
						<?php if (empty($_SESSION['forwarded_directory'])): ?>
							location.reload();
						<?php endif; ?>
					}
				});
				$( ".validate_date" ).datepicker({dateFormat: "m/d/yy"});
			//	var q=getQueryString().q;
				var validformat=/(^(1[0-2]|0?[1-9])\/(3[01]|[12][0-9]|0?[1-9])\/(?:[0-9]{2})?[0-9]{2})$/;
				var validemailformat=/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				var validwebsiteformat=/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/;
				$.post('../scripts/get_form_data.php',{filepath:'<?php echo $uri; if (!empty($_GET['account'])) echo "?q='+q+'"?>', userid:'<?php echo $_POST['userid']; ?>'}, function(data){
					getCountries();
					
					if (data !=='[]'){
						$('input:checkbox').removeAttr('checked');
						$('input:radio').removeAttr('checked');
						$('option:selected').removeAttr('selected');
					}
					var countries=false;
					var states=false;
					var obj = jQuery.parseJSON(data);
					var confirmation_email_number=0;
					$(obj).each(function(i,val){
						$.each(val,function(k,v){
							if ($('input[name='+k+']').is(':radio')){
								$('input[name='+k+'][value="'+v+'"]').first().attr('checked','checked');
							}
							else if ($('input[name^='+k+']').is(':checkbox')){
								 $('input[name^='+k+']').each(function() {
									if (v.indexOf($(this).val()) >= 0)
										$(this).attr('checked','true');
								 });
							}
							else if($('#'+k).is('select')){
								if ($('#'+k).hasClass('countries_list'))
									countries=$('#'+k);
								else if ($('#'+k).hasClass('states_list'))
									states=v;
								$('select[name='+k+']').val(v);
							}
							else	
								$('#'+k).val(v);
						});

					});
					if (states){
						getStates(countries.val(), states);
					}
					else
						getStates();
					var d = new Date();
					$('#date_of_completion_completiondate').val();
					$('#date_of_completion_completiondate').val(d.getMonth()+1+"/"+d.getDate()+"/"+d.getFullYear());
				});
				$('.countries_list').change(function(){
					if ($('.states_list').length){
						var t=$(this).val();
						$.ajax({
							url: "../scripts/get_states.php",
							data: {'country':t},
							type: "GET",
							context: $('.states_list'),
							dataType: "html"
						}).done(function(html){
							$(this)
							.find('option')
							.remove()
							.end()
							.append(html);
						});
					}
				});
				$('.word_counter').parent().prev('textarea').keyup(function(){
					var text = $(this).val();
					if(text === "") {
						wordcount = 0;
					} else {
						wordcount = $.trim(text).split(" ").length;
					}
					$(this).next().find('.word_counter').html(wordcount);
				});
				$('.character_counter').parent().prevAll('textarea').keyup(function(){
					var text = $(this).val();
					if(text === "") {
						charactercount = 0;
					} else {
						charactercount = $.trim(text).length;
					}
					$(this).nextAll().find('.character_counter').first().html(charactercount);
				});
				$(document).on('keyup change', '.highlighted', function(){
					if ($(this).hasClass('required_field') && $(this).val() != ''){
						$(this).removeClass('highlighted');
						$(this).prev('.error').remove();
					}
				});
				$('.checkbox.highlighted').live('click', function(){
					if ($(this).find('input[type=checkbox]:checked').length > 0){
						$(this).removeClass('highlighted');
						$(this).prev('.error').remove();
					}
						
				});
				$('.radio.highlighted').live('click', function(){
					if ($(this).find('input[type=radio]:checked').length > 0){
						$(this).removeClass('highlighted');
						$(this).prev('.error').remove();
					}
				});
				
				$('#submitform, #submitform2, #generateinvoice').click(function(e){
					$('.error').remove();
					
					var emptyfield=false;
					var validatedate=false;
					var validateemail=false;
					var emptymultiple=false;
					var validatephone=false;
					var validatewebsite=false;
					$('.checkbox.required').each(function(){
						if ($(this).find('input[type=checkbox]:checked').length == 0){
							emptymultiple=true;
							$(this).addClass("highlighted");
							$("<span class='error'>Required Field</span>").insertBefore($(this));
						}
					});
					$('.radio.required').each(function(){
						if ($(this).find('input[type=radio]:checked').length == 0){
							emptymultiple=true;
							$(this).addClass("highlighted");
							$("<span class='error'>Required Field</span>").insertBefore($(this));
						}
					});
					$('.required_field').each(function(){
						if ($(this).val()===''){
							emptyfield=true;
							$("<span class='error'>Required Field</span>").insertBefore($(this));
						}
					});
					$('.validate_date').each(function(){
						if ($(this).val()!="" && !validformat.test($(this).val())){
							validatedate=true;
							$("<span class='error'>Please insert a valid date (i.e. June 12, 2013)</span>").insertBefore($(this));
						}
					});
					
					$('.websitefield').each(function(){
						if ($(this).val()!="" && !validwebsiteformat.test($(this).val())){
							validatewebsite=true;
							$(this).addClass('highlighted');
							$("<span class='error'>Please insert a valid website (i.e. www.example.com)</span>").insertBefore($(this));
						}
					});
					$('.email_text').each(function(){
						if ($(this).val()!="" && !validemailformat.test($(this).val())){
							$(this).addClass('highlighted');
							validateemail=true;
							$(this).addClass("highlighted");
							$("<span class='error'>Please insert a valid email address(i.e. example@email.com)</span>").insertBefore($(this));
						}
					});
					if (emptyfield || emptymultiple){
						$('.required_field').each(function(){
							if ($(this).val()==='')
								$(this).addClass('highlighted');
						});
						alert("Please fill out required fields.");
					}
					if (validatedate){
						$('.validate_date').each(function(){
							if ($(this).val()!="" && !validformat.test($(this).val()))
								$(this).addClass('highlighted');
						});
						alert("Please use correct date format (mm/dd/YYYY).");
					}
					if (validateemail){
						alert("Please use a valid email address.");
					}
					if (validatephone){
						alert("Please use a valid phone format (xxx-xxx-xxxx).");
					}
					if (validatewebsite){
						alert("Please use a valid website format (http://wwww.example.com).");
					}
					if (emptyfield || validatedate || emptymultiple || validateemail || validatephone || validatewebsite)
						e.preventDefault();
				});
			});
			
			
			$(window).bind('load',function(){
				$('.word_counter').parent().prev('textarea').each(function(){
					var text = $(this).val();
					if(text === "") {
						wordcount = 0;
					} else {
						wordcount = $.trim(text).split(" ").length;
					}
					$(this).next().find('.word_counter').html(wordcount);
				});
				$('.character_counter').parent().prevAll('textarea').each(function(){
					var text = $(this).val();
					if(text === "") {
						charactercount = 0;
					} else {
						charactercount = $.trim(text).length;
					}
					$(this).nextAll().find('.character_counter').first().html(charactercount);
				});
			});
			function remember( selector ){
				$(selector).each(function(){
					//if this item has been cookied, restore it
					var name = $(this).attr('name');
					var val='';
					val=$(this).val();
					//assign a change function to the item to cookie it
					$.cookie(name, val, { path: '/', expires: 365 });
				});
			}

			function retrieve( selector ){
				$(selector).each(function(){
					//if this item has been cookied, restore it
					var name = $(this).attr('name');
					var val='';
					val=$(this).val();
					if( $.cookie( name ) ){
						$(this).val( $.cookie(name) );
					}
					//assign a change function to the item to cookie it
					$.cookie(name, $(this).val(), { path: '/', expires: 365 });
				});
			}
		</script>
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-75020241-1', 'auto');
		  ga('send', 'pageview');

		</script>
		<link type="text/css" rel="stylesheet" href="../css/styles.css">
		<link type="text/css" rel="stylesheet" href="../themes/<?php echo $theme; ?>/css/styles.css">
		<style type="text/css">

				.frmb table tr td:first-child{
					width:35%;
				}
				strong{
					font-weight:bold !important;
					
				}
				.fb_section{
					border-bottom: thin solid #3366FF;
					color: #3366FF;
					font-size: 1.2em;
					font-weight: bold;
					margin-bottom: 30px;
					margin-top: 30px;
					width: 100%;
				}
		
			.highlighted{
				background:#FFFFCC !important;
			}
			td div{
				margin-bottom:10px;
				
			}
			div.clbox4{
			background: #1e5799; /* Old browsers */
			background: -moz-linear-gradient(top, #1e5799 0%, #2989d8 50%, #207cca 51%, #7db9e8 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#1e5799), color-stop(50%,#2989d8), color-stop(51%,#207cca), color-stop(100%,#7db9e8)); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top, #1e5799 0%,#2989d8 50%,#207cca 51%,#7db9e8 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top, #1e5799 0%,#2989d8 50%,#207cca 51%,#7db9e8 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top, #1e5799 0%,#2989d8 50%,#207cca 51%,#7db9e8 100%); /* IE10+ */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#1e5799', endColorstr='#7db9e8',GradientType=0 ); /* IE6-9 */

			 background: linear-gradient(to bottom, #1E5799 0%, #207CCA 28%, #2989D8 41%, #7DB9E8 100%) repeat scroll 0 0 transparent;
			border-radius: 5px 5px 5px 5px;
			color: #FFFFFF;
			font-size: 1.3em;
			margin-bottom: 25px;
			margin-top: 25px;
			padding: 5px;
			}
			div.clbox4 ul{
				list-style: disc inside none;
			}
			p{
				font-size: 11px;
				font-weight: bold;
			}
			div.clbox4 p{
				font-size: 1.1em;
			}
		</style>
	</head>
	<body>
		<?php if (file_exists('../themes/'.$theme.'/header.php')) include('../themes/'.$theme.'/header.php'); ?>
		<?php
	
			if (($rec['date'] < date("U") || $visible == 0 || $enabled==0) && $_SESSION['membertype'] !='admin'){
				
				if ($rec['date'] < date("U"))
					echo "This form has closed.<br/>";
				if ($visible==0)
					echo html_entity_decode($rec['form_invisible_message'])."<br/>";
				else if ($enabled==0)
					echo html_entity_decode($rec['form_no_reg_message'])."<br/>";
				exit;
			}
			
			if ($userinfo['accepted'] == 0 && !is_null($userinfo['accepted'])  && $_SESSION['membertype'] !='admin'){
				echo "This form has already been reviewed and needs more information.";
				exit;
			}
			if ($userinfo['accepted'] == 1 && !is_null($userinfo['accepted'])  && $_SESSION['membertype'] !='admin'){
				echo "This form has already been reviewed and it was accepted.";
				exit;
			}
		?>
		<div id="form_container">
			<div id='h1_title'><h1><span>[[title]]</span></h1></div>
			<ul class="breadcrumbs"></ul><!--close breadcrumbs-->
			<div class="page-content">
				<div class="clbox5" style="clear:both">
					<?php if($_SESSION['membertype'] == 'admin'): ?>
					<?php if (empty($_GET['q'])): ?>
					<div id="back_button"><A HREF="../admin.php"><< Back</A></div>
					<?php else: ?>
					<div id="back_button"><A HREF="../review.php?id=<?php echo $formid;?>"><< Back</A></div>
					<?php endif; ?>
					<?php endif; ?>
					[[content]]
					
				</div>
			</div><!--close page-content-->
		</div><!--close form_container-->
		<?php if(file_exists('../themes/'.$theme.'/footer.php')) include('../themes/'.$theme.'/footer.php'); ?>
	</body>
</html>