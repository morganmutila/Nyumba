<?php require_once("../init.php"); ?>
<?php if (!$session->isLoggedIn()) { Redirect::to("login.php?redirect=saved"); } ?>

<?php

	if(Input::get('id') && is_numeric(Input::get('id'))){
		
		$property_id = Input::get('id');
		$savedproperty = new SavedProperty();
		$savedproperty->user_id     = $_SESSION['user_id'];
		$savedproperty->property_id = $property_id;
		
		// Save the listing to the save list
		if ($savedproperty->create()) {
			$session->message('Listing saved successfully');
            Redirect::to('index.php');
        }
        else{
        	$session->message('Could not save the listing');
            Redirect::to('index.php');
        }
	}

	