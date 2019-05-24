	
    </div>
    <div id="footer">
        <p style="text-align: center"><a href="location.php">Location&nbsp;></a>&nbsp;<strong><?php echo isset($session->location) ? Location::findLocationOn($session->location) : "Not set";?></strong>
        </p>
        <p style="text-align: center"><small>© <?php echo date("Y", time()); ?>
        Nyumba Yanga Group, All rights reserved.&nbsp;<br>Nyumba Yanga is not responsible for the content on the website<br><a href="#">Terms of Use</a> and <a href="#">Privacy Policy</a></small></p>
    </div>
  </body>
</html>