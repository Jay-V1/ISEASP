<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

global $mydb;
?>

<!-- <div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Qualified Applicants</h1>
    </div>
</div> -->

<!-- Action Buttons -->
<!-- <div class="row">
    <div class="col-lg-12" style="margin-bottom: 15px;">
        <a href="index.php" class="btn btn-primary">
            <i class="fa fa-arrow-left"></i> Back to Evaluation
        </a>
        <a href="#" onclick="window.print()" class="btn btn-default">
            <i class="fa fa-print"></i> Print List
        </a>
    </div>
</div> -->

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-success">
            <div class="panel-heading">
                <i class="fa fa-star"></i> Qualified Applicants Ready for Scholar Conversion
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="dash-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Applicant Name</th>
                                <th>Municipality</th>
                                <th>School</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Exam Score</th>
                                <th>Interview Score</th>
                                <th>Evaluation Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "
                                SELECT 
                                    a.*,
                                    er.TOTAL_SCORE as EXAM_SCORE,
                                    i.SCORE as INTERVIEW_SCORE,
                                    e.EVALUATION_DATE,
                                    e.FEEDBACK
                                FROM tbl_applicants a
                                LEFT JOIN tbl_exam_results er ON a.APPLICANTID = er.APPLICANTID
                                LEFT JOIN tbl_interview i ON a.APPLICANTID = i.APPLICANTID
                                INNER JOIN tbl_evaluation e ON a.APPLICANTID = e.APPLICANTID
                                WHERE a.STATUS = 'Qualified'
                                ORDER BY e.EVALUATION_DATE DESC
                            ";
                            
                            $mydb->setQuery($sql);
                            $mydb->executeQuery();
                            $applicants = $mydb->loadResultList();
                            
                            foreach ($applicants as $a):
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($a->LASTNAME . ', ' . $a->FIRSTNAME . ' ' . ($a->MIDDLENAME ?? '')) ?></td>
                                <td><?= htmlspecialchars($a->MUNICIPALITY ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($a->SCHOOL ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($a->COURSE ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($a->YEARLEVEL ?? 'N/A') ?></td>
                                <td><span class="label label-success"><?= $a->EXAM_SCORE ?? 'N/A' ?>%</span></td>
                                <td><span class="label label-info"><?= $a->INTERVIEW_SCORE ?? 'N/A' ?>%</span></td>
                                <td><?= $a->EVALUATION_DATE ? date('M d, Y', strtotime($a->EVALUATION_DATE)) : 'N/A' ?></td>
                                <td>
                                    <a href="../applications/index.php?view=convert&id=<?= $a->APPLICANTID ?>" class="btn btn-success btn-xs">
                                        <i class="fa fa-graduation-cap"></i> Convert to Scholar
                                    </a>
                                    <a href="index.php?view=view&id=<?= $a->EVALUATION_ID ?>" class="btn btn-info btn-xs">
                                        <i class="fa fa-eye"></i> View Evaluation
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($applicants)): ?>
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="alert alert-info" style="margin: 20px;">
                                        <i class="fa fa-info-circle"></i> No qualified applicants found.
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dash-table').DataTable({
        "pageLength": 25,
        "order": [[7, "desc"]]
    });
});
</script>