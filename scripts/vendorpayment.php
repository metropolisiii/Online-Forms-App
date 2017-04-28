<?php
require_once '../misc/AuthorizeNet.php'; // Include the SDK you downloaded in Step 2
require_once '../scripts/settings.php';
require_once '../scripts/connect.php';
if ($_GET['a'])
	$amount=$_GET['a'];
else
	$amount = "750.00";
$api_login_id = '29adQ9Gsa';
$transaction_key = '3Rvv8E2eZ8U8e32t';
$fp_timestamp = time();
$fp_sequence = "123" . time(); // Enter an invoice or other unique number.
$fingerprint = AuthorizeNetSIM_Form::getFingerprint($api_login_id,$transaction_key, $amount, $fp_sequence, $fp_timestamp);
?>
<html>
<head></head>
<body>
<form method='post' name='paymentform' action="https://secure.authorize.net/gateway/transact.dll">
<input type='hidden' name="x_login" value="<?php echo $api_login_id;?>" />
<input type='hidden' name="x_fp_hash" value="<?php echo $fingerprint;?>" />
<input type='hidden' name="x_amount" value="<?php echo $amount;?>" />
<input type='hidden' name="x_fp_timestamp" value="<?php echo $fp_timestamp;?>" />
<input type='hidden' name="x_fp_sequence" value="<?php echo $fp_sequence;?>" />

<input type='hidden' name="x_version" value="3.1">
<input type='hidden' name="x_show_form" value="payment_form">
<?php if (!empty($_GET['d'])): ?>
<input type='hidden' name="x_description" value="PAYMENT WAS DECLINED. PLEASE TRY AGAIN OR CONTACT YOUR CREDIT CARD COMPANY." />
<?php else: ?>
<input type='hidden' name="x_description" value="mycompany Payment Form" />
<?php endif; ?>
<input type='hidden' name="x_test_request" value="FALSE" />
<input type='hidden' name="Person_Being_Registered" value="<?php echo $_GET['p']; ?>" />
<input type='hidden' name="x_method" value="cc">
<input type='hidden' name="formid" value="<?php echo $_GET['id']; ?>">
<INPUT TYPE='hidden' NAME="x_relay_response" VALUE="TRUE">
<INPUT TYPE='hidden' NAME="x_relay_always" VALUE="FALSE">
<INPUT TYPE='hidden' NAME="x_relay_url" VALUE="https://www.mycompany.com/forms/finish_transaction.php">
<INPUT TYPE='HIDDEN' NAME="x_receipt_link_method" VALUE="LINK"/>
<INPUT TYPE='HIDDEN' NAME="x_receipt_link_text" VALUE="Click here to return to mycompany"/>
<INPUT TYPE='HIDDEN' NAME="x_receipt_link_URL" VALUE="https://www.mycompany.com/forms/finish_transaction.php"/>
<INPUT TYPE='HIDDEN' NAME="billcode" VALUE="<?php echo $_GET['bc']; ?>"/>
Redirecting to Payment Form
</form>
 <script language="javascript" type="text/javascript">
    document.paymentform.submit();
</script>
</body>