<?php
	require_once('simpletest/autorun.php');
	require_once('simpletest/web_tester.php');
	session_start();
	class ConferencesSignupTests extends WebTestCase{
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
			$this->restart();
		}
		public function testSignupPage(){
			$this->get($this->site.'create_account.php');
			$this->assertResponse(200);
			$this->assertPattern("/Create a mycompany Conference Account/");
			$this->assertLink("I already have an account");
			$this->assertField("email");
			$this->assertField("password");
			$this->assertField("verifypassword");
			$this->assertPattern('/<button class="btn btn-success" type="submit">Create account<\/button>/');
		}
		public function testAlreadyHaveAnAccount(){
			$this->get($this->site.'create_account.php');
			$this->clickLink("I already have an account");
			$this->assertResponse(200);
			$this->assertTitle("Conference User Account");			
		}
		public function testSignupForAccount(){
			$this->post($this->site.'scripts/create_account.php', array('email'=>'jkirby1325@gmail.com', 'password'=>'password','verifypassword'=>'password'));
			$this->assertResponse(200);
			$result = $this->db->select('accounts','email','jkirby1325@gmail.com');
			$this->assertEqual($result['id'],1);
			$this->assertPattern("/Your account has been created! Now it's time to sign up for mycompany Conference/");
			$this->assertTitle('Conference User Account');			
		}
		public function testAlreadyHaveAccount(){
			$this->post($this->site.'scripts/create_account.php', array('email'=>'jkirby1325@gmail.com', 'password'=>'password','verifypassword'=>'password'));
			$this->restart();
			$this->post($this->site.'scripts/create_account.php', array('email'=>'jkirby1325@gmail.com', 'password'=>'password','verifypassword'=>'password'));
			$this->assertResponse(200);
			$this->assertTitle('Conference User Account Creation');			
			$this->assertPattern("/This account already exists. If you have forgotten your password, you may reset your password by following/");
			$this->assertLink('this link');
			$result = $this->db->numRecords('accounts');
			$this->assertEqual($result, 1);
		}
	}