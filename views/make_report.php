<?php include ('includes/reports_header.php'); ?>
<form id='make_report_form' action='scripts/make_report.php' method='post'>
	<div class='notification'><?php echo $notification; ?></div>	
	<div id='reports_container' class='items_container'>
		<label>Report Name *</label>
		<input type='text' name='name' id='name' class='required' value='<?php echo $report_name; ?>' />
		
		<table width='100%'>
			<tr>
				<td width='40%'>
					<h3>Form Fields</h3>
					<div id='new_form_fields' class='items_container reports_container'>
						<?php $id_counter=0; ?>
						<?php foreach ($forms_array as $form_name => $form_fields): //Display each form ?> 
							<?php
								$js_form_name=remove_non_alphanumeric($form_name);
								
								if (is_numeric(substr($js_form_name, 0, 1))){
									$js_form_name="R__".$js_form_name;
								}
								
							?>
							<div id='<?php echo $form_ids[$form_name]; ?>' class='form_item'><img src='images/expand.png' /> <?php echo $form_name; ?></div>
							<div class='form_fields'>
								<div id="all__<?php echo $forms_ids[$form_name]; ?>" class='move_all_fields'>Move all fields</div>
								<?php foreach ($form_fields as $form_field): ?>
									<div id='<?php echo $js_form_name.":".remove_non_alphanumeric($form_field); ?>'  class='form_field'><?php echo $form_field; ?></div>
								<?php endforeach; ?>
							</div>
						<?php endforeach; ?>
					</div>
					<div class='bottom'>
						<label>Custom Field</label>
						<input type='text' name='custom_field' id='custom_field' />
					</div>
					<label>Plugins</label>
					<select name="plugins" id="plugins">
						<option value=""></option>
						<option <?php if ($report_plugins == "archive") echo "selected='selected'"; ?> value="archive">Archive</option>
					</select>
				</td>
				<td width='20%'>
					<div id='reports_move_controls'>
						<button type='button' id='move_field_left'><img src='images/moveright.png'/></button>
						<button type='button' id='move_field_right'><img src='images/moveleft.png'/></button>
					</div>
				</td>
				<td width='40%'>
					<h3>Report Fields</h3>
					<div id='existing_form_fields' class='items_container reports_container'>
						<?php foreach ($fields as $field): ?>
							<div <?php if ($field['form_name']) echo 'id="'.$field['id'].'"'; ?> class="form_field" rel="<?php if ($field['form_name']) echo $field['form_id']; ?>">
								<div class="field_info"><?php if ($field['form_name']) echo $field['form_name'].":"; ?><?php echo $field['field_name']; ?> AS:</div>
								<input <?php if ($field['form_name']) echo "id = 'as_".$field['id']."'"; else echo 'class="custom"'; ?>" type="text" value="<?php echo $field['field_as']; ?>" />
								<div class="moveupdown">
									<span class="moveup left">
										<img src="images/moveup.png" />
									</span>
									<span class="movedown right">
										<img src="images/movedown.png" />
									</span>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</td>
			</tr>
		</table>
		<div id='reports_additional_options'>
			<h3>Advanced Options</h3>
			<label>Match <img src='images/help.png' title='If you are using multiple forms in a report, match uses two comparison fields to join the two forms together into one row if the value is the same for both fields. For example: you may have two forms with an email field. Any forms that share this email address will be joined into one column. Example:Customer:email=Auther.email. Things can get a little complex when you want to merge fields from more than two reports. IN ORDER TO CREATE MULTIPLE MATCH CONDITIONS, comma separate each condition and order the match by REVERSE priority.'/></label>
			<input type='text' name='match' id='match' placeholder='optional' value="<?php echo $report_match; ?>" style='width:85%'/>
			<!--<label>Where <img src='images/help.png' title='Adds a condition to report. Uses SQL like syntax. Example: age>3.'/></label>
			<input type='text' name='where' id='where' placeholder='optional' value="<?php echo $report_where; ?>" style='width:85%'/>
			
			<label>Group By <img src='images/help.png' title='SQL like syntax to group a query by a certain field'/></label>
			<input type='text' name='groupby' id='groupby' placeholder='optional' value="<?php echo $report_groupby; ?>" />
			
			<label>Order By <img src='images/help.png' title='SQL like syntax to order the report by a certain field.'/></label>
			<input type='text' name='orderby' id='orderby' placeholder='optional' value="<?php echo $report_orderby; ?>" />
			
			<label>Limit <img src='images/help.png' title='Limits the number of results returned'/></label>
			<input type='text' name='limit' id='limit' placeholder='optional' value="<?php echo $report_limit; ?>" />
			-->
		</div>
		<input type='hidden' name='new_form_field' value="" id='new_form_field' />
		<input type='hidden' name='new_form_id' value="" id='new_form_id' />
		<input type='hidden' name='existing_form_field' value="" id='existing_form_field' />
		<input type='hidden' name='existing_form_id' value="" id='existing_form_id' />
		<input type='hidden' name='report_id' value="<?php if (isset($_GET['id']))echo $_GET['id']; ?>" id='report_id' />
		<input type='submit' value='Save Report' />
	</div>
</form>
</div>