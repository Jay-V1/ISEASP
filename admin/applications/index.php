<?php
require_once("../../include/initialize.php");
if(!isset($_SESSION['ADMIN_USERID'])){
    redirect(web_root."admin/index.php");
}

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title = "Applications";
$subtitle = "";

switch ($view) {
    case 'list':
        $content = 'list.php';
        $subtitle = 'All Applicants';
        break;
    case 'add':
        $content = 'add.php';
        $subtitle = 'Add New Applicant';
        break;
    case 'edit':
        $content = 'edit.php';
        $subtitle = 'Edit Applicant';
        break;
    case 'view':
        $content = 'view.php';
        $subtitle = 'Applicant Details';
        break;
    case 'exam_slip':
        $content = 'exam_slip.php';
        $subtitle = 'Generate Examination Slip';
        break;
    case 'print_slip':
        $content = 'print_slip.php';
        $subtitle = 'Print Examination Slip';
        break;
    case 'for_exam':
        $content = 'for_exam.php';
        $subtitle = 'For Examination';
        break;
    case 'for_evaluation':
        $content = 'for_evaluation.php';
        $subtitle = 'For Evaluation';
        break;
    case 'for_interview':
        $content = 'for_interview.php';
        $subtitle = 'For Interview';
        break;
    case 'qualified':
        $content = 'qualified.php';
        $subtitle = 'Qualified Applicants';
        break;
    case 'missing_requirements':
        $content = 'missing_requirements.php';
        $subtitle = 'Missing Requirements';
        break;
    case 'convert':
        $content = 'convert.php';
        $subtitle = 'Convert to Scholar';
        break;
    default:
        $content = 'list.php';
        $subtitle = 'All Applicants';
}

require_once("../theme/templates.php");
?>