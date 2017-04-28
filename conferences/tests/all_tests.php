<?php
	require_once('simpletest/autorun.php');
	require_once('database_functions.php');
	set_time_limit(3600);
	class AllTests extends TestSuite{
		function __construct(){
			parent::__construct();
			$this->addFile('admin_tests.php');
			//$this->addFile('user_tests.php');
			//$this->addFile('signup_tests.php');
		}
	}