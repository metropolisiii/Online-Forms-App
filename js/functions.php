<?php
/**
* File to hold all global functions
*
* @author Jason Kirby <jkirby1325@gmail.com>
*
*/

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
        $input  = cleanInput($input);
        $output = mysql_real_escape_string($input);
    }
    return $output;
}
/**
* Cleans and formats input for output suitable for reports
* 
* @param string $element
* @return string
*/
function format_for_report($element){
	$fieldid = preg_replace("/&lt;.+?&gt;/is", "", str_replace(" ", "_", $element) );
	$fieldid=strtolower( preg_replace("/<.+?>/is", "", str_replace(" ", "_", $fieldid) ) );
	$fieldid=html_entity_decode($fieldid, ENT_QUOTES);
	$patterns = array();
	$patterns[0] = '/[^a-zA-Z0-9_-]/';
	$replacements = array();
    $replacements[0] = '';
    $fieldid = preg_replace($patterns, $replacements, trim($fieldid));
	return $fieldid;
}
/**
* Determins if a substring exists within any elements of an array
* 
* @param string $haystack The substring to search for
* @param array $needle The array to search through
* @return bool
*/
function substr_in_array($haystack, $needle){
	$found = ARRAY();
 	// cast to array 
    $needle = (ARRAY) $needle;
 
    // map with preg_quote 
    $needle = ARRAY_MAP('preg_quote', $needle);
 
    // loop over  array to get the search pattern 
    FOREACH ($needle AS $pattern)
    {
        IF (COUNT($found = PREG_GREP("/$pattern/", $haystack)) > 0) {
        	RETURN $found;
        }
    }
    // if not found 
    RETURN FALSE;
}
/**
* Sanitizes input but keep HTML tags but in escaped form
* 
* @param string $input
* @return string
*/
function sanitize_leave_html($input){
	if (is_array($input)) {
        foreach($input as $var=>$val) {
            $output[$var] = sanitize_leave_html($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $search = '@<script[^>]*?>.*?</script>@si';   // Strip out javascript
		$output = htmlspecialchars(preg_replace($search, '', $input), ENT_QUOTES);
    }
    return $output;
}
/**
* Search for user in Active Directory via LDAP and returns an array of user information. Returns false if no user found or bad binding.
* 
* @param string $user
* @return array
*/
function ldap_user_info($user){
	$ds=ldap_connect("ldap.mycompany.com");
	if ($ds) { 
		$ldapbind=ldap_bind($ds, 'CTLINT\zz_itwebsvc', 'UAq,0@ki');
        if ($ldapbind) {
			$filter="sAMAccountName=".$user;
			$dn = "OU=community,DC=mycompany,DC=com";
			$LDAPFieldsToFind = array("mail","givenname", "sn");
			$sr=ldap_search($ds, $dn, $filter, $LDAPFieldsToFind);
			$info = ldap_get_entries($ds, $sr);
			return $info;
		}
		else 
			return false;
	}
	else
		return false;
}
/**
* Swaps two keys in a multiple dimensional array
* 
* @param array $two_dimensional_array
* @return array
*/
function array_two_key_swap( $two_dimensional_array ) {
	$keys = array_keys( $two_dimensional_array );
	$array_swaped = array();
	foreach( $two_dimensional_array[$keys[0]] as $key_counter => $value1 ) {
		$temp_array = array();
		foreach( $keys as $key)
			$temp_array[$key] = $two_dimensional_array[$key][$key_counter];
		$array_swaped[] = $temp_array;
	}
	return $array_swaped;
}
/**
* Generates a random sequence of alphanumeric characters
* 
* @return string
*/
function generate_random_url($length=10){
	$valid_chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_-';
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

function debug($key, $value, $debug=false) {
	if($debug){
		echo '<b>'.$key . "</b> = ";
		switch (gettype($value)) {
			case 'string' :
			echo $value;
				break;
			case 'array' :
			case 'object' :
			default :
				echo '<pre>';
				print_r($value);
				echo '</pre>';
				break;
		}
	}
}
?>