<?php
include '../../private/init.php';
$session->comfirm_logged_in("index.php");


if(!Input::get('id')) {
    Redirect::to('properties.php');
}

$property = Property::findById(Input::get('id'));
if($property && $property->delete()) {
    $session->message("The property ".$property->address." in ".$property->location()." was deleted");
    Redirect::to('properties.php');
} else {
    $session->message("The property could not be deleted.");
    Redirect::to('properties.php');
}
