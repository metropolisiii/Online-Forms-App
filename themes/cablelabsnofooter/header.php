<?php
	$d=htmlspecialchars($_SERVER['PHP_SELF']);
	$d=explode("/", $d);
	$dir="";
	if (count($d)>3)
		$dir="../";
?>
<?php
function curPageName() {
 return   "https://".$_SERVER["x_forwarded_host"] . $_SERVER["REQUEST_URI"];
}
?>
<link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet" type="text/css" />
<link href="https://fonts.googleapis.com/css?family=Roboto:400,400italic,700" rel="stylesheet" type="text/css" />
<script src="<?php echo $dir; ?>themes/<?php echo $theme; ?>/js/jquery.hover.js" type="text/javascript"></script>
<script src="<?php echo $dir; ?>themes/<?php echo $theme; ?>/js/init.js" type="text/javascript"></script>
<script src="<?php echo $dir; ?>themes/<?php echo $theme; ?>/js/main.js" type="text/javascript"></script>
<script>
	jQuery(document).ready( function($){
		 $('#i_share').click( function(){
			if( $('#follow_us').is(':visible')==false ){
				$('#follow_us, #search_form').hide();
				$('#follow_us').show();
				$('#i_share, #i_search').removeClass('on');
				$(this).addClass('on');
			}else{
				$('#follow_us, #search_form').hide();
				$(this).removeClass('on');
			}
			return false;
		});
		
		$('#i_search').click( function(){
			if( $('#search_form').is(':visible')==false ){
				$('#follow_us, #search_form').hide();
				$('#search_form').show();
				$('#i_share, #i_search').removeClass('on');
				$(this).addClass('on');
			}else{
				$('#follow_us, #search_form').hide();
				$(this).removeClass('on');
			}
			return false;
		});
	});
</script>
<div id="holder">
	<header>
		<div class="header_row1">
			<div class="page_width clearfix">
				<ul class='icons_social'>
					<li class='icon_share first-item' id='i_share'><a href='#' >Share</a></li>
					<li class="icon_rss"><a href="http://www.mycompany.com/feed">RSS</a></li>
					<li class="icon_search" id='i_search'><a href="#">Search</a></li>
				</ul>
				<ul id='top-menu' class='mini'>
				   <li class='menu-item'>
						<a href="http://www.mycompany.com/doczone" target="_blank">DocZone Login</a>
				   </li>
					<li class='menu-item'>
						<a href="http://www.mycompany.com/home/contact-us" target="_blank">Contact Us</a>
				   </li>
					<li class='menu-item'>
						<a href="http://www.mycompany.com/become-a-member/" target="_blank">Become a Member</a>
				   </li>
				 </ul>
			 </div>
		</div>
		<div class='header_row2'>
			<div id="follow_us" class="page_width" >
				<ul class="social_icon">
					<li class="icon_in first-item">
						<a target="blank" href="http://www.linkedin.com/company/14315">In</a>
					</li>
					<li class="icon_twitter">
						<a target="blank" href="https://twitter.com/mycompany">Twitter</a>
					</li>
					<li class="icon_youtube last-item">
						<a target="blank" href="http://youtube.com/user/thecableshow">Youtube</a>
					</li>
				</ul>
				<p>Follow Us:</p>
			</div>
			<div id="search_form" class="page_width" style="display: none;">
				<form class="form" method="get" action="http://www.mycompany.com/">
					<input type="text" onblur="if (this.value == '') {this.value = 'Type Search Terms';}" onfocus="if (this.value == 'Type Search Terms') {this.value = '';}" value="Type Search Terms" name="s">
				</form>
			</div>
		</div>
		
		<div id='header_row3'>
		   <div class="page_width  clearfix">
				<div id="logo">
					<a href="http://www.mycompany.com" target="_blank">mycompany</a>
				</div>
				<div id="nav_main">
					<nav>
						<ul id="nav-header" class="nav" >
							<li class="lev1">
								<a class="link" href="http://www.mycompany.com/about-mycompany/">About mycompany</a>
								<div class="sub_menu sub_menu1">
								<div class="menu_col">
								<p>Jump to Content About:</p>
								<ul class="">
								<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-53 first-item">
								<a href="http://www.mycompany.com/about-mycompany/the-board/">The Board</a>
								</li>
								<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-50">
								<a href="http://www.mycompany.com/about-mycompany/member-companies/">Member Companies</a>
								</li>
								<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-54">
								<a href="http://www.mycompany.com/about-mycompany/the-team/">The Team</a>
								</li>
								<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-51">
								<a href="http://www.mycompany.com/about-mycompany/social-responsibility/">Social Responsibility</a>
								</li>
								<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5393 last-item">
								<a href="http://www.mycompany.com/home/contact-us/">Contact Us</a>
								</li>
								</ul>
								</div>
								<div class="menu_col menu_col_last">
								<p>Jump to Content for:</p>
								<ul class="">
								<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-48 first-item">
								<a href="http://www.mycompany.com/about-mycompany/cable-industry/">Cable Industry</a>
								</li>
								<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-52">
								<a href="http://www.mycompany.com/about-mycompany/suppliers/">Suppliers</a>
								</li>
								<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-49 last-item">
								<a href="http://www.mycompany.com/about-mycompany/media/">Media</a>
								</li>
								</ul>
								</div>
								</div>
								</li>
							<li class="menu-item lev1">
								<a class="link" href="http://www.mycompany.com/innovations">Innovations</a>
								<div class="sub_menu">
									<div class="menu_col">
										<ul class="">
											<li class="menu-item">
												<a href="http://www.mycompany.com/innovations/featured-technology">Featured Technology</a>
											</li>
											<li class="menu-item">
												<a href="http://www.mycompany.com/innovations/patents-awards">Patents & Awards</a>
											</li>
											<li class="menu-item ">
												<a href="http://www.mycompany.com/innovations/showcase-opportunities">Showcase Opportunities</a>
											</li>
											<li class="menu-item ">
												<a href="http://www.mycompany.com/innovations/university-outreach/">University Outreach</a>
											</li>
										</ul>
									</div>
								</div>
							</li>
							<li class="menu-item lev1">
								<a class="link" href="http://www.mycompany.com/specs">Specifications</a>
								<div class="sub_menu">
									<div class="menu_col">
										<ul class="">
											<li class="menu-item">
												<a href="http://www.mycompany.com/specs/specification-search">Specifications Search</a>
											</li>
											<li class="menu-item">
												<a href="http://www.mycompany.com/specs/certification">Certification</a>
											</li>
										</ul>
									</div>
								</div>
							</li>
							<li class="menu-item lev1">
								<a class="link" href="http://www.mycompany.com/news-events">News & Events</a>
								<div class="sub_menu">
									<div class="menu_col">
										<ul class="">
											<li class="menu-item">
												<a href="http://www.mycompany.com/news-events/news">News</a>
											</li>
											<li class="menu-item">
												<a href="http://www.mycompany.com/news-events/events">Events</a>
											</li>
											<li class="menu-item">
												<a href="http://www.mycompany.com/news-events/blog">Blog</a>
											</li>
										</ul>
									</div>
								</div>
							</li>
							<li class="menu-item lev1">
								<a class="link" href="http://www.mycompany.com/careers">Careers</a>
								<div class="sub_menu">
									<div class="menu_col">
										<ul class="">
											<li class="menu-item">
												<a href="http://www.mycompany.com/careers/why-mycompany">Why mycompany</a>
											</li>
											<li class="menu-item">
												<a href="http://www.mycompany.com/careers/join-our-team">Join Our Team</a>
											</li>
											<li class="menu-item ">
												<a href="http://www.mycompany.com/careers/community-involvement">Community Involvement</a>
											</li>
											<li class="menu-item">
												<a href="http://www.mycompany.com/careers/university-outreach">University Outreach</a>
											</li>
										</ul>
									</div>
								</div>
							</li>
							<li class="menu-item lev1">
								<a class="link" href="http://www.mycompany.com/resources">Resources</a>
								<div class="sub_menu">
									<div class="menu_col">
										<ul class="">
											<li class="menu-item">
												<a href="http://www.mycompany.com/resources/shared-services">Shared Services</a>
											</li>
											<li class="menu-item">
												<a href="http://www.mycompany.com/resources/development-lab">Development Lab</a>
											</li>
											<li class="menu-item">
												<a href="http://www.mycompany.com/resources/digital-certificate-issuance-service">Security</a>
											</li>
										</ul>
									</div>
								</div>
							</li>
						</ul>
					</nav>
				</div>
		   </div>
		</div>	
	</header>
</div>