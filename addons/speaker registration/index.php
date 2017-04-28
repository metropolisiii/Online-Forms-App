<?php
	class Speaker_Registration{
		private $form_values = array();
		public function getEmails($fields){
			$send_to = array();
			if (!is_array($fields))
				$fields = array($fields);
			foreach ($fields as $field){
				$session = $_POST[$field];
				if (in_array($session, $_POST)){
					if (isset($_POST[$session])){
						$emails = $_POST[$session];
						if (strpos($emails, "||")){
							$emails = explode("||", $emails);
							foreach ($emails as $email)
								$send_to[] = $email;
						}
						else
							$send_to[]=$emails;
					}
				}
			}	
			return $send_to;
		}
		public function sendEmails($emails){
			$mail = new PHPMailer();
			global $id;
			if (is_array($emails)){
				foreach ($emails as $email)
					$mail->AddAddress($email);
			}
			else
				 $mail->AddAddress($emails);
			foreach ($_POST['field_to_check'] as $field){
				$mail->From         = "no-reply@mycompany.com";
				$mail->FromName     = "no-reply";
				$mail->Subject      = $_POST['speaker_registration_subject'];
				$mail->Body         = $this->formatMessage($_POST['speaker_registration_message'], $field);
				$mail->isHTML(true);    
				$mail->CharSet = 'UTF-8';
				if (!empty($_FILES)){
					foreach ($_FILES as $key=>$value){
						$target_path = "files/";
						if (!empty($value['name'])){
							$target_path = $target_path . $id."_".basename($_FILES[$key]['name']); 
							$filename[]=$target_path;
						}
					}
					if ($filename){
						foreach($filename as $key=>$value){
							if ($value)
								$mail->AddAttachment($value);
						}
					}
				}				
				$mail->send();				
			}			
		}
		public function formatMessage($message, $field){
			$slot_title = "";
			if (!is_array($_POST['field_to_check']))
				$_POST['field_to_check'] = array($_POST['field_to_check']);
			if ($_POST[$field] == "")
				return false;
			$slot_title=str_replace("_"," ", $_POST[$field]);
			$message = str_replace("{speaker_registration_slot}", $slot_title, $message);
			$message = preg_replace_callback('/(\{.*?\})/',
				function($matches){
					preg_match('#\{(.*?)\}#', $matches[1], $m);
					return $_POST[$m[1]];	
				}, $message
			);
			$message = preg_replace_callback('/(\[.*?\])/',
				function($matches){
					preg_match('#\[(.*?)\]#', $matches[1], $m);
					return "<".$m[1].">";	
				}, $message
			);
			return $message;
		}
		
	}

	function addonRun(){
		$sr = new Speaker_Registration();
		$emails = $sr->getEmails($_POST['field_to_check']);
		$sent = $sr->sendEmails($emails);
	}
?>