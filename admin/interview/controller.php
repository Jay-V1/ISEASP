<?php
require_once("../../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
    case 'edit':
        doEdit();
        break;
    case 'delete':
        doDelete();
        break;
}

function doEdit() {
    global $mydb;
    
    if (isset($_POST['save'])) {
        $interview_id = intval($_POST['id']);
        $interview_date = date('Y-m-d H:i:s', strtotime($_POST['INTERVIEW_DATE']));
        $interview_mode = $_POST['INTERVIEW_MODE'];
        $interviewer_id = intval($_POST['INTERVIEWER_ID']);
        $score = floatval($_POST['SCORE']);
        $recommendation = $_POST['RECOMMENDATION'];
        $comments = trim($_POST['COMMENTS']);
        
        // Get applicant ID from interview
        $mydb->setQuery("SELECT APPLICANTID FROM tbl_interview WHERE INTERVIEW_ID = $interview_id");
        $mydb->executeQuery();
        $result = $mydb->loadSingleResult();
        $applicant_id = $result->APPLICANTID;
        
        // Update interview record
        $sql = "UPDATE tbl_interview SET 
                INTERVIEW_DATE = '$interview_date',
                INTERVIEW_MODE = '$interview_mode',
                INTERVIEWER_ID = $interviewer_id,
                SCORE = $score,
                RECOMMENDATION = '$recommendation',
                COMMENTS = '$comments'
                WHERE INTERVIEW_ID = $interview_id";
        
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        // Update applicant status based on recommendation
        if ($recommendation == 'Pass') {
            $update_sql = "UPDATE tbl_applicants SET STATUS = 'Qualified' WHERE APPLICANTID = $applicant_id";
        } elseif ($recommendation == 'Fail') {
            $update_sql = "UPDATE tbl_applicants SET STATUS = 'Rejected' WHERE APPLICANTID = $applicant_id";
        } else {
            $update_sql = "UPDATE tbl_applicants SET STATUS = 'For Interview' WHERE APPLICANTID = $applicant_id";
        }
        
        $mydb->setQuery($update_sql);
        $mydb->executeQuery();
        
        // Get user info for logging
        $user_id = $_SESSION['ADMIN_USERID'];
        $mydb->setQuery("SELECT USERNAME, ROLE FROM tblusers WHERE USERID = $user_id");
        $mydb->executeQuery();
        $user = $mydb->loadSingleResult();
        
        // Log the action - FIXED VERSION
        $log_sql = "INSERT INTO tbl_application_log (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                    VALUES ($applicant_id, $user_id, '{$user->USERNAME}', '{$user->ROLE}', 'Interview result recorded: $recommendation', 'INTERVIEW', 'Score: $score, Recommendation: $recommendation')";
        $mydb->setQuery($log_sql);
        $mydb->executeQuery();
        
        message("Interview result saved successfully!", "success");
        redirect("index.php");
    }
}

function doDelete() {
    global $mydb;
    
    if (isset($_GET['id'])) {
        $interview_id = intval($_GET['id']);
        
        // Get applicant ID before deleting
        $mydb->setQuery("SELECT APPLICANTID FROM tbl_interview WHERE INTERVIEW_ID = $interview_id");
        $mydb->executeQuery();
        $result = $mydb->loadSingleResult();
        $applicant_id = $result->APPLICANTID;
        
        // Delete interview
        $sql = "DELETE FROM tbl_interview WHERE INTERVIEW_ID = $interview_id";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        // Reset applicant status to Qualified (since they passed exam)
        $update_sql = "UPDATE tbl_applicants SET STATUS = 'Qualified' WHERE APPLICANTID = $applicant_id";
        $mydb->setQuery($update_sql);
        $mydb->executeQuery();
        
        // Get user info for logging
        $user_id = $_SESSION['ADMIN_USERID'];
        $mydb->setQuery("SELECT USERNAME, ROLE FROM tblusers WHERE USERID = $user_id");
        $mydb->executeQuery();
        $user = $mydb->loadSingleResult();
        
        // Log the action - FIXED VERSION
        $log_sql = "INSERT INTO tbl_application_log (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE)
                    VALUES ($applicant_id, $user_id, '{$user->USERNAME}', '{$user->ROLE}', 'Interview deleted', 'INTERVIEW')";
        $mydb->setQuery($log_sql);
        $mydb->executeQuery();
        
        message("Interview deleted successfully!", "success");
        redirect("index.php");
    }
}
?>