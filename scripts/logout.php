<?php
/**
* Logs users out by destroying session
* @author Jason Kirby <jkirby1325@gmail.com>
*/
	session_start();
	session_destroy();
	header("Location: ../login.php");
?>