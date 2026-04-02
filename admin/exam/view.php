<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) redirect("index.php?view=results");

global $mydb;

// Get exam result details
$sql = "
    SELECT er.*, a.*, u.FULLNAME as EXAMINER_NAME
    FROM tbl_exam_results er
    INNER JOIN tbl_applicants a ON er.APPLICANTID = a.APPLICANTID
    LEFT JOIN tblusers u ON er.EXAMINER_ID = u.USERID
    WHERE er.EXAM_RESULT_ID = $id
";
$mydb->setQuery($sql);
$mydb->executeQuery();
$result = $mydb->loadSingleResult();

if (!$result) {
    message("Exam result not found!", "error");
    redirect("index.php?view=results");
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Exam Result Details</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-info">
            <div class="panel-heading">
                <i class="fa fa-file-text"></i> Exam Result Information
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th width="30%">Applicant Name:</th>
                        <td><?= htmlspecialchars($result->LASTNAME . ', ' . $result->FIRSTNAME . ' ' . ($result->MIDDLENAME ?? '')) ?></td>
                    </tr>
                    <tr>
                        <th>Municipality:</th>
                        <td><?= htmlspecialchars($result->MUNICIPALITY ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>School:</th>
                        <td><?= htmlspecialchars($result->SCHOOL ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Course:</th>
                        <td><?= htmlspecialchars($result->COURSE ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Exam Slip #:</th>
                        <td><?= htmlspecialchars($result->EXAM_SLIP_NUMBER ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Exam Date:</th>
                        <td><?= date('F d, Y h:i A', strtotime($result->EXAM_DATE)) ?></td>
                    </tr>
                    <tr>
                        <th>Total Score:</th>
                        <td><strong><?= $result->TOTAL_SCORE ?>%</strong></td>
                    </tr>
                    <tr>
                        <th>Passing Score:</th>
                        <td><?= $result->PASSING_SCORE ?>%</td>
                    </tr>
                    <tr>
                        <th>Result:</th>
                        <td>
                            <?php if ($result->TOTAL_SCORE >= $result->PASSING_SCORE): ?>
                                <span class="label label-success">PASSED</span>
                            <?php else: ?>
                                <span class="label label-danger">FAILED</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Examined By:</th>
                        <td><?= htmlspecialchars($result->EXAMINER_NAME ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Remarks:</th>
                        <td><?= nl2br(htmlspecialchars($result->REMARKS ?? 'No remarks')) ?></td>
                    </tr>
                </table>
                
                <div class="text-center" style="margin-top: 20px;">
                    <a href="index.php?view=edit&id=<?= $result->EXAM_RESULT_ID ?>" class="btn btn-primary">
                        <i class="fa fa-edit"></i> Edit Result
                    </a>
                    <a href="index.php?view=results" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Back to Results
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>