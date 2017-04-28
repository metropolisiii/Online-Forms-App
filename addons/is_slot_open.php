<?php
	$eidr_link = mysql_connect('localhost', 'edir', 'edir1265%');
	$eidr=mysql_select_db('eidr', $eidr_link);
	if (empty($_POST['name']))
		print 4;
	else{
		$result=mysql_query("SELECT open_slots FROM sessions where name='".$_POST['name']."'");
		$slots=mysql_fetch_object($result);
		print $slots->open_slots;
	}
?>