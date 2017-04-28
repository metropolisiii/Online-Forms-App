<?php
	/**
	* Logs user out
	* 
	* Simple script to log user out by destroying all session data and sending the user back to login.php
	*
	*/
	session_start();
	$_SESSION='';
	session_destroy();
	header("Location: login.php");
	exit;
?>