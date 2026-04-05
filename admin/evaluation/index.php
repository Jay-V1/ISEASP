<?php
require_once("../../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title = "Qualified Applicants";
$subtitle = "";

switch ($view) {
    case 'list':
        $content = 'list.php';
        $subtitle = 'For Evaluation';
        break;
    case 'add':
        $content = 'add.php';
        $subtitle = 'Evaluate Applicant';
        break;
    case 'edit':
        $content = 'edit.php';
        $subtitle = 'Edit Evaluation';
        break;
    case 'view':
        $content = 'view.php';
        $subtitle = 'Evaluation Details';
        break;
    case 'qualified':
        $content = 'qualified.php';
        $subtitle = 'Qualified Applicants';
        break;
    default:
        $content = 'list.php';
        $subtitle = 'For Evaluation';
}

require_once("../theme/templates.php");
?>