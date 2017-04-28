<?php
/**
* Connects to database
* @author Jason Kirby <jkirby1325@gmail.com>
*/

	mysql_connect($db_host,$db_user,$db_pass);

	mysql_select_db($db_name) or die( "Unable to select database");
?>