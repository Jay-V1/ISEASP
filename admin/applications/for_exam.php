<?php 
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">For Examination</h1>
    </div>
</div>

<!-- Action Buttons -->
<div class="row">
    <div class="col-lg-12" style="margin-bottom: 10px;">
        <a href="index.php?view=list" class="btn btn-primary">
            <i class="fa fa-arrow-left"></i> Back to List
        </a>
        <a href="#" onclick="window.print()" class="btn btn-default">
            <i class="fa fa-print"></i> Print List
        </a>
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
                <th>Exam Slip</th>
                <th>Exam Date</th>
                <th>Exam Time</th>
                <th>Venue</th>
                <th>Action</th>
            </tr>	
        </thead> 
        <?php
        $mydb->setQuery("
            SELECT a.*
            FROM tbl_applicants a
            WHERE a.EXAM_SLIP_GENERATED IS NOT NULL 
            AND a.EXAM_SLIP_GENERATED != '' 
            AND a.EXAM_STATUS = 'Pending'
            ORDER BY a.EXAM_DATE ASC, a.EXAM_TIME ASC
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
                <td><?= htmlspecialchars($a->EXAM_SLIP_NUMBER ?? 'N/A') ?></td>
                <td><?= $a->EXAM_DATE ? date('M d, Y', strtotime($a->EXAM_DATE)) : 'N/A' ?></td>
                <td><?= $a->EXAM_TIME ? date('h:i A', strtotime($a->EXAM_TIME)) : 'N/A' ?></td>
                <td><?= htmlspecialchars($a->EXAM_VENUE ?? 'N/A') ?></td>
                <td class="text-center">
                    <a href="./index.php?view=print_slip&id=<?= $a->APPLICANTID ?>" 
                       class="btn btn-primary btn-xs" title="Print Exam Slip" target="_blank">
                        <i class="fa fa-print"></i> Print
                    </a>
                    <a href="../exam/index.php?view=result&id=<?= $a->APPLICANTID ?>" 
                       class="btn btn-success btn-xs" title="Enter Exam Result">
                        <i class="fa fa-pencil"></i> Result
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if(empty($applicants)): ?>
            <tr>
                <td colspan="10" class="text-center">
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="fa fa-info-circle"></i> No applicants scheduled for examination.
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>