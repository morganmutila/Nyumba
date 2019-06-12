<?php
// This will connect to mysql database array, "mysql settings"
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'simasiku');
define('DB_PASS', 'morgan0581');
define('DB_NAME', 'nyumba_yanga');
define('DB_CHARSET', 'utf8mb4');

$GLOBALS['config'] = array(
    // This config will remember user in a cookie file
    'remember' => array(
        'cookie_name'   => 'rmbr',
        'cookie_expiry' => 604800 //60x60x24x7 expires in a week
    ),

    //These are the default reference values for the webapp
    'max_file_size'            => 10048576, // 10 MB
    'allow_user_folders'	   => false,
    'records_per_page'	       => 5,
    'mysql_date_format'        => "%m/%d/%Y",
    'mysql_date_time_format'   => "%Y-%m-%d %H:%M:%S",
    'php_date_format'		   => "n/j/Y",
    'php_date_time_format'	   => "m/d/Y, h:i a",
    'new_listing'              => "P5D",  // 5 days before we remove the NEW tag on the listing
    'end_post_date'            => "P20D", // 20 days before we remove the posted date
    'default_sortby'           => "new",  //Sort Listings by Newest
    'default_srch_filter'      => array('filter_price' => 'anyprice', 'filter_beds' => 'any')

);