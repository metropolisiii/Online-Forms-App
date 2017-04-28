<?php
	/**
	* Administrative area to view the change log of a user's form
	*
	* @author Jason Kirby <jkirby1325@gmail.com>
	*
	*/
	include_once("scripts/settings.php");
	
	include("includes/header.php");
	if ($_SESSION['membertype'] != "admin"){
		echo "You need to <a href='login.php'> login </a> to enter this area";
		exit;
	}
	$_GET=sanitize($_GET);
	$html.="";
	$date_created="";
	$result=mysql_query("SELECT * FROM form_answers WHERE user_form_id=".$_GET['id']);
	while ($form_answer = mysql_fetch_array($result)){
		if (empty($date_created)){
			$result2=mysql_query("SELECT userid, date_created, date_updated FROM user_form where id=".$form_answer['user_form_id']);
			$user_form=mysql_fetch_array($result2);
			$date_created=$user_form['date_created'];
			$date_updated=$user_form['date_updated'];
			if ($user_form['userid']==="__none__")
				$createduserid="The user";
			else
				$createduserid=$user_form['userid'];
		}
		if ($form_answer['field_id'] !== 'fid' && $form_answer['field_id'] !== 'url' && $form_answer['field_id'] !== 'userid'){
			$html.="<div class='field_id'>".$form_answer['field_id']."</div>";
			$html.="<div class='changes'>";
			$result2=mysql_query("SELECT * from change_log WHERE form_answer_id=".$form_answer['id']);
			$counter=0;
			$row="";
			while ($change=mysql_fetch_array($result2)){
				$userid="";
				if (empty($change['userid']))
					$userid="The user";
				else
					$userid=$change['userid'];
				$row[$counter]['userid']=$userid;
				$row[$counter]['response']=$change['previous_answer'];
				$row[$counter]['date']=$change['date'];
				$counter++;
			}
			if (empty($row))
				$html.="<div class='change'><span class='bold'>".$createduserid."'s</span> initial response was <span class='italics'>".$form_answer['response']."</span> on ".date("m/d/Y", $date_created)."</div>";
			elseif (!empty($row)){
				$html.="<div class='change'><span class='bold'>".$createduserid."'s</span> initial response was <span class='italics'>".$row[0]['response']."</span> on ".date("m/d/Y", $date_created)."</div>";
				foreach($row as $key=>$value){
					$html.="<div class='change'><span class='bold'>".$value['userid']."</span> changed the response from <span class='italics'>".$value['response']."</span> to <span class='italics'>";
					if (empty($row[$key+1]['response']))
						$html.=$form_answer['response']." on ".date("m/d/Y", $date_updated);
					else
						$html.=$row[$key+1]['response']." on ".date("m/d/Y", $value['date']);
					$html.="</span></div>";
				}
			}
			$html.="</div>";
		}
	}
?>
<!-------------------------------------------------------------Presentation------------------------------------------------------------------->
<?php echo $html; ?>