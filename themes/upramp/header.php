<?php
	$d=htmlspecialchars($_SERVER['PHP_SELF']);
	$d=explode("/", $d);
	$dir="";
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

