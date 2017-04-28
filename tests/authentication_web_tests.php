<?php
require_once('simpletest/autorun.php');
require_once('simpletest/web_tester.php');
ini_set("display_errors","1");
class AuthenticationWebTests extends WebTestCase{
	private $site;
	function __construct() {
		parent::__construct('Authentication tests');
		$this->site="http://itweb-dev.mycompany.com/forms_app/";
	}	
	public function setUp(){
		$this->restart();
		$this->db = new DatabaseFunctions();		
		$this->db->resetAll();
	}
	function tearDown() {
	}
	
	private function searchLogs($pattern_match){
		$contents= file_get_contents($logfile);
		$pattern = preg_quote($pattern_match, '/');
		$pattern = "/^.*$pattern.*\$/m";
		if(preg_match_all($pattern, $contents, $matches))
			return True;		   
		else
		   return False;		
	}
	private function login($username, $password){
		$this->get('http://itweb-dev.mycompany.com/forms_app/login.php');
		$this->setField("username",$username);
		$this->setField("password",$password);
		$this->click("log in");
	}
	private function addAccount($account="mycompany", $url="", $requires_login=0, $groups='', $theme=''){
		$fields = array('name','url','requires_login','groups','theme');
		$values = array($account,$url,$requires_login,$groups,$theme);
		$id = $this->db->insert("accounts", $fields, $values);
		return $id;
	}
	private function addUser($username, $account){
		$fields = array("user", "accountId");
		$values = array("{$username}","{$account}");
		$id = $this->db->insert("admins", $fields, $values);
	}
	function testHomePage(){
		$this->get('http://itweb-dev.mycompany.com/forms_app');
		$this->assertResponse(array(200));
		$this->assertPattern("/Please Login/");
		$this->assertField("username");
		$this->assertField("password");
		$this->assertLink("mycompany Password Reset Form");
		$this->assertNoPattern("/Current forms/");
	}
	function testHomePageNoTheme(){
		$this->addAccount();
		$this->get('http://itweb-dev.mycompany.com/forms_app');
		$this->assertPattern('/themes\/mycompany\/css\/styles.css/');
	}
	function testHomePageNotmycompanyTheme(){
		$this->addAccount('mycompany','','','','CableNET');
		$this->get('http://itweb-dev.mycompany.com/forms_app');
		$this->assertPattern('/themes\/CableNET\/css\/styles.css/');
	}
	function testSwitchThemes(){
		$this->addAccount('mycompany','','','','CableNET');
		$this->get('http://itweb-dev.mycompany.com/forms_app');
		$this->db->resetAll();
		$this->addAccount('mycompany','','','','mycompany');
		$this->get('http://itweb-dev.mycompany.com/forms_app');
		$this->assertPattern('/themes\/mycompany\/css\/styles.css/');
	}
	function testInvalidLogin(){
		$accountId=$this->addAccount();
		$this->login("gibberish","wefsd23fd");
		$this->assertPattern("/Login is invalid./");
	}
	function testAdminValidLogin(){
		$accountId=$this->addAccount();
		$this->addUser("employee-test", $accountId);
		$this->login("employee-test","G00gleTablet");
		$this->assertPattern("/Current forms/");
		$this->assertPattern("/Closed forms/");
		$this->assertLink("Log out");
		$this->assertNoPattern("/Please Login/");
		$this->assertNoPattern("/You are logged in as a super administrator which gives you the ability to handle all forms./");
	}
	function testAccessAdminPageNotLoggedIn(){
	    $this->get('http://itweb-dev.mycompany.com/forms_app/admin.php');
		$this->assertResponse(array(200));
		$this->assertPattern("/Please Login/");
		$this->assertNoPattern("/Current forms/");
	}
	function testLogout(){
		$accountId=$this->addAccount();
		$this->addUser("employee-test", $accountId);
		$this->login("employee-test","G00gleTablet");
		$this->clickLink("Log out");
		$this->assertPattern("/Please Login/");
		$this->assertNoPattern("/Current forms/");
	}
	function testSuperAdminLogin(){
		$accountId=$this->addAccount();
		$this->addUser("jsmith", $accountId);
		$this->login("jsmith","Dream336$");
		$this->assertPattern("/You are logged in as a super administrator which gives you the ability to handle all forms./");
		$this->assertLink("Click here to manage your forms");
		$this->assertLink("Click here to add administrators.");
	}
	function testLogIntoAccountUserNotPartOf(){
		$accountId=$this->addAccount('mycompany','Interops');
		$this->addUser("vendor-test", $accountId);
		$this->login("vendor-test","G00gleTablet");
		$this->assertPattern("/Login is invalid./");
		$this->assertField("username");
	}
	function testUserLogsInButIsNotAdminToFormsApp(){
		$this->login("employee-test","G00gleTablet");
		$this->assertPattern("/Login is invalid./");
	}
	function testBlankLogin(){
		$this->login("","");
		$this->assertPattern("/Please populate all fields/");
	}
	function testUserLoginsSuccessfullyAndLoginRequired(){
		$accountId=$this->addAccount("mycompany","",1,"cl-employees");
		$this->login("employee-test","G00gleTablet");
		$this->assertPattern("/Current forms/");
		$this->assertPattern("/Closed forms/");
		$this->assertLink("Log out");
	}
	function testUserLogsOutSuccessfullyAndLoginRequired(){
		$accountId=$this->addAccount("mycompany","",1,"cl-employees");
		$this->login("employee-test","G00gleTablet");
		$this->clickLink("Log out");
		$this->assertPattern("/Please Login/");
		$this->assertNoPattern("/Current forms/");
	}
	
	function testUserAlreadyLoggedInAndGoesToLoginPage(){
		$accountId=$this->addAccount("mycompany","",1,"cl-employees");
		$this->login("employee-test","G00gleTablet");
		$this->get('http://itweb-dev.mycompany.com/forms_app/login.php');
		$this->assertPattern("/Current forms/");
		$this->assertPattern("/Closed forms/");
		$this->assertNoPattern("/Please Login/");
	}
	function testUserAlreadyLoggedInAndGoesToAdminPage(){
		$accountId=$this->addAccount("mycompany","",1,"cl-employees");
		$this->login("employee-test","G00gleTablet");
		$this->get('http://itweb-dev.mycompany.com/forms_app/admin.php');
		$this->assertPattern("/Current forms/");
		$this->assertPattern("/Closed forms/");
		$this->assertNoPattern("/Please Login/");
	}	
}