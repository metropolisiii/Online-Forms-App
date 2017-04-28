<?php
	session_start();
	include_once("../includes/db_connect.php");
	
	if (!isset($_SESSION['conf_user']) && $_SESSION['type'] != 'admin')
		exit;
	
	if ($_POST['function'] == 'increment')
		mysql_query("UPDATE stats SET {$_POST['category']} = {$_POST['category']}+1 WHERE date = '{$_POST['date']}'") or die (mysql_error());
	else if ($_POST['function'] == 'decrement')
		mysql_query("UPDATE stats SET {$_POST['category']} = {$_POST['category']}-1 WHERE date = '{$_POST['date']}'") or die (mysql_error());
	echo "success";
?>
