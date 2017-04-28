<?php
/**
* Connects to database
* @author Jason Kirby <jkirby1325@gmail.com>
*/
	ini_set("session.gc_maxlifetime","7200");

	session_start();
	$superadmins=array('superadmin@mycompany.com');
	define("user",$_SERVER['DOCUMENT_user'].'/forms_app/conferences/');
	date_default_timezone_set('America/Denver');
	$website='https://www.mycompany.com/forms/conferences/';
	include(user.'misc/functions.php');
	
	include(user.'includes/db_connect.php');
	
?>
<!DOCTYPE html>	
<html>
	<head>
		<title><?php echo $title; ?></title>
		
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/a5734b29083/integration/bootstrap/3/dataTables.bootstrap.css">
		<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/2.1.2/css/dataTables.fixedHeader.css">
		<link rel="stylesheet" href="css/styles.css">
		<link rel="stylesheet" type="text/css" media="screen" href="css/jquery.ketchup.css" />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/jquery.ketchup.all.min.js"></script>
		<script type="text/javascript" src="js/scripts.js"></script>
		<script type="text/javascript" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="//cdn.datatables.net/plug-ins/a5734b29083/integration/bootstrap/3/dataTables.bootstrap.js"></script>
		<script type="text/javascript" src="//cdn.datatables.net/fixedheader/2.1.2/js/dataTables.fixedHeader.min.js"></script>
		<script type="text/javascript" src="js/jquery.floatThead._.js"></script>
		<script type="text/javascript" src="js/jquery.floatThead.js"></script>
		<?php if ($head) echo $head; ?>
	</head>