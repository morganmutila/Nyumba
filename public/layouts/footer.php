	
    </div>
    <p style="margin-left: 1.5rem;">◊&nbsp;<?php echo isset($_SESSION['location']) ? Location::findLocationById($_SESSION['location']) : "Not set";?><br><a href="location.php">Change this location</a></p>
    <div id="footer">© <?php echo date("Y", time()); ?> Nyumba Yanga Group, All rights reserved.<br><a href="#">Terms of Use</a> and <a href="#">Privacy Policy</a></div>
  </body>
</html>