<?php
	require_once('simpletest/autorun.php');
	require_once('simpletest/web_tester.php');
	
	class ConferencesUserTests extends WebTestCase{
		private $db;
		function __construct() {
			parent::__construct('User tests');
			global $dbh;
			$this->site="http://itweb-dev.mycompany.com/forms_app_test/conferences/";
			$this->dbh=$dbh;
		}	
		public function setUp(){
			$this->restart();
			$this->db = new DatabaseFunctions();
			$this->db->clearDB();
			$this->post($this->site.'scripts/create_account.php', array('email'=>'jkirby1325@gmail.com', 'password'=>'password','verifypassword'=>'password'));
		}
		public function testFillOutWinterConferenceForm(){
			$response = $this->post($this->site.'scripts/login.php', array('email'=>'jkirby1325@gmail.com', 'password'=>'password'));
			$this->get($this->site);
			$this->assertLink("Winter Conference 2015 Vendor Registration");
			$response = $this->clickLink("Winter Conference 2015 Vendor Registration");
			$this->assertResponse(200);
			$this->setField("Last_Name","Kirby");
			$this->setField("First_Name","Jason");
			$this->setField("Company","mycompany");
			$this->setField("Address_Line_1","1 Evergreen Street");
			$this->setField("City","Broomfield");
			$this->setField("State","CO");
			$this->setField("Zipcode","80020");
			$this->setField("Work_Phone","303-303-3030");
			$this->setField("Email_Address","jkirby1325@gmail.com");
			$this->setField("Demo_Type","Demo_Table_11000");
			$response = $this->clickSubmit("Save");
			$this->assertResponse(200);
			$numrecs = $this->db->numRecords('forms');
			$this->assertEqual($numrecs,1);
			
		}
	}