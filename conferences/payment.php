<?php
	ob_start();
	$title='Conference Payment';
	$head="	
	";
	include_once("scripts/settings.php");
	include_once("includes/connect.php");
	$checkpoints = $_SESSION['conf_user'];
	//If user is already logged in, take him to his account
	if (!isset($_SESSION['conf_user']) || $_SESSION['conf_user'] == "")
		header('Location: index.php');
	
	$checkpoints .= " 1.\r\n";
	//Get registrations
	$email=$_SESSION['conf_user'];
	$_GET = sanitize($_GET);
	if (isset($_SESSION['type']) && $_SESSION['type'] == 'admin'){
		if (isset($_GET['id'])){
			$query="SELECT email FROM accounts WHERE id = {$_GET['id']}";
			$result=mysql_query($query);
			$rec = mysql_fetch_object($result);
			$email = $rec->email;
		}
	}
	$checkpoints .= " 2. ".$email."\r\n";
	$invoice_number=$grandtotal=0;
	$company=$company_address1=$company_address2=$company_city=$company_state=$company_contact_last=$company_contact_first=$company_email='';
	$description=$fee=$discount=$total=$people=$subtotal=array();
	$total_paid=0;
	$num_people_paid=0;
	$i=0;
	$update_invoice = false;
	$query="SELECT accounts.id as id, accounts.total_paid, accounts.invoice_date, fb_savedforms.notifyees, fb_savedforms.id as form_id, name, pagename, user_form.url, user_form.id as user_form_id FROM forms INNER JOIN accounts ON accounts.id=forms.user_id INNER JOIN forms_app.user_form ON user_form.id=forms.user_form_id INNER JOIN forms_app.fb_savedforms ON fb_savedforms.id=user_form.formid WHERE accounts.email='{$email}'";
	
	$result=mysql_query($query);
	while ($rec = mysql_fetch_object($result)){
		$free=false;
		
		if (is_null($rec->invoice_date)){
			$update_invoice = true;
			mysql_query("UPDATE accounts set invoice_date = '".date('Y-m-d', time())."' WHERE id = ".$rec->id);
			$date = date('m/d/Y', time());
		}
		else
			$date = date('m/d/Y', strtotime($rec->invoice_date));
		$checkpoints .= " 3. ".$date."\r\n";
		if (!$notifyees && strpos($rec->name, "Exhibitor Registration"));
			$notifyees = $rec->notifyees;
		$additional_people=0;
		$invoice_number=$rec->id;
		if (strpos($rec->name, 'Exhibitor Registration')){			
			$query="SELECT field_id, response from forms_app.form_answers WHERE user_form_id=".$rec->user_form_id;
			$result2=mysql_query($query);
			while ($rec2=mysql_fetch_object($result2)){
				if (!$company && $rec2->field_id === 'Company')
					$company = $rec2->response;
				else if (!$company_address1 && $rec2->field_id === 'Address_Line_1')
					$company_address1 = $rec2->response;
				else if (!$company_address2 && $rec2->field_id === 'Address_Line_2')
					$company_address2 = $rec2->response;
				else if (!$company_city && $rec2->field_id === 'City')
					$company_city = $rec2->response;
				else if (!$company_state && $rec2->field_id === 'State')
					$company_state = $rec2->response;
				else if (!$company_email && $rec2->field_id === 'Email_Address')
					$company_email = $rec2->response;
				else if (!$company_contact_last && $rec2->field_id === 'Last_Name')
					$company_contact_last = $rec2->response;
				else if (!$company_contact_first && $rec2->field_id === 'First_Name')
					$company_contact_first = $rec2->response;
				else if ($rec2->field_id==='Demo_Type'){
					$demo_type = explode("__", $rec2->response);
					$description[$i]='Registration for '.str_replace("_"." ",$demo_type[0]).' at '.EVENT_NAME;
					$fee[$i]=$subtotal[$i] = $demo_type[1];
				}
				else if ($rec2->field_id=='dc-Promotional_Code' && $rec2->response != ''){
					$query="SELECT amount FROM forms_app.discounts WHERE form_id=".$rec->form_id." AND code='".$rec2->response."'";
					$result3=mysql_query($query);
					$rec3=mysql_fetch_object($result3);
					$discount[$i]=$rec3->amount;
					if ($discount[$i]=="-999"){
						$discount[$i] = $fee[$i];
						$free=true;
					}
					else
						$subtotal[$i]-=$discount[$i];
				}
				else if (preg_match('/^Person_[0-9]+Last_Name/', $rec2->field_id) && $rec2->response != ''){
					$additional_people++;
				}
				else if (preg_match('/^Person_[0-9]+_Paid/', $rec2->field_id) && $rec2->response == "Paid"){
					$num_people_paid++;
					if ($num_people_paid == NUM_DEFAULT_TABLE_ATTENDEES)
						$total_paid = $fee[$i];
					else	
						$total_paid += COST_PER_ATTENDEE;
				}				
			}
			
			$total[$i]=$fee[$i]-$discount[$i];
			$people[$i] = $additional_people-NUM_DEFAULT_TABLE_ATTENDEES;
			if ($people[$i]<1)
				$people[$i]=0;
			$subtotal[$i]+=($people[$i]*COST_PER_ATTENDEE);
			if ($free)
				$subtotal[$i]=0;
			$grandtotal+=$subtotal[$i];
			$i++;
			if ($total_paid > $grandtotal)
				$total_paid = $grandtotal;
		}
	}
	if ($update_invoice)
		mysql_query("UPDATE accounts set invoice_total={$grandtotal}, total_paid={$total_paid} WHERE id={$invoice_number}");
	$checkpoints .= " 4. "."UPDATE accounts set invoice_total={$grandtotal}, total_paid={$total_paid} WHERE id={$invoice_number}"."\r\n";
	mysql_close();
	?>
<body>
	<?php include('includes/nav-header.php'); ?>
	<style>
		.invoice_container {
    background: none repeat scroll 0 0 #FFFFFF;
    margin-left: auto;
    margin-right: auto;
    padding: 10px;
    width: 1200px;
}
#logo {
    float: left;
}
#billing_info {
    float: right;
    text-align: right;
}
#invoice_header {
    overflow: hidden;
}
#invoice_addresses {
    clear: both;
}
#invoice table {
    width: 100%;
}
#invoice table th, #invoice table td {
    border: thin solid;
    padding-left: 5px;
    padding-right: 5px;
}
#invoice table th {
    font-size: 1.2em;
    text-align: center;
}
.invoice_description {
    width: 40%;
	text-align:left;
}
.invoice_fee {
    text-align: center;
}
.invoice_total {
    text-align: right;
}
.invoice_expense {
    border: medium none !important;
}
.invoice_grand{
	font-size:1.2em; 
	text-align:right; 
	font-weight:bold;
}
#payment_options {
    margin-top: 50px;
}
.largespace {
    margin-top: 40px;
	clear:both;
}
h5{
	font-weight:bold;
}
.column {
    float: left;
    margin-left: 28px;
    width: 47%;
}
#invoice_credit_card {
    clear: both;
    margin-top: 188px;
}
#invoice_footer {
    font-style: italic;
    margin-left: auto;
    margin-right: auto;
    margin-top: 46px;
    text-align: center;
    width: 80%;
}
#print_invoice {
    background: none repeat scroll 0 0 #ED0E0E;
    border: medium none;
    color: #FFFFFF;
    font-size: 23px;
    padding: 10px;
    text-transform: uppercase;
}
@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
}
#cc_payment table {
    font-size: 20px;
    margin-bottom: 30px;
    margin-top: 40px;
    width: 100%;
}
#cc_payment td {
    border: thin solid #666666;
    padding: 10px;
    width: 50%;
}
#amount_total {
    background: none repeat scroll 0 0 #F23232;
}
#amount_to_pay {
    background: none repeat scroll 0 0 #699BBE;
}
.cc_total > input {
    text-align: right;
}
#main{
	width:960px;
}
	</style>
	<div id="main" class="invoice_container" style="background>
		<div class="content">
			<div class="Payment">
				<div id='invoice'>
					<div id='invoice_header'>
						<div id='logo'><img src='https://www.mycompany.com/forms/conferences/images/receipt_logo.png'/></div>
						<div id='billing_info'>
							<h2>INVOICE</h2>
							<h4>Number: <?php echo INVOICE_PREFIX.$invoice_number;?></h4>
							<h4>Date: <?php echo $date; ?></h4>
						</div>
					</div>
					<div id='invoice_addresses'>
						<div id='invoice_mycompany_address'>
							858 Coal Creek Cir<br/>
							Louisville CO 80027<br/>
							303-661-9199 Fax<br/>
							303-661-3331 Phone<br/>
							<a href='mailto:j.smith@mycompany.com'>j.smith@mycompany.com</a> Email<br/>
							04-3012897 Federal Tax ID<br/>
							ATTN: John Smith
						</div>
						<div id='invoice_billto_address'>
							<h3>BILL TO</h3>
							<?php echo $company; ?><br/>
							<?php echo $company_address1; ?><br/>
							<?php if ($company_address2 != ''): echo $company_address2."<br/>"; endif; ?>
							<?php echo $company_city; ?>, <?php echo $company_state; ?><br/>
							Attn: <?php echo $company_contact_first." ".$company_contact_last; ?><br/>
							<a href='mailto:<?php echo $company_email; ?>'><?php echo $company_email; ?></a>
						</div>
					</div>
					<div id='invoice_expenses'>
						<h3>FEES</h3>
						<table>
							<tr>
								<th>Description</th>
								<th>Fee</th>
								<th>Discount</th>
								<th>Total</th>
							</tr>
							<?php for ($i=0; $i<count($description); $i++): ?>
								<tr>
									<td class='invoice_expense invoice_description'><?php echo $description[$i]; ?></td>
									<td class='invoice_expense invoice_fee'>$<?php echo number_format($fee[$i],2); ?></td>
									<td class='invoice_expense invoice_fee'>$<?php echo number_format($discount[$i],2); ?></td>
									<td class='invoice_expense invoice_total'>$<?php echo number_format($total[$i],2); ?></td>
								</tr>
								<?php if ($people[$i] > 0): ?>
									<tr>
										<td class='invoice_expense invoice_description'>Registration fee for <?php echo convert_number_to_words($people[$i]); ?>(<?php echo $people[$i]; ?>) additional staff</td>
										<td class='invoice_expense invoice_fee'>$<?php echo number_format($people[$i] * 750, 2); ?></td>
										<td class='invoice_expense invoice_fee'><?php if ($free) echo "$".number_format($people[$i] * 750, 2); ?></td>
										<td class='invoice_expense invoice_total'>$<?php if ($free) echo "0.00"; else echo number_format($people[$i] * 750, 2); ?>
									</tr>
								<?php endif; ?>
								<tr>
									<td class='invoice_grand'>Total</td>
									<td></td>
									<td></td>
									<td class='invoice_total'>$<?php echo number_format($subtotal[$i],2);?></td>
								</tr>
							<?php endfor;?>
							<tr>
								<td class='invoice_expense'></td>
								<td colspan='2' class='invoice_grand'>Invoice Total</td>
								<td class='invoice_total'>$<?php echo number_format($grandtotal, 2); ?></td>
							</tr>
							<tr>
								<td class='invoice_expense'></td>
								<td colspan='2' class='invoice_grand'>Total Paid</td>
								<td class='invoice_total'>$<?php echo number_format($total_paid, 2); ?></td>
							</tr>
							<tr>
								<td class='invoice_expense'></td>
								<td colspan='2' class='invoice_grand'>Balance Due</td>
								<td class='invoice_total'>$<?php echo number_format($grandtotal-$total_paid, 2); ?></td>
							</tr>
						</table>
					</div>
					<div id='payment_options'>
						<h3>PAYMENT OPTIONS</h3>
						<h4>Checks:</h4>
						<p><i>Please make checks payable to: </i>mycompany<br/>
						Federal Tax ID#: 04-3012897
						<p class='largespace'></p>
						<h4>Funds Transfer: <span style='color:red;'><i>Payer is responsible for any wire transfer handling fees</i></span></h4>
						<div id='wire_transfer'>
							<div class='column'>
								<h5>Wire Transfer</h5>
								JP Morgan Chase Bank, N.A.; New York, NY USA<br/>
								Account Number: 069436218<br/>
								SWIFT: CHASUS33<br/>
								Routing Number: 021000021<br/>
								Account Name: Cable Television Laboratories, Inc.
							</div>
							<div class='column'>
								<h5>ACH Payment Information</h5>
								Bank One N.A.; Denver, CO USA<br/>
								Account Number: 069436218<br/>
								Routing Number: 102001017<br/>
								Account Name: Cable Television Laboratories, Inc.
							</div>
						</div>
						<div id='invoice_credit_card'>
							<h4>Credit Card (Visa, MasterCard, Discover and American Express):</h4>
							Please access this link to make credit card payment for your demo table via a secure payment site.
							<a href='<?php echo $website."cc_payment.php?invoice_id=".$invoice_number; ?>'><?php echo $website."cc_payment.php"; ?></a>
						</div>
					</div>
					<div id='invoice_footer'>
						Payment due by <?php echo PAYMENT_DUE_DATE; ?><br/>
						Please contact John Smith (information above) to make alternative arrangements.
					</div>
					<div id='invoice_print' class='no-print'>
						<input type='button' value='Print Invoice' id='print_invoice' onclick='window.print()'/>
					</div>
				</div>					
			</div>			
		</div>
	</div>
</body>
</html>
<?php
	$page = ob_get_contents();
	ob_end_flush();
	$fp = fopen('files/invoice_'.$invoice_number.".html","w");
	fwrite($fp,$page);
	fclose($fp);
	$checkpoints .= " 5. ".print_r($_GET, true)."\r\n";
	if ($_GET['action']=='generate_invoice'){
		$notifyees=explode("\n", $notifyees);
				include("../lib/phpmailer.php");
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8';
		foreach ($notifyees as $nemail){
			$mail->AddAddress($nemail);
		}
		
		$mail->From         = "no-reply@mycompany.com";
		$mail->FromName     = "no-reply";
		$mail->Subject      =  "Invoice for ".$email;		
		$mail->Body         = "Invoice attached";
		$mail->isHTML(true);    
		$mail->AddAttachment('files/invoice_'.$invoice_number.".html");
		
		$mail->send();
		
		if ($_SESSION['type'] != 'admin'){
			$mail->ClearAllRecipients( ); 
			$mail->AddAddress($company_email);
			$mail->send();
				$checkpoints .= " 6. ".$company_email."\r\n";
		}
	
	}
	mail("jason.kirby@mycompany.com","Vendor Conf Invoice Test", $checkpoints);
	if (isset($_GET['action']) && $_GET['action'] == 'generate_invoice')
		echo "<script>window.location='index.php';</script>";
?>
