<?php require_once("../init.php"); ?>
<?php if (!$session->isLoggedIn()) { Redirect::to("index.php"); } ?>
<?php


if(!Input::get('id')) {
    Redirect::to('properties.php');
}

$property = Property::findById(Input::get('id'));
if($property && $property->delete()) {
    $session->message("The property ".$property->address." in ".$property->getLocation()." was deleted");
    Redirect::to('properties.php');
} else {
    $session->message("The property could not be deleted.");
    Redirect::to('properties.php');
}
