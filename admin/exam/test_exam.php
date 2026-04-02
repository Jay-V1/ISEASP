<?php
require_once("../../include/initialize.php");

if (!isset($_SESSION['ADMIN_USERID'])) {
    die("Not logged in");
}

global $mydb;

echo "<h2>Test Exam Insert</h2>";

// Get the latest applicant
$mydb->setQuery("SELECT APPLICANTID, FIRSTNAME, LASTNAME, EXAM_STATUS FROM tbl_applicants ORDER BY APPLICANTID DESC LIMIT 1");
$mydb->executeQuery();
$applicant = $mydb->loadSingleResult();

if (!$applicant) {
    die("No applicant found. Please add an applicant first.");
}

echo "Testing for applicant: " . $applicant->FIRSTNAME . " " . $applicant->LASTNAME . " (ID: " . $applicant->APPLICANTID . ")<br>";
echo "Current EXAM_STATUS: " . $applicant->EXAM_STATUS . "<br><br>";

$applicant_id = $applicant->APPLICANTID;
$examiner_id = $_SESSION['ADMIN_USERID'];
$total_score = 85;
$passing_score = 75;
$exam_status = ($total_score >= $passing_score) ? 'Passed' : 'Failed';

echo "Will set EXAM_STATUS to: $exam_status<br><br>";

// Insert exam result
$sql = "INSERT INTO tbl_exam_results 
        (APPLICANTID, EXAMINER_ID, EXAM_DATE, TOTAL_SCORE, PASSING_SCORE, REMARKS)
        VALUES ($applicant_id, $examiner_id, NOW(), $total_score, $passing_score, 'Test insertion')";

echo "Insert SQL: " . htmlspecialchars($sql) . "<br><br>";

$mydb->setQuery($sql);
if ($mydb->executeQuery()) {
    echo "<span style='color:green'>✓ Exam result inserted</span><br>";
    
    // Update applicant's exam status
    $update_sql = "UPDATE tbl_applicants SET EXAM_STATUS = '$exam_status' WHERE APPLICANTID = $applicant_id";
    echo "Update SQL: " . htmlspecialchars($update_sql) . "<br>";
    
    $mydb->setQuery($update_sql);
    if ($mydb->executeQuery()) {
        echo "<span style='color:green'>✓ Applicant EXAM_STATUS updated</span><br>";
        
        // Verify the update
        $mydb->setQuery("SELECT EXAM_STATUS FROM tbl_applicants WHERE APPLICANTID = $applicant_id");
        $mydb->executeQuery();
        $result = $mydb->loadSingleResult();
        echo "New EXAM_STATUS: <strong>" . $result->EXAM_STATUS . "</strong><br>";
        
        // Clean up
        $mydb->setQuery("DELETE FROM tbl_exam_results WHERE APPLICANTID = $applicant_id AND REMARKS = 'Test insertion'");
        $mydb->executeQuery();
        echo "<span style='color:green'>✓ Test record deleted</span><br>";
    } else {
        echo "<span style='color:red'>✗ Failed to update EXAM_STATUS</span><br>";
    }
} else {
    echo "<span style='color:red'>✗ Failed to insert exam result</span><br>";
}
?>