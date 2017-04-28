<?php
	include_once("../conferences/includes/connect.php");
	include_once("../scripts/settings.php");
	$headers = 'From: '.$conference_admin['email'] . "\r\n" .
    'Reply-To: '.$conference_admin['email'] . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
	mail($conference_admin['email'], "Confirmation for Tekstadium", print_r($_POST, true), $headers);
	mail($_POST['email'], "mycompany Innovation Bootcamp", "Thank you for making a payment for the mycompany Innovation Bootcamp. If we have any questions we will contact you shortly.", $headers);
?>

<body>
	<p>Thank you for making a payment for the mycompany Innovation Bootcamp. We have received your payment in the amount of $<?php echo $_POST['x_amount'];?>. If we have any questions we will contact you shortly.</p>
</body>