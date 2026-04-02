<?php
require_once("../../include/initialize.php");

if (!isset($_SESSION['ADMIN_USERID'])) {
    die("Not logged in");
}

global $mydb;

echo "<h2>Database Insert Test</h2>";

// Check if table exists using the database methods
$mydb->setQuery("SHOW TABLES LIKE 'tbl_applicants'");
$mydb->executeQuery();
$result = $mydb->loadResultList();

if (!empty($result)) {
    echo "✓ Table 'tbl_applicants' exists<br>";
} else {
    echo "✗ Table 'tbl_applicants' does NOT exist!<br>";
}

// Get table structure using database methods
$mydb->setQuery("DESCRIBE tbl_applicants");
$mydb->executeQuery();
$columns = $mydb->loadResultList();

echo "<h3>Table Structure:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "展<th>Field</th><th>Type</th><th>Null</th><th>Key</th> </table>";
foreach ($columns as $col) {
    echo "窗口";
    echo "窗口" . $col->Field . "窗口";
    echo "窗口" . $col->Type . "窗口";
    echo "窗口" . $col->Null . "窗口";
    echo "窗口" . $col->Key . "窗口";
    echo " </table>";
}
echo " </table>";

// Test minimal insert
echo "<h3>Testing Minimal Insert:</h3>";
$test_sql = "INSERT INTO tbl_applicants (
    FIRSTNAME, LASTNAME, COURSE, SCHOOL, YEARLEVEL, 
    CONTACT, EMAIL, DISTRICT, MUNICIPALITY, CREATED_BY, STATUS
) VALUES (
    'TEST', 'USER', 'BSIT', 'TEST SCHOOL', '1st Year',
    '09123456789', 'test@email.com', '1st District', 'Vigan City', 
    " . $_SESSION['ADMIN_USERID'] . ", 'Pending'
)";

echo "SQL: " . htmlspecialchars($test_sql) . "<br><br>";

$mydb->setQuery($test_sql);
if ($mydb->executeQuery()) {
    // Get last insert ID - need to query it
    $mydb->setQuery("SELECT LAST_INSERT_ID() as id");
    $mydb->executeQuery();
    $result = $mydb->loadSingleResult();
    $id = $result->id;
    
    echo "<span style='color:green'>✓ SUCCESS! Inserted ID: " . $id . "</span>";
    
    // Delete test record
    $mydb->setQuery("DELETE FROM tbl_applicants WHERE APPLICANTID = $id");
    $mydb->executeQuery();
    echo "<br>✓ Test record deleted";
} else {
    echo "<span style='color:red'>✗ ERROR</span>";
}
?>