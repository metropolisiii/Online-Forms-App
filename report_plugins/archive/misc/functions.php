<?php
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
 function elemId($label, $prepend = false){
	if(is_string($label)){
		$prepend = is_string($prepend) ? $this->elemId($prepend).'-' : false;
		$fieldid = preg_replace("/&lt;.+?&gt;/is", "", str_replace(" ", "_", $label) );
		$fieldid= preg_replace("/<.+?>/is", "", str_replace(" ", "_", $fieldid) ) ;
		$fieldid=html_entity_decode($fieldid, ENT_QUOTES);
	
		$patterns = array(); //JK Mod
		$patterns[0] = '/[^a-zA-Z0-9\[\]_-]+/';//JK Mod
		$replacements = array(); //JK Mod
		$replacements[0] = ''; //JK Mod     
		$fieldid = preg_replace($patterns, $replacements, trim($fieldid));//JK Mod
		return $fieldid;
	}
	return false;
}