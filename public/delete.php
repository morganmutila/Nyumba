<?php
include '../private/init.php';
require_login("index.php");


if(!Input::get('id')) {
    Redirect::to('../properties.php');
}

$property_id = Input::get('id');
$property 	 = Property::findById($property_id);

if($property) {
	$sql = "SELECT * FROM photos WHERE property_id = ?";

	$photos = Photo::findBySql($sql, [$property_id]);
	//pre($photos);exit;
	if($photos){
		foreach ($photos as $photo) {
			$photo->delete();
		}
	}	 
	if($property->delete()){
		    $session->message("The property ".$property->address." in ".$property->location()." was deleted");
		    Redirect::to('properties.php');
	} 
}
else {
    $session->message("The property could not be deleted.");
    Redirect::to('properties.php');
}
