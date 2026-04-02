<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

global $mydb;
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Pending Requirements Verification</h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            <strong>Note:</strong> Applicants must have ALL required documents verified before they can take the examination.
        </div>
    </div>
</div>

<div class="table-responsive">
    <table id="dash-table" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Applicant Name</th>
                <th>Municipality</th>
                <th>School</th>
                <th>Course</th>
                <th>Requirements Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $mydb->setQuery("
                SELECT 
                    a.*,
                    (SELECT COUNT(*) FROM tbl_applicant_requirement_checklist 
                     WHERE APPLICANTID = a.APPLICANTID AND IS_VERIFIED = 1) as verified_req,
                    (SELECT COUNT(*) FROM tbl_requirement WHERE REQUIRED = 'Yes') as total_req
                FROM tbl_applicants a
                WHERE a.REQUIREMENT_STATUS != 'Complete' OR a.REQUIREMENT_STATUS IS NULL
                ORDER BY a.DATECREATED ASC
            ");
            $mydb->executeQuery();
            $applicants = $mydb->loadResultList();
            
            foreach ($applicants as $a):
                $percentage = ($a->total_req > 0) ? round(($a->verified_req / $a->total_req) * 100) : 0;
            ?>
            <tr>
                <td><?= htmlspecialchars($a->LASTNAME . ', ' . $a->FIRSTNAME . ' ' . ($a->MIDDLENAME ?? '')) ?></td>
                <td><?= htmlspecialchars($a->MUNICIPALITY ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($a->SCHOOL ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($a->COURSE ?? 'N/A') ?></td>
                <td>
                    <div class="progress">
                        <div class="progress-bar progress-bar-<?= ($percentage == 100) ? 'success' : 'warning' ?>" 
                             style="width: <?= $percentage ?>%">
                            <?= $a->verified_req ?>/<?= $a->total_req ?> Verified
                        </div>
                    </div>
                </td>
                <td>
                    <a href="../checklist/index.php?view=view&id=<?= $a->APPLICANTID ?>" 
                       class="btn btn-info btn-xs">
                        <i class="fa fa-check-square-o"></i> Verify Requirements
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>