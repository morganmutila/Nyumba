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
define('UPLOAD_FOLDER', "..".DS.'uploads');

// Path for loading external Libraries
define('LIB_PATH', INCLUDE_PATH.DS.'apps'.DS.'library');

// Path for loading external Packages using Composer
define('PACKAGE_PATH', LIB_PATH.DS.'vendor'.DS.'autoload.php');

// Include Application configuration script
include INCLUDE_PATH .DS. 'config.php';

// load basic functions next so that everything after can use them
include INCLUDE_PATH .DS. 'functions.php';

// load common messages for site wide usage
include INCLUDE_PATH .DS. 'messages.php';

//Include the common classes
include CLASS_PATH   .DS. "common.class.php";

//Include Nyumba yanga small classes
include CLASS_PATH   .DS. "ny.class.php";


// Start the session, checks Loggin through COOKIE and SESSION values and initialise $message
$session = new Session();
$message = $session->message();


// Get the currently logged in user
if($session->isLoggedIn()){
	$user = User::findById($session->user_id);
	Session::put("LOCATION", $user->location_id);

	if(isset($user->location_id) && is_numeric($user->location_id)){
		$session->location = (int) $user->location_id;
	}
	elseif(Session::exists('LOCATION')){
		$session->location = (int) Session::get('LOCATION');
	}
	else{
		$session->location = null;
	}
}
