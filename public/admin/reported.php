<?php require_once("../../init.php"); ?>
<?php if (!$session->isLoggedIn()) { Redirect::to("login.php?redirect=saved"); } ?>

<?php

	if(Input::get('property')){
		// Save the property to the save list
		$property = Property::findById(Input::get('property'));
		$sql  = "INSERT INTO saved_property (user_id, property_id) ";
		$sql .= "VALUES (?, ?) ";
		$params = array($_SESSION['user_id'], $property->id);
		$db->query($sql, $params);
		if($db->count()){       
			$session->message('Property has been added to your list successfully');
            Redirect::to('index.php');
        }
        else{
        	$session->message('Could not add the property to your save list');
            Redirect::to('index.php');
        }
	}

	elseif (!input::get('property')) {
		// Get all the saved properties for the user
		$sql = "SELECT * FROM saved_property WHERE user_id=?";
		$db->query($sql, array($_SESSION['user_id']));
		$properties = $db->fetchAll(null, null, 'FETCH_OBJ');
	}

?>


<?php layout_template('header.php'); ?>

	<h2>Reported properties</h2>
	
	<?php echo output_message($message); ?>

	<?php 
	// List all the property that the user favours
	if($properties){
		foreach ($properties as $property): ?>
			<?php
				echo "<strong>K ".(int)$property->price."&nbsp;<small>".$property->getRentTerms()."</small></strong><br>";	
				echo "<a href=\"property.php?id=$property->id\">".$property->address."<br>";
				echo $property->beds . " bedrooms <strong>·</strong> "; 
				echo $property->baths . " bathrooms <strong>·</strong> ";
				echo $property->size . " sqft<br>";
				echo "<a href=\"remove.php?property={$property->id}\">× Remove</a>"; 

			?>
		<?php endforeach;
	} else{
		echo "<p>You have no saved properties yet</p>";
	}?>
  

<?php layout_template('footer.php'); ?>
		
