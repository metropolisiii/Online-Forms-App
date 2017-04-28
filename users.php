<?php
	/**
	* Area for superadmins to create admins.
	* @author Jason Kirby <jkirby1325@gmail.com>
	*/
	
	include_once("scripts/settings.php");
	if (!isset($_SESSION['superadmin'])){
		header("Location: login.php");
		exit;
	}
	
	include("includes/header.php");
	$column=array();
	
	// Get Accounts and users in each account
	$result=mysql_query("SELECT * FROM accounts order by name");
	while ($account=mysql_fetch_array($result)){
		$column[$account['name']]['url']=$account['url'];
		$column[$account['name']]['requires_login']=$account['requires_login'];
		$column[$account['name']]['id']=$account['id'];
		$column[$account['name']]['groups']=$account['groups'];
		$result2=mysql_query("SELECT id, user FROM admins WHERE accountId=".$account['id']." order by user");
		while ($admin=mysql_fetch_array($result2)){
			$column[$account['name']][$admin['id']]=$admin['user'];
		}
	}	
?>

<!----------------------------------------------------------------------Presentation------------------------------------------------------------------------------------->
<form method="post" id='operationform' action="scripts/removeuser.php">
	<table id='usertable' width='100%'>
		<tr>
			<th width='10%'></th>
			<th>Account/User</th>
			<th>Url of Account</th>
			<th>Groups</th>
			<th>Requires Login</th>
		</tr>
		<?php foreach($column as $key=>$value): ?>
			<tr class='accountrow'>
				<td><input type='checkbox' class='noclickevent' name='record[<?php echo $column[$key]['id'];?>]'/></td>
				<td><input type='text' name='account[<?php echo $column[$key]['id'];?>]' value='<?php echo $key; ?>'/></td>
				<td><input type='text' name='url[<?php echo $column[$key]['id'];?>]' value='<?php echo $column[$key]['url']; ?>'/></td>
				<td><input type='text' name='groups[<?php echo $column[$key]['id'];?>]' value='<?php echo $column[$key]['groups']; ?>'/></td>
				<td><input type='checkbox' name='requires_login[<?php echo $column[$key]['id'];?>]' <?php if ($column[$key]['requires_login']) echo "checked='checked'"; ?>/></td>
			</tr>
			<?php foreach ($value as $k=>$user): ?>
			<?php if (!empty($user) && is_numeric($k)): ?>
				<tr class='userrow'>
					<td><input type='checkbox' class='noclickevent' name='user[<?php echo $k; ?>]'/></td>
					<td><?php echo $user; ?></td>
					<td></td>
					<td></td>
				</tr>
			<?php endif; ?>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</table>
	<i>With selected: </i>
	<select id='useroperation' name='operation'>
		<option value='edit'>Change</option>
		<option value='delete'>Delete</option>
	</select>
	<input type='submit' id='removeusersubmit' value='Go' /></p>
</form>
<div class='inputcontainer' id='accountadd'>
	<h3>Accounts</h3>
	<form method='post' action='scripts/addaccount.php'>
		<label>Account name</label>
		<input type='text' name='account' id='accountfield' class='searchtext'/> 
		
		<label>Url of account</label>		
		<input type='text' name='url' id='accounturl' class='searchtext'/> 
		
		<label>Is login required to fill out a form?</label>		
		<input type='radio' id='requiresloginy' name='requireslogin' value='y' /> Yes
		<input type='radio' id='requiresloginn' name='requireslogin' value='n' /> No
		
		<div id='logingroups' class='hidden'>
		<label>Limit login to these groups: (optional, separate groups with comma)</label>
		<input type='text' name='groups' id='groups' class='searchtext'/> 
		</div>
		
		<input type='submit' value='Add Account' />
	</form>
</div>
<div class='inputcontainer'>
	<h3>Admins</h3>
	<form method='post' action='scripts/adduser.php'>
		<label>Admin name</label>
		<input type='text' name='user' id='userfield' class='searchtext'/> 
		
		<label>Admin account</label>
		<select name='adminaccount'>
			<?php foreach ($column as $key=>$value): ?>
				<option value='<?php echo $column[$key]['id']; ?>'><?php echo $key; ?></option>
			<?php endforeach; ?>
		</select>
		<input type='submit' value='Add Admin'/>
	</form>
</div>