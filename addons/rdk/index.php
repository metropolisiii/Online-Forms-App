<?php	
	class RDK{
		private $db;
		function __construct(){
			$this->db = mysql_connect('localhost', 'rdk', 'rdkT&T!t3bv');
			mysql_select_db("rdk");
		}
		
		public function storeSession(){
			global $id;
			$session_value = $_POST['PLEASE_CHOOSE_A_RECEPTION_OR_MEAL_TO_SPONSOR'];
			
			//If the user already has sessions, reset them
			$result = mysql_query("UPDATE sessions SET taken = 0, user_form_id=0 WHERE user_form_id = {$id}");
			
			//Get the particular session
			$result = mysql_query("SELECT * from sessions WHERE name='{$session_value}'");
			
			//If the value of the select option is empty, the user has erased his selection. Simple return true
			if (mysql_num_rows($result) ==0)
				return true;
			$row = mysql_fetch_object($result);
			
			//If it's already taken, user can't have it
			if ($row->confirmed == 1){
				mysql_query("UPDATE forms_app.form_answers set response='' WHERE user_form_id = {$id} AND field_id='Please_Choose_a_Meal_or_Reception_To_Sponsor'") or die (mysql_error());
				return false;
			}
			
			//The session isn't taken, give it to the user
			mysql_query("UPDATE sessions set taken = 1, user_form_id={$id} WHERE id=".$row->id);
			return str_replace("_"," ",$row->name);				
		}
		
		public function getSessions(){
			$result = mysql_query("SELECT * FROM sessions") or die(mysql_error());
			$slots = array();
			while ($rec = mysql_fetch_object($result)){
				$slots[$rec->name] = $rec->confirmed;
			}
			return $slots;
		}
		public function confirmPayment($id){
			mysql_query("UPDATE sessions set confirmed=1 WHERE user_form_id='".$id."'");
			return true;
		}
	}
	function addonRunAfterPayment(){
		$rdk = new RDK();
		$success = $rdk->confirmPayment($_POST['formid']);
	}
	function addonRun(){
		$rdk = new RDK();
		$success = $rdk->storeSession();
		if (!$success)
			echo "<p style='font-size:18px; color:red'>This slot has already been taken. It might have been taken as you were filling out your RDK Tech Summit 2015 Sponsor Registration. Please click the link below and choose another session.</p>";
		else
			echo "<p style='font-size:18px; border:solid thin; padding:10px; margin-left:auto; margin-right:auto; width:50%;'>You have been signed up to sponsor the following meal: {$success}. If there is an error in this selection, please use the link below to select a different meal.";
	}
	function initForm(){
		$rdk = new RDK();
		$sessions = $rdk->getSessions();
		return $sessions;
	}
?>