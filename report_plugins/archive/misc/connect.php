<?php
	$db_name=($mode=='test')?'forms_app_test':'forms_app';	
	$db_user='forms_app';
	$db_pass='AtMzhttUUfzffvRm';
	
	mysql_connect($db_host,$db_user,$db_pass) or die("unable to connect");

	mysql_select_db($db_name) or die( "Unable to select database");