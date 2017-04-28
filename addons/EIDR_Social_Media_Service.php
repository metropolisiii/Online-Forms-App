<?php
	$eidr_link = mysql_connect('localhost', 'edir', 'edir1265%');
	$eidr=mysql_select_db('eidr', $eidr_link);
	$slot = $_POST['I_would_like_to_sign_up_for_the_following_session_select_only_one'];
	$result=mysql_query("SELECT open_slots FROM sessions WHERE name='".$slot."'");
	$session=mysql_fetch_array($result);
	$open_slots=$session['open_slots'];
	if ($open_slots-1<0){
		echo "You were not added to this slot as there are no openings for this slot.";
	}
	else{
		mysql_query("UPDATE sessions SET open_slots=".($open_slots-1)." WHERE name='".$slot."'");
	}	
?>