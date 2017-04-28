<?php
	$title='Conference User Account';
	include_once("includes/connect.php");
	unset($_SESSION['conf_user']);
	unset($_SESSION['type']);
	unset($_SESSION['superadmin']);
?>
<body>
	<?php include('includes/nav-header.php'); ?>
	<div id="main" class="main_container">
		<div class="content">
			<h4>You are now logged out</h4>
		</div>
	</div>
</body>
</html>