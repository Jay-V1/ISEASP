<?php
require_once("../../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title = "Reports";
$subtitle = "";

switch ($view) {
    case 'applicants':
        $content = 'applicants.php';
        $subtitle = 'Applicants Report';
        break;
    case 'scholars':
        $content = 'scholars.php';
        $subtitle = 'Scholars Report';
        break;
    case 'district':
        $content = 'district.php';
        $subtitle = 'Per District Report';
        break;
    case 'municipality':
        $content = 'municipality.php';
        $subtitle = 'Per Municipality Report';
        break;
    case 'statistics':
        $content = 'statistics.php';
        $subtitle = 'Program Statistics';
        break;
    default:
        $content = 'statistics.php';
        $subtitle = 'Dashboard Statistics';
}

require_once("../theme/templates.php");
?>