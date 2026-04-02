<?php
require_once("../../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
    case 'update':
        doUpdate();
        break;
    case 'verify_all':
        verifyAll();
        break;
    case 'reset':
        resetChecklist();
        break;
}

function doUpdate() {
    global $mydb;
    
    if (isset($_POST['update'])) {
        $applicant_id = intval($_POST['applicant_id']);
        
        foreach ($_POST['requirements'] as $req_id => $values) {
            $is_submitted = isset($values['submitted']) ? 1 : 0;
            $is_verified = isset($values['verified']) ? 1 : 0;
            $remarks = trim($values['remarks']);
            
            $sql = "UPDATE tbl_applicant_requirement_checklist SET 
                    IS_SUBMITTED = $is_submitted,
                    IS_VERIFIED = $is_verified,
                    REMARKS = '$remarks',
                    VERIFIED_BY = " . ($is_verified ? $_SESSION['ADMIN_USERID'] : "NULL") . ",
                    VERIFIED_DATE = " . ($is_verified ? "NOW()" : "NULL") . "
                    WHERE APPLICANTID = $applicant_id AND REQUIREMENT_ID = $req_id";
            
            $mydb->setQuery($sql);
            $mydb->executeQuery();
        }
        
        // ============================================
        // UPDATE APPLICANT REQUIREMENT STATUS
        // ============================================
        
        // Check if all required requirements are now complete
        if (checkRequirementsComplete($applicant_id)) {
            // Update applicant requirement status to Complete
            $update_applicant = "UPDATE tbl_applicants SET 
                                 REQUIREMENT_STATUS = 'Complete',
                                 REQUIREMENT_DATE = NOW()
                                 WHERE APPLICANTID = $applicant_id";
            $mydb->setQuery($update_applicant);
            $mydb->executeQuery();
            
            // Log the action
            $log_sql = "INSERT INTO tbl_application_log 
                        (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                        SELECT 
                            $applicant_id, 
                            " . $_SESSION['ADMIN_USERID'] . ", 
                            USERNAME, 
                            ROLE, 
                            'All requirements verified - Ready for Exam',
                            'REQUIREMENT',
                            'All requirements verified - Applicant ready for exam'
                        FROM tblusers 
                        WHERE USERID = " . $_SESSION['ADMIN_USERID'];
            $mydb->setQuery($log_sql);
            $mydb->executeQuery();
        } else {
            // Update applicant requirement status to Incomplete
            $update_applicant = "UPDATE tbl_applicants SET 
                                 REQUIREMENT_STATUS = 'Incomplete',
                                 REQUIREMENT_DATE = NOW()
                                 WHERE APPLICANTID = $applicant_id";
            $mydb->setQuery($update_applicant);
            $mydb->executeQuery();
            
            // Log the action (optional)
            $log_sql = "INSERT INTO tbl_application_log 
                        (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                        SELECT 
                            $applicant_id, 
                            " . $_SESSION['ADMIN_USERID'] . ", 
                            USERNAME, 
                            ROLE, 
                            'Requirements checklist updated - Incomplete',
                            'REQUIREMENT',
                            'Requirements checklist updated - Some requirements still missing'
                        FROM tblusers 
                        WHERE USERID = " . $_SESSION['ADMIN_USERID'];
            $mydb->setQuery($log_sql);
            $mydb->executeQuery();
        }
        
        message("Requirements checklist updated successfully!", "success");
        redirect("../applications/index.php?view=view&id=$applicant_id");
    }
}

function verifyAll() {
    global $mydb;
    
    if (isset($_POST['applicant_id'])) {
        $applicant_id = intval($_POST['applicant_id']);
        
        $sql = "UPDATE tbl_applicant_requirement_checklist 
                SET IS_VERIFIED = 1, 
                    VERIFIED_BY = " . $_SESSION['ADMIN_USERID'] . ", 
                    VERIFIED_DATE = NOW()
                WHERE APPLICANTID = $applicant_id";
        
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        // Update applicant requirement status to Complete
        $update_applicant = "UPDATE tbl_applicants SET 
                             REQUIREMENT_STATUS = 'Complete',
                             REQUIREMENT_DATE = NOW()
                             WHERE APPLICANTID = $applicant_id";
        $mydb->setQuery($update_applicant);
        $mydb->executeQuery();
        
        // Log the action
        $log_sql = "INSERT INTO tbl_application_log 
                    (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                    SELECT 
                        $applicant_id, 
                        " . $_SESSION['ADMIN_USERID'] . ", 
                        USERNAME, 
                        ROLE, 
                        'All requirements verified',
                        'REQUIREMENT',
                        'All requirements marked as verified'
                    FROM tblusers 
                    WHERE USERID = " . $_SESSION['ADMIN_USERID'];
        $mydb->setQuery($log_sql);
        $mydb->executeQuery();
        
        echo json_encode(['status' => 'success']);
        exit;
    }
}

function resetChecklist() {
    global $mydb;
    
    if (isset($_POST['applicant_id'])) {
        $applicant_id = intval($_POST['applicant_id']);
        
        $sql = "UPDATE tbl_applicant_requirement_checklist 
                SET IS_SUBMITTED = 0, 
                    IS_VERIFIED = 0, 
                    REMARKS = NULL,
                    VERIFIED_BY = NULL, 
                    VERIFIED_DATE = NULL
                WHERE APPLICANTID = $applicant_id";
        
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        // Update applicant requirement status to Incomplete
        $update_applicant = "UPDATE tbl_applicants SET 
                             REQUIREMENT_STATUS = 'Incomplete',
                             REQUIREMENT_DATE = NOW()
                             WHERE APPLICANTID = $applicant_id";
        $mydb->setQuery($update_applicant);
        $mydb->executeQuery();
        
        // Log the action
        $log_sql = "INSERT INTO tbl_application_log 
                    (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                    SELECT 
                        $applicant_id, 
                        " . $_SESSION['ADMIN_USERID'] . ", 
                        USERNAME, 
                        ROLE, 
                        'Requirements checklist reset',
                        'REQUIREMENT',
                        'All requirements verification reset to pending'
                    FROM tblusers 
                    WHERE USERID = " . $_SESSION['ADMIN_USERID'];
        $mydb->setQuery($log_sql);
        $mydb->executeQuery();
        
        echo json_encode(['status' => 'success']);
        exit;
    }
}

// this function checks if all required requirements are complete
function checkRequirementsComplete($applicant_id) {
    global $mydb;
    
    // Get count of required requirements
    $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_requirement WHERE REQUIRED = 'Yes'");
    $mydb->executeQuery();
    $result = $mydb->loadSingleResult();
    $total_required = $result ? $result->total : 0;
    
    // Get count of verified requirements
    $mydb->setQuery("SELECT COUNT(*) as verified FROM tbl_applicant_requirement_checklist 
                     WHERE APPLICANTID = $applicant_id AND IS_VERIFIED = 1");
    $mydb->executeQuery();
    $result = $mydb->loadSingleResult();
    $verified = $result ? $result->verified : 0;
    
    return ($verified >= $total_required);
}
?>