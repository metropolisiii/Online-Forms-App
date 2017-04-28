<?php
/**
* Get list of countries from database
* @author Jason Kirby <jkirby1325@gmail.com>
*/
include("../misc/functions.php");
include("settings.php");
include("connect.php");
mysql_set_charset('utf8');
$region_id=840;
if (!empty($_GET) && !empty($_GET['country'])){
	$_GET=sanitize($_GET);
	$country=str_replace("_"," ", $_GET['country']);
	$result=mysql_query("SELECT id from regions WHERE country='".$country."'");
	$id=mysql_fetch_array($result);
	$region_id=$id['id'];
}
$html = '<option value=""></option>';
$result = mysql_query("SELECT name FROM subregions WHERE region_id=".$region_id." order by name");
while ($state = mysql_fetch_array($result)){
	$html.='<option value="'.str_replace(" ","_", $state['name']).'">'.$state['name'].'</option>';
}
print $html;