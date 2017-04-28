<?php
/**
* Generic script to check, format, and send an email
* @author Jason Kirby <jkirby1325@gmail.com>
*/
include("settings.php");
include("../misc/functions.php");
include_once("/var/www/forms_app/classes/logController.php");
$log=new logController();
$_POST=sanitize($_POST);
$replyto=explode(";", $_POST['replyto']);
foreach($replyto as $key=>$sendto){
	if ($key==0)
		$from=$sendto;
	else
		$cc.=$sendto.",";
}
$cc=substr($cc, 0, -1);

if(isset($_POST['email'])) {
	$html="<div id='form_container'>";
	$html.="<div class='form_head'><img src='../images/close.png'/></div>";
	
	$email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
	if(!isset($_POST['email']) ||
       !isset($_POST['subject']) ||
       !isset($_POST['message'])){
       $html.= 'We are sorry, but there appears to be a problem with the form you submitted.';      
	}
	else if (!preg_match($email_exp,$_POST['email'])) {
		$html.= 'The Email Address you entered does not appear to be valid.';
	}
	else{
		  $bad = array("content-type","bcc:","to:","cc:","href");
		  $message=str_replace($bad,"", $_POST['message']);
		  $message=str_replace("\\r\\n", "\r\n", $message);
		  $message=str_replace("\'","'", $message);
		  $headers = 'From: '.$from. "\r\n".
					 'Reply-To: '.$from."\r\n" .
					 'Cc: '.$cc."\r\n" .
					 'X-Mailer: PHP/' . phpversion();
					 mail($_POST['email'], $_POST['subject'], $message, $headers); 
		$log->log(" Sent email to ".$_POST['email']." with subject of ".$_POST['subject']);
		$html.= "Email has been sent.";					 
	}
	 
	 $html.= "</div>";
}
?>
<!--------------------------------------------------------------------------Presentation------------------------------------------------------------------------------->
<html>
<head>
<title>Mail Complete</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1252">
<meta http-equiv="Cache-control" content="no-cache">
<link type="text/css" rel="stylesheet" href="../themes/<?php echo $theme; ?>/css/styles.css">
<link type="text/css" rel="stylesheet" href="css/styles.css">
<script src="../js/jquery-1.3.2.min.js" type="text/javascript"></script>
<script>
$('.form_head img').live('click', function(){
		window.location='../review.php?id=<?php echo $_POST['id']; ?>';
	});
</script>
</head>
<body>
<?php echo $html; ?>
</body>
</html>