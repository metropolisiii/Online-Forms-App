<?php
	require_once('simpletest/autorun.php');
	require_once('../misc/functions.php');
	
	class Validationtests extends UnitTestCase{
		function __construct() {
			parent::__construct('Validation tests');
		}
		
		public function setUp(){
		
		}
		
		function testNormalValidation(){
			$test = sanitize("test input");
			$this->assertEqual($test, "test input");			
		}
		
		function testApostrophe(){
			$test = sanitize("Test's");
			$this->assertEqual($test, "Test's");
		}
		
		function testQuotes(){
			$test = sanitize('This is a "test"');
			$this->assertEqual($test, "This is a \"test\"");
		}
		
		function testJavascript(){
			$test = sanitize("<script>This is a test</script>");
			$this->assertEqual($test,'');
		}
		
		function testHTML(){
			$test = sanitize("<h1>This is a test</h1>");
			$this->assertEqual($test,'This is a test');
		}
		function testCSS(){
			$test = sanitize("<style>This is a test</style>");
			$this->assertEqual($test,'This is a test');
		}
	}
