<?php
//define the core paths
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

// Detect if running on Render.com
$is_render = getenv('RENDER') !== false;

if ($is_render) {
    defined('SITE_ROOT') ? null : define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);
} else {
    defined('SITE_ROOT') ? null : define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT'] . DS . 'ISEASP');
}

defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT . DS . 'include');

// Load required files
require_once(LIB_PATH . DS . "config.php");
require_once(LIB_PATH . DS . "function.php");
require_once(LIB_PATH . DS . "session.php");
require_once(LIB_PATH . DS . "database.php");
require_once(LIB_PATH . DS . "accounts.php");
require_once(LIB_PATH . DS . "applicants.php");
require_once(LIB_PATH . DS . "scholars.php");

// Initialize database connection
global $mydb;
$mydb = new Database();
?>