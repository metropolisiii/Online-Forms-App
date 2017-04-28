<?php
	include("../includes/settings.php");
	$_GET=sanitize($_GET);
	$filename="../files/";
	$fileinfo=explode("_-_-_",$_GET['f']);
	$fileid=$fileinfo[0];
	$name=$fileinfo[1];
	$result=mysql_query("select * from files where user_form_id=".$fileid." AND name='".$name."'");

	if (mysql_num_rows($result) == 0)
		exit;
	$filename.=$fileid."_".$name;
	
	header('Content-Description: File Transfer');
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename='.$name);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename));
    ob_clean();
    flush();
    readfile($filename);
    exit;