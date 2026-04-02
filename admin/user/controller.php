<?php
require_once ("../../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])){
    redirect(web_root."admin/index.php");
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

    case 'photos':
        doupdateimage();
        break;
    
    case 'reset_password':
        resetPassword();
        break;
}

function doInsert(){
    global $mydb;
    
    if(isset($_POST['save'])){
        if ($_POST['FULLNAME'] == "" OR $_POST['USERNAME'] == "" OR $_POST['PASS'] == "") {
            message("All required fields must be filled out!", "error");
            redirect('index.php?view=add');
        } else {    
            // Check if username already exists
            $mydb->setQuery("SELECT * FROM tblusers WHERE USERNAME = '" . $_POST['USERNAME'] . "'");
            $mydb->executeQuery();
            $existing = $mydb->loadResultList();
            if (!empty($existing)) {
                message("Username already exists!", "error");
                redirect('index.php?view=add');
                return;
            }
            
            // Handle file upload
            $piclocation = '';
            if (isset($_FILES['PICLOCATION']) && $_FILES['PICLOCATION']['error'] == 0) {
                $target_dir = "photos/";
                
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
            
            $user = New User();
            $user->USERID        = $_POST['user_id'];
            $user->FULLNAME      = $_POST['FULLNAME'];
            $user->USERNAME      = $_POST['USERNAME'];
            $user->PASS          = sha1($_POST['PASS']);
            $user->ROLE          = $_POST['ROLE'];
            $user->PICLOCATION   = $piclocation;
            $user->create();

            // Insert into tbl_admin
            if(isset($_POST['STATUS'])) {
                $mydb->setQuery("INSERT INTO tbl_admin (USERID, STATUS, CREATED_AT) VALUES (" . $user->USERID . ", '" . $_POST['STATUS'] . "', NOW())");
                $mydb->executeQuery();
            }

            $autonum = New Autonumber(); 
            $autonum->auto_update('userid');

            // Log action
            $log_sql = "INSERT INTO tbl_application_log (APPLICANTID, ACTION, ACTION_BY) 
                        VALUES (NULL, 'Created new user: " . $_POST['USERNAME'] . "', " . $_SESSION['ADMIN_USERID'] . ")";
            $mydb->setQuery($log_sql);
            $mydb->executeQuery();

            message("User [". $_POST['FULLNAME'] ."] created successfully!", "success");
            redirect("index.php");
        }
    }
}

function doEdit(){
    global $mydb;
    
    if(isset($_POST['save'])){
        $userid = $_POST['USERID'];

        // Check if username already exists for other users
        $mydb->setQuery("SELECT * FROM tblusers WHERE USERNAME = '" . $_POST['USERNAME'] . "' AND USERID != " . $userid);
        $mydb->executeQuery();
        $existing = $mydb->loadResultList();
        if (!empty($existing)) {
            message("Username already exists!", "error");
            redirect("index.php?view=edit&id=" . $userid);
            return;
        }

        // Server-side password validation
        if (!empty($_POST['PASS'])) {
            if ($_POST['PASS'] !== $_POST['CONFIRM_PASS']) {
                message("New password and confirm password do not match!", "error");
                redirect("index.php?view=view");
                exit;
            }

            if (strlen($_POST['PASS']) < 8) {
                message("Password must be at least 8 characters long!", "error");
                redirect("index.php?view=view");
                exit;
            }
        }
        
        // Handle file upload
        $pic_sql = "";
        if (isset($_FILES['PICLOCATION']) && $_FILES['PICLOCATION']['error'] == 0) {
            $target_dir = "photos/";
            
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES["PICLOCATION"]["name"], PATHINFO_EXTENSION);
            $piclocation = "user_" . time() . "." . $file_extension;
            $target_file = $target_dir . $piclocation;
            
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array(strtolower($file_extension), $allowed_types)) {
                if (move_uploaded_file($_FILES["PICLOCATION"]["tmp_name"], $target_file)) {
                    $pic_sql = ", PICLOCATION = '$piclocation'";
                    
                    if (!empty($_POST['old_picture']) && file_exists($target_dir . $_POST['old_picture'])) {
                        @unlink($target_dir . $_POST['old_picture']);
                    }
                }
            }
        }
        
        $pass_sql = "";
        if (!empty($_POST['PASS'])) {
            $pass_sql = ", PASS = '" . sha1($_POST['PASS']) . "'";
        }
        
        $sql = "UPDATE tblusers SET 
                FULLNAME = '" . $_POST['FULLNAME'] . "',
                USERNAME = '" . $_POST['USERNAME'] . "',
                ROLE = '" . $_POST['ROLE'] . "'
                $pass_sql
                $pic_sql
                WHERE USERID = " . $userid;
        
        $mydb->setQuery($sql);
        $mydb->executeQuery();

        message("Profile has been updated!", "success");
        redirect("index.php?view=view");
    }
}

function doDelete(){
    global $mydb;
    
    $id = $_GET['id'];
    
    // Don't allow deleting own account
    if ($id == $_SESSION['ADMIN_USERID']) {
        message("You cannot delete your own account!", "error");
        redirect('index.php');
        return;
    }
    
    // Get username for log
    $mydb->setQuery("SELECT USERNAME FROM tblusers WHERE USERID = " . $id);
    $mydb->executeQuery();
    $user = $mydb->loadSingleResult();
    $username = $user ? $user->USERNAME : 'Unknown';
    
    // Delete from tbl_admin first (foreign key)
    $mydb->setQuery("DELETE FROM tbl_admin WHERE USERID = " . $id);
    $mydb->executeQuery();
    
    // Delete user
    $user = New User();
    $user->delete($id);
    
    // Log action
    $log_sql = "INSERT INTO tbl_application_log (APPLICANTID, ACTION, ACTION_BY) 
                VALUES (NULL, 'Deleted user: " . $username . "', " . $_SESSION['ADMIN_USERID'] . ")";
    $mydb->setQuery($log_sql);
    $mydb->executeQuery();
    
    message("User has been deleted!", "info");
    redirect('index.php');
}

function doupdateimage() {
    global $mydb;
    
    // Check if file was uploaded
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] == UPLOAD_ERR_NO_FILE) {
        message("No image selected!", "error");
        redirect("index.php?view=view");
        exit;
    }
    
    $error = $_FILES['photo']['error'];
    $temp = $_FILES['photo']['tmp_name'];
    $myfile = $_FILES['photo']['name'];
    $file_type = $_FILES['photo']['type'];
    $file_size = $_FILES['photo']['size'];
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file_type, $allowed_types)) {
        message("Invalid file type! Only JPG, PNG, GIF, and WEBP are allowed.", "error");
        redirect("index.php?view=view");
        exit;
    }
    
    // Validate file size (max 2MB)
    if ($file_size > 2 * 1024 * 1024) {
        message("File too large! Maximum size is 2MB.", "error");
        redirect("index.php?view=view");
        exit;
    }
    
    // Create upload directory if it doesn't exist
    $upload_dir = "../../admin/user/photos/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $file_extension = pathinfo($myfile, PATHINFO_EXTENSION);
    $new_filename = "user_" . time() . "_" . rand(1000, 9999) . "." . $file_extension;
    $target_file = $upload_dir . $new_filename;
    
    // Upload the file
    if (move_uploaded_file($temp, $target_file)) {
        // Get current user
        $userid = $_SESSION['ADMIN_USERID'];
        
        // Get old photo to delete
        $mydb->setQuery("SELECT PICLOCATION FROM tblusers WHERE USERID = $userid");
        $mydb->executeQuery();
        $old_user = $mydb->loadSingleResult();
        
        // Delete old photo if exists and it's not the default
        if (!empty($old_user->PICLOCATION) && $old_user->PICLOCATION != 'default-profile.png') {
            $old_file = $upload_dir . $old_user->PICLOCATION;
            if (file_exists($old_file)) {
                @unlink($old_file);
            }
        }
        
        // Update database with new filename
        $sql = "UPDATE tblusers SET PICLOCATION = '$new_filename' WHERE USERID = $userid";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        // Log the action
        $log_sql = "INSERT INTO tbl_application_log 
                    (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                    SELECT 
                        NULL, 
                        $userid, 
                        USERNAME, 
                        ROLE, 
                        'Profile picture updated',
                        'UPDATE',
                        'User updated profile picture'
                    FROM tblusers 
                    WHERE USERID = $userid";
        $mydb->setQuery($log_sql);
        $mydb->executeQuery();
        
        message("Profile picture updated successfully!", "success");
        redirect("index.php?view=view");
    } else {
        message("Error uploading file! Please try again.", "error");
        redirect("index.php?view=view");
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
        
        // Log action
        $log_sql = "INSERT INTO tbl_application_log (APPLICANTID, ACTION, ACTION_BY) 
                    VALUES (NULL, 'Reset password for user: $username', " . $_SESSION['ADMIN_USERID'] . ")";
        $mydb->setQuery($log_sql);
        $mydb->executeQuery();
        
        echo json_encode(['status' => 'success', 'password' => $new_password]);
        exit;
    }
}
?>