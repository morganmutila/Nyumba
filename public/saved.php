<?php require_once("../init.php"); ?>
<?php if (!$session->isLoggedIn()) { Redirect::to("login.php?redirect=savedproperty"); } ?>

<?php
		
	if(isset($_SESSION['user_id'])){
		$user_id = $_SESSION['user_id'];
	}	

	$sql  = "SELECT property.* FROM property ";
	$sql .="INNER JOIN saved_property ON (property.id = saved_property.property_id)";

	// Get all the saved properties for the user
	$properties = Property::findBySql($sql, array($user_id));

?>


<?php include_layout_template('header.php'); ?>

	<h2>Saved properties</h2>
	
	<?php echo output_message($message); ?>

	<?php 
	// List all the property that the user saved
		foreach ($properties as $property): ?>
			<?php
				echo "<p style=\"margin-bottom:1rem;\"><a href=\"property.php?id=$property->id\"><strong>K ".(int)$property->price."&nbsp;<small>".$property->getRentTerms()."</small></strong><br>";	
				echo $property->beds   . " bedrooms <strong>·</strong> "; 
				echo $property->baths  . " bathrooms <strong>·</strong> ";
				echo $property->size   . " sqft<br>";
				echo $property->address."<br>";
				echo "<a href=\"listremove.php?id=$property->id&redirect=saved\">x Remove</a></p>";

			?>
		<?php endforeach; ?>
		<?php if(!count($properties)){?>
				<div style="padding: 1rem 0.3rem;">
					You currently do not have any saved properties
					<p>Click save to add a listing to saved property</p>
				</div>
		<?php } ?>
  

<?php include_layout_template('footer.php'); ?>
		
