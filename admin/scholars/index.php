<?php
require_once("../../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';

// For print views, don't use the template
if ($view == 'print_payroll' || $view == 'print_masterlist') {
    $print_file = $view . '.php';
    if (file_exists($print_file)) {
        require_once($print_file);
    } else {
        echo "Print file not found!";
    }
    exit;
}

$title = "Scholars Management";
$subtitle = "";

switch ($view) {
    case 'view':
        $content = 'view.php';
        $subtitle = 'Scholar Details';
        break;
    case 'renew':
        $content = 'renew.php';
        $subtitle = 'Process Scholarship Renewal';
        break;
    case 'history':
        $content = 'history.php';
        $subtitle = 'Scholarship History';
        break;
    case 'graduates':
        $content = 'graduate.php';
        $subtitle = 'Graduated Scholars';
        break;
    case 'payroll':
        $content = 'payroll.php';
        $subtitle = 'Payroll Management';
        break;
    case 'payroll_details':
        $content = 'payroll_details.php';
        $subtitle = 'Payroll Details';
        break;
    case 'disbursement':
        $content = 'disbursement.php';
        $subtitle = 'Disbursement Records';
        break;
    case 'payroll_reports':
        $content = 'payroll_reports.php';
        $subtitle = 'Payroll Reports';
        break;
    case 'list':
    default:
        $content = 'list.php';
        $subtitle = 'Active Scholars';
}

require_once("../theme/templates.php");
?>