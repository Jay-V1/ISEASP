<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) redirect("index.php?view=results");

global $mydb;

// Get exam result details
$sql = "
    SELECT er.*, a.*
    FROM tbl_exam_results er
    INNER JOIN tbl_applicants a ON er.APPLICANTID = a.APPLICANTID
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
        <h1 class="page-header">Edit Exam Result</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-edit"></i> Edit Exam Result for <?= htmlspecialchars($result->LASTNAME . ', ' . $result->FIRSTNAME) ?>
            </div>
            <div class="panel-body">
                <!-- Applicant Information -->
                <div class="well">
                    <h4>Applicant Information</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Name:</th>
                            <td><?= htmlspecialchars($result->LASTNAME . ', ' . $result->FIRSTNAME . ' ' . ($result->MIDDLENAME ?? '')) ?></td>
                        </tr>
                        <tr>
                            <th>Exam Slip #:</th>
                            <td><?= htmlspecialchars($result->EXAM_SLIP_NUMBER ?? 'N/A') ?></td>
                        </tr>
                    </table>
                </div>
                
                <form method="POST" action="controller.php?action=edit" class="form-horizontal">
                    <input type="hidden" name="id" value="<?= $result->EXAM_RESULT_ID ?>">
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Total Score (%):</label>
                        <div class="col-md-6">
                            <input type="number" name="TOTAL_SCORE" class="form-control" 
                                   value="<?= $result->TOTAL_SCORE ?>" min="0" max="100" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Passing Score (%):</label>
                        <div class="col-md-6">
                            <input type="number" name="PASSING_SCORE" class="form-control" 
                                   value="<?= $result->PASSING_SCORE ?>" min="0" max="100" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Remarks:</label>
                        <div class="col-md-6">
                            <textarea name="REMARKS" class="form-control" rows="3"><?= htmlspecialchars($result->REMARKS) ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Exam Date:</label>
                        <div class="col-md-6">
                            <input type="datetime-local" name="EXAM_DATE" class="form-control" 
                                   value="<?= date('Y-m-d\TH:i', strtotime($result->EXAM_DATE)) ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-md-offset-3 col-md-6">
                            <button type="submit" name="save" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Result
                            </button>
                            <a href="index.php?view=results" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 
                        <strong>Note:</strong> If you change the result to "Passed", an interview record will be automatically created if one doesn't exist already.
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>