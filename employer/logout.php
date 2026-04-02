<?php
session_start();

// Destroy all session variables to log the user out
session_unset();
session_destroy();

// Prevent the browser from caching the page
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page after logout
header("Location: login.php");
exit();
?>
