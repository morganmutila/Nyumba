<?php require "../init.php"; 
if (!$session->isLoggedIn()) { Redirect::to("login.php?redirect=saved"); }


if(Input::get('id') && is_numeric(Input::get('id'))){

	$property_id = Input::get('id');

	$sql  = "SELECT * FROM saved ";
	$sql .= "WHERE property_id = ?";

	$savedproperty = Saved::findBySql($sql, array($property_id));
	$savedproperty = array_shift($savedproperty);
	
	if($savedproperty && $savedproperty->delete()){
	// Delete the listing from the save list
		// $session->message('You have removed the listing from saved property', 'info');
		if(Input::get('redirect') == "saved"){
			Redirect::to('saved.php');
		}else{
       		Redirect::to('index.php');
		}
    }
    else{
    	$session->message('Could not remove the listing from saved list', 'warning');
        if(Input::get('redirect') == "saved"){
			Redirect::to('saved.php');
		}else{
       		Redirect::to('index.php');
		}
    }
}


