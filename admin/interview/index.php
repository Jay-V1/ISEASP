<?php
require_once("../../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title = "Interview Management";
$subtitle = "";

switch ($view) {
    case 'schedule':
        $content = 'schedule.php';
        $subtitle = 'Interview Schedule';
        break;
    case 'results':
        $content = 'results.php';
        $subtitle = 'Interview Results';
        break;
    case 'edit':
        $content = 'edit.php';
        $subtitle = 'Edit Interview';
        break;
    case 'view':
        $content = 'view.php';
        $subtitle = 'Interview Details';
        break;
    default:
        $content = 'schedule.php';
        $subtitle = 'Interview Schedule';
}

require_once("../theme/templates.php");
?>