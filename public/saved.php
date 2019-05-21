<?php require_once("../init.php"); ?>
<?php if (!$session->isLoggedIn()) { Redirect::to("login.php?redirect=savedproperty"); } ?>

<?php


$page_title = "Saved Properties";		 

	$sql  = "SELECT DATE_FORMAT(saved_property.created, '%W, %d %M') AS saved, property.* FROM saved_property";
	$sql .= " INNER JOIN property ON ('saved_property.property_id' = 'property.id')";
	$sql .= " WHERE saved_property.user_id = ?";

	// Get all the saved properties for the user
	$properties = Property::findBySql($sql, array($session->user_id));
?>


<?php include_layout_template('header.php'); ?>

	<h2>Saved Properties(<?php echo SavedProperty::total();?>)</h2>
	
	<?php echo output_message($message); ?>

	<?php 
	// List all the property that the user saved
		foreach ($properties as $property): ?>
			<?php
				echo thumb_image($property->image()) . "<br>";
				echo "<a href=\"listremove.php?id=$property->id&redirect=saved\">x</a><br>";
				echo "<p style=\"margin-bottom:1rem;\"><a href=\"property.php?id=$property->id\"><strong>K ".(int)$property->price."&nbsp;<small>".$property->rentTerms()."</small></strong><br>";	
				echo $property->beds   . " beds <strong>·</strong> "; 
				echo $property->baths  . " baths <strong>·</strong> ";
				echo $property->size   . " sqft<br>";
				echo $property->address."<br>";
				// echo "Saved on ".$property->saved."<br>";
				echo "<a href=\"listremove.php?id=$property->id&redirect=saved\">Share -></a></p>";

			?>
		<?php endforeach; ?>
		<?php if(!count($properties)){?>
				<div style="padding: 1rem 0.3rem;">
					You currently do not have any saved properties
					<p>Click save to add a listing to saved property</p>
				</div>
		<?php } ?>
  

<?php include_layout_template('footer.php'); ?>
		
