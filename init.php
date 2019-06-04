<?php
/***************************************************************************************************
 *   
 *   Appname                : Nyumba Yanga
 *   Author                 : Morgan Mutila
 *   Description            : Nyumba yanga is a web platform for listing property for sale or rent
 *   Copyright              : (C)2018 Nyumba yanga
    
 ***************************************************************************************************
 *   NOTE: This file must be included on every php file
 ***************************************************************************************************/

ob_start();

// Set the correct time() and date() function using the correct timezone
date_default_timezone_set("Africa/Lusaka");

// Toggle PHP errors with error reporting using the custom error handler function
ini_set("error_reporting", true);
error_reporting(E_ALL);

// Flag variable for web site status:
//Displays the full error stack instead of friendly page
// define('DEBUG_MODE', FALSE);

// Define the paths
// DIRECTORY_SEPARATOR is a PHP pre-defined constant (\ for Windows, / for Unix)
define('DS', DIRECTORY_SEPARATOR);
// Set the full path to the docroot
// define('SITE_ROOT', DS.'xampp'.DS.'htdocs'.DS.'ny');
define('SITE_ROOT',     realpath(dirname(__FILE__)));
define('INCLUDE_PATH',  SITE_ROOT.DS.'includes');
define('CLASS_PATH',    INCLUDE_PATH.DS.'apps');
define('UPLOAD_FOLDER', SITE_ROOT.DS.'uploads'.DS.'property');

// Path for loading external Packages
define('PACKAGE_PATH', CLASS_PATH.DS.'vendor'.DS.'vendor'.DS.'autoload.php');

// Include Application configuration script
require INCLUDE_PATH .DS. 'config.php';

// load basic functions next so that everything after can use them
require INCLUDE_PATH .DS. 'functions.php';

// load common messages for site wide usage
require INCLUDE_PATH .DS. 'messages.php';

//Include the common classes
require CLASS_PATH   .DS. "Common.php";

//Include Nyumba yanga small classes
require CLASS_PATH   .DS. "NY.php";


// Start the session and initialise $message
$session = new Session();
$message = $session->message();

//Get the currently logged in user
if($session->isLoggedIn()){
	$user = User::findById($session->user_id);
	Session::put("LOCATION", $user->location_id);
	$session->location = isset($user->location_id) ? $user->location_id : null;
}