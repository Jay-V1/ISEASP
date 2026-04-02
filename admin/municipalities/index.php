<?php
require_once("../../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title = "Municipalities Management";
$subtitle = "";

switch ($view) {
    case 'list':
        $content = 'list.php';
        $subtitle = 'Municipalities List';
        break;
    case 'add':
        $content = 'add.php';
        $subtitle = 'Add New Municipality';
        break;
    case 'edit':
        $content = 'edit.php';
        $subtitle = 'Edit Municipality';
        break;
    default:
        $content = 'list.php';
        $subtitle = 'Municipalities List';
}

require_once("../theme/templates.php");
?>