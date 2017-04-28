<div class="navbar no-print navbar-inverse" role="navigation">
	 <div id="logo">
			<img width="290" height="85" src="images/<?php echo LOGO; ?>">
		</div>
	<div class="container">
		 <div class="navbar-header">
			<a class='navbar-brand' href='/forms/conferences/'>mycompany Conferences User Account</a>
		 </div>
		 <div >
			<?php if (!$_SESSION['conf_user']): ?>
				<form action='scripts/login.php' method='post' class="navbar-form navbar-right" role="form">
					<div class="form-group">
						<input class="form-control" name='email' type="text" placeholder="Email">
					</div>
					<div class="form-group">
						<input class="form-control" name='password' type="password" placeholder="Password">
					</div>
					<button class="btn btn-success" type="submit">Sign in</button>
					<button class="btn btn-success" type="button" onclick="window.location='reset_password.php'">Forgot Password</button>
				</form>
			<?php else: ?>
				<div class="float_right">
					<div id="logged_in_as">Logged in as <?php echo $_SESSION['conf_user']; ?></div>
					<div id="logout"><button class="btn btn-success" type="button" onclick="window.location='logout.php'">Log out</button></div>	
				</div>						
			<?php endif; ?>
		 </div>
	</div>
</div>