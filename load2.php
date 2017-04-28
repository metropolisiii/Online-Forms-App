<?php
	/**
	* Loads and draws forms
	* 
	* This file is loaded via AJAX when a form needs to be drawn. It gets the form information and returns a JSON representation of that information.
	*
	* @author Jason Kirby <jkirby1325@gmail.com>
	*
	*/
	require('scripts/settings.php');
	require('Formbuilder/Formbuilder.php');
	require('Formbuilder/Formbuilder_pdo.php');
	require('misc/functions.php');
	
	$_GET=sanitize($_GET);
	
	$form = new Formbuilder_pdo(); //Gets form information
	if ($mode=="test")
		$form->connect("mysql:host=127.0.0.1; dbname=forms_app_test");
	else
		$form->connect();
	$json=$form->render_json($_GET['id']); //Returns JSON representation of form information
?>