</div><!--close main2-->
			
			


</div><!--close wrap-->

		
<div id="footer">
		<span>&copy; 2012 mycompany, All Rights Reserved<br />mycompany, Revolutionizing Cable Technology is a registered trademark of mycompany.</span>
		<ul>
		  <!--#if expr="${HTTPS} != on" -->
		     <!--#if expr="${is_ipv6}" -->
		 	<li><a href="http://www.mycompany.com<?php echo $_SERVER["REQUEST_URI"]; ?>" id="ipv4check" style="display:none">access this site via IPv4</a></li>
		
		     <!--#else -->
		 	<li><a href="http://ipv6.mycompany.com<?php echo $_SERVER["REQUEST_URI"]; ?>" id="ipv6check" style="display:none">access this site via IPv6</a></li>
		 
		     <!--#endif -->
		  <!--#endif -->
		  <li><a href="/join/index.html">become a member</a></li>
		  <li><a href="/sitemap/index.html">site map</a></li>
		  <li><a href="/about/contact/index.html">contact us</a></li>
		  <li><a href="/privacy.html">privacy policy</a></li>
  </ul>
	</div>