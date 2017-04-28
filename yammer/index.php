 <?php
ini_set("display_errors","1");

if($_SERVER['HTTP_REFERER'] != "https://community.mycompany.com/wiki/display/DISCUSSION/Discussions+Home") {
	die();
	exit();
}

/* require the user as the parameter */

if(isset($_GET['user'])) {

	/* soak in the passed variable or set our own */
	$user = filter_var($_GET['user'], FILTER_SANITIZE_STRING); //no default
	$group=filter_var($_GET['group'], FILTER_SANITIZE_STRING);
	$feedid=$_GET['group_id']; 
	$delete=$_GET['delete'];
	
	/* connect to the db */
	$link = mysql_connect('localhost','yammer','yummyyamyam423$') or die('Cannot connect to the DB');
	mysql_select_db('community_yammer',$link) or die('Cannot select the DB');
	if ($_GET['delete']){
		$query="DELETE FROM users_groups WHERE user='".$user."' and feedid=".$feedid;
		mysql_query($query);
		echo $_GET['callback'].'()';
		exit;
	}
	/* grab the posts from the db */
	
		$query = "SELECT users_groups.group, feedid FROM users_groups WHERE user='".$user."' ";
		if (isset($_GET['group_id']))
			$query.=" AND feedid=".$feedid;
		$query." ORDER BY users_groups.group;";
	
	$result = mysql_query($query,$link) or die('Errant query:  '.$query);

	/* create one master array of the records */
	$groups = array();
	if (isset($_GET['group_id']) && mysql_num_rows($result)==0){
	   mysql_query("INSERT INTO users_groups (user, users_groups.group, feedid) VALUES ('".$user."','".$group."', ".$feedid.")") or die(mysql_error());
	   echo $_GET['callback'].'({group:"'.$group.'", feedid:"'.$feedid.'"})';
	   exit;
	}
	else if(mysql_num_rows($result) && !isset($_GET['group_id'])) {
		while($group = mysql_fetch_assoc($result)) {
			$groups[] = array('group'=>$group);
		}
	}
	else{
		 echo $_GET['callback'].'({success:"false"})';
		 exit;
	}

	/* output in necessary format */
	header('Content-type: application/json');
	$json = json_encode(array('groups'=>$groups));
	echo $_GET['callback']."(".$json.")";
	
	/* disconnect from the db */
	@mysql_close($link);
}
?>