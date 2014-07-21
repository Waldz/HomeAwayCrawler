<?php

if(!defined('APP_ENVIRONMENT')) {
    define('APP_ENVIRONMENT', 'development');
}

define('APP_PATH', __DIR__ . '/../');

if(APP_ENVIRONMENT=='development') {
    define('DB_DRIVER', 'mysqli');
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'flatfindr');
    define('DB_USER', 'root');
    define('DB_PASS', 'oadlt');

} else {
    // TODO Here goes PRODUCTION settings
    define('DB_DRIVER', 'mysqli');
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'flatfindr');
    define('DB_USER', 'root');
    define('DB_PASS', 'oadlt');
}

//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ALL);