<?php
	class DatabaseFunctions{
		private $conn;
		private $tables;
		function __construct(){
			try {
				$this->conn = new PDO('mysql:host=localhost;dbname=forms_app_test', 'user', 'Password');
				$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch(PDOException $e) {
				echo 'ERROR: ' . $e->getMessage();
			}
			$this->tables = array('accounts','admins','change_log','confirmation_emails','discounts','fb_savedforms','files','form_answers', 'permissions','report_answers','report_fields','reports','user_form','users_reports');
		}
		public function resetAll(){
			for ($i=0; $i<count($this->tables); $i++)
				$this->conn->query('TRUNCATE table '.$this->tables[$i]);
		}
		
		public function select($table, $field, $value){
			 $stmt = $this->conn->prepare('SELECT * FROM '.$table.' WHERE '.$field.'=:value');
			 $stmt->execute(array('value' => $value));
			 $row = $stmt->fetch();
			 return $row;
		}
		public function insert($table, $fields, $values){
			$fields = join(",", $fields);
			$values = implode("','", $values);
			$stmt = $this->conn->prepare("INSERT INTO ".$table." (".$fields.") VALUES ('".$values."')");
			$stmt->execute();
			return $this->conn->lastInsertId();
		}
		public function numRecords($table){
			 $stmt = $this->conn->query('SELECT * FROM '.$table);
			 return $stmt->rowCount();
		}
		
	}