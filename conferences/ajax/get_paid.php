<?php
	session_start();
	include_once("../includes/db_connect.php");
	
	if (!isset($_SESSION['conf_user']) && $_SESSION['type'] != 'admin')
		exit;
	$customers = array();
	$result = mysql_query("SELECT customer_information_id, quantity FROM conferences.daily_stats WHERE date = '".$_GET['date']."' AND stat='paid'") or die(mysql_error());
	while ($rec = mysql_fetch_array($result)){
		$customers[] = array($rec['customer_information_id'] => $rec['quantity']);
	}
	$customers = json_encode($customers);
	
	echo $customers;
?>
