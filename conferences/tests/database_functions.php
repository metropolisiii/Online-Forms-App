<?php
	class DatabaseFunctions{
		private $conn;
		function __construct(){
			try {
				$this->conn = new PDO('mysql:host=localhost;dbname=confereces_test', 'user', 'Password');
				$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch(PDOException $e) {
				echo 'ERROR: ' . $e->getMessage();
			}
		}
		
		public function select($table, $field, $value){
			 $stmt = $this->conn->prepare('SELECT * FROM '.$table.' WHERE '.$field.'=:value');
			 $stmt->execute(array('value' => $value));
			 $row = $stmt->fetch();
			 return $row;
		}
		public function numRecords($table, $where='1=1'){
			 $stmt = $this->conn->query('SELECT * FROM '.$table.' WHERE '.$where);
			 return $stmt->rowCount();
		}
		public function clearDB(){
			$stmt = $this->conn->query("TRUNCATE TABLE accounts");
			$stmt = $this->conn->query("TRUNCATE TABLE forms");
		}
		public function insert($table, $flds, $vals){
			$fields=implode(",",$flds);
			$values="'".implode("','",$vals)."'";
			$query="INSERT INTO {$table} ({$fields}) VALUES ({$values})";
			$stmt = $this->conn->query($query);
			return $this->conn->lastInsertId();
		}
	}