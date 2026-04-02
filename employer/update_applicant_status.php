<?php
require_once("./include/initialize.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicantId = $_POST['applicant_id'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$applicantId || !$status) {
        http_response_code(400);
        echo "Invalid input.";
        exit;
    }

    // Fetch job ID related to the applicant
    $query = "SELECT JOBID FROM tbljobregistration WHERE APPLICANTID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $applicantId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo "Applicant registration not found.";
        exit;
    }

    $jobId = $result->fetch_assoc()['JOBID'];

    if ($status === 'Accepted') {
        // Reject all other applicants for the same job
        $conn->query("UPDATE tbljobregistration SET IS_ACCEPTED = 'no' WHERE JOBID = '$jobId'");

        // Accept selected applicant
        $stmt = $conn->prepare("UPDATE tbljobregistration 
                                SET IS_ACCEPTED = 'yes', DATETIMEAPPROVED = NOW() 
                                WHERE APPLICANTID = ?");
        $stmt->bind_param("i", $applicantId);
        $stmt->execute();

        // Close the job posting
        $stmt = $conn->prepare("UPDATE tbljob SET JOBSTATUS = 'Closed' WHERE JOBID = ?");
        $stmt->bind_param("i", $jobId);
        $stmt->execute();

        echo "Applicant accepted, job closed, and others rejected.";
    } elseif ($status === 'Rejected') {
        // Mark applicant as rejected
        $stmt = $conn->prepare("UPDATE tbljobregistration 
                                SET IS_ACCEPTED = 'no' 
                                WHERE APPLICANTID = ?");
        $stmt->bind_param("i", $applicantId);
        $stmt->execute();

        echo "Applicant rejected.";
    } else {
        http_response_code(400);
        echo "Invalid status.";
    }
}
?>
