<?php 
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">For Interview</h1>
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
                <th>Evaluation Result</th>
                <th>Interview Status</th>
                <th>Action</th>
            </tr>	
        </thead> 
        <?php
        $mydb->setQuery("
            SELECT a.*, 
                   e.FEEDBACK as evaluation_feedback,
                   i.INTERVIEW_DATE,
                   i.RECOMMENDATION
            FROM tbl_applicants a
            LEFT JOIN tbl_evaluation e ON a.APPLICANTID = e.APPLICANTID
            LEFT JOIN tbl_interview i ON a.APPLICANTID = i.APPLICANTID
            WHERE a.STATUS = 'For Interview'
            ORDER BY a.LASTNAME ASC
        ");

        $applicants = $mydb->loadResultList();
        ?>

        <tbody>
            <?php foreach ($applicants as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a->LASTNAME . ', ' . $a->FIRSTNAME . ' ' . ($a->MIDDLENAME ?? '')) ?></td>
                <td><?= htmlspecialchars($a->MUNICIPALITY ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($a->SCHOOL ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($a->COURSE ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($a->YEARLEVEL ?? 'N/A') ?></td>
                <td>
                    <?php if($a->evaluation_feedback): ?>
                        <span class="label label-success">Evaluated</span>
                    <?php else: ?>
                        <span class="label label-warning">Pending</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($a->INTERVIEW_DATE): ?>
                        <span class="label label-info">Scheduled: <?= date('M d, Y', strtotime($a->INTERVIEW_DATE)) ?></span>
                    <?php else: ?>
                        <span class="label label-warning">Not Scheduled</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if(!$a->INTERVIEW_DATE): ?>
                        <a href="../interview/index.php?view=schedule&id=<?= $a->APPLICANTID ?>" 
                           class="btn btn-warning btn-xs" title="Schedule Interview">
                            <i class="fa fa-calendar"></i> Schedule
                        </a>
                    <?php else: ?>
                        <a href="../interview/index.php?view=result&id=<?= $a->APPLICANTID ?>" 
                           class="btn btn-success btn-xs" title="Update Result">
                            <i class="fa fa-check-circle"></i> Result
                        </a>
                    <?php endif; ?>
                    <a href="./index.php?view=view&id=<?= $a->APPLICANTID ?>" 
                       class="btn btn-info btn-xs" title="View Details">
                        <i class="fa fa-eye"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if(empty($applicants)): ?>
            <tr>
                <td colspan="8" class="text-center">
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="fa fa-info-circle"></i> No applicants scheduled for interview.
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>