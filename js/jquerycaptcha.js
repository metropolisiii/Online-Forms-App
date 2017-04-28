

//Validate the Recaptcha' Before continuing with POST ACTION
function validateCaptcha()
{
	challengeField = $("input#recaptcha_challenge_field").val();
	responseField = $("input#recaptcha_response_field").val();
	var html = $.ajax({
		type: "POST",
		url: "../lib/validateform.php",
		data: "form=signup&recaptcha_challenge_field=" + challengeField + "&recaptcha_response_field=" + responseField,
		async: false
		}).responseText;
	if(html == "success") {
		//Add the Action to the Form
		$("#formform").attr("action", "../storeform.php"); //<-- your script to process the form
		
		$("form").submit();
	} else {
		$("#captcha-status").html("<p class=\"red bold\">The security code you entered did not match. Please try again.</p>");
		Recaptcha.reload();
	}
}	
