<?php
require_once("../../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

if ($_SESSION['ADMIN_ROLE'] != 'Super Admin') {
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
    case 'reset_password':
        resetPassword();
        break;
    case 'clear_logs':
        clearLogs();
        break;
}

function doInsert() {
    global $mydb;
    
    if (isset($_POST['save'])) {
        $fullname = trim($_POST['FULLNAME']);
        $username = trim($_POST['USERNAME']);
        $password = trim($_POST['PASS']);
        $confirm = trim($_POST['CONFIRM_PASS']);
        $role = $_POST['ROLE'];
        $status = $_POST['STATUS'];
        $email = isset($_POST['EMAIL']) ? trim($_POST['EMAIL']) : '';
        
        // Validate
        if ($password != $confirm) {
            message("Passwords do not match!", "error");
            redirect("index.php?view=add_user");
            return;
        }
        
        if (strlen($password) < 8) {
            message("Password must be at least 8 characters!", "error");
            redirect("index.php?view=add_user");
            return;
        }
        
        // Check if username exists
        $mydb->setQuery("SELECT * FROM tblusers WHERE USERNAME = '$username'");
        $mydb->executeQuery();
        $existing = $mydb->loadResultList();
        if (!empty($existing)) {
            message("Username already exists!", "error");
            redirect("index.php?view=add_user");
            return;
        }
        
        // Handle file upload
        $piclocation = '';
        if (isset($_FILES['PICLOCATION']) && $_FILES['PICLOCATION']['error'] == 0) {
            $target_dir = "../../admin/user/photos/";
            
            // Create directory if it doesn't exist
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES["PICLOCATION"]["name"], PATHINFO_EXTENSION);
            $piclocation = "user_" . time() . "." . $file_extension;
            $target_file = $target_dir . $piclocation;
            
            // Validate file type
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array(strtolower($file_extension), $allowed_types)) {
                if (move_uploaded_file($_FILES["PICLOCATION"]["tmp_name"], $target_file)) {
                    // Success
                } else {
                    $piclocation = '';
                    message("Error uploading file!", "error");
                }
            } else {
                $piclocation = '';
                message("Invalid file type! Only JPG, PNG, GIF and WEBP are allowed.", "error");
            }
        }
        
        // Hash password
        $hashed_password = sha1($password);
        
        // Insert user
        $sql = "INSERT INTO tblusers (FULLNAME, USERNAME, PASS, ROLE, PICLOCATION, EMAIL, DATECREATED) 
                VALUES ('$fullname', '$username', '$hashed_password', '$role', '$piclocation', '$email', NOW())";
        
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        $userid = $mydb->insert_id();
        
        // Insert into tbl_admin
        $admin_sql = "INSERT INTO tbl_admin (USERID, STATUS, CREATED_AT) VALUES ($userid, '$status', NOW())";
        $mydb->setQuery($admin_sql);
        $mydb->executeQuery();
        
        // Log action - FIXED: Changed ACTION_BY to USERID
        $log_sql = "INSERT INTO tbl_application_log 
                    (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                    VALUES (
                        NULL, 
                        " . $_SESSION['ADMIN_USERID'] . ", 
                        (SELECT USERNAME FROM tblusers WHERE USERID = " . $_SESSION['ADMIN_USERID'] . "),
                        (SELECT ROLE FROM tblusers WHERE USERID = " . $_SESSION['ADMIN_USERID'] . "),
                        'Created new user: $username',
                        'CREATE',
                        'New user account created'
                    )";
        $mydb->setQuery($log_sql);
        $mydb->executeQuery();
        
        message("User created successfully!", "success");
        redirect("index.php?view=users");
    }
}

function doEdit() {
    global $mydb;
    
    if (isset($_POST['save'])) {
        $userid = intval($_POST['USERID']);
        $fullname = trim($_POST['FULLNAME']);
        $username = trim($_POST['USERNAME']);
        $password = trim($_POST['PASS']);
        $confirm = trim($_POST['CONFIRM_PASS']);
        $role = $_POST['ROLE'];
        $status = $_POST['STATUS'];
        $email = isset($_POST['EMAIL']) ? trim($_POST['EMAIL']) : '';
        
        // Check if username exists for other users
        $mydb->setQuery("SELECT * FROM tblusers WHERE USERNAME = '$username' AND USERID != $userid");
        $mydb->executeQuery();
        $existing = $mydb->loadResultList();
        if (!empty($existing)) {
            message("Username already exists!", "error");
            redirect("index.php?view=edit_user&id=$userid");
            return;
        }
        
        // Handle file upload
        $pic_sql = "";
        if (isset($_FILES['PICLOCATION']) && $_FILES['PICLOCATION']['error'] == 0) {
            $target_dir = "../../admin/user/photos/";
            
            // Create directory if it doesn't exist
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES["PICLOCATION"]["name"], PATHINFO_EXTENSION);
            $piclocation = "user_" . time() . "." . $file_extension;
            $target_file = $target_dir . $piclocation;
            
            // Validate file type
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array(strtolower($file_extension), $allowed_types)) {
                if (move_uploaded_file($_FILES["PICLOCATION"]["tmp_name"], $target_file)) {
                    $pic_sql = ", PICLOCATION = '$piclocation'";
                    
                    // Delete old picture if exists
                    if (!empty($_POST['old_picture']) && file_exists($target_dir . $_POST['old_picture'])) {
                        @unlink($target_dir . $_POST['old_picture']);
                    }
                } else {
                    message("Error uploading file!", "error");
                }
            } else {
                message("Invalid file type! Only JPG, PNG, GIF and WEBP are allowed.", "error");
            }
        }
        
        // Update password if provided
        $pass_sql = "";
        if (!empty($password)) {
            if ($password != $confirm) {
                message("Passwords do not match!", "error");
                redirect("index.php?view=edit_user&id=$userid");
                return;
            }
            
            if (strlen($password) < 8) {
                message("Password must be at least 8 characters!", "error");
                redirect("index.php?view=edit_user&id=$userid");
                return;
            }
            
            $hashed_password = sha1($password);
            $pass_sql = ", PASS = '$hashed_password'";
        }
        
        // Update user
        $sql = "UPDATE tblusers SET 
                FULLNAME = '$fullname',
                USERNAME = '$username',
                ROLE = '$role',
                EMAIL = '$email'
                $pass_sql
                $pic_sql
                WHERE USERID = $userid";
        
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        // Update admin status
        $admin_sql = "UPDATE tbl_admin SET STATUS = '$status' WHERE USERID = $userid";
        $mydb->setQuery($admin_sql);
        $mydb->executeQuery();
        
        // Log action - FIXED: Changed ACTION_BY to USERID
        $log_sql = "INSERT INTO tbl_application_log 
                    (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                    VALUES (
                        NULL, 
                        " . $_SESSION['ADMIN_USERID'] . ", 
                        (SELECT USERNAME FROM tblusers WHERE USERID = " . $_SESSION['ADMIN_USERID'] . "),
                        (SELECT ROLE FROM tblusers WHERE USERID = " . $_SESSION['ADMIN_USERID'] . "),
                        'Updated user: $username',
                        'UPDATE',
                        'User account updated'
                    )";
        $mydb->setQuery($log_sql);
        $mydb->executeQuery();
        
        message("User updated successfully!", "success");
        redirect("index.php?view=users");
    }
}

function doDelete() {
    global $mydb;
    
    if (isset($_GET['id'])) {
        $userid = intval($_GET['id']);
        
        // Don't allow deleting own account
        if ($userid == $_SESSION['ADMIN_USERID']) {
            message("You cannot delete your own account!", "error");
            redirect("index.php?view=users");
            return;
        }
        
        // Get username for log
        $mydb->setQuery("SELECT USERNAME FROM tblusers WHERE USERID = $userid");
        $mydb->executeQuery();
        $user = $mydb->loadSingleResult();
        $username = $user ? $user->USERNAME : 'Unknown';
        
        // Delete from tbl_admin first (foreign key)
        $admin_sql = "DELETE FROM tbl_admin WHERE USERID = $userid";
        $mydb->setQuery($admin_sql);
        $mydb->executeQuery();
        
        // Delete user
        $sql = "DELETE FROM tblusers WHERE USERID = $userid";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        // Log action - FIXED: Changed ACTION_BY to USERID
        $log_sql = "INSERT INTO tbl_application_log 
                    (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                    VALUES (
                        NULL, 
                        " . $_SESSION['ADMIN_USERID'] . ", 
                        (SELECT USERNAME FROM tblusers WHERE USERID = " . $_SESSION['ADMIN_USERID'] . "),
                        (SELECT ROLE FROM tblusers WHERE USERID = " . $_SESSION['ADMIN_USERID'] . "),
                        'Deleted user: $username',
                        'DELETE',
                        'User account deleted'
                    )";
        $mydb->setQuery($log_sql);
        $mydb->executeQuery();
        
        message("User deleted successfully!", "success");
        redirect("index.php?view=users");
    }
}

function resetPassword() {
    global $mydb;
    
    if (isset($_POST['id'])) {
        $userid = intval($_POST['id']);
        
        // Generate random password
        $new_password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10);
        $hashed_password = sha1($new_password);
        
        $sql = "UPDATE tblusers SET PASS = '$hashed_password' WHERE USERID = $userid";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        // Get username for log
        $mydb->setQuery("SELECT USERNAME FROM tblusers WHERE USERID = $userid");
        $mydb->executeQuery();
        $user = $mydb->loadSingleResult();
        $username = $user ? $user->USERNAME : 'Unknown';
        
        // Log action - FIXED: Changed ACTION_BY to USERID
        $log_sql = "INSERT INTO tbl_application_log 
                    (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                    VALUES (
                        NULL, 
                        " . $_SESSION['ADMIN_USERID'] . ", 
                        (SELECT USERNAME FROM tblusers WHERE USERID = " . $_SESSION['ADMIN_USERID'] . "),
                        (SELECT ROLE FROM tblusers WHERE USERID = " . $_SESSION['ADMIN_USERID'] . "),
                        'Reset password for user: $username',
                        'UPDATE',
                        'Password reset'
                    )";
        $mydb->setQuery($log_sql);
        $mydb->executeQuery();
        
        echo json_encode(['status' => 'success', 'password' => $new_password]);
        exit;
    }
}

function clearLogs() {
    global $mydb;
    
    // Delete logs older than 30 days
    $sql = "DELETE FROM tbl_application_log WHERE LOG_DATE < DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    
    // Log this action - FIXED: Changed ACTION_BY to USERID
    $log_sql = "INSERT INTO tbl_application_log 
                (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                VALUES (
                    NULL, 
                    " . $_SESSION['ADMIN_USERID'] . ", 
                    (SELECT USERNAME FROM tblusers WHERE USERID = " . $_SESSION['ADMIN_USERID'] . "),
                    (SELECT ROLE FROM tblusers WHERE USERID = " . $_SESSION['ADMIN_USERID'] . "),
                    'Cleared old activity logs',
                    'OTHER',
                    'Deleted logs older than 30 days'
                )";
    $mydb->setQuery($log_sql);
    $mydb->executeQuery();
    
    echo json_encode(['status' => 'success']);
    exit;
}
?>