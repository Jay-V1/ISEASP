<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) redirect("index.php");

global $mydb;

// Get interview details
$sql = "
    SELECT i.*, a.LASTNAME, a.FIRSTNAME, a.MIDDLENAME, a.SCHOOL, a.COURSE, a.MUNICIPALITY
    FROM tbl_interview i
    INNER JOIN tbl_applicants a ON i.APPLICANTID = a.APPLICANTID
    WHERE i.INTERVIEW_ID = $id
";

$mydb->setQuery($sql);
$mydb->executeQuery();
$interview = $mydb->loadSingleResult();

if (!$interview) {
    message("Interview not found!", "error");
    redirect("index.php");
}

// Get interviewers
$mydb->setQuery("SELECT USERID, FULLNAME FROM tblusers WHERE ROLE IN ('Evaluator', 'Admin', 'Super Admin') ORDER BY FULLNAME");
$mydb->executeQuery();
$interviewers = $mydb->loadResultList();
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Enter Interview Result</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-edit"></i> Interview Result for <?= htmlspecialchars($interview->LASTNAME . ', ' . $interview->FIRSTNAME) ?>
            </div>
            <div class="panel-body">
                
                <div class="well">
                    <h4>Applicant Information</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Name:</th>
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
                    </table>
                </div>
                
                <form method="POST" action="controller.php?action=edit" class="form-horizontal">
                    <input type="hidden" name="id" value="<?= $interview->INTERVIEW_ID ?>">
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Interview Date & Time:</label>
                        <div class="col-md-8">
                            <input type="datetime-local" name="INTERVIEW_DATE" class="form-control" 
                                   value="<?= date('Y-m-d\TH:i', strtotime($interview->INTERVIEW_DATE)) ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Interview Mode:</label>
                        <div class="col-md-8">
                            <select name="INTERVIEW_MODE" class="form-control" required>
                                <option value="Face-to-face" <?= $interview->INTERVIEW_MODE == 'Face-to-face' ? 'selected' : '' ?>>Face-to-face</option>
                                <option value="Online" <?= $interview->INTERVIEW_MODE == 'Online' ? 'selected' : '' ?>>Online</option>
                                <option value="Phone" <?= $interview->INTERVIEW_MODE == 'Phone' ? 'selected' : '' ?>>Phone</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Interviewer:</label>
                        <div class="col-md-8">
                            <select name="INTERVIEWER_ID" class="form-control" required>
                                <option value="">Select Interviewer</option>
                                <?php foreach ($interviewers as $inv): ?>
                                <option value="<?= $inv->USERID ?>" <?= $inv->USERID == $interview->INTERVIEWER_ID ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($inv->FULLNAME) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Score (%):</label>
                        <div class="col-md-8">
                            <input type="number" name="SCORE" class="form-control" 
                                   value="<?= $interview->SCORE ?>" min="0" max="100" step="0.01" required>
                            <span class="help-block">Enter the interview score (0-100)</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Recommendation:</label>
                        <div class="col-md-8">
                            <select name="RECOMMENDATION" class="form-control" required>
                                <option value="For Review" <?= $interview->RECOMMENDATION == 'For Review' ? 'selected' : '' ?>>For Review (Pending)</option>
                                <option value="Pass" <?= $interview->RECOMMENDATION == 'Pass' ? 'selected' : '' ?>>Pass</option>
                                <option value="Fail" <?= $interview->RECOMMENDATION == 'Fail' ? 'selected' : '' ?>>Fail</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Comments/Notes:</label>
                        <div class="col-md-8">
                            <textarea name="COMMENTS" class="form-control" rows="4" 
                                      placeholder="Enter interview feedback, observations, etc."><?= htmlspecialchars($interview->COMMENTS ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-md-offset-3 col-md-8">
                            <button type="submit" name="save" class="btn btn-success">
                                <i class="fa fa-save"></i> Save Interview Result
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