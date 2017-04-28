<?php
	if (empty($id))
		exit();
	$log2=new logController("/var/log/subscriptions/log.txt");
	
	$random_hash = md5(date('r', time())); 
	$eol = PHP_EOL;
	
	$headers .= 'From: no-reply@mycompany.com'."\r\n" .
				'MIME-Version: 1.0'."\r\n".
				'Reply-To: noreply@mycompany.com'."\r\n" .
				'X-Mailer: PHP/' . phpversion();
	$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\""; 
	$result=mysql_query("SELECT id, name FROM files where user_form_id=".$id);
	while ($filename=mysql_fetch_array($result)){
		$linkname=$filename['name'];
		$fname=$id."_".$linkname;	
	}
	$result=mysql_query("SELECT field_id, response FROM form_answers where user_form_id=".$id." AND (field_id='fid' OR field_id='select_one_or_more_topics' OR field_id='content_categories' OR field_id='email_subject_line' OR field_id='email_body')" );

	while ($response=mysql_fetch_array($result)){
		if ($response['field_id']==='fid')
			$fid=$response['response'];
		else if ($response['field_id']==='select_one_or_more_topics')
			$options=explode(";",$response['response']);
		else if ($response['field_id']==='content_categories')
			$content_categories=explode(";",$response['response']);
		else if ($response['field_id']==='email_subject_line')
			$subject=$response['response'];
		else if ($response['field_id']==='email_body')
			$message=str_replace(array("\n","\r\n"), "<br/>", $response['response']);
	}
	
	$result=mysql_query("SELECT user_form_id FROM form_answers where field_id='subscriber_id' AND response='".$fid."'");
	while ($subscriber=mysql_fetch_array($result)){
		$result2=mysql_query("SELECT * FROM form_answers where user_form_id=".$subscriber['user_form_id']." AND (field_id = 'email_address' OR field_id='select_one_or_more_topics_of_interest' OR field_id='content_categories')");
		while ($form_answer=mysql_fetch_array($result2)){
			if ($form_answer['field_id']==='email_address')
				$email=$form_answer['response'];
			elseif ($form_answer['field_id'] === 'select_one_or_more_topics_of_interest'){
				$topics=explode(";",$form_answer['response']);
				$user_likes_topics=false;
				for ($i=0; $i<count($topics); $i++){
					if (in_array($topics[$i], $options)){
						$user_likes_topics=true;
					}
				}
			}
			elseif ($form_answer['field_id'] === 'content_categories'){
				$cc=explode(";",$form_answer['response']);
				$user_likes_cc=false;
				for ($i=0; $i<count($cc); $i++){
					if (in_array($cc[$i], $content_categories)){
						$user_likes_cc=true;
					}
				}
			}
		}
		
		if ($user_likes_topics && $user_likes_cc){
			$attachment = chunk_split(base64_encode(file_get_contents('/var/www/html/forms_app/files/'.$fname))); 
			ob_start(); //Turn on output buffering 
?>
--PHP-mixed-<?php echo $random_hash; ?> 
Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $random_hash; ?>" 
--PHP-alt-<?php echo $random_hash; ?> 
Content-Type: text/plain; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

<?php echo $message; ?>

--PHP-alt-<?php echo $random_hash; ?> 
Content-Type: text/html; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

<?php echo $message; ?>

--PHP-mixed-<?php echo $random_hash; ?> 
Content-Type: application/octet-stream; name="<?php echo $linkname; ?>" 
Content-Transfer-Encoding: base64 
Content-Disposition: attachment 

<?php echo $attachment; ?>
--PHP-mixed-<?php echo $random_hash; ?>-- 
<?php 
			$mess=ob_get_clean(); 
			$mail_sent=@mail($email, $subject, $mess, $headers);
			$log2->log("Sent ".$linkname." to ".$email);
		}
	}
	
