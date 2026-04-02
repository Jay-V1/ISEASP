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
    
    if (isset($_POST['save'])) {
        $applicant_id = intval($_POST['APPLICANTID']);
        $final_status = $_POST['FINAL_STATUS'];
        $feedback = trim($_POST['FEEDBACK']);
        $evaluated_by = $_SESSION['ADMIN_USERID'];
        
        // Insert evaluation
        $sql = "INSERT INTO tbl_evaluation 
                (APPLICANTID, EVALUATED_BY, FINAL_STATUS, FEEDBACK, EVALUATION_DATE)
                VALUES ($applicant_id, $evaluated_by, '$final_status', '$feedback', NOW())";
        
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        // Update applicant status based on final status
        if ($final_status == 'Qualified') {
            $update_sql = "UPDATE tbl_applicants SET STATUS = 'Qualified' WHERE APPLICANTID = $applicant_id";
            $message = "Applicant has been QUALIFIED. They can now be converted to scholar.";
        } elseif ($final_status == 'Not Qualified') {
            $update_sql = "UPDATE tbl_applicants SET STATUS = 'Rejected' WHERE APPLICANTID = $applicant_id";
            $message = "Applicant has been REJECTED.";
        } else {
            $update_sql = "UPDATE tbl_applicants SET STATUS = 'For Review' WHERE APPLICANTID = $applicant_id";
            $message = "Applicant moved to FOR REVIEW status.";
        }
        
        $mydb->setQuery($update_sql);
        $mydb->executeQuery();
        
        // Add to scholarship history if qualified
        if ($final_status == 'Qualified') {
            // Get applicant's GPA
            $mydb->setQuery("SELECT GPA FROM tbl_applicants WHERE APPLICANTID = $applicant_id");
            $mydb->executeQuery();
            $gpa_result = $mydb->loadSingleResult();
            $gpa = $gpa_result->GPA ?? 0;
            
            $history_sql = "INSERT INTO tbl_scholarship_history 
                           (APPLICANTID, SCHOOL_YEAR, SEMESTER, STATUS, GPA, REMARKS, UPDATED_BY)
                           SELECT 
                               $applicant_id, 
                               SCHOOL_YEAR, 
                               SEMESTER, 
                               'Interviewed', 
                               $gpa, 
                               'Passed final evaluation - Qualified', 
                               $evaluated_by
                           FROM tbl_applicants 
                           WHERE APPLICANTID = $applicant_id";
            
            $mydb->setQuery($history_sql);
            $mydb->executeQuery();
        }
        
        // Log the action
        $log_sql = "INSERT INTO tbl_application_log (APPLICANTID, ACTION, ACTION_BY)
                    VALUES ($applicant_id, 'Final evaluation: $final_status', $evaluated_by)";
        $mydb->setQuery($log_sql);
        $mydb->executeQuery();
        
        message("Evaluation saved successfully! " . $message, "success");
        redirect("index.php?view=qualified");
    }
}

function doEdit() {
    global $mydb;
    
    if (isset($_POST['save'])) {
        $evaluation_id = intval($_POST['id']);
        $final_status = $_POST['FINAL_STATUS'];
        $feedback = trim($_POST['FEEDBACK']);
        
        // Get applicant ID
        $mydb->setQuery("SELECT APPLICANTID FROM tbl_evaluation WHERE EVALUATION_ID = $evaluation_id");
        $mydb->executeQuery();
        $result = $mydb->loadSingleResult();
        $applicant_id = $result->APPLICANTID;
        
        // Update evaluation
        $sql = "UPDATE tbl_evaluation SET 
                FINAL_STATUS = '$final_status',
                FEEDBACK = '$feedback'
                WHERE EVALUATION_ID = $evaluation_id";
        
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        // Update applicant status
        if ($final_status == 'Qualified') {
            $update_sql = "UPDATE tbl_applicants SET STATUS = 'Qualified' WHERE APPLICANTID = $applicant_id";
        } elseif ($final_status == 'Not Qualified') {
            $update_sql = "UPDATE tbl_applicants SET STATUS = 'Rejected' WHERE APPLICANTID = $applicant_id";
        } else {
            $update_sql = "UPDATE tbl_applicants SET STATUS = 'For Review' WHERE APPLICANTID = $applicant_id";
        }
        
        $mydb->setQuery($update_sql);
        $mydb->executeQuery();
        
        // Log the action
        $log_sql = "INSERT INTO tbl_application_log (APPLICANTID, ACTION, ACTION_BY)
                    VALUES ($applicant_id, 'Evaluation updated: $final_status', " . $_SESSION['ADMIN_USERID'] . ")";
        $mydb->setQuery($log_sql);
        $mydb->executeQuery();
        
        message("Evaluation updated successfully!", "success");
        redirect("index.php?view=qualified");
    }
}

function doDelete() {
    global $mydb;
    
    if (isset($_GET['id'])) {
        $evaluation_id = intval($_GET['id']);
        
        // Get applicant ID
        $mydb->setQuery("SELECT APPLICANTID FROM tbl_evaluation WHERE EVALUATION_ID = $evaluation_id");
        $mydb->executeQuery();
        $result = $mydb->loadSingleResult();
        $applicant_id = $result->APPLICANTID;
        
        // Delete evaluation
        $sql = "DELETE FROM tbl_evaluation WHERE EVALUATION_ID = $evaluation_id";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        // Reset applicant status to For Interview
        $update_sql = "UPDATE tbl_applicants SET STATUS = 'For Interview' WHERE APPLICANTID = $applicant_id";
        $mydb->setQuery($update_sql);
        $mydb->executeQuery();
        
        // Log the action
        $log_sql = "INSERT INTO tbl_application_log (APPLICANTID, ACTION, ACTION_BY)
                    VALUES ($applicant_id, 'Evaluation deleted', " . $_SESSION['ADMIN_USERID'] . ")";
        $mydb->setQuery($log_sql);
        $mydb->executeQuery();
        
        message("Evaluation deleted successfully!", "success");
        redirect("index.php");
    }
}
?>