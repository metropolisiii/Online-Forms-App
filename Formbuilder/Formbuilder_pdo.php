<?php
/**
* @package 	jquery.Formbuilder
* @author 		Michael Botsko
* @copyright 	2009, 2012 Trellis Development, LLC
*
* This PHP object is the server-side component of the jquery formbuilder
* plugin. The Formbuilder allows you to provide users with a way of
* creating a formand saving that structure to the database.
*
* Using this class you can easily prepare the structure for storage,
* rendering the xml file needed for the builder, or render the html of the form.
*
* This package is licensed using the Mozilla Public License 1.1
*
* We encourage comments and suggestion to be sent to mbotsko@trellisdev.com.
* Please feel free to file issues at http://github.com/botskonet/jquery.formbuilder/issues
* Please feel free to fork the project and provide patches back.
*
* Modified by Jason Kirby <jkirby1325@gmail.com>
*/


// Here is an example as how you could store the form data in a MySQL database using PDO

/**
* @abstract This class is a database integration handler example
* the jquery formbuilder plugin.
* @package jquery.Formbuilder
*/
class Formbuilder_pdo extends Formbuilder {
	
	/**
	* Contains PDO connection object
	* @var object 
	*/
	private $_db;	
	
	
	/**
	* Connection statement
	* @param type $url
	* @param type $user
	* @param type $pass
	* @return boolean 
	*/
	public function connect($url = "mysql:host=127.0.0.1;dbname=forms_app", $user = "user", $pass = "Password"){
		try {
			$this->_db = new PDO($url, $user, $pass);
			return true;
		} catch(PDOException $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
		}
		return false;
	}	
	
	/**
	* Save the data to the database, but still returns the $for_db array.
	*
	* @param string|$name The form name
	* @param string|$date The form date
	* @param string|$userId 
	* @param bool|$enabled Is registration enabled?
	* @param string|$notifyees Emails that get notified when form is submitted
	* @param bool|visible Is the form visible to end users?
	* @param string|$accepted_email The email message that gets sent to end-users when a form is accepted
	* @param string|$decline_email The email message that gets sent to end-users when a form is declined
	* @param string|$sitename The site the form is coming from
	*/
	public function save_form($name, $date, $userId, $enabled, $notifyees, $notification_email, $visible, $accepted_email, $declined_email,$sitename, $account, $url, $form_invisible_message,  $form_no_reg_message,$thank_you_page_message,$groups, $users, $no_restrictions, $num_times_filled_out, $thankyou_url, $email_confirmation_to_administrator, $email_confirmation_to_customer,$email_confirmation_to_administrator_subject, $email_confirmation_to_customer_subject, $invoice, $theme){
		$real_theme=$theme;
		include('scripts/settings.php');
		include('scripts/connect.php');
		if (!$real_theme)
			$real_theme = $theme;
		$theme = $real_theme;
		$for_db = parent::get_encoded_form_array(); //Encodes the form into an array of elements
		
		//JK Mod
		if($for_db['form_id']){
			$stmt = $this->_db->prepare("UPDATE fb_savedforms SET form_structure = :struct, name = :name, date = :date, enabled=:enabled, notifyees=:notifyees, notification_email=:notification_email, visible=:visible, accepted_email=:accepted_email, declined_email=:declined_email, sitename=:sitename, accountId=:accountId, url=:url, form_invisible_message=:form_invisible_message, thank_you_page_message=:thank_you_page_message, form_no_reg_message=:form_no_reg_message, reports_no_restrictions=:no_restrictions, num_times_filled_out=:num_times_filled_out, thankyou_url=:thankyou_url, email_confirmation_to_administrator=:email_confirmation_to_administrator, email_confirmation_to_customer=:email_confirmation_to_customer,email_confirmation_to_administrator_subject=:email_confirmation_to_administrator_subject, email_confirmation_to_customer_subject=:email_confirmation_to_customer_subject, invoice=:invoice, theme=:theme WHERE id = :id");
			$stmt->bindParam(':id', $for_db['form_id'], PDO::PARAM_INT);
			
		} else {
			$stmt = $this->_db->prepare("INSERT INTO fb_savedforms (form_structure, userId,name, date, enabled, notifyees, notification_email, visible, accepted_email, declined_email, sitename, accountId, url, form_invisible_message ,form_no_reg_message, thank_you_page_message, reports_no_restrictions, num_times_filled_out, thankyou_url, email_confirmation_to_administrator, email_confirmation_to_customer, email_confirmation_to_administrator_subject, email_confirmation_to_customer_subject, invoice, theme) VALUES (:struct, :userId, :name, :date, :enabled, :notifyees, :notification_email, :visible, :accepted_email, :declined_email, :sitename, :accountId, :url, :form_invisible_message, :form_no_reg_message, :thank_you_page_message, :no_restrictions, :num_times_filled_out, :thankyou_url, :email_confirmation_to_administrator, :email_confirmation_to_customer,:email_confirmation_to_administrator_subject, :email_confirmation_to_customer_subject, :invoice, :theme)");
			$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
		}
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindParam(':date', strtotime($date." midnight +23 hours +59 minutes "), PDO::PARAM_INT);
		$stmt->bindParam(':enabled', $enabled, PDO::PARAM_INT);
		$stmt->bindParam(':notifyees', $notifyees, PDO::PARAM_STR);
		$stmt->bindParam(':notification_email', $notification_email, PDO::PARAM_STR);
		$stmt->bindParam(':visible', $visible, PDO::PARAM_INT);
		$stmt->bindParam(':accepted_email', $accepted_email, PDO::PARAM_STR);
		$stmt->bindParam(':declined_email', $declined_email, PDO::PARAM_STR);
		$stmt->bindParam(':sitename', $sitename, PDO::PARAM_STR);
		$stmt->bindParam(':accountId', $account, PDO::PARAM_INT);
		$stmt->bindParam(':url', $url, PDO::PARAM_STR);
		$stmt->bindParam(':form_invisible_message', $form_invisible_message, PDO::PARAM_STR);
		$stmt->bindParam(':thank_you_page_message', $thank_you_page_message, PDO::PARAM_STR);
		$stmt->bindParam(':form_no_reg_message', $form_no_reg_message, PDO::PARAM_STR);
		$stmt->bindParam(':no_restrictions', $no_restrictions, PDO::PARAM_INT);
		$stmt->bindParam(':num_times_filled_out', $num_times_filled_out, PDO::PARAM_INT);
		$stmt->bindParam(':thankyou_url', $thankyou_url, PDO::PARAM_STR);
		$stmt->bindParam(':email_confirmation_to_administrator', $email_confirmation_to_administrator, PDO::PARAM_STR);
		$stmt->bindParam(':email_confirmation_to_customer', $email_confirmation_to_customer, PDO::PARAM_STR);
		$stmt->bindParam(':email_confirmation_to_administrator_subject', $email_confirmation_to_administrator_subject, PDO::PARAM_STR);
		$stmt->bindParam(':email_confirmation_to_customer_subject', $email_confirmation_to_customer_subject, PDO::PARAM_STR);
		$stmt->bindParam(':invoice', $invoice, PDO::PARAM_STR);
		$stmt->bindParam(':theme', $theme, PDO::PARAM_STR);
		$stmt->bindParam(':struct', $for_db['form_structure'], PDO::PARAM_STR);
	
		$stmt->execute();
		$r=$this->_db->lastInsertId();	
		
		if (!$r)
			$r=$for_db['form_id'];
		
		//confirmation emails
		$form_structure = json_decode($for_db['form_structure']);
		$found=false;
		for ($i=0; $i<count($form_structure); $i++){
			if ($form_structure[$i]->use_for_confirmation==='checked'){
				if (!$found){
					mysql_query("DELETE FROM confirmation_emails WHERE form_id=".$r);
					$found=true;
				}
				mysql_query("INSERT INTO confirmation_emails (form_id, value) VALUES (".$r.", '".str_replace(array('&lt;h&gt;','&lt;/h&gt;'), '',$form_structure[$i]->values)."')");
			}
			if ($form_structure[$i]->discount_code != ''){
				mysql_query("DELETE FROM discounts WHERE form_id=".$r);
				//multiple codes
				$discount_codes=explode(",",$form_structure[$i]->discount_code);
				$discount_amounts=explode(",",$form_structure[$i]->discount_amount);
				$discount_multipliers=explode(",",$form_structure[$i]->discount_multiplier);
				foreach ($discount_codes as $key => $discount_code){
					if (trim($discount_code) != ''){ //Avoid code that were accidentally left blank
						if (!$discount_amounts[$key])
							$discount_amounts[$key] = $discount_amounts[count($discount_amounts)-1];
						if (!$discount_multipliers[$key])
							$discount_multipliers[$key] = $discount_multipliers[count($discount_multipliers)-1];
						mysql_query("INSERT INTO discounts (code, amount, multiplier, form_id) VALUES ('".trim($discount_code)."',".trim($discount_amounts[$key]).",'".trim($discount_multipliers[$key])."',".$r.")");
					}
				}
			}
		}
		//rebuild permissions
		mysql_query("DELETE FROM permissions WHERE formid=".$r);
						
		if (!empty($groups)){
			foreach ($groups as $group => $value){	
				$edit=($value->edit=='true')?1:0;
				$report=($value->report=='true')?1:0;
				$stmt = mysql_query("INSERT INTO permissions (`group`, formid, edit, view_report) VALUES ('".$group."', ".$r.",".$edit.",".$report.")");
			}
		}
		if (!empty($users)){
			foreach ($users as $user => $value){
				$edit=($value->edit=='true')?1:0;
				$report=($value->report=='true')?1:0;
				if ($edit || $report)
					$stmt = mysql_query("INSERT INTO permissions (user, formid, edit, view_report) VALUES ('".$user."', ".$r.",".$edit.",".$report.")");
			}
		}
		$filename=trim(preg_replace("/[^a-zA-Z0-9_\- ]/", "", $name)); //JK Mod. Creates a filename from the form name and date
		$fh = fopen("forms/".$filename."_".str_replace("/","",$date)."_".$r.".html", 'w') or die("can't open file");//JK Mod
		
		$title=$this->get_title($r);
		$html=@$this->get_html($r, "../storeform.php"); //Form action
		$html=@html_entity_decode($html);
		
		//JK Mod
		$template=file_get_contents('template.php'); //Get the predefined form template and fills in values
		$needles=array('[[title]]','[[content]]','[[postid]]','[[stylesheet]]');
		$replacements=array($title, $html, $r, $stylesheet);
		$haystack=$template;
		$newcontent=str_replace($needles, $replacements, $haystack);
		fwrite($fh, $newcontent);
		fclose($fh);
	
		mysql_query("UPDATE fb_savedforms set filename='".$filename."_".str_replace("/","",$date)."_".$r.".html' WHERE id=".$r); //Stores filename in fb_savedforms table
		//End JK Mod
	
		return $r;
	}
		
	/**
	* Overrides the render json method to load the structure from the database
	*/
	public function render_json( $form_db_id = false ){
		if($form_db_id){
			$form = $this->loadFormRecord($form_db_id);
			
			if($form){
				header("Content-Type: application/json");
				$ret = array("form_id" => $form['id'], "form_structure" => json_decode($form['form_structure']) );
				print json_encode( $ret );
			}
		}
	}
	
	
	/**
	*
	* @param type $form_db_id
	* @param type $form_action 
	*/
	public function render_html( $form_db_id = false, $form_action = "" ){
		if($form_db_id){
			$form = $this->loadFormRecord($form_db_id);
			if($form){
				parent::__construct($form);
			}
		}
		parent::render_html($form_action);
	}
	
	public function get_title($form_id){
		$stmt = $this->_db->prepare("SELECT * FROM fb_savedforms WHERE id = :id");
			$stmt->bindParam(':id', $form_id, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount()){
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					return $row['name'];
					break;
				}
			}
	}
	public function get_html( $form_db_id = false, $form_action = "" ){
		if($form_db_id){
			$form = $this->loadFormRecord($form_db_id);
			if($form){
				parent::__construct($form);
			}
		}
		$html=parent::get_html($form_action, $form_db_id);
		return $html;
	}
	
	/**
	* 
	* @param type $form_db_id
	* @return type 
	*/
	public function save_response( $form_db_id = false ){
		$results = $this->process($form_db_id);
		if($results['success']){
			$stmt = $this->_db->prepare("INSERT INTO fb_savedresponses (response_json,date_created) VALUES (:json,:date)");
			$stmt->bindParam(':json', json_encode($results['results']), PDO::PARAM_STR);
			$stmt->bindParam(':date', date("Y-m-d H:i:s"), PDO::PARAM_STR);
			$stmt->execute();
		}
		return $results;
	}
	
	
	/**
	*
	* @param type $form_db_id
	* @param type $form_action 
	*/
	public function process( $form_db_id = false ){
		if($form_db_id){
			$form = $this->loadFormRecord($form_db_id);
			if($form){
				parent::__construct($form);
			}
		}
		return parent::process();
	}
	
	
	/**
	* Query the database for the form
	* @param type $form_db_id
	* @return boolean 
	*/
	protected function loadFormRecord($form_db_id = false){
		if($form_db_id){
			$stmt = $this->_db->prepare("SELECT * FROM fb_savedforms WHERE id = :id");
			$stmt->bindParam(':id', $form_db_id, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount()){
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					return $row;
					break;
				}
			}
			exit;
		}
		return false;
	}
	
	
}
?>