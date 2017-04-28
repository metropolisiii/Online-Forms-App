<?php
	/**
	* If a form requires a payment, the user is brought to this page once the transaction is made.
	*
	* @author Jason Kirby <jkirby1325@gmail.com>
	*
	*/
	
	include_once("../scripts/settings.php");
	include("../includes/header.php");
	mail("jason.kirby@mycompany.com","test",print_r($_POST, true));