<?php
require_once("../../include/initialize.php");

if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) redirect("index.php");

global $mydb;

// Get applicant details with exam and interview
$sql = "
    SELECT 
        a.*,
        er.TOTAL_SCORE as EXAM_SCORE,
        er.PASSING_SCORE,
        i.SCORE as INTERVIEW_SCORE,
        i.RECOMMENDATION,
        i.COMMENTS as INTERVIEW_COMMENTS,
        (SELECT COUNT(*) FROM tbl_applicant_requirement_checklist 
         WHERE APPLICANTID = a.APPLICANTID AND IS_VERIFIED = 1) as VERIFIED_REQ,
        (SELECT COUNT(*) FROM tbl_requirement WHERE REQUIRED = 'Yes') as TOTAL_REQ
    FROM tbl_applicants a
    LEFT JOIN tbl_exam_results er ON a.APPLICANTID = er.APPLICANTID
    LEFT JOIN tbl_interview i ON a.APPLICANTID = i.APPLICANTID
    WHERE a.APPLICANTID = $id
";

$mydb->setQuery($sql);
$mydb->executeQuery();
$applicant = $mydb->loadSingleResult();

if (!$applicant) {
    message("Applicant not found!", "error");
    redirect("index.php");
}

// Check if already evaluated
$mydb->setQuery("SELECT * FROM tbl_evaluation WHERE APPLICANTID = $id");
$mydb->executeQuery();
$existing = $mydb->loadSingleResult();

if ($existing) {
    message("This applicant has already been evaluated. You can edit the evaluation.", "info");
    redirect("index.php?view=edit&id=" . $existing->EVALUATION_ID);
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Final Evaluation</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-gavel"></i> Evaluate: <?= htmlspecialchars($applicant->LASTNAME . ', ' . $applicant->FIRSTNAME . ' ' . ($applicant->MIDDLENAME ?? '')) ?>
            </div>
            <div class="panel-body">
                
                <!-- Summary Card -->
                <div class="well">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Exam Score:</strong> 
                            <span class="label label-success"><?= $applicant->EXAM_SCORE ?? 'N/A' ?>%</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Interview Score:</strong> 
                            <span class="label label-info"><?= $applicant->INTERVIEW_SCORE ?? 'N/A' ?>%</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Requirements:</strong> 
                            <span class="label label-warning"><?= $applicant->VERIFIED_REQ ?>/<?= $applicant->TOTAL_REQ ?> Verified</span>
                        </div>
                    </div>
                </div>
                
                <form method="POST" action="controller.php?action=add" class="form-horizontal">
                    <input type="hidden" name="APPLICANTID" value="<?= $applicant->APPLICANTID ?>">
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Final Status:</label>
                        <div class="col-md-6">
                            <select name="FINAL_STATUS" class="form-control" required>
                                <option value="">-- Select Status --</option>
                                <option value="Qualified">QUALIFIED - Approve as Scholar</option>
                                <option value="Not Qualified">NOT QUALIFIED - Reject Application</option>
                                <option value="For Review">FOR REVIEW - Need More Information</option>
                            </select>
                            <span class="help-block">
                                <strong>Qualified:</strong> Applicant meets all requirements and is approved<br>
                                <strong>Not Qualified:</strong> Applicant does not meet requirements<br>
                                <strong>For Review:</strong> Needs additional review or documents
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Feedback/Comments:</label>
                        <div class="col-md-6">
                            <textarea name="FEEDBACK" class="form-control" rows="5" required 
                                      placeholder="Enter your evaluation feedback, reasons for approval/rejection, etc."></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Recommendation:</label>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="SEND_NOTIFICATION" value="1" checked>
                                    Send notification to applicant
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="SCHEDULE_ORIENTATION" value="1">
                                    Schedule orientation (for qualified applicants)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-md-offset-3 col-md-6">
                            <button type="submit" name="save" class="btn btn-success">
                                <i class="fa fa-save"></i> Submit Evaluation
                            </button>
                            <a href="index.php" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>