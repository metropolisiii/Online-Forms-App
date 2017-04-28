<?php
	require_once('simpletest/autorun.php');
	require_once('database_functions.php');
	set_time_limit(3600);
	class AllTests extends TestSuite{
		function __construct(){
			parent::__construct();
			$this->addFile('validation_tests.php');
			$this->addFile('authentication_web_tests.php');
			$this->addFile('admin_tests.php');
			
			//$this->addFile('superadmin_tests.php');
			//$this->addFile('payment_tests.php');
			//$this->addFile('report_tests.php');
		}
	}