<?php
	
	$title = "Conference Accountant Report";
	include_once("includes/connect.php");
	include_once("misc/functions.php");
	
	//Make sure the user is of type accountant
	if ($_SESSION['type'] != 'accountant' && $_SESSION['type'] != 'admin')
		exit;
	define("COST_PER_ATTENDEE",  750);
	define("NUM_DEFAULT_TABLE_ATTENDEES",  6);
	define("VENDOR_FORM_ID",  927);
	define("BILL_CODE","SC2016");
	$registration_ids = array(921, 922, 923, 924, 928);
	
	
	//Standard vendor registrations	
	$vendor_registrations = getVendorRegistration(VENDOR_FORM_ID);	
	//Any other registrations that are paid at the time the form is filled out (non-invoiced)
	$member_registrations = getMemberRegistrations($registration_ids);
	
	$all_registrations = array_merge($vendor_registrations, $member_registrations);
	
	//Convert vendor registrationdata to CSV
	$csv = '"Company","Invoice Amount","Invoice #","Amount Paid","Invoice Date"'.PHP_EOL;
	foreach ($all_registrations as $account)
		$csv.='"'.$account['company'].'","'.$account['invoice_amount'].'","'.$account['invoice_number'].'","'.$account['total_paid'].'","'.$account['time_updated'].'"'.PHP_EOL;
	
	//Save to CSV and make it a link to download
	 $filename="/var/www/html/forms_app/tmp/conference_accounting_report.csv";
	 $fh = fopen($filename, 'w');
	 fwrite($fh, $csv);
	 header('Content-Description: File Transfer');
     header('Content-Type: text/csv');
     header('Content-Disposition: attachment; filename=conference_accounting_report.csv');
     header('Content-Transfer-Encoding: binary');
     header('Expires: 0');
     header('Cache-Control: must-revalidate');
     header('Pragma: public');
     header('Content-Length: ' . filesize($filename));
     ob_clean();
     flush();
     readfile($filename);
