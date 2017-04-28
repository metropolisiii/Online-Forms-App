<?php
	header('Content-Type: application/json');
	include_once("../../misc/functions.php");
	$name=sanitize($_POST['name']);
	$ds=ldap_connect("ldap.mycompany.com");
	if ($ds) { 
		$ldapbind=ldap_bind($ds, 'CTLINT\zz_ldap', 'Q36buCA$');
        if ($ldapbind) {
			if (strpos($name, "@") !== FALSE)
				$filter = "mail=".$name;
			else
				$filter="cn=".$name;
			$dn = "OU=community,DC=mycompany,DC=com";
			$LDAPFieldsToFind = array("mail", "telephoneNumber");
			$sr=ldap_search($ds, $dn, $filter, $LDAPFieldsToFind);
			$info = ldap_get_entries($ds, $sr);
			echo json_encode(array('email'=>$info[0]['mail'][0], 'phone'=>$info[0]['telephonenumber'][0]));
		}
	}