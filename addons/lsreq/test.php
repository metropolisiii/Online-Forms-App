<?php
	/*
	$service_url = 'https://community.mycompany.com/rest/api/2/issue';
	$curl = curl_init($service_url);
	$data=array(
		"fields" => array(
			"project" => array(
				"key"=>"LSREQ"
			),
			"issuetype"=>array(
				"name"=>"Dev Activity Request"
			),
			"customfield_12122"=>"Test Activity",
			"customfield_12102"=>array(
				"id"=>"13098"
			),
			"summary"=>"REST Test",
			
		)
	);
	$data = json_encode($data);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
		'Content-Type: application/json',                                                                                
		'Content-Length: ' . strlen($data),
		)                                                                       
	); 
	curl_setopt($curl, CURLOPT_USERPWD, "employee-test:G00gleTablet");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST"); 
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	$curl_response = curl_exec($curl);
	curl_close($curl);
	*/
	
	$service_url = 'https://community.mycompany.com/rest/api/2/issue/81722';
	$curl = curl_init($service_url);
	$data=array(
		"fields" => array(
			"customfield_12494"=>"jsmith",
			"customfield_12495"=>"jsmith",
			"customfield_12093"=>"303-661-3712",
			"customfield_12099"=>"Test description",
			"customfield_10102"=>"2015-07-14",
			"customfield_12103"=>"2015-08-14"
		)
	);
	$data = json_encode($data);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
		'Content-Type: application/json',                                                                                
		'Content-Length: ' . strlen($data),
		)                                                                       
	); 
	curl_setopt($curl, CURLOPT_USERPWD, "employee-test:G00gleTablet");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	$curl_response = curl_exec($curl);
	print_r($curl_response);
	curl_close($curl);
	
?>