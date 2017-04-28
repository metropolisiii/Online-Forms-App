<div id='container' class='bordered' style='width: 90%;'>
	<div class='container_heading'><h2>Choose a user's form to review.</h2></div>
	<?php if (!$user_login_required): //If a login isn't required, we show a select menu of fields the admin can view in the list of forms  ?>
		<div id='show_fields'>
			<form id='show_fields_form' method='get' action='review.php'>
				<input type='hidden' name='id' value='<?php echo $_GET['id']; ?>' />
				Show: 
				<select name='show_field' id='show_field'>
					<option value=''></option>
					<?php while ($field=mysql_fetch_array($fieldlist_result)): //Show the fields of the form ?>
						<option value='<?php echo $field['field_id']; ?>'><?php echo substr($field['field_id'], 0, 20); ?></option>
					<?php endwhile; ?>
				</select>
			</form>
		</div>
	<?php endif; ?>
	<table id='review_table' cellspacing='10'>
		<?php while ($rec=mysql_fetch_array($formlist_result)): //Loop through the forms and display them in a table ?>
			<?php $formid=$rec['id']; $userid=$rec['userid']; $show_field=$_GET['show_field']; $url=$rec['url']; ?>
			<?php if ($user_login_required) $userinfo=ldap_user_info($userid); //Get the username and name of the user we're currently looping ?>
			<tr>
				<td width='45%'>
					<form action='forms/<?php echo $rec['pagename']; ?>?q=<?php echo $url;?>' method='POST' target='_blank'>
						<input class='review' type='submit' value='<?php if (!$user_login_required && !$show_field) echo $rec['response']; else if (!$user_login_required && $show_field) echo $rec['response'];  else echo $userinfo[0]['givenname'][0]." ".$userinfo[0]['sn'][0]." ".$userinfo[0]['company'][0]; ?>'/>
						<?php if ($rec['submitted'] !=1): ?>(Incomplete)<?php endif; ?>
						<?php if (!empty($rec['date_updated'])): ?><a target="_blank" href="change_log.php?id=<?php echo $formid; ?>">View Changes</a><?php endif; ?>
						<input type='hidden' name='userid' value='<?php echo $userid; ?>'/>
					</form>
				</td>
				<td>
				<?php $files_result=mysql_query("SELECT id, name FROM files where user_form_id={$formid} ORDER BY name"); //If there are files associated with the forms, display a files link that pops up a clickalble list of files the admin can download ?>
				<?php if (mysql_num_rows($files_result) > 0): ?>
					<div id="<?php echo $formid; ?>" class="files">Files</div>
					<div id="files_<?php echo $formid; ?>" class="files_popup">
						(Right-click -> Save as To download)<br/>
						<?php while($file=mysql_fetch_array($files_result)): ?><a href='files/<?php echo "{$formid}_{$file['name']}";?>'><?php echo $file['name']; ?></a><br/><?php endwhile;?>
					</div>
				<?php endif; ?>
				</td>
				<td width='45%'>
					<button value='userid:<?php echo $userid; if ($url) echo ", url:{$url}"; ?>,status:accept' class='accept'>Accept</button> 
					<button value='userid:<?php echo $userid; if ($url) echo ", url:{$url}"; ?>,status:reject' class='reject'>Incomplete</button>
					<button value='userid:<?php echo $userid; if ($url) echo ", url:{$url}"; ?>,status:reset' class='reset'>Need more info</button>
				</td>
				<td>
					<span id='<?php echo "{$userid}_";  if ($url) echo $url; ?>_accept_status'>
						<?php if ($rec['accepted']==1):?>
							<img src="images/accept.png">
						<?php elseif ($rec['accepted']==0 && !is_null($rec['accepted'])):?>
							<img src="images/reject.png">
						<?php endif; ?>
					</span>
				</td>
				<td width='35%' align='right'>
					<button value='userid:<?php echo $userid; if ($url) echo ", url:".$url; ?>,status:delete' class='delete_answers'>Delete</button>
				</td>
			</tr>
		<?php endwhile; ?>
	</table>
</div>
<br/>
<br/>
<a href='admin.php'>Back to forms</a>
<?php 
	mysql_free_result($field_list_result);
	mysql_free_result($formlist_result);