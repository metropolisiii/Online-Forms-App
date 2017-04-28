<?php 
require_once("recaptchalib.php");

$privatekey = "6LdYiOgSAAAAACrmawjcxWIB6_joX3ituZSrpQPw"; //<!----- your key here
$resp = recaptcha_check_answer ($privatekey,
                              $_SERVER["REMOTE_ADDR"],
                              $_POST["recaptcha_challenge_field"],
                              $_POST["recaptcha_response_field"]);

if (!$resp->is_valid) {
  // What happens when the CAPTCHA was entered incorrectly
  //die ("The reCAPTCHA wasn't entered correctly. Please go back and try it again. <a href=\"javascript:history.go(-1)\">Back</a>"
  //"(reCAPTCHA said: " . $resp->error . ")
	echo "fail";
} else {
	echo "success";
}

?>