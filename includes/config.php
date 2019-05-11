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
        'cookie_name'   => 'hash',
        'cookie_expiry' => 604800 //60x60x24x7 expires in a week
    ),

    //These are the default reference values for the webapp
    'max_file_size'            => 1048576, // 1 MB
    'allow_user_folders'	   => true,
    'members_per_page'	       => 10,
    'records_per_page'	       => 2,
    'mysql_date_format'        => "%m/%d/%Y",
    'mysql_date_time_format'   => "%Y-%m-%d %H:%M:%S",
    'php_date_format'		   => "n/j/Y",
    'php_date_time_format'	   => "m/d/Y, h:i a"
);