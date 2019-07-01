<?php
include "../init.php";
$session->comfirm_logged_in("login.php?redirect=saved");

if(Input::get('id') && is_numeric(Input::get('id'))){
	
	$property_id = Input::get('id');
	$savedproperty = new Saved();
	$savedproperty->user_id     = $session->user_id;
	$savedproperty->property_id = $property_id;
	
	// Save the listing to the save list
	if ($savedproperty->create()) {
		$session->message('Added to saved properties', 'success');
        Redirect::prevPage();
    }
    else{
    	$session->message('Could not save the listing', 'warning');
        Redirect::prevPage();
    }
}

	