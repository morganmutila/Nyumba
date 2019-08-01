	
    </section>
    <footer class="bg-light py-3 border-top">
        <p class="text-center">
            <a href="location.php" style="color: #444;"><?php echo isset($session->location) ? "<i class=\"mdi mdi-map-marker\"></i>&nbsp;" . Location::cityLocation($session->location) : "Change location";?></a>
        </p>
        <div class="nav justify-content-center">
        	<a href="aboutus" class="nav-item nav-link">About</a>
        	<a href="support" class="nav-item nav-link">Support</a>
        	<a href="terms" class="nav-item nav-link">Terms</a>
        	<a href="privacy" class="nav-item nav-link">Privacy</a>
        	<a href="sitemap" class="nav-item nav-link">Sitemap</a>
        </div>
        <p class="text-center small">© <?php echo date("Y", time()); ?> NyumbaYanga.com. All rights reserved.&nbsp;</p>
    </footer>

    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.bundle.js"></script>

  </body>
</html>