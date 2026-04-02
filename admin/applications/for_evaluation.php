<?php 
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">For Evaluation</h1>
    </div>
</div>

<!-- Action Buttons -->
<div class="row">
    <div class="col-lg-12" style="margin-bottom: 10px;">
        <a href="index.php?view=list" class="btn btn-primary">
            <i class="fa fa-arrow-left"></i> Back to List
        </a>
        <!-- <a href="#" onclick="window.print()" class="btn btn-default">
            <i class="fa fa-print"></i> Print List
        </a> -->
    </div>
</div>

<div class="table-responsive">					
    <table id="dash-table" class="table table-striped table-bordered table-hover" style="font-size:13px" cellspacing="0">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Municipality</th>
                <th>School</th>
                <th>Course</th>
                <th>Year</th>
                <th>Exam Result</th>
                <th>Requirements</th>
                <th>Action</th>
            </tr>	
        </thead> 
        <?php
        $mydb->setQuery("
            SELECT a.*,
                   er.TOTAL_SCORE,
                   (SELECT COUNT(*) FROM tbl_applicant_requirement_checklist 
                    WHERE APPLICANTID = a.APPLICANTID AND IS_VERIFIED = 1) AS VERIFIED_REQ,
                   (SELECT COUNT(*) FROM tbl_requirement WHERE REQUIRED = 'Yes') AS TOTAL_REQ
            FROM tbl_applicants a
            LEFT JOIN tbl_exam_results er ON a.APPLICANTID = er.APPLICANTID
            WHERE a.EXAM_STATUS = 'Passed' 
            AND a.STATUS = 'Pending'
            ORDER BY a.LASTNAME ASC
        ");

        $applicants = $mydb->loadResultList();
        ?>

        <tbody>
            <?php foreach ($applicants as $a): 
                $req_percentage = ($a->TOTAL_REQ > 0) ? round(($a->VERIFIED_REQ / $a->TOTAL_REQ) * 100) : 0;
            ?>
            <tr>
                <td><?= htmlspecialchars($a->LASTNAME . ', ' . $a->FIRSTNAME . ' ' . ($a->MIDDLENAME ?? '')) ?></td>
                <td><?= htmlspecialchars($a->MUNICIPALITY ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($a->SCHOOL ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($a->COURSE ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($a->YEARLEVEL ?? 'N/A') ?></td>
                <td><span class="label label-success"><?= $a->TOTAL_SCORE ?? 'Passed' ?>%</span></td>
                <td>
                    <div class="progress progress-xs">
                        <div class="progress-bar progress-bar-success" style="width: <?= $req_percentage ?>%"></div>
                    </div>
                    <small><?= $a->VERIFIED_REQ ?>/<?= $a->TOTAL_REQ ?> verified</small>
                </td>
                <td class="text-center">
                    <a href="<?php echo web_root;?>admin/checklist/index.php?view=view&id=<?= $a->APPLICANTID ?>" 
                        class="btn btn-info btn-xs" title="Check Requirements">
                        <i class="fa fa-check-square-o"></i> Checklist
                    </a>
                    <a href="../evaluation/index.php?view=add&id=<?= $a->APPLICANTID ?>" 
                       class="btn btn-success btn-xs" title="Evaluate">
                        <i class="fa fa-gavel"></i> Evaluate
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if(empty($applicants)): ?>
            <tr>
                <td colspan="8" class="text-center">
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="fa fa-info-circle"></i> No applicants pending evaluation.
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>