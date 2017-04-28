<?php
/**
* Sanitizes input for safe insertion into database
* 
* @param string $input
* @return string
*/
function cleanInput($input) {
	$search = array(
		'@<script[^>]*?>.*?</script>@si',   // Strip out javascript
		'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
		'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
		'@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
	);
	$output = preg_replace($search, '', $input);
    return $output;
}
/**
* Takes input, decides if it's an array. If it is, recursively call this function until input is not an array and sends it to the cleanInput function
* 
* @param string $input May be an array
* @return string
*/  
function sanitize($input) {
	if (is_array($input)) {
        foreach($input as $var=>$val) {
            $output[$var] = sanitize($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $output  = cleanInput($input);
      //  $output = mysql_real_escape_string($input);
    }
    return $output;
}
function generate_random_url($length=10){
	$valid_chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-';

	// start with an empty random string
    $random_string = "";

    // count the number of chars in the valid chars string so we know how many choices we have
    $num_valid_chars = strlen($valid_chars);

    // repeat the steps until we've created a string of the right length
    for ($i = 0; $i < $length; $i++)
    {
        // pick a random number from 1 up to the number of valid chars
        $random_pick = mt_rand(1, $num_valid_chars);

        // take the random character out of the string of valid chars
        // subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
        $random_char = $valid_chars[$random_pick-1];

        // add the randomly-chosen char onto the end of our string so far
        $random_string .= $random_char;
    }

    // return our finished random string
    return $random_string;
}
function convert_number_to_words($number) {
   
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );
   
    if (!is_numeric($number)) {
        return false;
    }
   
    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }
   
    $string = $fraction = null;
   
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
   
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }
   
    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
   
    return $string;
}

function getVendorRegistration($formid){
	//get the company, the amount of the invoice, the invoice number, the amount paid, and the invoice date
	$query = "SELECT accounts.id, field_id, response, total_paid, time_updated FROM accounts INNER JOIN forms ON accounts.id = forms.user_id INNER JOIN forms_app.user_form ON forms.user_form_id = user_form.id INNER JOIN forms_app.form_answers ON user_form.id = form_answers.user_form_id LEFT JOIN customer_information ON form_answers.id = customer_information.form_answer_id WHERE formid=".$formid." AND (field_id LIKE 'Person_%_Last_Name' OR field_id = 'Company' OR field_id='Demo_Type' OR field_id='dc-Promotional_Code')";
	$result = mysql_query($query);
	while ($rec = mysql_fetch_object($result)){
		if (!$customer_information[$rec->id])
			$customer_information[$rec->id] = array("table_payment"=>0);
		if ($rec->field_id=="Company"){
			$invoice_date_updated = strtotime($rec->time_updated);
			if ($invoice_date_updated < 0)
				$invoice_date_updated = 0;
			$accounts[$rec->id]['company']=$rec->response;
			$accounts[$rec->id]['discount_multiplier'] = 1;
			$accounts[$rec->id]['invoice_number'] = BILL_CODE."-".$rec->id;
			$accounts[$rec->id]['total_paid'] = $rec->total_paid;
			if ($invoice_date_updated != 0)
				$accounts[$rec->id]['time_updated'] = date("m/d/Y", $invoice_date_updated);
			else
				$accounts[$rec->id]['time_updated'] = "";
		}			
		else if ($rec->field_id == "Demo_Type"){
			preg_match('!\d+!', $rec->response, $response);
			$accounts[$rec->id]['invoice_amount']+=$response[0];			
		}
		else if (strpos($rec->field_id, "Last_Name") !== false){
			if ($rec->response != ""){
				$accounts[$rec->id]['personnel_count']++;
				if ($accounts[$rec->id]['personnel_count'] > NUM_DEFAULT_TABLE_ATTENDEES)
					$accounts[$rec->id]['invoice_amount'] += 750;
			}
		}
		else if ($rec->field_id=='dc-Promotional_Code' && $rec->response != ''){
			/* Adjust the total if a promotional code is applied to the invoice */
			$query="SELECT amount FROM forms_app.discounts WHERE form_id=".$formid." AND code='".$rec->response."'";
			$result2=mysql_query($query);
			$rec2=mysql_fetch_object($result2);
			$discount=$rec2->amount;
			if ($discount=="-999"){
				$accounts[$rec->id]['discount_multiplier'] = 0;
			}
			else
				$accounts[$rec->id]['invoice_amount']-=$discount;
		}
		
		$accounts[$rec->id]['invoice_amount'] *= $accounts[$rec->id]['discount_multiplier'];
	}
	usort($accounts, function($a, $b) {
		return strcasecmp($a['company'], $b['company']);
	});
	return $accounts;
}

function getMemberRegistrations($registration_ids){
	for ($i=0; $i<count($registration_ids); $i++){
		$company = "";
		$query = "SELECT uf.id, field_id, response FROM forms_app.form_answers fa INNER JOIN forms_app.user_form uf ON fa.user_form_id = uf.id WHERE uf.formid = {$registration_ids[$i]}";
		$result = mysql_query($query);
		while ($rec = mysql_fetch_object($result)){
			if (strpos($rec->field_id,'Company') !== false)
				$member_registrations[$rec->id]['company'] = $rec->response;
			else if ($rec->field_id == 'authamount')
				$member_registrations[$rec->id]['invoice_amount'] = $rec->response;
			else if ($rec->field_id == 'Registration_Sequence')
				$member_registrations[$rec->id]['invoice_number'] = $rec->response;
			else if ($rec->field_id == 'Amount_Paid')
				$member_registrations[$rec->id]['total_paid'] = $rec->response;
			else if ($rec->field_id == 'Date_Paid')
				$member_registrations[$rec->id]['time_updated'] = $rec->response;				
		}
	}
	return $member_registrations;
}