<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$APPLICANTID = $_GET['id'];

// ================= APPLICANT INFO =================
$mydb->setQuery("
    SELECT * FROM tbl_applicants WHERE APPLICANTID = '$APPLICANTID'
");
$applicant = $mydb->loadSingleResult();

// ================= REQUIREMENTS =================
$mydb->setQuery("
    SELECT r.REQUIREMENT_ID, r.REQUIREMENT_NAME,
           ar.STATUS, ar.REMARKS
    FROM tbl_requirement r
    LEFT JOIN tbl_applicant_requirement ar
        ON r.REQUIREMENT_ID = ar.REQUIREMENT_ID
        AND ar.APPLICANTID = '$APPLICANTID'
    ORDER BY r.REQUIREMENT_NAME ASC
");
$requirements = $mydb->loadResultList();
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Applicant Evaluation</h1>
    </div>
</div>

<!-- ================= APPLICANT INFO ================= -->
<div class="panel panel-primary">
    <div class="panel-heading">Applicant Information</div>
    <div class="panel-body">

        <div class="row">
            <div class="col-md-6">
                <p><strong>Name:</strong> <?= $applicant->FIRSTNAME . ' ' . $applicant->LASTNAME ?></p>
                <p><strong>Contact:</strong> <?= $applicant->CONTACT ?></p>
            </div>

            <div class="col-md-6">
                <p><strong>Email:</strong> <?= $applicant->EMAIL ?></p>
                <p><strong>School:</strong> <?= $applicant->SCHOOL ?> (<?= $applicant->YEARLEVEL ?>)</p>
            </div>
        </div>

    </div>
</div>

<form method="POST" action="controller.php?action=saveEvaluation">

<input type="hidden" name="APPLICANTID" value="<?= $APPLICANTID ?>">

<!-- ================= REQUIREMENTS ================= -->
<div class="panel panel-info">
    <div class="panel-heading">Requirement Verification</div>
    <div class="panel-body">

        <table class="table table-bordered">
            <thead style="background:#3c8dbc; color:white;">
                <tr>
                    <th>Requirement</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($requirements as $req): ?>
                <tr>
                    <td><?= $req->REQUIREMENT_NAME ?></td>

                    <td>
                        <select name="status[<?= $req->REQUIREMENT_ID ?>]" class="form-control">
                            <?php
                            $statuses = ['Complete','Incomplete','Missing','Invalid'];
                            foreach ($statuses as $s):
                            ?>
                            <option value="<?= $s ?>" <?= ($req->STATUS == $s) ? 'selected' : '' ?>>
                                <?= $s ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </td>

                    <td>
                        <input type="text"
                               name="remarks[<?= $req->REQUIREMENT_ID ?>]"
                               value="<?= $req->REMARKS ?>"
                               class="form-control">
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>

    </div>
</div>

<!-- ================= FINAL DECISION ================= -->
<div class="panel panel-success">
    <div class="panel-heading">Evaluator Decision</div>
    <div class="panel-body">

        <div class="form-group">
            <label>Decision</label>
            <select name="FINAL_STATUS" class="form-control">
                <option value="Evaluation">Pending</option>
                <option value="Exam">For Exam</option>
                <option value="Rejected">Failed Evaluation</option>
            </select>
        </div>

        <div class="form-group">
            <label>Feedback</label>
            <textarea name="FEEDBACK" class="form-control"></textarea>
        </div>

    </div>
</div>

<button class="btn btn-primary btn-lg">
    <i class="fa fa-save"></i> Save Evaluation
</button>

<a href="index.php?view=list" class="btn btn-default btn-lg">
    Return
</a>

</form>
