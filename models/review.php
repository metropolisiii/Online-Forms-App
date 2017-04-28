<?php
	/**
	*	$fieldlist_result = Handler of the list of fields to display to the administrator in a select menu. These fields allow the admin to display the values associated with those fields in the forms list to review
	*	$formlist_result = Handler of the list of forms indexed by the field the admin wishes to see in the list
	*	@author Jason Kirby <jkirby1325@gmail.com>
	*/
	
	$query_select="SELECT user_form.*, form_answers.response FROM user_form INNER JOIN form_answers ON user_form.id=form_answers.user_form_id";
	$query_where="WHERE formid={$_GET['id']}";
	$query_orderby="ORDER BY response";
	
	if ($user_login_required) {//Show the username in the forms list
		$query_select="SELECT * FROM user_form";
		$query_orderby="ORDER BY userid";
	}
	else{
		if ($_GET['show_field']){ //If there is not a field to show, show a generic name "form 1"
			$query_select="SELECT user_form.*, form_answers.response FROM user_form INNER JOIN form_answers ON user_form.id=form_answers.user_form_id";
			$query_where.=" AND field_id='{$_GET["show_field"]}'";
		}
		else { //get the first field to display as default
			$query_where.=" AND field_id !='fid' AND field_id != 'url' AND field_id != 'userid' GROUP BY id";
		}
		//Get fields for the drop down menu that allows users to select which field to display
		$fieldlist_result=mysql_query("SELECT field_id from form_answers INNER JOIN user_form ON form_answers.user_form_id=user_form.id WHERE formid={$_GET['id']} GROUP BY field_id ORDER BY form_answers.id ");
	}
	
	$query="{$query_select} {$query_where} {$query_orderby}";
	$formlist_result=mysql_query($query);