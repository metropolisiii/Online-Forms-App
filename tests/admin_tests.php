<?php
require_once('simpletest/autorun.php');
require_once('simpletest/web_tester.php');


class FormsAppAdminTests extends WebTestCase{
	private $db;
	private $site;
	function __construct() {
		parent::__construct('Admin tests');
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
	private function addUser($username, $account){
		$fields = array("user", "accountId");
		$values = array("{$username}","{$account}");
		$id = $this->db->insert("admins", $fields, $values);
		return $id;
	}
	function testMainPageNotLoggedIn(){
		$this->get('http://itweb-dev.mycompany.com/forms_app/admin.php');
		$this->assertResponse(array(200));
		$this->assertPattern("/Please Login/");
		$this->assertField("username");	
		$this->assertNoPattern("/Current Forms/");
	}
	function testMainPageAsUser(){
		$this->addAccount("mycompany","",1);
		$this->login('employee-test','G00gleTablet');
		$this->get('http://itweb-dev.mycompany.com/forms_app/admin.php');
		$this->assertPattern("/Current forms/");
		$this->assertPattern("/Closed forms/");
		$this->assertNoPattern("/Please Login/");
		$this->assertNoPattern("/Reports Beta/");
	}
	function testMainPageLoggedIn(){
		$accountId=$this->addAccount();
		$this->addUser('employee-test', $accountId);
		$this->login('employee-test', 'G00gleTablet');
		$this->get('http://itweb-dev.mycompany.com/forms_app/admin.php');
		$this->assertResponse(array(200));
		$this->assertLink('Home');
		$this->assertLink("New Form");
		$this->assertLink("Reports");
		$this->assertLink("Reports Beta");
		$this->assertPattern('/In order to edit a form, click the name of the form. To preview a form, click the "preview" link next to the form. To review forms, click the "review" link that corresponds with the form you want to review./');
		$this->assertPattern("/Current forms/");
		$this->assertPattern("/Closed forms/");
		$this->assertNoPattern("/You are logged in as a super administrator which gives you the ability to handle all forms./");
	}
	function testCurrentFormExists(){
		$accountId=$this->addAccount();
		$userId=$this->addUser('employee-test', $accountId);
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("+1 week"), 0, "", 0, $accountId);
		$id = $this->db->insert("fb_savedforms", $fields, $values);
		$fields = array("user","formid","edit","view_report");
		$values = array("employee-test",$id, 1, 1);
		$id = $this->db->insert("permissions", $fields, $values);
		$this->login('employee-test', 'G00gleTablet');
		$this->assertLink("Test form");
		$this->assertLink("(Preview)");
		$this->assertPattern("/<button id='".$id."' class='disable_button'>Enable Registration<\/button>/");
		$this->assertPattern("/<button id='".$id."' class='invisible_button'>Make Visible to Public<\/button>/");
		$this->assertPattern("/<button value='".$id."' class='copy_form'>Copy<\/button>/");
		$this->assertPattern("/<button class='delete_form' value='".$id."'>Delete<\/button>/");
		$this->assertLink("Review");	
	}	
	function testClosedFormExists(){
		$accountId=$this->addAccount();
		$userId=$this->addUser('employee-test', $accountId);
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("-1 week"), 0, "", 0, $accountId);
		$id = $this->db->insert("fb_savedforms", $fields, $values);
		$fields = array("user","formid","edit","view_report");
		$values = array("employee-test",$id, 1, 1);
		$id = $this->db->insert("permissions", $fields, $values);
		$this->login('employee-test', 'G00gleTablet');
		$this->assertLink("Test form");
		$this->assertLink("(Preview)");
		$this->assertNoPattern("/<button id='".$id."' class='disable_button'>Enable Registration<\/button>/");
		$this->assertNoPattern("/<button id='".$id."' class='invisible_button'>Make Visible to Public<\/button>/");
		$this->assertPattern("/<button value='".$id."' class='copy_form'>Copy<\/button>/");
		$this->assertPattern("/<button class='delete_form' value='".$id."'>Delete<\/button>/");
		$this->assertLink("Review");	
	}
	function testEditFormBasic(){
		$accountId=$this->addAccount();
		$userId=$this->addUser('employee-test', $accountId);
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("+1 week"), 0, "", 0, $accountId);
		$id = $this->db->insert("fb_savedforms", $fields, $values);
		$fields = array("user","formid","edit","view_report");
		$values = array("employee-test",$id, 1, 1);
		$id = $this->db->insert("permissions", $fields, $values);
		$this->login('employee-test', 'G00gleTablet');
		$this->assertPattern('/createform.php\?id=1/');
		$this->clickLink('Test form');
		$this->assertResponse(array(200));
		$this->assertPattern('/value="Test form" name="savename"/');
	}
	function testEnableRegistration(){
		$accountId=$this->addAccount();
		$userId=$this->addUser('employee-test', $accountId);
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("+1 week"), 0, "", 0, $accountId);
		$form_id = $this->db->insert("fb_savedforms", $fields, $values);
		$fields = array("user","formid","edit","view_report");
		$values = array("employee-test",$form_id, 1, 1);
		$id = $this->db->insert("permissions", $fields, $values);
		$this->login('employee-test', 'G00gleTablet');
		$this->post('http://itweb-dev.mycompany.com/forms_app/scripts/change_enabled.php', array('status' =>1, 'id'=>$form_id));
		$this->get('http://itweb-dev.mycompany.com/forms_app/admin.php');
		$this->assertPattern("/<button id='".$form_id."' class='enable_button'>Disable Registration<\/button>/");
		$form = $this->db->select("fb_savedforms","id", $form_id);
		$this->assertEqual($form['enabled'],1);
	}
	function testDisableRegistration(){
		$accountId=$this->addAccount();
		$userId=$this->addUser('employee-test', $accountId);
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("+1 week"), 0, "", 0, $accountId);
		$form_id = $this->db->insert("fb_savedforms", $fields, $values);
		$fields = array("user","formid","edit","view_report");
		$values = array("employee-test",$form_id, 1, 1);
		$id = $this->db->insert("permissions", $fields, $values);
		$this->login('employee-test', 'G00gleTablet');
		$this->post('http://itweb-dev.mycompany.com/forms_app/scripts/change_enabled.php', array('status' => 0, 'id'=>$form_id));
		$this->get('http://itweb-dev.mycompany.com/forms_app/admin.php');
		$this->assertPattern("/<button id='".$form_id."' class='disable_button'>Enable Registration<\/button>/");
		$form = $this->db->select("fb_savedforms","id", $form_id);
		$this->assertEqual($form['enabled'],0);
	}
	function testEnableRegistrationNotLoggedInAsAdmin(){
		$accountId=$this->addAccount();
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("+1 week"), 0, "", 0, $accountId);
		$form_id = $this->db->insert("fb_savedforms", $fields, $values);
		$this->post('http://itweb-dev.mycompany.com/forms_app/scripts/change_enabled.php', array('status' => 0, 'id'=>1));
		$this->assertPattern("/Please Login/");
		$form = $this->db->select("fb_savedforms","id", $form_id);
		$this->assertEqual($form['enabled'],0);
	}
	function testEnableRegistrationEmptyPost(){
		$accountId=$this->addAccount();
		$userId=$this->addUser('employee-test', $accountId);
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("+1 week"), 0, "", 0, $accountId);
		$form_id = $this->db->insert("fb_savedforms", $fields, $values);
		$fields = array("user","formid","edit","view_report");
		$values = array("employee-test",$form_id, 1, 1);
		$id = $this->db->insert("permissions", $fields, $values);
		$this->login('employee-test', 'G00gleTablet');
		$this->post('http://itweb-dev.mycompany.com/forms_app/scripts/change_enabled.php', array());
		$this->assertNoText(" ");
	}
	function testMakeFormVisible(){
		$accountId=$this->addAccount();
		$userId=$this->addUser('employee-test', $accountId);
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("+1 week"), 0, "", 0, $accountId);
		$form_id = $this->db->insert("fb_savedforms", $fields, $values);
		$fields = array("user","formid","edit","view_report");
		$values = array("employee-test",$form_id, 1, 1);
		$id = $this->db->insert("permissions", $fields, $values);
		$this->login('employee-test', 'G00gleTablet');
		$this->post('http://itweb-dev.mycompany.com/forms_app/scripts/change_visibility.php', array('status' => 1, 'id'=>$form_id));
		$this->get('http://itweb-dev.mycompany.com/forms_app/admin.php');
		$this->assertPattern("/<button id='".$form_id."' class='visible_button'>Make Invisible to Public<\/button>/");
		$form = $this->db->select("fb_savedforms","id", $form_id);
		$this->assertEqual($form['visible'],1);
	}
	function testMakeFormNotVisible(){
		$accountId=$this->addAccount();
		$userId=$this->addUser('employee-test', $accountId);
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("+1 week"), 0, "", 0, $accountId);
		$form_id = $this->db->insert("fb_savedforms", $fields, $values);
		$fields = array("user","formid","edit","view_report");
		$values = array("employee-test",$form_id, 1, 1);
		$id = $this->db->insert("permissions", $fields, $values);
		$this->login('employee-test', 'G00gleTablet');
		$this->post('http://itweb-dev.mycompany.com/forms_app/scripts/change_visibility.php', array('status' => 0, 'id'=>$form_id));
		$this->get('http://itweb-dev.mycompany.com/forms_app/admin.php');
		$this->assertPattern("/<button id='".$form_id."' class='invisible_button'>Make Visible to Public<\/button>/");
		$form = $this->db->select("fb_savedforms","id", $form_id);
		$this->assertEqual($form['visible'],0);
	}
	function testMakeFormVisibleNotLoggedIn(){
		$accountId=$this->addAccount();
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("+1 week"), 0, "", 0, $accountId);
		$form_id = $this->db->insert("fb_savedforms", $fields, $values);
		$this->post('http://itweb-dev.mycompany.com/forms_app/scripts/change_visibility.php', array('status' => 0, 'id'=>1));
		$this->assertPattern("/Please Login/");
		$form = $this->db->select("fb_savedforms","id", $form_id);
		$this->assertEqual($form['visible'],0);
	}
	function testCopyFormNotLoggedInAsAdmin(){
		$accountId=$this->addAccount();
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("+1 week"), 0, "", 0, $accountId);
		$form_id = $this->db->insert("fb_savedforms", $fields, $values);
		$this->post('http://itweb-dev.mycompany.com/forms_app/scripts/copy.php', array('status' => 0, 'id'=>1));
		$this->assertPattern("/Please Login/");
		$form = $this->db->select("fb_savedforms","id", $form_id+1);
		$this->assertFalse($form);
	}
	function testCopyFormEmptyPost(){
		$accountId=$this->addAccount();
		$userId=$this->addUser('employee-test', $accountId);
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("+1 week"), 0, "", 0, $accountId);
		$form_id = $this->db->insert("fb_savedforms", $fields, $values);
		$fields = array("user","formid","edit","view_report");
		$values = array("employee-test",$form_id, 1, 1);
		$id = $this->db->insert("permissions", $fields, $values);
		$this->login('employee-test', 'G00gleTablet');
		$this->post('http://itweb-dev.mycompany.com/forms_app/scripts/copy.php', array());
		$this->assertNoText(" ");
		$form = $this->db->select("fb_savedforms","id", $form_id+1);
		$this->assertFalse($form);
	}
	function testCopyFormInvalidId(){
		$accountId=$this->addAccount();
		$userId=$this->addUser('employee-test', $accountId);
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("+1 week"), 0, "", 0, $accountId);
		$form_id = $this->db->insert("fb_savedforms", $fields, $values);
		$fields = array("user","formid","edit","view_report");
		$values = array("employee-test",$form_id, 1, 1);
		$id = $this->db->insert("permissions", $fields, $values);
		$this->login('employee-test', 'G00gleTablet');
		$this->post('http://itweb-dev.mycompany.com/forms_app/scripts/copy.php', array("id"=>2));
		$numrecords = $this->db->numRecords("fb_savedforms");
		$this->assertEqual($numrecords, 1);
	}
	function testCopyForm(){
		$accountId=$this->addAccount();
		$userId=$this->addUser('employee-test', $accountId);
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "filename","visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("+1 week"), 0, "", "Test form_".date("mdY", strtotime("+1 week"))."_1.html", 0, $accountId);
		$fp=fopen("../forms/Test form_".date("mdY", strtotime("+1 week"))."_1.html", 'w');
		fwrite($fp, '<input type="hidden" name="fid" value="1"/>');
		fclose($fp);
		$form_id = $this->db->insert("fb_savedforms", $fields, $values);
		$oldform = $this->db->select("fb_savedforms", "id", $form_id);
		$fields = array("user","formid","edit","view_report","reportid");
		$values = array("employee-test",$form_id, 1, 1,1);
		$id = $this->db->insert("permissions", $fields, $values);
		$this->login('employee-test', 'G00gleTablet');
		$this->post('http://itweb-dev.mycompany.com/forms_app/scripts/copy.php', array("id"=>1));
		$form = $this->db->select("fb_savedforms","id", $form_id+1);
		$this->assertEqual($form['id'], $form_id+1);
		$this->assertEqual($form['name'], $oldform['name']."_copy");
		$this->assertEqual($form['form_structure'], $oldform['form_structure']); 
		$this->assertEqual($form['visible'], 0);
		$this->assertEqual($form['enabled'], 0);
		$this->assertTrue(file_exists("../forms/Test form_copy_".date("mdY", strtotime("+1 week"))."_2.html"));
		$f2 = file_get_contents("../forms/Test form_copy_".date("mdY", strtotime("+1 week"))."_2.html");
		$this->assertEqual( '<input type="hidden" name="fid" value="2"/>', $f2);
		$oldpermissions = $this->db->select("permissions","formid",$id);
		$newpermissions = $this->db->select("permissions","formid",$form['id']);
		$this->assertEqual($oldpermissions['user'], $newpermissions['user']);
		$this->assertEqual($oldpermissions['group'], $newpermissions['group']);
		$this->assertEqual($oldpermissions['edit'], $newpermissions['edit']);
		$this->assertEqual($oldpermissions['view_report'], $newpermissions['view_report']);
		$this->assertEqual($newpermissions['formid'],2);
		$this->assertNotEqual($oldpermissions['reportid'], $newpermissions['reportid']);
	}
	function testDeleteFormNotLoggedInAsAdmin(){
		$accountId=$this->addAccount();
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("+1 week"), 0, "", 0, $accountId);
		$form_id = $this->db->insert("fb_savedforms", $fields, $values);
		$fp=fopen("../forms/Test form_".date("mdY", strtotime("+1 week"))."_1.html", 'w');
		fwrite($fp, '<input type="hidden" name="fid" value="1"/>');
		fclose($fp);
		$this->post('http://itweb-dev.mycompany.com/forms_app/scripts/delete.php', array('id'=>$form_id));
		$this->assertPattern("/Please Login/");
		$form = $this->db->select("fb_savedforms","id", $form_id);
		$this->assertTrue($form);
		$this->assertTrue(file_exists("../forms/Test form_".date("mdY", strtotime("+1 week"))."_1.html"));
	}
	function testDeleteFormEmptyPost(){
		$accountId=$this->addAccount();
		$userId=$this->addUser('employee-test', $accountId);
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("+1 week"), 0, "", 0, $accountId);
		$form_id = $this->db->insert("fb_savedforms", $fields, $values);
		$fields = array("user","formid","edit","view_report");
		$values = array("employee-test",$form_id, 1, 1);
		$id = $this->db->insert("permissions", $fields, $values);
		$this->login('employee-test', 'G00gleTablet');
		$this->post('http://itweb-dev.mycompany.com/forms_app/scripts/delete.php', array());
		$form = $this->db->select("fb_savedforms","id", $form_id);
		$this->assertTrue($form);
		$this->assertTrue(file_exists("../forms/Test form_".date("mdY", strtotime("+1 week"))."_1.html"));
	}
	function testDeleteForm(){
		$accountId=$this->addAccount();
		$userId=$this->addUser('employee-test', $accountId);
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "filename","visible","accountId");
		$values = array("{name:test}", "employee-test", "Test form", strtotime("+1 week"), 0, "", "Test form_".date("mdY", strtotime("+1 week"))."_1.html", 0, $accountId);
		$fp=fopen("../forms/Test form_".date("mdY", strtotime("+1 week"))."_1.html", 'w');
		fwrite($fp, '<input type="hidden" name="fid" value="1"/>');
		fclose($fp);
		$form_id = $this->db->insert("fb_savedforms", $fields, $values);
		$oldform = $this->db->select("fb_savedforms", "id", $form_id);
		$fields = array("user","formid","edit","view_report","reportid");
		$values = array("employee-test",$form_id, 1, 1,1);
		$id = $this->db->insert("permissions", $fields, $values);
		$this->login('employee-test', 'G00gleTablet');
		$this->post('http://itweb-dev.mycompany.com/forms_app/scripts/delete.php', array('id'=>$form_id));
		$form = $this->db->select("fb_savedforms","id", $form_id);
		$this->assertFalse($form);
		$form_permissions = $this->db->select("permissions", "formid", $form_id);
		$this->assertFalse($form_permissions);
		$this->assertFalse(file_exists("../forms/Test form_".date("mdY", strtotime("+1 week"))."_1.html"));
		$this->assertPattern("/Current forms/");
	}
	function testReviewNotLoggedInAsAdmin(){
		$this->get('http://itweb-dev.mycompany.com/forms_app/review.php');
		$this->assertField("username");
		$this->assertNoPattern("/Choose a user's form to review/");
	}
	function testReviewFormHomePageLoginNotRequiredLoggedIn(){
		$accountId=$this->addAccount();
		$userId=$this->addUser('employee-test', $accountId);
		$fields = array("form_structure", "userId", "name", "date", "enabled", "sitename", "filename","visible","accountId");
		$values = array('[{"cssClass":"input_text","required":"undefined","readonly":"undefined","validate_date":"undefined","word_count":"undefined","defaultvalue":"test","use_for_confirmation":"undefined","character_count":"undefined","values":"name"},{"cssClass":"input_text","required":"undefined","readonly":"undefined","validate_date":"undefined","word_count":"undefined","defaultvalue":"test","use_for_confirmation":"undefined","character_count":"undefined","values":"address"}]', "employee-test", "Test form", strtotime("+1 week"), 0, "", "Test form_".date("mdY", strtotime("+1 week"))."_1.html", 0, $accountId);
		$fp=fopen("../forms/Test form_".date("mdY", strtotime("+1 week"))."_1.html", 'w');
		fwrite($fp, '<input type="hidden" name="fid" value="1"/>');
		fclose($fp);
		$form_id = $this->db->insert("fb_savedforms", $fields, $values);
		$fields = array("user","formid","edit","view_report","reportid");
		$values = array("employee-test",$form_id, 1, 1,1);
		$id = $this->db->insert("permissions", $fields, $values);
		$fields = array("userid","formid", "url","pagename");
		$values = array("__none__",1,"aaa","Test%20Form_1.html");
		$user_form_id = $this->db->insert("user_form", $fields, $values);
		$fields = array("field_id","response", "user_form_id");
		$values = array("name","Jason",$user_form_id);
		$form_answers_id = $this->db->insert("form_answers", $fields, $values);
		$fields = array("field_id","response", "user_form_id");
		$values = array("address","1325 Street",$user_form_id);
		$form_answers_id = $this->db->insert("form_answers", $fields, $values);
		$this->login('employee-test', 'G00gleTablet');
		$this->clickLink("Review");
		$this->assertPattern("/Choose a user's form to review/");
		$this->assertPattern("/<option value='name'>name<\/option>/");
		$this->assertPattern("/<option value='address'>address<\/option>/");
		$form = $this->db->select("fb_savedforms","id",$form_id);
		$user_form = $this->db->select("user_form","id",$user_form_id);
		$this->assertPattern("/<form action='forms\/".$user_form['pagename']."\?q=aaa' method='POST' target='_blank'>/");
		$this->assertPattern("/<input class='review' type='submit' value='Jason'\/>/");
		$this->assertNoPattern("/<a target='_blank' href='change_log.php\?id=".$user_form['id'].">View Changes<\/a>/");
		$this->assertPattern("/<input type='hidden' name='userid' value='__none__'\/>/");
		$this->assertPattern("/<button value='userid:__none__, url:aaa,status:accept' class='accept'>Accept<\/button>/");
		$this->assertPattern("/<button value='userid:__none__, url:aaa,status:reject' class='reject'>Incomplete<\/button>/");
		$this->assertPattern("/<button value='userid:__none__, url:aaa,status:reset' class='reset'>Need more info<\/button>/");
		//Should have button "Delete"
		//Should not see forms with other ids
		//Should have link "Back to forms"
		//Home tab should have class selected
	}
	function testReviewFormHomePageLoginNotRequiredAndShowField(){
		//Should see <input class="review" type="submit" value="[Form field]">
	}
	
	function testReviewFormHomePageLoginRequired(){
		//Should not see Choose a user's form to review
		//Should not see all of the form fields in <option value=''></option> tags
		//Should see form target="_blank" method="POST" action="forms/[form name]?q=">
		//Should see <input class="review" type="submit" value="[user full name] [company]">
		//Should not see <a target="_blank" href="change_log.php?id=[formid]">View Changes</a>
		//Should see <input type='hidden' name='userid' value='[userid]'/>
		//Should have button "accept"
		//Should have button "Incomplete"
		//Should have button "Need more info"
		//Should have button "Delete"
		//Should not see forms with other ids
		//Should have link "Back to forms"
	}
	function testReviewFormHomePageWithMultipleForms(){
		//Should see form target="_blank" method="POST" action="forms/[form name]?q=">
		//Should see form target="_blank" method="POST" action="forms/[form name]?q=">
		//Should not see forms with other ids
		//Should see Form 1
		//Should see Form 2
	}
	function testReviewUserSavesFormForLater(){
		//Should see (Incomplete)
	}
	function testReviewFormGetsUpdated(){
		//Should see <a target="_blank" href="change_log.php?id=<[formid]">View Changes</a>
	}
	function testReviewWithFileField(){
		//Should see <div id="files_[formid]" class="files_popup">
		//Should see <a href='files/[formid]_[form_name]'>[form_name]</a>
	}
	function testReviewWithMultipleFileFields(){
		//Should see <a href='files/echo "{$formid}_{$file['name']}";'>echo $file['name'];</a>
		//Should see <a href='files/echo "{$formid}_{$file['name']}";'>echo $file['name']; </a>
	}
	function testReviewViewFile(){
		//Should get a file header
	}
	function testReviewWithoutFieldField(){
		//Should not see <div id="files_echo $formid; " class="files_popup">
		//Should not see <a href='files/ "{$formid}_{$file['name']}";'>echo $file['name']; </a>
	}
	function testReviewWithFormAccepted(){
		//Should see <img src="images/accept.png">
	}
	function testReviewWithFormDeclined(){
		//Should see <img src="images/reject.png">
	}
	function testAcceptFormNotLoggedIn(){
		//Should have link Log in
		//Databased should have accepted = NULL
	}
	function testAcceptFormAccepted(){
		//Datebase should have accepted = 1
	}
	function testAcceptFormRejected(){
		//Datebase should have accepted = 0
	}
	function testAcceptFormReset(){
		//Datebase should have accepted = NULL
	}
	function testAcceptButtonPressedLoginNotRequired(){
		//Should have field email with value blank
		//Should have field replyto with value of notifyees in database
		//Should have field Subject with value = Information Regarding the echo $form['name'];  form
		//Should See field message with value of accepted_email value in database with [user] not replaced
		//Should have field user
		//Should have field id
	}
	function testAcceptButtonPressedLoginRequired(){
		//Should have field email with value of AD email
		//Should have field replyto with value of notifyees in database
		//Should have field Subject with value = Information Regarding the echo $form['name'];  form
		//Should See field message with value of accepted_email value in database with [user] replaced with AD first name
		//Should have field user
		//Should have field id
	}
	function testRejectButtonPressedLoginNotRequired(){
		//Should have field email with value blank
		//Should have field replyto with value of notifyees in database
		//Should have field Subject with value = Information Regarding the  echo $form['name']; form
		//Should See field message with value of declined_email value in database with [user] not replaced
		//Should have field user
		//Should have field id
	}
	function testRejectButtonPressedLoginRequired(){
		//Should have field email with value of AD email
		//Should have field replyto with value of notifyees in database
		//Should have field Subject with value = Information Regarding the  echo $form['name'];  form
		//Should See field message with value of declined_email value in database with [user] replaced with AD first name
		//Should have field user
		//Should have field id
	}
	function testSendFormEmailWhenAcceptedNotLoggedIn(){
		//Should have link Log in
		//Should not see Email has been sent
		//Should not see <img src='../images/close.png'/>
	}
	function testSendFormEmailWhenAcceptedEmailNotSet(){
		//Should not see Email has been sent
		//Should not see <img src='../images/close.png'/>
	}
	function testSendFormEmailWhenAccepted(){
		//Should see Email has been sent
	}
	function testViewChangeLogNotLoggedIn(){
		//Should have link Log in
		//Should not see <div class="field_id">
	}
	function testViewChangeLogLoggedInAsAdminFormLoginNotRequired(){
		//Should see field names
		//Should not see fid
		//should not see url
		//Should not see userid
		//Should see The user_error
		//Should see initial response was [answer] on [date created]
	}
	function testViewChangeLogLoggedInAsAdminFormLoginRequired(){
		//Should see the userid of a user
	}
	function testViewChangeLogNoChangeOnAnAnswer(){
		//Should see initial response was [answer] on [date created]
	}
	function testViewChangeLogOneChangeOnAnswer(){
		//Should see initial response was [answer] on [date created]
		//Should see <div class='change'><span class='bold'>".$value['userid']."</span> changed the response from <span class='italics'>".$value['response']."</span> to <span class='italics'> [answer] on [date]
	}
	function testViewChangeLogMultipleChangesOnAnswer(){
		//Should see initial response was [answer] on [date created]
		//Should see <div class='change'><span class='bold'>".$value['userid']."</span> changed the response from <span class='italics'>".$value['response']."</span> to <span class='italics'> [answer] on [date]
	}
}
	
