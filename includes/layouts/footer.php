	
    </div>
    <div id="footer" style="padding-bottom: 1rem">
        <p style="text-align: center">
            <a href="location.php" style="color: #444;"><?php echo isset($session->location) ? "<i class=\"mdi mdi-map-marker\"></i>&nbsp;" . Location::cityLocation($session->location) : "Change location";?></a>
        </p>
        <ul class="menu">
        	<li><a href="aboutus">About</a></li>
        	<li><a href="support">Support</a></li>
        	<li><a href="terms">Terms</a></li>
        	<!-- <li><a href="privacy">Privacy</a></li> -->
        	<li><a href="sitemap">Sitemap</a></li>
        </ul>
        <p style="text-align: center"><small>© <?php echo date("Y", time()); ?>
        NyumbaYanga.com. All rights reserved.&nbsp;<br>Nyumba Yanga is not responsible for the innacuracy of content posted on the website</small></p>
    </div>
  </body>
</html>