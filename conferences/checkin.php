<?php
	$title = "Conference Check-in";
	$toc = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","1","2","3","4","5","6","7","8","9","0");
	$head ="
		<script type='text/javascript' src='//cdn.datatables.net/plug-ins/725b2a2115b/api/fnAddTr.js'></script>
		<script type='text/javascript' src='js/checkin_scripts.js'></script>		
		
	";
	include_once("scripts/settings.php");
	include_once("includes/connect.php");
	define("NUM_COLS", 11);
	if (!isset($_SESSION['conf_user']))
		header('Location: index.php');
	if ($_SESSION['type'] != 'admin'){
		exit;
	}
		
	/* Initialize arrays to build table */
	$companies = array();
	$urls = array();
	$people = array();
	$customer_information = array();
	$accounts = array();
	$personnel_count = array();
	$checked_in_query = mysql_query("SELECT * FROM daily_stats WHERE stat = 'checked_in' AND quantity=1");
	$total_checked_in = mysql_num_rows($checked_in_query);
	$paid_query = mysql_query("SELECT customer_information.id from customer_information INNER JOIN forms_app.form_answers ON customer_information.form_answer_id = form_answers.id WHERE paid=1 AND field_id LIKE 'Person_%First_Name' AND response != ''");
	$total_payments = mysql_num_rows($paid_query);
	$registration_query =  "SELECT user_form.id as id, customer_information.id as customer_information_id, accounts.id as account_id, field_id, response, url, checkedin, paid, table_paid, square_info, cc_type, comments FROM accounts INNER JOIN forms ON accounts.id = forms.user_id INNER JOIN forms_app.user_form ON forms.user_form_id = user_form.id INNER JOIN forms_app.form_answers ON user_form.id = form_answers.user_form_id LEFT JOIN customer_information ON form_answers.id = customer_information.form_answer_id WHERE formid=".REGISTRATION_ID." AND (field_id LIKE 'Person_%_First_Name' OR field_id LIKE 'Person_%_Last_Name' OR field_id = 'Company' OR field_id='Demo_Type') ORDER BY account_id;";
	$results = mysql_query($registration_query);
	
	/* Go through each registration and construct a table */
	$account_id=0;
	while ($rec = mysql_fetch_object($results)){
		if (!$people[$rec->id])
			$people[$rec->id] = array();
		if (!$customer_information[$rec->id])
			$customer_information[$rec->id] = array("table_payment"=>0);
		if (!$personnel_count[$rec->id])
			$personnel_count[$rec->id] = 0;
		if ($rec->field_id=="Company"){
			$companies[$rec->id]=$rec->response;
			$urls[$rec->id] = $rec->url;
			$accounts[$rec->id]['id'] = $rec->account_id;
		}
		else{
			preg_match('!\d+!', $rec->field_id, $matches);
			if (strpos($rec->field_id, "First_Name") !== false){
				$people[$rec->id][$matches[0]] = $rec->response;
				$customer_information[$rec->id][$matches[0]] = array();
				$customer_information[$rec->id][$matches[0]]['paid'] = $rec->paid;
				$customer_information[$rec->id][$matches[0]]['table_paid'] = $rec->table_paid;
				$customer_information[$rec->id][$matches[0]]['checkedin'] = $rec->checkedin;
				$customer_information[$rec->id][$matches[0]]['comments'] = $rec->comments;				
				$customer_information[$rec->id][$matches[0]]['square_info'] = $rec->square_info;
				$customer_information[$rec->id][$matches[0]]['cc_type'] = $rec->cc_type;
				if ($rec->customer_information_id){
					$customer_information[$rec->id][$matches[0]]['customer_information_id'] = $rec->customer_information_id;
					$results2 = mysql_query("SELECT quantity FROM daily_stats WHERE stat = 'paid' AND customer_information_id = ".$rec->customer_information_id);
					$customer_paid = mysql_fetch_array($results2);
					$customer_information[$rec->id][$matches[0]]['amount_paid'] = $customer_paid['quantity'];			
				}
				else
					$customer_information[$rec->id][$matches[0]]['amount_paid'] = 0;
			}		
			else if (strpos($rec->field_id, "Last_Name") !== false){
				$people[$rec->id][$matches[0]] = $people[$rec->id][$matches[0]]." ".$rec->response;
				if ($rec->response != "")
					$personnel_count[$rec->id]++;
			}
			else if ($rec->field_id == "Demo_Type"){
				preg_match('!\d+!', $rec->response, $response);
				$customer_information[$rec->id]['table_payment']+=$response[0];			
			}
		}
	}	
	
	$result = mysql_query("SELECT sum(quantity) as quantity FROM daily_stats WHERE stat = 'paid'");
	
	$stats = mysql_fetch_array($result);
	$seven_fifty = $stats['quantity'] * COST_PER_ATTENDEE;
	$result = mysql_query("SELECT sum(quantity) as quantity FROM daily_stats WHERE stat = 'checked_in'");
		
	$stats = mysql_fetch_array($result);
	$checked_in = $stats['quantity'];
	if (!is_numeric($checked_in))
		$checked_in=0;
	mysql_close();
?>
<body>
	<?php include('includes/nav-header.php'); ?>
	<div id="main" class='main_container extended_container'>
		<div class="content">
			<div class="row">
				<?php if ($_SESSION['error']): ?>
					<p class='error'><?php echo $_SESSION['error']; ?></p>
					<?php unset($_SESSION['error']); ?>
				<?php endif; ?>
				<div>
					<a href='index.php'>Back to conference administration</a>
				</div>				
				<div id='stats'>
					<span class='stat'>Total # Checkin: <?php echo $total_checked_in; ?></span>
					<span class='stat'>Total # Paid: <?php echo $total_payments; ?></span>
				</div>
				<table width='100%' id="checkin_table" class="table table-striped table-bordered">
					<thead>
						<tr>
							<th colspan='<?php echo NUM_COLS; ?>'>
								<div id='refresh'>
									<input class='btn btn-success' type='button' value='Refresh' onclick='location.reload()' /> 
									<input class='btn btn-success' type='button' value='Back to Top' onclick='window.scrollTo(0,0)' />
								</div>
							</th>
						</tr>
						<tr>
							<th colspan='<?php echo NUM_COLS; ?>'>
								<div class="stat_column">
									<div class="stat_subcolumn">
										<span>
											Total Amount in $<?php echo COST_PER_ATTENDEE; ?> Payments: 
										</span>
										<span id="seven_fifty">$<?php echo $seven_fifty; ?></span>
									</div>
									<div class="stat_subcolumn">
										<span>
											Total checked per day:
										</span>
										<span id="checked_in"><?php echo $checked_in; ?></span>
									</div>
								</div>					
							</th>
						</tr>
						<tr>
							<th colspan = "<?php echo NUM_COLS; ?>">
								<div id="TOC">
									<?php for ($i=0; $i<count($toc); $i++): ?>
										<a href='#startswith_<?php echo $toc[$i];?>'><?php echo $toc[$i];?></a>
									<?php endfor; ?>
								</div>
							</th>
						</tr>
						<tr>
							<th>User</th>
							<th>Company</th>
							<th>Registration Link</th>
							<th>Checked In</th>
							<th>Paid</th>
							<th>Table Paid</th>
							<th>Amount</th>
							<th>Comments</th>
							<th></th>
							<th>Auth.net ID</th>
							<th>CC Type</th>							
						</tr>
					</thead>
					<tbody>
						<?php 
							$i = "";
							foreach ($people as $form=>$person){
								foreach ($person as $index=>$p){
									$id="";
									if (trim($p) != ""){
										if ($i != substr($companies[$form], 0, 1)){
											$id="id = 'startswith_".substr($companies[$form], 0, 1)."'";
											$i=substr($companies[$form], 0, 1);
										}
										echo "<tr {$id} userform='".$form."' person='".$index."'>";
										echo "<td class='name'>{$p}</td>";
										echo "<td class='company'>{$companies[$form]}</td>";
										echo "<td class='registration'><a href='".VENDOR_REG_URL."?q={$urls[$form]}#p{$index}' target='blank'>Registration</a></td>";
										echo "<td class='checkedin'><input type='checkbox' action='checkedin' ".($customer_information[$form][$index]['checkedin']?'checked':'')."/></td>";
										echo "<td class='paid'><input type='checkbox' action='paid' ".($customer_information[$form][$index]['paid']?'checked':'')."/></td>";
										echo "<td class='table_paid'><input type='checkbox' action='table_paid' ".($customer_information[$form][$index]['table_paid']?'checked':'')."/></td>";
										echo "<td>${COST_PER_ATTENDEE} x <input type='text' size='3' class='comments amount_quantity nochange amount_paid_{$customer_information[$form][$index]['customer_information_id']}' value='".$customer_information[$form][$index]['amount_paid']."'/><input type='button' class='update_comments' value='Update'/><br/><span class='amount_paid'>$".($customer_information[$form][$index]['amount_paid'] * COST_PER_ATTENDEE)."</span></td>";
										echo "<td class='comments'><textarea class='comments nochange'>".$customer_information[$form][$index]['comments']."</textarea> <input type='button' class='update_comments' value='Update'/></td>";
										$additional_costs = ($personnel_count[$form]-NUM_DEFAULT_TABLE_ATTENDEES)*COST_PER_ATTENDEE;
										if ($additional_costs < 0)
											$additional_costs = 0;
										echo "<td><a class='btn btn-success' target='_blank' href='payment.php?id=".$accounts[$form]['id']."'>Invoice </a><br/>Inv #: ".INVOICE_PREFIX.$accounts[$form]['id']."<br/>Tab Pmt: $".$customer_information[$form]['table_payment']."<br/>Adnl Ppl: $".$additional_costs."</td>";
										echo "<td class='square_info'><input type='text' class='comments square_info nochange' value='".$customer_information[$form][$index]['square_info']."' /><input type='button' class='update_comments' value='Update'/></td>";
										echo "<td class='cc_type'><select class='nochange comments cc_type'>";
										echo "<option value=''></option>";
										echo "<option value='VISA' ".($customer_information[$form][$index]['cc_type']=="VISA"?"selected='selected'":"").">VISA</option>";
										echo "<option value='MC' ".($customer_information[$form][$index]['cc_type']=="MC"?"selected='selected'":"").">Master Card</option>";
										echo "<option value='DISC' ".($customer_information[$form][$index]['cc_type']=="DISC"?"selected='selected'":"").">Discover</option>";
										echo "<option value='AMEX' ".($customer_information[$form][$index]['cc_type']=="AMEX"?"selected='selected'":"").">AMEX</option>";										
										echo "</select>";
										echo "<input type='button' class='update_comments' value='Update'/></td>";
										echo "</tr>";
									}
								}
							}
						?>
					</tbody>
				</table>	
				<div>
					<a href='index.php'>Back to conference administration</a>
				</div>				
			</div>
		</div>
	</div>
</body>
</html>