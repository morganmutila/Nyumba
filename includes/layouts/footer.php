	
    </div>
    <p><strong><?php echo isset($session->location) ? Location::findLocationOn($session->location) : "Not set";?></strong><br><a href="location.php">Change location</a></p>
    <div id="footer">© <?php echo date("Y", time()); ?> Nyumba Yanga Group, All rights reserved.<br><a href="#">Terms of Use</a> and <a href="#">Privacy Policy</a></div>
  </body>
</html>