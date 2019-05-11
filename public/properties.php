<?php require_once("../init.php"); ?>
<?php if (!$session->isLoggedIn()) { Redirect::to("login.php"); } ?>

<?php
	// Get all the property for the user
	$sql = "SELECT * FROM property WHERE user_id=? AND status >= ?";
	$properties = Property::findBySql($sql, array($user->id, 1));
?>


<?php include_layout_template('header.php'); ?>

	<h2>My properties (<?php echo $user->numberOfProperty();?>)&nbsp;&nbsp;<small style="font-weight: normal;"><a href="list.php">+ Add property</a></small></h2>
	
	<?php echo output_message($message); ?>

	<?php 
	// List all the property that belongs to the user
	if($properties){
		foreach ($properties as $property): ?>
			<?php
				echo "<p><a href=\"property.php?id=$property->id\">".$property->address."<br>";
				echo $property->beds . " beds <strong>·</strong> "; 
				echo $property->baths . " baths <strong>·</strong> ";
				echo $property->size . " sqft<br>"; 
				echo "Status: ".$property->getPropertyStatus() . "<br></a>";
				echo "<a href=\"delete.php?id={$property->id}\">× Delete</a></p>"; 

			?>
		<?php endforeach;
	} else{
		echo "<p>You currently have no listings</p>";
	}?>
  

<?php include_layout_template('footer.php'); ?>
		
