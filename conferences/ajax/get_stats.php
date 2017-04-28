<?php
	session_start();
	include_once("../includes/db_connect.php");
	
	if (!isset($_SESSION['conf_user']) && $_SESSION['type'] != 'admin')
		exit;
	$stats = array();
	$result = mysql_query("SELECT sum(quantity) as quantity FROM daily_stats WHERE date = '".$_GET['date']."' AND stat = 'paid'");
	$rec = mysql_fetch_array($result);
	$stats['seven_fifty'] = $rec['quantity'];
	$result = mysql_query("SELECT sum(quantity) as quantity FROM daily_stats WHERE date = '".$_GET['date']."' AND stat = 'checked_in'");
	$rec = mysql_fetch_array($result);
	$stats['checked_in'] = $rec['quantity'];
	$stats = json_encode($stats);
	
	echo $stats;
?>
