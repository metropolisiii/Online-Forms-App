<?php
	$eidr_link = mysql_connect('localhost', 'edir', 'edir1265%');
	$eidr=mysql_select_db('eidr', $eidr_link);
	$rows=array();
	$result=mysql_query("SELECT id, open_slots FROM sessions");
	while ($r=mysql_fetch_assoc($result)){
		$rows[] = $r;
	}
	print json_encode($rows);
?>