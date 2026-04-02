<?php
require_once("../../include/initialize.php");

if (!isset($_SESSION['ADMIN_USERID'])) {
    die("Not logged in");
}

global $mydb;

echo "<h2>Testing Trigger and Insert</h2>";

$admin_id = $_SESSION['ADMIN_USERID'];

// 1. Insert a test applicant
$sql = "INSERT INTO tbl_applicants (
    FIRSTNAME, LASTNAME, COURSE, SCHOOL, YEARLEVEL, 
    CONTACT, EMAIL, DISTRICT, MUNICIPALITY, CREATED_BY, STATUS
) VALUES (
    'TRIGGER', 'TEST', 'BSIT', 'TEST SCHOOL', '1st Year',
    '09123456789', 'trigger@test.com', '1st District', 'Vigan City', 
    $admin_id, 'Pending'
)";

echo "<h3>1. Inserting applicant...</h3>";
$mydb->setQuery($sql);
if ($mydb->executeQuery()) {
    $mydb->setQuery("SELECT LAST_INSERT_ID() as id");
    $mydb->executeQuery();
    $result = $mydb->loadSingleResult();
    $id = $result->id;
    echo "✓ Inserted ID: $id<br>";
    
    // 2. Check if trigger created checklist entries
    echo "<h3>2. Checking checklist entries (trigger should create them)...</h3>";
    $mydb->setQuery("SELECT COUNT(*) as count FROM tbl_applicant_requirement_checklist WHERE APPLICANTID = $id");
    $mydb->executeQuery();
    $count = $mydb->loadSingleResult();
    echo "Checklist entries found: " . $count->count . "<br>";
    
    if ($count->count > 0) {
        echo "✓ Trigger is working!<br>";
        
        // Show the entries
        $mydb->setQuery("SELECT r.REQUIREMENT_NAME, c.IS_SUBMITTED, c.IS_VERIFIED 
                        FROM tbl_applicant_requirement_checklist c
                        JOIN tbl_requirement r ON c.REQUIREMENT_ID = r.REQUIREMENT_ID
                        WHERE c.APPLICANTID = $id");
        $mydb->executeQuery();
        $entries = $mydb->loadResultList();
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Requirement</th><th>Submitted</th><th>Verified</th></tr>";
        foreach ($entries as $e) {
            echo "<tr>";
            echo "<td>" . $e->REQUIREMENT_NAME . "</td>";
            echo "<td>" . ($e->IS_SUBMITTED ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($e->IS_VERIFIED ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "✗ Trigger is NOT working!<br>";
    }
    
    // 3. Clean up
    echo "<h3>3. Cleaning up...</h3>";
    $mydb->setQuery("DELETE FROM tbl_applicants WHERE APPLICANTID = $id");
    $mydb->executeQuery();
    echo "✓ Test record deleted<br>";
} else {
    echo "✗ Insert failed: " . $mydb->conn->error . "<br>";
}
?>