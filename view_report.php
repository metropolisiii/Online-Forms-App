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

	/* include plugins */
	if (file_exists('report_plugins/'.$report['plugins'].'/index.php')){
	
		include('report_plugins/'.$report['plugins'].'/index.php');				
	}
	if (function_exists('addonRun')){
		addonRun();
	}
	
	/** Build CSV file **/
	$csv = "\"".htmlspecialchars_decode($report['report_name'], ENT_QUOTES)."\"\n";//csv representation of report
	
	//Write columns
	for ($i=0; $i<count($report['column_names']); $i++)
		$csv.="\"".htmlspecialchars_decode($report['column_names'][$i], ENT_QUOTES)."\",";
	$csv.="\n";
	
	//Prepare data
	foreach ($report['report_data'] as $user_form=>$fields){
		foreach ($report['column_ids'] as $column_id){
			$csv.="\"".htmlspecialchars_decode($fields[$column_id])."\",";
		}	
		$csv.="\n";
	}
	

	//save CSV file
	$csv_name=preg_replace("/[^A-Za-z0-9 ]/", '', str_replace(" ","_",$report['report_name']))."_".date('mdy').".csv";
	$fh=fopen("tmp/{$csv_name}","w"); //Write information to csv
	fwrite($fh,$csv);
	fclose($fh);
	
	$csv_name=preg_replace("/[^A-Za-z0-9 ]/", '', str_replace(" ","_",$report['report_name']))."_".date('mdy').".csv";
	if ($_GET['download']=='true')
		echo '<script>window.location="scripts/downloadfile.php?file='.$csv_name.'"</script>';
	include ('views/view_report.php');
	if (function_exists('postView')){
		postView();
	}
	include("includes/footer.php");
?>
