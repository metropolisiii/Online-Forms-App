<?php
/**
* Get list of countries from database
* @author Jason Kirby <jkirby1325@gmail.com>
*/

include("settings.php");
include("connect.php");

$htm = '<option value=""></option>';
$result = mysql_query("SELECT country FROM regions order by country");
while ($country = mysql_fetch_array($result)){
	if ($country['country']=='United States')
		$first='<option value="'.str_replace(" ","_", $country['country']).'">'.$country['country'].'</option>';
	else
		$html.='<option value="'.str_replace(" ","_", $country['country']).'">'.$country['country'].'</option>';
}
print $htm.$first.$html;