<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) redirect("index.php?view=schedule");

global $mydb;

// Get applicant details
$mydb->setQuery("SELECT * FROM tbl_applicants WHERE APPLICANTID = $id");
$mydb->executeQuery();
$applicant = $mydb->loadSingleResult();

if (!$applicant) {
    message("Applicant not found!", "error");
    redirect("index.php?view=schedule");
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add Exam Result</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-pencil"></i> Enter Exam Result for <?= htmlspecialchars($applicant->LASTNAME . ', ' . $applicant->FIRSTNAME) ?>
            </div>
            <div class="panel-body">
                <!-- Applicant Information -->
                <div class="well">
                    <h4>Applicant Information</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Name:</th>
                            <td><?= htmlspecialchars($applicant->LASTNAME . ', ' . $applicant->FIRSTNAME . ' ' . ($applicant->MIDDLENAME ?? '')) ?></td>
                        </tr>
                        <tr>
                            <th>Exam Slip #:</th>
                            <td><?= htmlspecialchars($applicant->EXAM_SLIP_NUMBER ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <th>Exam Date:</th>
                            <td><?= $applicant->EXAM_DATE ? date('F d, Y', strtotime($applicant->EXAM_DATE)) : 'N/A' ?></td>
                        </tr>
                        <tr>
                            <th>Exam Time:</th>
                            <td><?= $applicant->EXAM_TIME ? date('h:i A', strtotime($applicant->EXAM_TIME)) : 'N/A' ?></td>
                        </tr>
                        <tr>
                            <th>Venue:</th>
                            <td><?= htmlspecialchars($applicant->EXAM_VENUE ?? 'N/A') ?></td>
                        </tr>
                    </table>
                </div>
                
                <form method="POST" action="controller.php?action=add" class="form-horizontal">
                    <input type="hidden" name="APPLICANTID" value="<?= $applicant->APPLICANTID ?>">
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Total Score (%):</label>
                        <div class="col-md-6">
                            <input type="number" name="TOTAL_SCORE" class="form-control" 
                                   min="0" max="100" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Passing Score (%):</label>
                        <div class="col-md-6">
                            <input type="number" name="PASSING_SCORE" class="form-control" 
                                   value="75" min="0" max="100" step="0.01" required>
                            <span class="help-block">Default passing score is 75%</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Remarks:</label>
                        <div class="col-md-6">
                            <textarea name="REMARKS" class="form-control" rows="3" 
                                      placeholder="Optional remarks about the exam"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Exam Date:</label>
                        <div class="col-md-6">
                            <input type="datetime-local" name="EXAM_DATE" class="form-control" 
                                   value="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>
                    </div>
                    
                    
                    <div class="form-group">
                        <div class="col-md-offset-3 col-md-6">
                            <button type="submit" name="save" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save Result
                            </button>
                            <a href="index.php?view=schedule" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 
                        <strong>Note:</strong> If the applicant passes (score >= passing score), an interview record will be automatically created.
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>