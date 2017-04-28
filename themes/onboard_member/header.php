<?php
	$d=htmlspecialchars($_SERVER['PHP_SELF']);
	$d=explode("/", $d);
	if (count($d)>3)
		$dir="../";
?>
<script src="<?php echo $dir; ?>themes/<?php echo $theme; ?>/js/jquery.hover.js" type="text/javascript"></script>
<script src="<?php echo $dir; ?>themes/<?php echo $theme; ?>/js/init.js" type="text/javascript"></script>
<div id="wrap">
	<div id="header">
		<div class="logo"><a href="/"><img src="<?php echo $dir; ?>themes/<?php echo $theme; ?>/images/logo.gif" alt="mycompany" width="224" height="55"/></a></div>
			<ul class="nav-top">
				<li><a href="https://www.mycompany.com/doczone" title="DocZone Login">Login</a></li>
				<li><a href="/about/careers/" title="mycompany Careers">Careers</a></li>
				<li><a href="/supporttools/" title="Support Tools">Support Tools</a></li>
				<li><a href="/about/" title="About mycompany">About mycompany</a></li>
			</ul>

			<div id="nav-main">
<div class="line-top"></div>

<!--tab menu-->
<ul id="menu" >
<!--technologies tab-->
<li class="mega">
	<h2><a href="/technologies/" class="tab-tech"></a></h2>
	<div class="innertube" >

     <table class="tbl-tech">
     <tr>	 
     <td><td>	 
     <td style="width:25%; vertical-align:top"><h3><a href="/cablemodem/index.html">High-Speed Data Services</a></h3>
	 <span class="text">High-speed data services over cable&rsquo;s hybrid fiber-coaxial plant enable cable customers to access information at lightning fast speeds&nbsp;&hellip;&nbsp;</span><br />
	 <a href="/cablemodem/index.html" class="btn">explore</a></td>
	 
	 <td></td>	 
     <td style="width:25%; vertical-align:top"><h3><a href="/opencable/index.html">Advanced Video Services</a></h3>
	 <span class="text">There are more ways of interacting with your television than just clicking on the remote control. Tru2way&reg; is one enabling technology that cable operators are adding to their headends</span>&nbsp;&hellip;&nbsp;<br />
	 <a href="/opencable/index.html" class="btn">explore</a></td>
	
     <td></td>	 
	 <td style="width:25%; vertical-align:top"><h3><a href="/projects/go2/index.html">Cable Information Services</a></h3>
     <span class="text">Working with cable company experts, CIS develops standard interfaces and provides PaaS (Platform as a Service) to enable information exchange&nbsp;&hellip;&nbsp;</span><br />
     <a href="/projects/go2/index.html" class="btn">explore</a></td>
     </tr>
	 
     <tr>	 
     <td></td>	 
     <td><h3><a href="/packetcable/index.html">Digital Voice Services</a></h3>
     <span class="text">Cable's digital voice delivers residential and small/medium business voice services using a managed Internet Protocol-based data network</span>&nbsp;&hellip;&nbsp;<br />
     <a href="/packetcable/index.html" class="btn">explore</a></td>

     <td></td>	 
     <td>
     <h3><a href="/advancedadvertising/index.html">Advertising &amp; Interactive Services</a></h3>
     <span class="text">By improving advanced advertising technologies such as EBIF&trade;, mycompany&reg; is providing the cable industry new and powerful ways for advertisers to deliver their message&nbsp;&hellip;&nbsp;</span><br />
     <a href="/advancedadvertising/index.html" class="btn">explore</a></td>

     <td></td>
     <td><h3><a href="/business/index.html">Business Services</a></h3>
     <span class="text">Providing voice, video, and high-speed data services to small and medium businesses is accelerating new revenue growth in the cable industry&nbsp;&hellip;&nbsp;</span><br />
     <a href="/business/index.html" class="btn">explore</a></td>
     </tr>
	 
	  <tr>	 
     <td></td>	 
     <td><h3><a href="/wireless/index.html">Wireless</a></h3>
     <span class="text">The cable industry is aggressively pursuing new wireless opportunities with competitive product offerings for data services. In</span>&nbsp;&hellip;&nbsp;<br />
     <a href="/wireless/index.html" class="btn">explore</a></td>
     
     <td> </td>	 
     <td><h3><a href="/peerconnect/index.html">PeerConnect</a></h3>
     <span class="text">Connecting cable operators and their partners together for voice, text, SMS-MMS messaging and video calling services&nbsp;&hellip;&nbsp;</span><br />
     <a href="/peerconnect/index.html" class="btn">explore</a></td>
	 
	 <td><!-- <img src="/graphics/icon-advertising.gif" alt="icon-advertising" width="50" height="50"/> --></td>	 
     <td><!-- 
     <h3><a href="/advancedadvertising/index.html">Advertising &amp; Interactive Services</a></h3>
     <span class="text">By improving advanced advertising technologies such as EBIF&trade;, mycompany&reg; is providing the cable industry new and powerful ways for advertisers to deliver their message&nbsp;&hellip;&nbsp;</span><br />
     <a href="/advancedadvertising/index.html" class="btn">explore</a> --></td>

     <td><!-- <img src="/graphics/icon-commercial.gif" alt="icon-commercial" width="50" height="50"/> --></td>
     <td><!-- <h3><a href="/business/index.html">Business Services</a></h3>
     <span class="text">Providing voice, video, and high-speed data services to small and medium businesses is accelerating new revenue growth in the cable industry&nbsp;&hellip;&nbsp;</span><br />
     <a href="/business/index.html" class="btn">explore</a> --></td>
     </tr>
	 
	 
	
     </table>


     </div><!--close innertube-->
</li>

<!--specifications tab-->
<li class="mega">
	<h2><a href="/specifications/" class="tab-spec<?php echo $spec; ?>"></a></h2>
	<div class="innertube" >
    <table class="tbl-spec" >
    	<tr>
    	<td>
    	 <h3><a href="/cablemodem/specifications/index.html">DOCSIS&reg;</a></h3>
         <p>Cable television operators have transitioned from a traditional core business of entertainment programming to a position&nbsp;&hellip;&nbsp;</p></td>
    	<td>
    	 <h3><a href="/projects/metadata/specifications/index.html">VOD Metadata</a></h3>
         <p>The Metadata project aims to specify the metadata and interfaces for distribution of content from multiple content providers to cable operators&nbsp;&hellip;&nbsp;</p></td>
		 
		 <td><h3><a href="/wireless/specifications/index.html">Wireless</a></h3>
		The mycompany Wi-Fi specifications are designed to help bring cable operator broadband services to mobile data subscribers. Subscribers&nbsp;&hellip;&nbsp;</td>
		
		 
    	<td rowspan="3">
    	  <h3>Search</h3>
    	  Search the Product Specification database by entering keywords or phrases below.
          <form name="fm-search" method="get" id="fm-search" action="/search/specsearch.html">
  <input name="words" class="txt-search" type="text" size="14"  />
  <input type="hidden" name="config" value="public">
  <input type="image"  alt="Search Specifications" class="btn-search" />
          </form>          <span><a href="/search/specsearch.html?config=public&words=">advanced search</a></span></td>
    	</tr>
    	<tr>
    	<td>
    	 <h3><a href="/packetcable/specifications/index.html">PacketCable&trade;</a></h3>
         <p>PacketCable&trade; seeks to define a QoS-enabled, IP-based services delivery platform which uses the capabilities of the DOCSIS&reg; access network&nbsp;&hellip;&nbsp;</p></td>
    	<td>
         <h3><a href="/advancedadvertising/specifications/index.html">Advertising &amp; Interactive Services</a></h3>
         <p>The cable industry has targeted strategies for gaining additional advertising revenues through the use of enhanced TV specifications&nbsp;&hellip;&nbsp;</p></td>
		 
		 <td><h3><a href="/olca/index.html">OLCA</a></h3>
		<p>The Online Content Access (OLCA) project aims to develop use cases, technical requirements, protocols and architecture to allow digital &nbsp;&hellip;&nbsp;</p></td>
    	</tr>
    	<tr>
    	<td>
    		 <h3><a href="/opencable/specifications/index.html">OpenCable&trade;</a></h3>
            <p>The OpenCable&trade; Platform was developed to enable a national platform for the delivery of interactive services, programming, and advertising&nbsp;&hellip;&nbsp;</p></td>
    	<td>
        <h3><a href="/projects/crossproject/specifications/index.html">Cross Project</a></h3>
Investigating the distribution of content assets from multiple content providers sent over diverse networks to cable operators&nbsp;&hellip;&nbsp;</td>
    	<td>
        <h3><a href="/dpoe/specifications/index.html">DOCSIS&reg; Provisioning of EPON</a></h3>
        <p>DOCSIS&reg; Provisioning of EPON (DPoE&trade;) enables Ethernet Passive Optical Network (EPON) equipment to be provisioned using existing DOCSIS-based&nbsp;&hellip;&nbsp;</p>
		</li></td>
    	<td>    	</td>
    	</tr>
		
		
    </table>
     </div><!--close innertube-->
</li>


<!--certification/qualification tab-->
<li class="mega">
	<h2><a href="/certqual/" class="tab-cert<?php echo $cert; ?>"></a></h2>
	<div class="innertube" >
      <table class="tbl-cert">
      <tr>
      <td style="width:40%; vertical-align:top">
      mycompany strives to provide accurate and relevant information to our vendor and member communities in the best format possible.This area is designed to provide information related to DOCSIS&reg;, PacketCable&trade;,     CableHome&reg;, and OpenCable&trade; certification testing and specification programs.<br /><br />

Guidelines referenced in the Certification/Qualification area generally do not apply to UDCP products. If you are building unidirectional digital cable-ready products, please refer to the mycompany UDCP site.<br /><br />

<a href="mailto:se-mail@mycompany.com">Contact us</a> for information on mycompany Certification.

      </td>
      <td valign="top">
      <h3><a href="/certqual/">Guidelines</a></h3>
      <ul class="list-arrow2">
		<li><a href="/cablemodem/downloads/DOCSISCertWaveGuidelines.pdf">Certification Wave Requirements and Guidelines</a></li>
		<li><a href="/certqual/guidelines/specs.html">General Information</a></li>
		<li><a href="/certqual/guidelines/vendors.html">Rules for Vendors</a></li>
		<li><a href="/certqual/guidelines/certification.html">How Certification is Determined</a></li>
		<li><a href="/certqual/trademarks/">mycompany Trademark Guidelines</a></li>
	 </ul><br />
	  <h3><a href="/certqual/">Certification Wave Applications</a></h3>
      <ul class="list-arrow2">
		<li><a href="https://www.mycompany.com/certwave/">mycompany Certification Wave Application</a></li>
	 </ul>
      </td>

      <td valign="top">
       <h3><a href="/certqual/">Schedules &amp; Fees</a></h3>
     	<ul class="list-arrow2">
     		<li><a href="/downloads/2013_Certification_Schedule.pdf">Certification Wave Schedule</a></li>
     		<li><a href="/certqual/schedules/">Current Interoperability Event Schedule</a></li>
     		<li><a href="/downloads/Cert_Fees.pdf">DOCSIS&reg;/PacketCable&trade;/ CableHome&reg;/OpenCable&trade; Certification Fees</a></li>
     		<li><a href="/opencable/downloads/OCPricing.pdf">OpenCable&trade; Price List</a></li>
     		<li><a href="/udcp/downloads/UDCPPricing.pdf">UDCP Testing Fees</a></li>
     	</ul>
      </td>
      </tr>
      </table>
	</div>
</li>
</ul><!--close tab menu-->


</div><!--close nav-main-->
<div class="search">
		<form name="fm-search" method="get" id="fm-search" action="/search/htsearch.html">
		<input name="words" class="txt-search" type="text" size="14"  />
		<input type="hidden" name="config" value="public">
	 	<input type="image" src="/graphics/btn-search.png"  alt="Search" class="btn-search" />
		</form>
		<span><a href="/search/htsearch.html?config=public&words=">advanced search</a></span>
		</div><!--close search-->
	</div><!--close header-->
<div id="blue-bar-top"></div>

	<div id="sidebar2">
	<ul class="nav-sidebar">
			<li><a href="/" id="home">Home</a></li>
            <li><a href="/about/overview/" id="overview">Overview</a></li>
            <li><a href="/anniversary/" id="history">History</a></li>
            <li><a href="/about/board/" id="board_of_directors">Board of Directors</a></li>
            <li><a href="/about/companies/" id="member_companies">Member Companies</a></li>
            <li><a href="/about/careers/" id="careers">Careers</a></li>
            <li><a href="/conferences_public/" id="events">Events</a>
            <ul class="sub">
					<li><a href="https://www.mycompany.com/doczone/" class="subnav_1">Member Conferences</a></li>
					<li><a href="/conferences_public/Demos/" class="subnav_2">Demonstration Opportunities</a></li>
					<li><a href="/conferences_public/Demos/calendar.html" class="subnav_3">Events Calendar</a></li>					
			</ul>
			</li>
           <li><a href="/news/" id="news_rooms">News Room</a></li>
            <li><a href="/news/primers/" id="primers">Primers</a></li>
            <li><a href="/about/patents/" id="patents">Patents</a></li>
            <li><a href="/about/inventions/" id="inventions">Inventions</a></li>
            <li><a href="/certqual/lists/">Who's Certified/Qualified</a></li>	
</ul><!--close nav-sidebar-->
	
	</div><!--close sidebar2-->
			
				<div id="main2">
				