<?php
    include_once("settings.php");
	if ($_SESSION['membertype'] !== "admin"){
		header("Location: login.php");
		exit;
	}
	include("../misc/functions.php");
	$_GET=sanitize($_GET);
	$filename="/var/www/html/forms_app/tmp/".$_GET['file'];
	header('Content-Description: File Transfer');
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename='.$_GET['file']);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename));
    ob_clean();
    flush();
    readfile($filename);

?>