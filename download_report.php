<?php 
	/* 
	* Administrative module for viewing reports
	* @author Jason Kirby <jkirby1325@gmail.com>
	*/
	include_once("scripts/settings.php");
	include("includes/header.php");
	$_GET=sanitize($_GET);
	$_POST=sanitize($_POST);
	
	include('models/view_report.php');
	if ($_SESSION['membertype'] != "admin"){
		echo "You need to <a href='login.php'> login </a> to enter this area";
		exit;
	}	
	$strings = array("’", '\\"', "\\n");
	$replace = array("'", '"', "\n");
	/** Build CSV file **/
	$csv = array(array(htmlspecialchars_decode($report['report_name'], ENT_QUOTES)));//csv representation of report
	$head=array();
	//Write columns
	foreach ($report['column_names'] as $column_name)
		$head[]=htmlspecialchars_decode($column_name, ENT_QUOTES);
	$csv[]=$head;
	
	
	//Prepare data
	foreach ($report['report_data'] as $user_form=>$fields){
		$columns=array();
		foreach ($report['column_ids'] as $column_id){
			$columns[]=str_replace($strings,$replace,$fields[$column_id]);
		}	
		
		$csv[]=$columns;
	}
	

	//save CSV file
	$csv_name=preg_replace("/[^A-Za-z0-9 ]/", '', str_replace(" ","_",$report['report_name']))."_".date('mdy').".csv";
	$fh=fopen("tmp/{$csv_name}","w"); //Write information to csv
	fprintf($fh, chr(0xEF).chr(0xBB).chr(0xBF));

	foreach ($csv as $c)
		fputcsv($fh, $c);
	fclose($fh);
	//print_r($csv);

	$csv_name=preg_replace("/[^A-Za-z0-9 ]/", '', str_replace(" ","_",$report['report_name']))."_".date('mdy').".csv";
	echo '<script>window.location="scripts/downloadfile.php?file='.$csv_name.'"</script>';
	
?>
