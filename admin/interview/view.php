<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) redirect("index.php");

global $mydb;

// Get interview details
$sql = "
    SELECT 
        i.*,
        a.LASTNAME, a.FIRSTNAME, a.MIDDLENAME,
        a.MUNICIPALITY, a.SCHOOL, a.COURSE, a.YEARLEVEL,
        a.CONTACT, a.EMAIL,
        u.FULLNAME as INTERVIEWER_NAME
    FROM tbl_interview i
    INNER JOIN tbl_applicants a ON i.APPLICANTID = a.APPLICANTID
    LEFT JOIN tblusers u ON i.INTERVIEWER_ID = u.USERID
    WHERE i.INTERVIEW_ID = $id
";

$mydb->setQuery($sql);
$mydb->executeQuery();
$interview = $mydb->loadSingleResult();

if (!$interview) {
    message("Interview not found!", "error");
    redirect("index.php");
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Interview Details</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-info">
            <div class="panel-heading">
                <i class="fa fa-file-text"></i> Interview Information
            </div>
            <div class="panel-body">
                
                <div class="well">
                    <h4>Applicant Information</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Full Name:</th>
                            <td><?= htmlspecialchars($interview->LASTNAME . ', ' . $interview->FIRSTNAME . ' ' . ($interview->MIDDLENAME ?? '')) ?></td>
                        </tr>
                        <tr>
                            <th>Municipality:</th>
                            <td><?= htmlspecialchars($interview->MUNICIPALITY ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <th>School:</th>
                            <td><?= htmlspecialchars($interview->SCHOOL ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <th>Course:</th>
                            <td><?= htmlspecialchars($interview->COURSE ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <th>Year Level:</th>
                            <td><?= htmlspecialchars($interview->YEARLEVEL ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <th>Contact:</th>
                            <td><?= htmlspecialchars($interview->CONTACT ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><?= htmlspecialchars($interview->EMAIL ?? 'N/A') ?></td>
                        </tr>
                    </table>
                </div>
                
                <h4>Interview Details</h4>
                <table class="table table-bordered table-striped">
                    <tr>
                        <th width="30%">Interview Date:</th>
                        <td><?= date('F d, Y h:i A', strtotime($interview->INTERVIEW_DATE)) ?></td>
                    </tr>
                    <tr>
                        <th>Interview Mode:</th>
                        <td><?= $interview->INTERVIEW_MODE ?></td>
                    </tr>
                    <tr>
                        <th>Interviewer:</th>
                        <td><?= htmlspecialchars($interview->INTERVIEWER_NAME ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Score:</th>
                        <td><strong><?= $interview->SCORE ?>%</strong></td>
                    </tr>
                    <tr>
                        <th>Recommendation:</th>
                        <td>
                            <?php 
                            $rec_color = $interview->RECOMMENDATION == 'Pass' ? 'label-success' : 
                                        ($interview->RECOMMENDATION == 'Fail' ? 'label-danger' : 'label-warning');
                            ?>
                            <span class="label <?= $rec_color ?>"><?= $interview->RECOMMENDATION ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Comments/Notes:</th>
                        <td><?= nl2br(htmlspecialchars($interview->COMMENTS ?? 'No comments')) ?></td>
                    </tr>
                </table>
                
                <div class="text-center" style="margin-top: 20px;">
                    <a href="index.php?view=edit&id=<?= $interview->INTERVIEW_ID ?>" class="btn btn-primary">
                        <i class="fa fa-edit"></i> Edit Interview
                    </a>
                    <a href="index.php" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Back to Schedule
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>