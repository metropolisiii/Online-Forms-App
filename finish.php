<?php
	/**
	* If a form requires a payment, the user is brought to this page once the transaction is made.
	*
	* @author Jason Kirby <jkirby1325@gmail.com>
	*
	*/
	session_start();
	include_once("scripts/settings.php");
	include("includes/header.php");
	
	$_GET=sanitize($_GET);
	if ($_GET['d']=='true')
		$_SESSION['error']='Payment was declined! Please try to make the payment again.';
	else
		$_SESSION['error']='Payment has been made. If there are any issues with the payment, we will contact you shortly';
	$result=mysql_query("SELECT fb_savedforms.name as name, fb_savedforms.url as url2, user_form.pagename as url, user_form.id as id, user_form.url as link, thank_you_page_message FROM user_form INNER JOIN fb_savedforms on fb_savedforms.id = formid WHERE user_form.url='".$_GET['link']."'");
	
	$url2=mysql_fetch_array($result);
	$ref=explode("/",$_SERVER['HTTP_REFERER']); //Need to get the actual name of the page the form so that when the form is loaded for later use, we know what values to put into that form (and in case the name gets changed manually)
	$referer=$ref[count($ref)-1];
	$referer=explode("?", $referer);
	$referer=$referer[0];
	$full_referer="";
	if (strpos($url2['name'], "Conference") !== false){
		$form_email='c.santana-hudson@mycompany.com';
		$headers = 'From: '.$form_email . "\r\n" .
							'Reply-To: '.$form_email. "\r\n" .
							'Content-Type: text/html; charset=ISO-8859-1'. "\r\n".
							'X-Mailer: PHP/' . phpversion();

		$signature="

Candice Santana-Hudson<br/>
Administrative Assistant, Communications & Events mycompany | 858 Coal Creek Cir. | Louisville, CO 80027 Office (303) 661-3888 Mobile (720) 261-1563 c.santana-hudson@mycompany.com<br/>
<br/>
Stay up to date with mycompany: Read the blog and follow us on Twitter.
";
	}
	else{
		$form_email='j.smith@mycompany.com';
		$headers = 'From: '.$form_email . "\r\n" .
							'Reply-To: '.$form_email. "\r\n" .
							'Content-Type: text/html; charset=ISO-8859-1'. "\r\n".
							'X-Mailer: PHP/' . phpversion();

		$signature="

John Smith<br/>
Events Manager | j.smith@mycompany.com | Office: 303-661-3751<br/><br/>
Stay up to date with mycompany: Read the blog and follow us on Twitter.
";
	
	}
	for ($i=3; $i<count($ref); $i++)
		$full_referer.="/".$ref[$i];
	
	if (!empty($url2['thank_you_page_message'])){ //If the admin has created a custom thank you message
		
		if (!empty($_GET['d']) && $_GET['d']==="true"){
			$message="Submission of ".$url2['name']." has failed because your credit card was declined. Please call your credit card company for assistance. ";
		    $subject=$url2['name']."  Payment Declined";
		}
		else{
			$subject="Thank You For Filling Out the Registration for ".$url2['name'];
			$message =str_replace("[form_link]", "<a href='".$url2['url']."?q=".$url."' target='_parent'>".$url2['url']."?q=".$url."</a>", htmlspecialchars_decode($url2['thank_you_page_message']));
		
		}
		echo $message;
	}	
	else{ //Else use a hard coded thank you message
		if (!empty($_GET['d']) && $_GET['d']==="true"){
			$message="Submission of ".$url2['name']." has failed because your credit card was declined. Please call your credit card company for assistance. ";
		    $subject=$url2['name']."  Payment Declined";
		}
		else{
			$subject="Thank You For Filling Out the Registration for ".$url2['name'];
			$message="Thank you for submitting your registration for ".$url2['name'].".  Once you have received your payment confirmation, your registration is considered complete.  
			
			If your credit card is declined, please call your credit card company for assistance before re-processing payment.
		";
		}
	}
	
	

	if (!empty($_GET['email']))
		mail ($_GET['email'], $subject, $message.$signature, $headers);
    if (!empty($_GET['d']) && $_GET['d']=="true"){
		echo "<script>";
		echo "window.location = 'scripts/authpayment.php?id=".$url2['id']."&a=".$_GET['amount']."&d=true'";  
		echo "</script>";
	}
	
	
?>

<?php if (isset($_SESSION['conf_user']))
	echo '<meta http-equiv="refresh" content="0;url=conferences/index.php">';
 else if ($_GET['url'] && $_GET['url'] != 'http://')
	echo '<meta http-equiv="refresh" content="0;url='.$_GET['url'].'">';
?>


