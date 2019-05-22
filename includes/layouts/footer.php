	
    </div>
    <div id="footer">
        <p><strong><?php echo isset($session->location) ? Location::findLocationOn($session->location) : "Not set";?></strong><br><a href="location.php">Change location</a>
        </p>
        <p>© <?php echo date("Y", time()); ?>
        Nyumba Yanga Group, All rights reserved.&nbsp;<a href="#">Terms of Use</a> and <a href="#">Privacy Policy</a></p>
    </div>
  </body>
</html>