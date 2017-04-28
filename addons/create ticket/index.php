<?php
class CreateTicket{
	private $email, $assignTo, $user;
	function __construct($user){
		$this->email = "cl_helpdesk@mycompany.com";
		$this->assignTo = "r.dixon@mycompany.com,m.yahna@mycompany.com";
		$this->user = $user;
	}
	
	public function create($url){
		$fullurl = $url;
		$to = $this->email;
		$subject = "VPN Access Request for ".$this->user;
		$message = $this->user." is requesting VPN access. Please see {$fullurl} for full information.";
		$headers = 'From: C.Internal@mycompany.com' . "\r\n" .
			'Reply-To: C.Internal@mycompany.com' . "\r\n" .
			'Cc: '.$this->assignTo . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		mail ($to, $subject, $message, $headers);		
	}
	private static function generateEmailConfirmationCode() {
	  $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	  $code = '';
	 
	  for ($i = 0; $i < 10; $i++) {
		$code .= $chars[ rand( 0, strlen( $chars ) - 1 ) ];
	  }
	  return $code;
}
	public function storeInfoInTable($id, $database, $table ){
		$results = mysql_query("SELECT id from {$database}.{$table} WHERE user_form_id={$id}") or die(mysql_error());
		if (mysql_num_rows($results) == 0){
			//Generate random verification code
			$generatedKey = $this->generateEmailConfirmationCode();
			mysql_query("INSERT INTO {$database}.{$table} (user_form_id, sponsor_accepted, end_user_accepted, verification_code) VALUES ({$id}, 0, 0, '{$generatedKey}')") or die(mysql_error());
			return mysql_insert_id();
		}
		return false;
	}
	
	public function emailVerificationLink($formid, $vpnid){
		
		
		//Get the form answers in order to get the email of the requestor and sponsor
		$result = mysql_query("SELECT fa.field_id, fa.response FROM forms_app.form_answers fa WHERE field_id in ('End_User','Email','Sponsoring_mycompanyKyrio_Personnel','Sponsoring_mycompanyKyrio_Personnel_Email_Address') AND user_form_id = {$formid}");
		
		while ($row = mysql_fetch_array($result))
			$$row['field_id'] = $row['response'];
		
		//Get verification code
		$result = mysql_query("SELECT verification_code FROM VPN.vpn_form WHERE id = {$vpnid}");
		$row = mysql_fetch_array($result);
		$verification_code = $row['verification_code'];

		//Email the verification
		$sponsor_to = $Sponsoring_mycompanyKyrio_Personnel_Email_Address;
		$requestor_to = $Email;
		$subject = "Verify Your Request for mycompany VPN Access";
		$tac = "
		<ol>
<li><u>Services.</u> mycompany/Kyrio will provide VPN connectivity and a mechanism for the exchange of
security keys to End User, to allow communications between mycompany/Kyrio and the End User.</li>
<li>mycompany/Kyrio may provide connectivity between different End Users, but shall not be required to do so.
mycompany/Kyrio reserves the right to change the services provided at any time with or without notice to
End User.</li>
<li>mycompany/Kyrio shall use reasonable efforts to maintain the VPN but shall be under no obligation to do
so. mycompany/Kyrio shall have the right to selectively limit VPN service at any time, with or without
notice to End User.</li>
<li>End User agrees that its use of the VPN shall be for the Reason for Access and limited to What Needs to be Accessed
 as stated above.  Any other use of the VPN by End User is prohibited.</li>
<li>End User’s User ID and password are for use by End User only and shall not be shared with other
employees of End User’s employer. So that we may know who is on our network, please have each
person using the VPN send in a different completed copy of this VPN Rules sheet with their
identifying information filled in the boxes above.</li>
<li><u>Costs.</u> mycompany/Kyrio is providing these services free of charge at this time. mycompany/Kyrio shall not be responsible 
or liable for any costs incurred by the End User as a result of these VPN rules or of End User’s connection to the VPN.</li>
<li><u>Hardware/Software Requirements for End User.</u> End User agrees to comply with the
Requirements for Client-to-site or Site-to-site (as applicable)” described in the mycompany VPN User
Guide, which includes Kyrio, as is made available to End User.</li>
<li><u>Measurement of Traffic on the VPN.</u> mycompany/Kyrio and End User shall each have the right to
measure and analyze the VPN traffic of End User including its content. End User shall have no
access to the VPN traffic of any other End User, unless such other End User has notified mycompany/Kyrio
in writing of its consent.</li>
<li>Each party shall own such data as it may collect, and shall provide the other party with a copy of any
such data. mycompany may share End User-specific data with its member companies.</li>
<li><u>Liability.</u> mycompany/Kyrio assumes no responsibilities with respect to the security, the use of or
performance of the VPN. mycompany/Kyrio makes no representation or warranty that the VPN will
perform as expected by your organization or for any particularly purpose.</li>
<li>End User agrees to indemnify and hold mycompany/Kyrio harmless from any and all claims arising out of
End User’s use of the VPN, including but not limited to: (a) transmission of information to or from
the VPN, (b) loss or corruption of data, (c) unauthorized access to VPN, (d) the introduction of or
transmission by any computer virus through the VPN and (e) third party claims of infringement
caused by End User’s use of the VPN or the activities of End User hereunder.
IN NO EVENT SHALL mycompany BE LIABLE FOR ANY SPECIAL, INCIDENTAL, OR
CONSEQUENTIAL DAMAGES.</li>
<li><u>Protection from Software Threats.</u> All computers participating on the VPN are expected to be
patched for operating system and application security, and End User and End User’s employer
should make all reasonable attempts to prevent the spreading of viruses, spyware, and other malware
via the use of current, self-updating antivirus and antispyware agents. mycompany/Kyrio reserves the right
to disconnect the VPN if threats to network security are detected.</li>
<li><u>General Provisions.</u> Use of the VPN shall not be construed (i) to require any additional
obligations on the part of either party including without limitation any future consultation related to
any matter hereunder, (ii) to form or create a joint venture or partnership between mycompany/Kyrio and the
End User. Nothing contained in these Rules shall be deemed to grant, either directly or by
implication, estoppel or otherwise, any license under any patents, patent applications, copyrights
mask work or other intellectual property rights.Use of the VPN may not be assigned by either party
without the prior written consent of the other. This Agreement sets forth the entirety of the parties' understanding as it
relates to this subject matter.  It may be amended only in a writing agreed to by both parties, and shall be
governed by the laws of the State of Colorado as they apply to contracts executed and to be performed in Colorado
without giving effect to the principles of conflicts of law.</li>
<li><u>Term and Termination.</u> These VPN Rules shall be effective from the dates that End User has
indicated in the box above that End User desires to use the VPN. After termination, the obligations
of each party under Section 10 shall continue for five years. Either party shall be entitled to terminate
the use of the VPN at any time on written notice to the other.</li>
<li><u>Export Control.</u> The export of commodities or technical data from the United States of
America and/or the re-export from foreign countries of commodities or technical data or direct
products of technical data of United States of America origin, may be conditioned upon the issuance
of an export license by the government of the United States of America. End User represents that it
will not export or re-export any commodities or technical data or direct products of technical data in
furtherance of or as a result of its VPN connection unless and until it has complied in all respects
with the United States of America Export Control Regulations. End User shall indemnify and hold
mycompany/Kyrio harmless for any violation of the export control laws.</li>
</ol>
	";
	
		
		$requestor_message = $End_User.", <br/><br/>{$Sponsoring_mycompanyKyrio_Personnel} has recently made a request regarding mycompany/Kyrio VPN access for you. Below are the Terms of Service. In order to obtain VPN access, you will need to accept them by clicking the link below. {$tac} Please go to <a href='https://apps.mycompany.com/forms/forms/VPN Access.html?code={$verification_code}&user=requestor'>https://apps.mycompany.com/forms/forms/VPN Access.html?code={$verification_code}&user=requestor</a> to agree to this request.";
		$headers = 'From: no-reply@mycompany.com' . "\r\n" .
			'Content-Type: text/html; charset=ISO-8859-1\r'. "\r\n".
			'X-Mailer: PHP/' . phpversion();
		mail ($requestor_to, $subject, $requestor_message, $headers);				
	}
}
function addonRun(){
	global $full_referer;
	global $url;
	global $id;
	
	if (isset($_POST['sponsor_accepted']) && $_POST['sponsor_accepted'] == 'true'){
		$VPN_message = "We have recorded your acceptance of this mycompany/Kyrio VPN Agreement. ";
		$VPN_message.=" The requestor has also accepted this agreement. We will being processing your request and contact you with any additional information.";
		$result = mysql_query("SELECT pagename, url, response from user_form uf INNER JOIN form_answers fa ON uf.id=fa.user_form_id WHERE user_form_id=".$id." AND field_id='End_User'");
		$vpnrec = mysql_fetch_array($result);
		$ticket = new CreateTicket($vpnrec['response']);
		$ticket -> create("https://www.mycompany.com/forms/forms/".$vpnrec['pagename']."?q=".$vpnrec['url']);
		echo $VPN_message;
	}
	
	if (!isset($_POST['url']) || $_POST['url'] == ""){
			
		$ticket = new CreateTicket($_POST['End_User']);
		$stored = $ticket -> storeInfoInTable($id, 'VPN', 'vpn_form');
		if ($stored){
			//Email the sponsor and the requestor a verification link
			$email = $ticket -> emailVerificationLink($id, $stored);
		}
		//$ticket -> create($full_referer."?q=".$url);
		
	}
}