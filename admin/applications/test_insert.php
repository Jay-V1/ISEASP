<?php
require_once("../../include/initialize.php");

if (!isset($_SESSION['ADMIN_USERID'])) {
    echo "Not logged in. <a href='../../admin/login.php'>Login here</a>";
    exit;
}

global $mydb;

echo "<h2>Testing Insert Functionality</h2>";

// Test 1: Simple insert with minimal fields
$admin_id = $_SESSION['ADMIN_USERID'];

$sql = "INSERT INTO tbl_applicants (
    FIRSTNAME, LASTNAME, COURSE, SCHOOL, YEARLEVEL, 
    CONTACT, EMAIL, DISTRICT, MUNICIPALITY, CREATED_BY, STATUS
) VALUES (
    'JUAN', 'DELA CRUZ', 'BS INFORMATION TECHNOLOGY', 'UNP', '1st Year',
    '09123456789', 'juan@email.com', '1st District', 'Vigan City', 
    $admin_id, 'Pending'
)";

$mydb->setQuery($sql);
if ($mydb->executeQuery()) {
    $mydb->setQuery("SELECT LAST_INSERT_ID() as id");
    $mydb->executeQuery();
    $result = $mydb->loadSingleResult();
    $id = $result->id;
    
    echo "<span style='color:green'>✓ SUCCESS! Inserted ID: $id</span><br>";
    
    // Clean up
    $mydb->setQuery("DELETE FROM tbl_applicants WHERE APPLICANTID = $id");
    $mydb->executeQuery();
    echo "<span style='color:green'>✓ Test record deleted</span><br>";
    echo "<p>Your database insert is working correctly!</p>";
} else {
    echo "<span style='color:red'>✗ INSERT FAILED</span><br>";
    echo "Error: " . $mydb->conn->error;
}
?>