<?php
require_once("../../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
    case 'add':
        doInsert();
        break;
    case 'edit':
        doEdit();
        break;
    case 'delete':
        doDelete();
        break;
}

function doInsert() {
    global $mydb;



    

    error_log("=== EXAM CONTROLLER doInsert() START ===");
    error_log("POST data: " . print_r($_POST, true));






    
    if (isset($_POST['save'])) {
        $applicant_id = intval($_POST['APPLICANTID']);
        $total_score = floatval($_POST['TOTAL_SCORE']);
        $passing_score = floatval($_POST['PASSING_SCORE']);
        $remarks = trim($_POST['REMARKS']);
        $exam_date = date('Y-m-d H:i:s', strtotime($_POST['EXAM_DATE']));
        $examiner_id = $_SESSION['ADMIN_USERID'];
        
        // Determine exam status
        $exam_status = ($total_score >= $passing_score) ? 'Passed' : 'Failed';
        
        // Insert exam result
        $sql = "INSERT INTO tbl_exam_results 
                (APPLICANTID, EXAMINER_ID, EXAM_DATE, TOTAL_SCORE, PASSING_SCORE, REMARKS)
                VALUES ($applicant_id, $examiner_id, '$exam_date', $total_score, $passing_score, '$remarks')";
        
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        // Update applicant's exam status
        $update_sql = "UPDATE tbl_applicants SET EXAM_STATUS = '$exam_status' WHERE APPLICANTID = $applicant_id";
        $mydb->setQuery($update_sql);
        $mydb->executeQuery();
        
        // AUTO-CREATE INTERVIEW RECORD IF EXAM IS PASSED
        if ($exam_status == 'Passed') {
            // Check if interview record already exists
            $check_sql = "SELECT COUNT(*) as count FROM tbl_interview WHERE APPLICANTID = $applicant_id";
            $mydb->setQuery($check_sql);
            $mydb->executeQuery();
            $check_result = $mydb->loadSingleResult();
            
            if ($check_result->count == 0) {
                // Set default interview date (3 days from now at 9:00 AM)
                $default_interview_date = date('Y-m-d H:i:s', strtotime('+3 days 09:00:00'));
                
                // Insert interview record with current admin as default interviewer
                $default_interviewer = $_SESSION['ADMIN_USERID'];
                
                $interview_sql = "INSERT INTO tbl_interview 
                                 (APPLICANTID, INTERVIEW_DATE, INTERVIEW_MODE, RECOMMENDATION, COMMENTS, INTERVIEWER_ID)
                                 VALUES 
                                 ($applicant_id, '$default_interview_date', 'Face-to-face', 'For Review', 'Awaiting interview schedule', $default_interviewer)";
                
                $mydb->setQuery($interview_sql);
                $mydb->executeQuery();
                
                // Update applicant status to 'For Interview'
                $status_sql = "UPDATE tbl_applicants SET STATUS = 'For Interview' WHERE APPLICANTID = $applicant_id";
                $mydb->setQuery($status_sql);
                $mydb->executeQuery();
                
                // Log the interview creation
                $log_sql = "INSERT INTO tbl_application_log 
                            (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                            SELECT 
                                $applicant_id, 
                                $examiner_id, 
                                USERNAME, 
                                ROLE, 
                                'Interview automatically scheduled (exam passed)',
                                'INTERVIEW',
                                'Interview automatically created after passing exam'
                            FROM tblusers 
                            WHERE USERID = $examiner_id";
                $mydb->setQuery($log_sql);
                $mydb->executeQuery();
            }
        }
        
        // Log the exam result action - FIXED
        $log_sql = "INSERT INTO tbl_application_log 
                    (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                    SELECT 
                        $applicant_id, 
                        $examiner_id, 
                        USERNAME, 
                        ROLE, 
                        CONCAT('Exam result recorded: ', '$exam_status'),
                        'EXAM',
                        CONCAT('Score: ', $total_score, '%, ', '$exam_status')
                    FROM tblusers 
                    WHERE USERID = $examiner_id";
        $mydb->setQuery($log_sql);
        $mydb->executeQuery();
        
        message("Exam result saved successfully! " . ($exam_status == 'Passed' ? "Interview has been scheduled." : ""), "success");
        redirect("index.php?view=results");
    }
}

function doEdit() {
    global $mydb;
    
    if (isset($_POST['save'])) {
        $result_id = intval($_POST['id']);
        $total_score = floatval($_POST['TOTAL_SCORE']);
        $passing_score = floatval($_POST['PASSING_SCORE']);
        $remarks = trim($_POST['REMARKS']);
        $exam_date = date('Y-m-d H:i:s', strtotime($_POST['EXAM_DATE']));
        
        // Get applicant ID from result
        $mydb->setQuery("SELECT APPLICANTID FROM tbl_exam_results WHERE EXAM_RESULT_ID = $result_id");
        $mydb->executeQuery();
        $result = $mydb->loadSingleResult();
        
        if (!$result) {
            message("Exam result not found!", "error");
            redirect("index.php?view=results");
            return;
        }
        
        $applicant_id = $result->APPLICANTID;
        
        // Determine exam status
        $exam_status = ($total_score >= $passing_score) ? 'Passed' : 'Failed';
        
        // Get old exam status
        $mydb->setQuery("SELECT EXAM_STATUS FROM tbl_applicants WHERE APPLICANTID = $applicant_id");
        $mydb->executeQuery();
        $old_status_result = $mydb->loadSingleResult();
        $old_status = $old_status_result->EXAM_STATUS;
        
        // Update exam result
        $sql = "UPDATE tbl_exam_results SET
                EXAM_DATE = '$exam_date',
                TOTAL_SCORE = $total_score,
                PASSING_SCORE = $passing_score,
                REMARKS = '$remarks'
                WHERE EXAM_RESULT_ID = $result_id";
        
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        // Update applicant's exam status
        $update_sql = "UPDATE tbl_applicants SET EXAM_STATUS = '$exam_status' WHERE APPLICANTID = $applicant_id";
        $mydb->setQuery($update_sql);
        $mydb->executeQuery();
        
        // CHECK IF STATUS CHANGED FROM FAILED TO PASSED
        if ($exam_status == 'Passed' && $old_status != 'Passed') {
            // Check if interview record already exists
            $check_sql = "SELECT COUNT(*) as count FROM tbl_interview WHERE APPLICANTID = $applicant_id";
            $mydb->setQuery($check_sql);
            $mydb->executeQuery();
            $check_result = $mydb->loadSingleResult();
            
            if ($check_result->count == 0) {
                // Set default interview date
                $default_interview_date = date('Y-m-d H:i:s', strtotime('+3 days 09:00:00'));
                $default_interviewer = $_SESSION['ADMIN_USERID'];
                
                $interview_sql = "INSERT INTO tbl_interview 
                                 (APPLICANTID, INTERVIEW_DATE, INTERVIEW_MODE, RECOMMENDATION, COMMENTS, INTERVIEWER_ID)
                                 VALUES 
                                 ($applicant_id, '$default_interview_date', 'Face-to-face', 'For Review', 'Awaiting interview schedule', $default_interviewer)";
                
                $mydb->setQuery($interview_sql);
                $mydb->executeQuery();
                
                // Update applicant status
                $status_sql = "UPDATE tbl_applicants SET STATUS = 'For Interview' WHERE APPLICANTID = $applicant_id";
                $mydb->setQuery($status_sql);
                $mydb->executeQuery();
                
                // Log the interview creation
                $log_sql = "INSERT INTO tbl_application_log 
                            (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                            SELECT 
                                $applicant_id, 
                                " . $_SESSION['ADMIN_USERID'] . ", 
                                USERNAME, 
                                ROLE, 
                                'Interview automatically scheduled (exam passed on update)',
                                'INTERVIEW',
                                'Interview automatically created after passing exam'
                            FROM tblusers 
                            WHERE USERID = " . $_SESSION['ADMIN_USERID'];
                $mydb->setQuery($log_sql);
                $mydb->executeQuery();
            }
        } elseif ($exam_status == 'Failed') {
            // Delete any existing interview for failed applicants
            $delete_interview_sql = "DELETE FROM tbl_interview WHERE APPLICANTID = $applicant_id";
            $mydb->setQuery($delete_interview_sql);
            $mydb->executeQuery();
            
            // Update applicant status back to Pending
            $status_sql = "UPDATE tbl_applicants SET STATUS = 'Pending' WHERE APPLICANTID = $applicant_id";
            $mydb->setQuery($status_sql);
            $mydb->executeQuery();
        }
        
        // Log the action - FIXED
        $log_sql = "INSERT INTO tbl_application_log 
                    (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                    SELECT 
                        $applicant_id, 
                        " . $_SESSION['ADMIN_USERID'] . ", 
                        USERNAME, 
                        ROLE, 
                        CONCAT('Exam result updated: ', '$exam_status'),
                        'EXAM',
                        CONCAT('Score updated to: ', $total_score, '%')
                    FROM tblusers 
                    WHERE USERID = " . $_SESSION['ADMIN_USERID'];
        $mydb->setQuery($log_sql);
        $mydb->executeQuery();
        
        message("Exam result updated successfully!", "success");
        redirect("index.php?view=results");
    }
}

function doDelete() {
    global $mydb;
    
    if (isset($_GET['id'])) {
        $result_id = intval($_GET['id']);
        
        // Get applicant ID before deleting
        $mydb->setQuery("SELECT APPLICANTID FROM tbl_exam_results WHERE EXAM_RESULT_ID = $result_id");
        $mydb->executeQuery();
        $result = $mydb->loadSingleResult();
        
        if (!$result) {
            message("Exam result not found!", "error");
            redirect("index.php?view=results");
            return;
        }
        
        $applicant_id = $result->APPLICANTID;
        
        // Delete exam result
        $sql = "DELETE FROM tbl_exam_results WHERE EXAM_RESULT_ID = $result_id";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        // Reset applicant's exam status to Pending
        $update_sql = "UPDATE tbl_applicants SET EXAM_STATUS = 'Pending' WHERE APPLICANTID = $applicant_id";
        $mydb->setQuery($update_sql);
        $mydb->executeQuery();
        
        // Delete any associated interview record
        $delete_interview_sql = "DELETE FROM tbl_interview WHERE APPLICANTID = $applicant_id";
        $mydb->setQuery($delete_interview_sql);
        $mydb->executeQuery();
        
        // Reset applicant status to Pending
        $status_sql = "UPDATE tbl_applicants SET STATUS = 'Pending' WHERE APPLICANTID = $applicant_id";
        $mydb->setQuery($status_sql);
        $mydb->executeQuery();
        
        // Log the action - FIXED
        $log_sql = "INSERT INTO tbl_application_log 
                    (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                    SELECT 
                        $applicant_id, 
                        " . $_SESSION['ADMIN_USERID'] . ", 
                        USERNAME, 
                        ROLE, 
                        'Exam result deleted',
                        'EXAM',
                        'Exam result deleted'
                    FROM tblusers 
                    WHERE USERID = " . $_SESSION['ADMIN_USERID'];
        $mydb->setQuery($log_sql);
        $mydb->executeQuery();
        
        message("Exam result deleted successfully!", "success");
        redirect("index.php?view=results");
    }
}
?>