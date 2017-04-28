<?php

define("SAASID","62c75ae6-0a8a-4e90-a9f4-d038c673fb3f");

/**
 * Look up idpId for SSO - implement me!
 */
function getIdpId()
{
    return "mycompany.com";
}

$idpId = urlencode(getIdpId());
$saasId = urlencode(SAASID);
if (!isset($_GET['tokenid']))
header("Location: https://sso.connect.pingidentity.com/sso/sp/initsso?saasid=$saasId&idpid=$idpId");

?>

<?php

/**
 * Requires libcurl to be installed. For more info, see:
 * http://us.php.net/manual/en/book.curl.php
 */

/**
 * Create a new user session for this user, identified by "$username",
 * by the identity provider identified by "$idpId"
 *
 * Implement me!! Must validate that subject belongs to this idpId
 */
function createUserSession($username, $idpid)
{
   echo "<p>Welcome, ".strip_tags($username)."</p>";
}

$tokenid = $_GET['tokenid'];
$agentid = $_GET['agentid'];

$restAuthUsername = '1f76ec0b-d68d-49a7-b50e-6276498b4980';
$restApiKey = '1SecretPassword';

$sso_service = "https://sso.connect.pingidentity.com/sso/TXS/2.0/1/$tokenid";
$c = curl_init($sso_service);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($c, CURLOPT_COOKIE, "agentid=$agentid;");
curl_setopt($c, CURLOPT_USERPWD, "$restAuthUsername:$restApiKey");
$response = curl_exec($c);
curl_close($c);
$responseData = json_decode($response, true);
print_r($responseData);
createUserSession($responseData['pingone.subject'],
        $responseData['pingone.idp.id']);
?>
