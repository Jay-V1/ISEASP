<?php
require_once("../../include/initialize.php");

if (!isset($_SESSION['ADMIN_USERID'])) {
    die("Not logged in");
}

global $mydb;

echo "<h2>Full Insert Test</h2>";

// Get the current admin user
$admin_id = $_SESSION['ADMIN_USERID'];
echo "Admin ID: $admin_id<br>";

// Get a sample requirement count
$mydb->setQuery("SELECT COUNT(*) as cnt FROM tbl_requirement");
$mydb->executeQuery();
$result = $mydb->loadSingleResult();
$req_count = $result->cnt;
echo "Requirements count: $req_count<br><br>";

// Full insert test with all fields
$full_sql = "INSERT INTO tbl_applicants (
    FIRSTNAME, MIDDLENAME, LASTNAME, SUFFIX, LRN, BIRTHDATE, BIRTHPLACE,
    GENDER, CIVIL_STATUS, RELIGION, NATIONALITY, PERMANENT_ADDRESS, CURRENT_ADDRESS,
    DISTRICT, MUNICIPALITY, BARANGAY, COURSE, SCHOOL, YEARLEVEL, GPA, CONTACT, EMAIL,
    FACEBOOK_URL, EMERGENCY_CONTACT_NAME, EMERGENCY_CONTACT_NUMBER, EMERGENCY_CONTACT_RELATION,
    APPLICATION_TYPE, SCHOOL_YEAR, SEMESTER, IS_4PS_BENEFICIARY, IS_INDIGENOUS,
    FAMILY_ANNUAL_INCOME, PARENT_OCCUPATION, STATUS, CREATED_BY
) VALUES (
    'JUAN', 'DELA', 'CRUZ', 'JR', '123456789012', '1995-01-15', 'VIGAN CITY',
    'Male', 'Single', 'ROMAN CATHOLIC', 'Filipino', 'BRGY. TAMAG, VIGAN CITY', NULL,
    '1st District', 'Vigan City', 'TAMAG', 'BS INFORMATION TECHNOLOGY', 'UNIVERSITY OF NORTHERN PHILIPPINES', '1st Year', 85.50,
    '09123456789', 'juan@email.com', NULL,
    'MARIA CRUZ', '09123456788', 'Mother',
    'New Applicant', '2025-2026', '1st Semester', 'No', 'No',
    NULL, NULL, 'Pending', $admin_id
)";

echo "SQL: " . htmlspecialchars(substr($full_sql, 0, 500)) . "...<br><br>";

$mydb->setQuery($full_sql);
if ($mydb->executeQuery()) {
    // Get last insert ID
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