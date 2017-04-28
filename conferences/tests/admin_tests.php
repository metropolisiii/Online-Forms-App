<?php
	session_start();
	require_once('simpletest/autorun.php');
	require_once('simpletest/web_tester.php');
	
	class ConferencesAdminTests extends WebTestCase{
		private $db;
		function __construct() {
			parent::__construct('Admin tests');
			global $dbh;
			$this->site="http://itweb-dev.mycompany.com/forms_app/conferences/";
			$this->dbh=$dbh;
		}	
		public function setUp(){
			$this->restart();
			$this->db = new DatabaseFunctions();
			$this->db->clearDB();
			$fields = array('email','invoice_total','total_paid');
			$values = array('jkirby1325@gmail.com',11000,0);
			$this->db->insert('accounts', $fields, $values);
			$fields = array('user_form_id', 'user_id');
			$values = array(1,1);
			$this->db->insert('forms', $fields, $values);
			$_SESSION['conf_user']="jason.kirby@mycompany";
			$_SESSION['type'] = 'admin';
		}
		public function testIndexPageAdminLoggedIn()
		{
			$this->post($this->site.'scripts/login.php',array('email'=>'jason.kirby@mycompany.com', 'password'=>'dreamtheater'));
			$this->assertResponse(200);
			$this->assertPattern('/Conferences Administration/');
			$this->assertPattern('/jkirby1325@gmail.com/');
			$this->assertLink('View Invoice');			
			$this->assertLink('Winter Conference 2015 Vendor Registration');			
			$this->assertLink('Participation Agreement');	
			//Sees jkirby1325@gmail.com
		}
		public function testIndexNormalLoggedIn()
		{
			$this->post($this->site.'scripts/login.php',array('email'=>'jkirby1325@gmail.com', 'password'=>'Password'));
			$this->assertResponse(200);
			$this->assertNoPattern('/Conferences Administration/');
			$this->assertNoPattern('/jason.kirby@mycompany.com/');
			$this->assertNoLink('View Invoice');			
			$this->assertLink('Winter Conference 2015 Vendor Registration');			
			$this->assertLink('Participation Agreement');	
		}
		public function testLogout()
		{
			$this->post($this->site.'scripts/login.php',array('email'=>'jason.kirby@mycompany.com', 'password'=>'dreamtheater'));
			$this->get($this->site.'logout.php');
			$this->assertPattern('/Sign in/');
			$this->assertPattern('/You are now logged out./');
			//no pattern jkirby1325@gmail.com
		}
		public function testViewInvoice()
		{
			$record = $this->db->select('accounts','email', 'jkirby1325@gmail.com');
			$this->get($this->site.'payment.php?id='.$record['id']);
			$this->assertResponse(200);
			$this->assertPattern('/\$11,000\.00/');
			$this->assertPattern('/Registration for one demo table space at Winter Conference 2015/');
			$this->assertPattern('/Jason Kirby/');
		}
		public function testViewInvoiceNotLoggedIn()
		{
			$record = $this->db->select('accounts','email', 'jkirby1325@gmail.com');
			$this->get($this->site.'payment.php?id='.$record['id']);
			$this->assertResponse(200);
			$this->assertLink('register');
			$this->assertPattern('/Sign in/');
		}
		public function testViewInvoiceNormalLoggedIn()
		{
			$record = $this->db->select('accounts','email', 'jkirby1325@gmail.com');
			$this->post($this->site.'scripts/login.php',array('email'=>'jkirby1325@gmail.com', 'password'=>'Password'));
			$this->get($this->site.'payment.php?id='.$record['id']);
			$this->assertPattern('/\$11,000\.00/');
			$this->assertPattern('/Jason Kirby/');
		}
		
	
		//Click I already have an account
		//Sign up for account
		//Fill out Winter conferences
		//add another registration
		//delete forms
		//Click participation agreement
		//Super admin views someone's invoice
		
		//Payments
		//Title = Winter Conference 2015
		//Invalid vendor company
		//Valid vendor company
		//Vendor company not set
		//Email in GET
		//Invoice id in GET
		//Invalid GET (no email variable set)
		
		//Reset password
		//Link in GET
		//Invalid Link in GET
		//No link in GET
		//Submit form no duplicate accounts
		//Duplicate account
		
		//Finish transaction
		//Good response code
		//Invalid response code
		//Redirects to finish.php
		
		//Log in
		//Test invalid login
		//Valid login
		//Valid superadmin login
		
		//Superadmin
		//checkin link
		//Search box
		//Listing
		//Click to view/edit registration
		//Paid checkbox
		//Checked in checkbox
		//Comments
		//Attending checkbox
		//isHere checkbox
		//Pay here button
		//When pay button is pressed, it goes to the payment form with the vendor company filled in and payment amount
		//When one user makes a change, the change is pushed to everyone else
		
		
		
	}