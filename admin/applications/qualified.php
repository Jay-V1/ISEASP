<?php 
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Qualified Applicants</h1>
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
                <th>4Ps/IP</th>
                <th>Interview Result</th>
                <th>Action</th>
            </tr>	
        </thead> 
        <?php
        $mydb->setQuery("
            SELECT a.*, 
                   i.RECOMMENDATION,
                   i.SCORE as interview_score
            FROM tbl_applicants a
            LEFT JOIN tbl_interview i ON a.APPLICANTID = i.APPLICANTID
            WHERE a.STATUS = 'Qualified'
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
                    <?php if($a->IS_4PS_BENEFICIARY == 'Yes'): ?>
                        <span class="label label-success">4Ps</span>
                    <?php endif; ?>
                    <?php if($a->IS_INDIGENOUS == 'Yes'): ?>
                        <span class="label label-info">IP</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($a->RECOMMENDATION == 'Pass'): ?>
                        <span class="label label-success">Passed (<?= $a->interview_score ?>%)</span>
                    <?php else: ?>
                        <span class="label label-warning">Pending</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <a href="./index.php?view=convert&id=<?= $a->APPLICANTID ?>" 
                       class="btn btn-success btn-xs" title="Convert to Scholar">
                        <i class="fa fa-graduation-cap"></i> Convert
                    </a>
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
                        <i class="fa fa-info-circle"></i> No qualified applicants found.
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>