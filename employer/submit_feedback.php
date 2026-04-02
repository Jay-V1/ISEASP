<?php
require_once("./include/initialize.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicant_id = $_POST['applicant_id'];
    $employer_id = $_POST['employer_id'];
    $feedback = $conn->real_escape_string($_POST['feedback']);

    $sql = "INSERT INTO tbl_applicant_feedback (APPLICANTID, EMPLOYERID, FEEDBACK, CREATED_AT)
            VALUES ('$applicant_id', '$employer_id', '$feedback', NOW())";

    if ($conn->query($sql)) {
        echo "Success";
    } else {
        http_response_code(500);
        echo "Error";
    }
}
?>
