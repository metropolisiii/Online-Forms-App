<?php
require_once('simpletest/autorun.php');
require_once('simpletest/web_tester.php');
require_once('../scripts/settings.php');

class FormsAppAuthenticationTests extends UnitTestCase{
	private $db;
	private $site;
	function __construct() {
		parent::__construct('Authentication tests');
		global $dbh;
		$this->site="http://itweb-dev.mycompany.com/forms_app/";
		$this->dbh=$dbh;
	}	
	public function setUp(){
		$this->restart();
		$this->db = new DatabaseFunctions();
		$this->clearLogs();
		$this->db->resetAll();
	}
	function tearDown() {
		
	}
	function testAccountRequiresLogin(){
		//assert $user_login_required = true
	}
	function testAccountNoRequoresLogin(){
		//assert $user_login_required = false
	}
	function testBlankLogin(){
		//assert $_SESSION['error']="Please populate all fields";
		//assert $_SESSION['userid'] is not set
	}
	function testInvalidLogin(){
		//assert $_SESSION['error']="Login is invalid.";
		//assert $_SESSION['userid'] is not set
	}
	function testAdminValidLogin(){
		//Assert $_SESSION['superadmin'] is not set
		//assert $_SESSION['userid'] = $user;
		//assert $_SESSION['membertype']="admin";
		//assert $_SESSION['account'] is set
		//assert $_SESSION['timeout'] is set to current time
	}
	
	function testLogout(){
		//assert $_SESSION = is not set;
	}
	function testSuperAdminLogin(){
		//assert $_SESSION['userid'] = $user;
		//assert $_SESSION['membertype']="admin";
		//assert $_SESSION['superadmin']="true";
		//assert $_SESSION['account'] is set
		//assert $_SESSION['timeout'] is set to current time
	}
	function testLogIntoAccountUserNotPartOf(){
		//assert $_SESSION['error']="Login is invalid.";
		//assert $_SESSION['userid'] is not set
	}
	
	function testUserLoginGroupNotMatter(){
		//assert $_SESSION['userid'] = $user;
		//assert $_SESSION['membertype']="user";
		//assert $_SESSION['timeout'] is set to current time
		
	}
	function testUserLoginGroupMatters(){
		//assert $_SESSION['userid'] = $user;
		//assert $_SESSION['membertype']="user";
		//assert $_SESSION['timeout'] is set to current time
	}
	function testUserLoginGroupMattersAndMemberNotInGroup(){
		//assert $_SESSION['error']="Login is invalid."; 
		//assert $_SESSION['userid'] is not set
	}
	
	
}