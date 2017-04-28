<?php
/**
* Login script. Connects to Active Directory via LDAP and determines if the user is worthy of being authenticated by checking the bind and checking if the user is in a group that can access the app.
* @author Jason Kirby <jkirby1325@gmail.com>
*/
	include("settings.php");
	require_once('connect.php');
	if (empty($_SESSION['redirectpage']))
		$_SESSION['redirectpage']='/admin.php';
	include("../misc/functions.php");
	include_once("/var/www/forms_app/classes/logController.php");
	
	$result=mysql_query("SELECT requires_login FROM accounts WHERE url LIKE '".$forwarded_directory."'");
	
	$ulr=mysql_fetch_array($result);
	if ($ulr['requires_login']==1)
		$user_login_required=true;
		
	$log=new logController();
	
	$_POST=sanitize($_POST);
	$user= filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
	$pass= filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
	
	if (empty($user) || empty($pass)){ //No username and password were entered. Buh bye.
		$_SESSION['error']="Please populate all fields";
		header("Location: ../login.php");
		exit;
	}
	else{
		$adServer = "ldap.mycompany.com";
		$ldapconn = ldap_connect($adServer);
		$ldaprdn = "CTLINT\\".$user;
		$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $pass); 
		if (!$ldapbind){ //Bad credentials. Buh bye
			$log->log($user." tried to login and failed.");
			$_SESSION['error']="Login is invalid.";
			header("Location: ../login.php");
			exit;
		}
		$dn="OU=community,DC=mycompany,DC=com";
		$filter="sAMAccountName=".$user;

		if ($ldapbind) { //Yay! You made it!
			$sr=ldap_search($ldapconn, $dn, $filter);
			$info = ldap_get_entries($ldapconn, $sr);
			$admins='';
			$groups='';
			$result=mysql_query("SELECT user, groups from admins INNER JOIN accounts on accountId=accounts.id WHERE url LIKE '".$forwarded_directory."'");
			while ($userad=mysql_fetch_array($result)){
				$admins[]=$userad['user'];
				if (!empty($userad['groups'])){
					$g=explode(",", $userad['groups']);
					foreach ($g as $group)
						$groups[]=$group;
				}
			}
				
			if (in_array($info[0]["samaccountname"][0],$superusers)){
				$_SESSION['userid'] = $user;
				$_SESSION['membertype']="admin";
				$_SESSION['superadmin']="true";
				if (!empty($_POST['account'])) //If we're logging into an account, the admin can only work in the subset of the forms that belong to that account, so we set a session to denote what account that is. 
					$_SESSION['account']=$_POST['account'];
				$log->log($user." logged in as super-admin.");
				$_SESSION['timeout'] = time();
				header("Location: ../admin.php");
				exit;
			}
			if (in_array($info[0]["samaccountname"][0],$admins)){ //If you're an admin, set the session variable, membertype, to admin
				$_SESSION['userid'] = $user;
				$_SESSION['membertype']="admin";
				if (!empty($_POST['account'])) //If we're logging into an account, the admin can only work in the subset of the forms that belong to that account, so we set a session to denote what account that is. 
					$_SESSION['account']=$_POST['account'];
				$log->log($user." logged in as admin.");
				$_SESSION['timeout'] = time();
				header("Location: ../admin.php");
				exit;
			}

			if ($user_login_required){ //If the account requires a user login
				if (empty($groups)){ //For the special case where groups aren't considered
					$_SESSION['userid'] = $user;
					$_SESSION['membertype']="user";
					if (isset($_SESSION['redirectpage'])){
						$log->log($user." logged in as user.");
						$_SESSION['timeout'] = time();
						header("Location: ../index.php");
						exit;
					}
				}
				foreach($groups as $group){ //Loop through acceptable groups
					if (substr_in_array($info[0]['memberof'], $group)){ //You are authenticated. Congrats!
						$_SESSION['userid'] = $user;
						$_SESSION['membertype']="user";
						if (isset($_SESSION['redirectpage'])){
							$_SESSION['timeout'] = time();
							$log->log($user." logged in as user.");
							header("Location: ../index.php");
							exit;
						}
					}
				}
			}
			$_SESSION['error']="Login is invalid."; //You aren't worthy. Buh bye.
			$log->log($user." attempted to login and failed.");
			header("Location: ../login.php");
			exit;
		} 
	}
?>