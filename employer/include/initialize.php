<?php
// // Start session
session_start();

// Set your web_root
define('web_root', 'http://localhost/iseasp/'); 


// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'mangged');

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . $conn->connect_error);
}

// Function for redirect
function redirect($url) {
    header("Location: " . $url);
    exit;
}



function count_records($table) {
    global $conn;
    $sql = "SELECT COUNT(*) AS total FROM `$table` ";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}
?>
