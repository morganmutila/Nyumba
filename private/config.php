<?php
/**
 * The base MySQL settings of Osclass
 */

/** MySQL database name for Osclass */
define('DB_NAME', 'nyumba_yanga');

/** MySQL database username */
define('DB_USER', 'simasiku');

/** MySQL database password */
define('DB_PASS', 'morgan0581');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

/** MySQL charset */
define('DB_CHARSET', 'utf8mb4');



// Global application settings

$GLOBALS['config'] = [
    // This config will remember user in a cookie file
    // Expires in a week
    'remember' => ['cookie_name' => 'rmbr', 'cookie_expiry' => 604800],

    // These are the default reference values for the webapp
    'max_file_size'            => 10048576, // 10 MB
    'allow_user_folders'	   => false,
    'records_per_page'	       => 6,
    'mysql_date_format'        => "%m/%d/%Y",
    'mysql_date_time_format'   => "%Y-%m-%d %H:%M:%S",
    'php_date_format'		   => "n/j/Y",
    'php_date_time_format'	   => "m/d/Y, h:i a",
    'new_listing'              => "P5D",  // 5 days before we remove the NEW tag on the listing
    'end_post_date'            => "P20D", // 20 days before we remove the posted date
    'default_sort'             => "new",  //Sort Listings by Newest
];