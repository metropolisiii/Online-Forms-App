<?php
	include_once("../scripts/settings.php");
	include("../scripts/connect.php");
	if ($this->input->server('REQUEST_METHOD') == "GET"){
		$conference_info = array();
		$result = mysql_query("SELECT fb.name, uf.url, uf.pagename FROM form_answers fa INNER JOIN user_form uf ON fa.user_form_id = uf.id INNER JOIN fb_savedforms fb ON uf.formid = fb.id WHERE fa.response LIKE '".$_GET['email']."' AND fb.category='Conference' AND fb.visible=1 AND fb.enabled=1 AND date<=".time());
		while ($rec = mysql_fetch_object($result)){
			$conference_info[] = array("event_name" => $rec->name, "url" => $rec->url);
		}
		echo json_encode($conference_info);
		http_response_code(200);
		return true;
	}