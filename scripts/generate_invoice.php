<?php
	$invoice_number=$grandtotal=0;
	$date=date('n/d/Y', time());
	$company=$company_address1=$company_address2=$company_city=$company_state=$company_contact_last=$company_contact_first=$company_email='';
	$description=$fee=$discount=$total=$people=$subtotal=array();
	
	$html = <<<EOD
<body>	
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

	</style>
	<div id="main" class="invoice_container" style="background>
		<div class="content">
			<div class="Payment">
				<div id='invoice'>
					<div id='invoice_header'>
						<div id='logo'><img src='https://www.mycompany.com/forms/images/receipt_logo.png'/></div>
						<div id='billing_info'>
							<h2>INVOICE</h2>
							<h4>Number: <?php echo $billcode; ?>-<?php echo $invoice_number;?></h4>
							<h4>Date: <?php echo $date; ?></h4>
						</div>
					</div>
					<div id='invoice_addresses'>
						<div id='invoice_mycompany_address'>
							858 Coal Creek Cir<br/>
							Louisville CO 80027<br/>
							303-661-9199 Fax<br/>
							303-661-3331 Phone<br/>
							<a href='mailto:b.lusz@mycompany.com'>b.lusz@mycompany.com</a> Email<br/>
							04-3012897 Federal Tax ID<br/>
							ATTN: Ben Lusz
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
										<td class='invoice_expense invoice_fee'></td>
										<td class='invoice_expense invoice_total'>$<?php echo number_format($people[$i] * 750, 2); ?>
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
						Payment due by March 1, 2015<br/>
						Please contact Ben Lusz (information above) to make alternative arrangements.
					</div>
					<div id='invoice_print' class='no-print'>
						<input type='button' value='Print Invoice' id='print_invoice' onclick='window.print()'/>
					</div>
				</div>					
			</div>			
		</div>
	</div>
</body>
EOD
