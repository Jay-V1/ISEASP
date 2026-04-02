<?php
require_once('include/connection.php');

if (isset($_GET['code'])) {
    $activation_code = $_GET['code'];

    // Check if the code exists
    $stmt = $conn->prepare("SELECT * FROM tblemployers WHERE ACTIVATION_CODE = ?");
    $stmt->execute([$activation_code]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['IS_ACTIVE'] == 1) {
            echo "<h2>Account already activated.</h2>";
        } else {
            // Activate the account
            $stmt = $conn->prepare("UPDATE tblemployers SET IS_ACTIVE = 1 WHERE ACTIVATION_CODE = ?");
            $stmt->execute([$activation_code]);
            echo "<h2>Account activated successfully! You can now log in.</h2>";
        }
    } else {
        echo "<h2>Invalid activation code.</h2>";
    }
} else {
    echo "<h2>No activation code provided.</h2>";
}
?>
