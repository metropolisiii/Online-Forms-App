<?php

	
	//Get list of reports that user has permissions to
	$reports_query="SELECT reports.id, reports.name FROM reports INNER JOIN permissions ON reportid=reports.id where user='{$_SESSION['userid']}'";
	$reports_result=mysql_query($reports_query);

