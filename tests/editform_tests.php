<?php
require_once('simpletest/autorun.php');
require_once('simpletest/web_tester.php');
include("../includes/settings.php");

class FormsAppNewFormTests extends WebTestCase{
	private $db;
	private $site;
	function __construct() {
		parent::__construct('New Form tests');
		global $dbh;
		$this->site="http://itweb-dev.mycompany.com/forms_app/";
		$this->dbh=$dbh;
	}	
	public function setUp(){
		$this->restart();
		$this->db = new DatabaseFunctions();
		$this->db->resetAll();
	}
	function tearDown() {
		
	}	
	private function login($username, $password){
		$this->get('http://itweb-dev.mycompany.com/forms_app/login.php');
		$this->setField("username",$username);
		$this->setField("password",$password);
		$this->click("log in");
	}
	private function addAccount($account="mycompany", $url="", $requires_login=0, $groups=''){
		$fields = array('name','url','requires_login','groups','theme');
		$values = array($account,$url,$requires_login,$groups,'mycompany');
		$id = $this->db->insert("accounts", $fields, $values);
		return $id;
	}
	
	
	function testNewFormHomePage(){
		//Should see Form Information
		//Should see Form Design
		//Should see Form Permissions
		//Should have field savename
		//Should have field date
		//Should have field enabled with value = 0
		//Should have field visibile with value = 0
		//Should have field url with value = http://
		//Should have field thankyou_url with value http://
		//Should have field with id='notify_name'
		//Should have field notifyees
		//Should have field notification_email
		//Should have field accepted_email
		//Should have field declined_email
		//Should have field thank_you_page_message with value "Thank you for filling out this form. Submission of this form has been successful. If there are questions about your responses, we will email you. You may return to fill out your form at [form_link]"
		//Should see Email Confirmation Legend
		//Should see Email confirmation message to administrator (optional)
		//Should have field email_confirmation_to_administrator_subject
		//Should have field email_confirmation_to_administrator with value [field list]
		//Should see Email confirmation message to customer (optional)
		//Should have field email_confirmation_to_customer_subject
		//Should have field email_confirmation_to_customer with value [field list]
		//Should have field with id frmb-0-save-button and value = Save
		//Should have field with id frmb-0-save-button and value = Done
	}
	function testPreview(){
		//Should return 200
	}
}