<?php

global $mydb;
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Applicants with Missing Requirements</h1>
    </div>
</div>

<div class="alert alert-danger">
    <i class="fa fa-exclamation-triangle"></i>
    <strong>NOTE:</strong> These applicants have incomplete requirements and are NOT eligible for examination.
    They must complete ALL requirements before an exam slip can be generated.
</div>

<div class="table-responsive">
    <table id="dash-table" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Applicant Name</th>
                <th>Municipality</th>
                <th>School</th>
                <th>Course</th>
                <th>Requirements Status</th>
                <th>Missing Requirements</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $mydb->setQuery("
                SELECT 
                    a.*,
                    (SELECT COUNT(*) FROM tbl_applicant_requirement_checklist 
                     WHERE APPLICANTID = a.APPLICANTID AND IS_SUBMITTED = 0) as missing_count,
                    (SELECT COUNT(*) FROM tbl_requirement WHERE REQUIRED = 'Yes') as total_req
                FROM tbl_applicants a
                WHERE a.REQUIREMENT_STATUS = 'Incomplete' 
                   OR a.REQUIREMENT_STATUS IS NULL
                ORDER BY a.DATECREATED DESC
            ");
            $mydb->executeQuery();
            $applicants = $mydb->loadResultList();
            
            foreach ($applicants as $a):
                $mydb->setQuery("
                    SELECT r.REQUIREMENT_NAME 
                    FROM tbl_applicant_requirement_checklist c
                    INNER JOIN tbl_requirement r ON c.REQUIREMENT_ID = r.REQUIREMENT_ID
                    WHERE c.APPLICANTID = $a->APPLICANTID AND c.IS_SUBMITTED = 0
                ");
                $mydb->executeQuery();
                $missing = $mydb->loadResultList();
            ?>
            <tr>
                <td><?= $a->APPLICANTID ?></td>
                <td><?= htmlspecialchars($a->LASTNAME . ', ' . $a->FIRSTNAME . ' ' . ($a->MIDDLENAME ?? '')) ?></td>
                <td><?= htmlspecialchars($a->MUNICIPALITY ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($a->SCHOOL ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($a->COURSE ?? 'N/A') ?></td>
                <td>
                    <span class="label label-danger">MISSING <?= $a->missing_count ?>/<?= $a->total_req ?></span>
                </td>
                <td>
                    <ul class="list-unstyled">
                        <?php foreach ($missing as $m): ?>
                        <li><i class="fa fa-times text-danger"></i> <?= htmlspecialchars($m->REQUIREMENT_NAME) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </td>
                <td>
                    <a href="../checklist/index.php?view=view&id=<?= $a->APPLICANTID ?>" 
                       class="btn btn-warning btn-xs">
                        <i class="fa fa-check-square-o"></i> Update Requirements
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($applicants)): ?>
            <tr>
                <td colspan="8" class="text-center">
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i> All applicants have complete requirements!
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>