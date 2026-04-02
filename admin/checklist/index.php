<?php
require_once("../../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title = "Requirements Checklist";
$subtitle = "";

switch ($view) {
    case 'list':
        $content = 'list.php';
        $subtitle = 'All Applicants Requirements';
        break;
    case 'view':
        $content = 'view.php';
        $subtitle = 'Applicant Requirements';
        break;
    case 'manage':
        $content = 'checklist.php';
        $subtitle = 'Manage Applicant Requirements';
        break;
    case 'manage_req':
        $content = 'manage_requirements.php';
        $subtitle = 'Manage Requirements List';
        break;
    default:
        // If ID is provided, go to view, otherwise show list
        if (isset($_GET['id']) && $_GET['id'] > 0) {
            $content = 'view.php';
            $subtitle = 'Applicant Requirements';
        } else {
            $content = 'list.php';
            $subtitle = 'All Applicants Requirements';
        }
}

require_once("../theme/templates.php");
?>