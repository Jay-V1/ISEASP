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
        $municipality = trim($_POST['MUNICIPALITY_NAME']);
        $district = $_POST['DISTRICT'];
        $is_active = $_POST['IS_ACTIVE'];
        
        // Check if already exists
        $mydb->setQuery("SELECT * FROM tbl_municipalities WHERE MUNICIPALITY_NAME = '$municipality'");
        $mydb->executeQuery();
        $exists = $mydb->loadResultList();
        
        if (!empty($exists)) {
            message("Municipality already exists!", "error");
            redirect("index.php?view=add");
            return;
        }
        
        $sql = "INSERT INTO tbl_municipalities (MUNICIPALITY_NAME, DISTRICT, IS_ACTIVE) 
                VALUES ('$municipality', '$district', '$is_active')";
        
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        message("Municipality added successfully!", "success");
        redirect("index.php");
    }
}

function doEdit() {
    global $mydb;
    
    if (isset($_POST['save'])) {
        $id = intval($_POST['MUNICIPALITY_ID']);
        $municipality = trim($_POST['MUNICIPALITY_NAME']);
        $district = $_POST['DISTRICT'];
        $is_active = $_POST['IS_ACTIVE'];
        
        // Check if name already exists for other records
        $mydb->setQuery("SELECT * FROM tbl_municipalities WHERE MUNICIPALITY_NAME = '$municipality' AND MUNICIPALITY_ID != $id");
        $mydb->executeQuery();
        $exists = $mydb->loadResultList();
        
        if (!empty($exists)) {
            message("Municipality name already exists!", "error");
            redirect("index.php?view=edit&id=$id");
            return;
        }
        
        $sql = "UPDATE tbl_municipalities SET 
                MUNICIPALITY_NAME = '$municipality',
                DISTRICT = '$district',
                IS_ACTIVE = '$is_active'
                WHERE MUNICIPALITY_ID = $id";
        
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        message("Municipality updated successfully!", "success");
        redirect("index.php");
    }
}

function doDelete() {
    global $mydb;
    
    if ($_SESSION['ADMIN_ROLE'] != 'Super Admin') {
        message("Access denied!", "error");
        redirect("index.php");
        return;
    }
    
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        
        // Check if municipality is being used
        $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE MUNICIPALITY = (SELECT MUNICIPALITY_NAME FROM tbl_municipalities WHERE MUNICIPALITY_ID = $id)");
        $mydb->executeQuery();
        $usage = $mydb->loadSingleResult();
        
        if ($usage->total > 0) {
            message("Cannot delete municipality because it has $usage->total applicant records. Set it to inactive instead.", "error");
            redirect("index.php");
            return;
        }
        
        $sql = "DELETE FROM tbl_municipalities WHERE MUNICIPALITY_ID = $id";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        
        message("Municipality deleted successfully!", "success");
        redirect("index.php");
    }
}
?>