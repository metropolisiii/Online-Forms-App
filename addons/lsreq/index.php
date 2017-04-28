<?php
	class LSREQ{
		private $userinfo;
		private $data;
		/* Get username and password */
		function __construct(){
			$fh = fopen('/etc/cinternal_key.txt','r');
			$this->userinfo = fgets($fh);
			fclose($fh);
			$this->initializeRequest();
		}
		
		public function initializeRequest(){
			$this->data=array(
				"fields" => array(
					"project" => array(
						"key"=>"LSREQ"
					),
					"issuetype"=>array(
						"name"=>"Dev Activity Request"
					),
					"customfield_12102"=>array(
						"id"=>"13098"
					)
				)
			);
		}
		
		public function createIssue($fields){
			$service_url = 'https://community.mycompany.com/rest/api/2/issue';
			$curl = curl_init($service_url);
			/* Assign fields to JIRA fields */
			$this->data['fields']['customfield_12099'] = "Requestor Name:{$fields['requestor_name']}\nRequestor Phone:{$fields['requestor_phone']}\nEmail Address:{$fields['Email_Address']}\nPurpose (Please identitfy your reasons for requesting the mycompany Development Lab (i.e.; running specific 
TEPS/scripts, DCC issues, interoperability issues, pre-certification or pre-verification preparation, 
etc).\n{$fields['purpose_please_identitfy_your_reasons_for_requesting_the_mycompany_development_lab_ie_running_specific_tepsscripts_dcc_issues_interoperability_issues_pre-certification_or_pre-verification_preparation_etc']}\n\nDevelopment Lab Equipment Requests: (If known, please list the mycompany equipment 
you would like to access (e.q., specify headend types, special test equipment, RF rack, special configurations or power consumption requirements, etc). 
\n{$fields['development_lab_equipment_requests_if_known_please_list_the_mycompany_equipment_you_would_like_to_access_eq_specify_headend_types_special_test_equipment_rf_rack_special_configurations_or_power_consumption_requirements_etc']}\n\nDevelopment Lab Station Request: (If known, please list the number of 
test stations needed including the exact requirements for each stations E. Q., specify the number of TVs, HDMI cables, RF connections etc 
needed)\n{$fields['Development_Lab_Station_Request_If_known_please_list_the_number_of_test_stations_needed_including_the_exact_requirements_for_each_stations_E_Q_specify_the_number_of_TVs_HDMI_cables_RF_connections_etc_needed']}\n\nEngineer Names:\n1. {$fields['engineer_names1']}\n2. 
{$fields['engineer_names2']}\n3. {$fields['engineer_names3']}\n\nAfter-hours usage:\n# of after-hours test nights: {$fields['_of_afterhours_test_nights']}\nDates of after-hours use: {$fields['dates_of_after-hours_use']}\nAmount: {$fields['afterhoursamount']}\n\nAfter-hours for Additional Benches:\n# of 
after-hours test nights: {$fields['additional_benches_of_afterhours_test_nights']}\n# of additional benches: {$fields['_of_additional_benches']}\nDates of after-hours use: {$fields['additional_benchesdates_of_after-hours_use']}\n\nAny Comments?\n{$fields['Any_Comments']}";
			$this->data['fields']["customfield_12122"]="LSREQ {$fields['supplier_name']}";
			$this->data['fields']['summary']="LSREQ {$fields['supplier_name']}";
			$this->data['fields']['customfield_10102'] = date("Y-m-d", strtotime($fields['lab_use_start_date']));
			$this->data['fields']['customfield_12103'] = date("Y-m-d", strtotime($fields['end_date']));
			$this->data['fields']['customfield_12495'] = "mmeyer";
			$this->data['fields']['customfield_12093'] = "(303) 661-3747";
			
			/* Create initial post request */
			$data = json_encode($this->data);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
				'Content-Type: application/json',                                                                                
				'Content-Length: ' . strlen($data),
				)                                                                       
			); 
			
			curl_setopt($curl, CURLOPT_USERPWD, $this->userinfo);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST"); 
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			$curl_response = curl_exec($curl);
			
			$response = json_decode($curl_response);
			print_r($response);
			curl_close($curl);
			return $response->id;
		}
		
		public function updateIssue($issueid, $fields){
			#Create secondary put request
			$service_url = "https://community.mycompany.com/rest/api/2/issue/{$issueid}";
			
			$curl = curl_init($service_url);
			$data=array();
			$data['fields'] = array();
			$data['fields']['reporter']=array();
			$data['fields']['reporter']['name']="mmeyer";
			$data['fields']['customfield_12099'] = "Requestor Name:{$fields['requestor_name']}\nRequestor Phone:{$fields['requestor_phone']}\nEmail Address:{$fields['Email_Address']}\nPurpose (Please identitfy your reasons for requesting the mycompany Development Lab (i.e.; running specific TEPS/scripts, DCC issues, interoperability issues, pre-certification or pre-verification preparation, etc).\n{$fields['purpose_please_identitfy_your_reasons_for_requesting_the_mycompany_development_lab_ie_running_specific_tepsscripts_dcc_issues_interoperability_issues_pre-certification_or_pre-verification_preparation_etc']}\n\nDevelopment Lab Equipment Requests: (If known, please list the mycompany equipment you would like to access (e.q., specify headend types, special test equipment, RF rack, special configurations or power consumption requirements, etc). \n{$fields['development_lab_equipment_requests_if_known_please_list_the_mycompany_equipment_you_would_like_to_access_eq_specify_headend_types_special_test_equipment_rf_rack_special_configurations_or_power_consumption_requirements_etc']}\n\nDevelopment Lab Station Request: (If known, please list the number of test stations needed including the exact requirements for each stations E. Q., specify the number of TVs, HDMI cables, RF connections etc needed)\n{$fields['Development_Lab_Station_Request_If_known_please_list_the_number_of_test_stations_needed_including_the_exact_requirements_for_each_stations_E_Q_specify_the_number_of_TVs_HDMI_cables_RF_connections_etc_needed']}\n\nEngineer Names:\n1. {$fields['engineer_names1']}\n2. {$fields['engineer_names2']}\n3. {$fields['engineer_names3']}\n\nAfter-hours usage:\n# of after-hours test nights: {$fields['_of_afterhours_test_nights']}\nDates of after-hours use: {$fields['dates_of_after-hours_use']}\nAmount: {$fields['afterhoursamount']}\n\nAfter-hours for Additional Benches:\n# of after-hours test nights: {$fields['additional_benches_of_afterhours_test_nights']}\n# of additional benches: {$fields['_of_additional_benches']}\nDates of after-hours use: {$fields['additional_benchesdates_of_after-hours_use']}\n\nAny Comments?\n{$fields['Any_Comments']}";
			$data['fields']['customfield_10102'] = date("Y-m-d", strtotime($fields['lab_use_start_date']));
			$data['fields']['customfield_12103'] = date("Y-m-d", strtotime($fields['end_date']));
			$data['fields']['customfield_12494'] = "mmeyer";
			$data['fields']['customfield_12495'] = "mmeyer";
			$data['fields']['customfield_12093'] = "(303) 661-3747";
			$data['fields']['customfield_10994'] = "(303) 359-8898";
			$data['fields']['customfield_10994'] = "(303) 661-3402";
			$data['fields']['customfield_12497'] = "ggordon";
			$data = json_encode($data);
			
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
				'Content-Type: application/json',                                                                                
				'Content-Length: ' . strlen($data),
				)                                                                       
			); 
			curl_setopt($curl, CURLOPT_USERPWD, $this->userinfo);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			$curl_response = curl_exec($curl);
			print_r($curl_response);
			curl_close($curl);
		}		
	}
	
	

	
	function addonRun(){
		if (!isset($_POST['This_submission_is_an_update'])){
			$LSREQ = new LSREQ();
			$id = $LSREQ->createIssue($_POST);
			$LSREQ->updateIssue($id, $_POST);		
		}
	}
	
