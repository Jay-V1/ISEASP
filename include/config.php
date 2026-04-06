<?php
// Database configuration - Auto-detect environment
if (getenv('RENDER') !== false) {
    // Running on Render.com
    $database_url = getenv('DATABASE_URL');
    
    if ($database_url) {
        // Parse database URL (for MySQL)
        $db_parts = parse_url($database_url);
        
        define('server', $db_parts['host']);
        define('user', $db_parts['user']);
        define('pass', $db_parts['pass']);
        define('database_name', ltrim($db_parts['path'], '/'));
        
        // Handle port if present
        if (isset($db_parts['port'])) {
            define('DB_PORT', $db_parts['port']);
        }
    } else {
        // Fallback for Render without DATABASE_URL
        define('server', 'localhost');
        define('user', 'root');
        define('pass', '');
        define('database_name', 'iseasp_db');
    }
    
    // Web root for Render
    $web_root = "https://" . $_SERVER['HTTP_HOST'] . "/";
    define('web_root', $web_root);
} else {
    // Local development
    define('server', 'localhost');
    define('user', 'root');
    define('pass', '');
    define('database_name', 'iseasp_db');
    
    // Web root for local development
    $web_root = "http://" . $_SERVER['HTTP_HOST'] . "/ISEASP/";
    define('web_root', $web_root);
}

// Time zone
date_default_timezone_set('Asia/Manila');

// Error reporting
if (getenv('RENDER') !== false) {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
} else {
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
}
error_reporting(E_ALL);
?>