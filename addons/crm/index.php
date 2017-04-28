<?php
	require_once dirname(__FILE__).'/DynamicsCRM2011.php';
	include dirname(__FILE__).'/DynamicsCRM2011.config.php';
	
	class CRM{
		private $contact_info = array();
		private $account_info = array();
		private $crmConnector;
		
		function __construct($discoveryServiceURI, $organizationUniqueName, $loginUsername, $loginPassword, $post){
			try{
				$this->crmConnector = new DynamicsCRM2011_Connector($discoveryServiceURI, $organizationUniqueName, $loginUsername, $loginPassword);
			}catch (Exception $e) {
				  return false;
			 }
			foreach ($post as $key=>$value){
				$postParts = explode(":", $key);
				if ($postParts[0] === "crm"){
					if ($postParts[1]){
						$entity = $postParts[1];
						switch($entity){
							case "contact":
								$this->contact_info[$postParts[2]]=$_POST[$value];
								break;
							case "account":
								$this->account_info[$postParts[2]]=$_POST[$value];
								break;
						}
					}
				}
			}
		}
		
		public function getAccount($account){
			$accountQueryXML=
<<<END
<fetch version="1.0" output-format="xml-platform" mapping="logical" distinct="false">
<entity name="account">
<attribute name="name"/>
<attribute name="accountid"/>
<order attribute="name" descending="false"/>
<filter type="and">
<condition attribute="name" operator="eq" value="{$account}"/>
</filter>
</entity>
</fetch>
END;
			$accountData = $this->crmConnector->retrieveMultiple($accountQueryXML);
			return $accountData;
		}
		
		public function getContact($contact){
			$contactQueryXML=
<<<END
<fetch version="1.0" output-format="xml-platform" mapping="logical" distinct="false">
<entity name="contact">
<attribute name="fullname"/>
<attribute name="telephone1"/>
<attribute name="contactid"/>
<order attribute="fullname" descending="false"/>
<filter type="and">
<condition attribute="emailaddress1" operator="eq" value="{$contact}"/>
</filter>
</entity>
</fetch>
END;
			$contactData = $this->crmConnector->retrieveMultiple($contactQueryXML);
			return $contactData;
		}
		
		public function getOrCreateAccount($account){
			while (true){
				$accountData = $this->getAccount($account);
				if (!empty($accountData->Entities)){
					foreach ($accountData->Entities as $acc)
						return $acc;
				}
				$acc = new DynamicsCRM2011_Account($this->crmConnector);
				foreach ($this->account_info as $field => $value){
					$acc->$field=$value;
				}
				$acc->name = $account;
				$accountId = $this->crmConnector->create($acc);
			}
		}	
		
		public function getOrCreateContact(){
			try{
				$contact = new DynamicsCRM2011_Contact($this->crmConnector);
			}catch (Exception $e) {
				  return false;
			 }
			$contactData = $this->getContact($this->contact_info['emailaddress1']);
			if (!empty($contactData->Entities)){
				return true;
			}
			try{
				$con = new DynamicsCRM2011_Contact($this->crmConnector);
			}catch (Exception $e) {
				  return false;
			 }
			foreach ($this->contact_info as $field=>$value){
				if ($field == "company"){
					$companyID = $this->getOrCreateAccount($value);
					$contact->parentcustomerid = $companyID;
				}
				else
					$contact->$field=$value;
			}
			$contactId = $this->crmConnector->create($contact);
			return true;
		}	
	}

	function CRMError(){
		mail("jason.kirby@mycompany.com","Issue connecting to CRM", "There was an issue connecting to CRM. Please check it out.");
	}
	
	function addonRun(){
		global $discoveryServiceURI;
		global $organizationUniqueName;
		global $loginUsername;
		global $loginPassword;
		$crm = new CRM($discoveryServiceURI, $organizationUniqueName, $loginUsername, $loginPassword, $_POST);
	
		if ($crm){
			$contactId = $crm->getOrCreateContact();
			if(!$contactId)
				CRMError();
		}
		else
			CRMError();
	}

?>