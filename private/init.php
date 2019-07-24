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

// Turn on output buffering
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

define('PRIVATE_PATH',  realpath(dirname(__FILE__)));
define('SITE_ROOT',     dirname(PRIVATE_PATH));
define("PUBLIC_PATH",   SITE_ROOT    . '/public');
define("SHARED_PATH",   PRIVATE_PATH . '/shared');
define('CLASS_PATH',    PRIVATE_PATH . '/classes');
define('UPLOAD_FOLDER', '../uploads');

// Path for loading external Libraries
define('LIB_PATH', PRIVATE_PATH . '/library');
// Path for loading external Packages using Composer
define('PACKAGE_PATH', LIB_PATH .'/vendor/autoload.php');


$public_end = strpos($_SERVER['SCRIPT_NAME'], '/public') + 7;
$doc_root = substr($_SERVER['SCRIPT_NAME'], 0, $public_end);
define("WWW_ROOT", $doc_root);

// Include Application configuration script
require_once('config.php');
// load basic functions next so that everything after can use them
require_once('functions.php');
// load common messages for site wide usage
require_once('msgs.php');

// Auto load classes in case they have not been required
function my_autoload ($class_name){
    if(preg_match("/\A\w+\Z/", $class_name)){
        $path = CLASS_PATH .'/'. strtolower($class_name) . ".class.php";
        if (file_exists($path)) {
            include($path);
        }
        else{
            die("The file {$class_name}.class.php can not be found");
        }   
    }    
}
spl_autoload_register("my_autoload");

//Include the common classes
require_once(CLASS_PATH . "/common.class.php");
//Include Nyumba yanga small classes
require_once(CLASS_PATH . "/ny.class.php");

// Start the session, checks Loggin through COOKIE and SESSION values and initialise $message
$session = new Session();
$message = $session->message();

// Get the currently logged in user
if($session->isLoggedIn()){
	$user = User::findById(current_user_id());
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
