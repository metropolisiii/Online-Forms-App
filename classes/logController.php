<?php

	class logController{
		public function log($content){
			global $logfile;
			$time="";
			$time = date("m/d/Y h:iA");
			if ($_SESSION['userid'])
				$user=$_SESSION['userid'];
			else
				$user="Anonymous";
			$message=$time." ".$user." ".$_SERVER['REMOTE_ADDR']." ".$content."\n";
			file_put_contents($logfile, $message, FILE_APPEND | LOCK_EX);
		}
	}