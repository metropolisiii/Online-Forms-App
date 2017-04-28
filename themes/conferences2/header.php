<?php
	$d=htmlspecialchars($_SERVER['PHP_SELF']);
	$d=explode("/", $d);
	if (count($d)>3)
		$dir="../";
?>
<?php
function curPageName() {
 return   "https://".$_SERVER["x_forwarded_host"] . $_SERVER["REQUEST_URI"];
}
?>
<link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet" type="text/css" />
<link href="https://fonts.googleapis.com/css?family=Roboto:400,400italic,700" rel="stylesheet" type="text/css" />
<script src="<?php echo $dir; ?>themes/<?php echo $theme; ?>/js/jquery.hover.js" type="text/javascript"></script>
<script src="<?php echo $dir; ?>themes/<?php echo $theme; ?>/js/init.js" type="text/javascript"></script>
<script src="<?php echo $dir; ?>themes/<?php echo $theme; ?>/js/main.js" type="text/javascript"></script>
<script>
	jQuery(document).ready( function($){
		 $('#i_share').click( function(){
			if( $('#follow_us').is(':visible')==false ){
				$('#follow_us, #search_form').hide();
				$('#follow_us').show();
				$('#i_share, #i_search').removeClass('on');
				$(this).addClass('on');
			}else{
				$('#follow_us, #search_form').hide();
				$(this).removeClass('on');
			}
			return false;
		});
		
		$('#i_search').click( function(){
			if( $('#search_form').is(':visible')==false ){
				$('#follow_us, #search_form').hide();
				$('#search_form').show();
				$('#i_share, #i_search').removeClass('on');
				$(this).addClass('on');
			}else{
				$('#follow_us, #search_form').hide();
				$(this).removeClass('on');
			}
			return false;
		});
	});
</script>
