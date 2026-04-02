<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) redirect("index.php");

global $mydb;

// Get scholar award details
$sql = "
    SELECT 
        sa.*,
        a.LASTNAME, a.FIRSTNAME, a.MIDDLENAME, a.SUFFIX,
        a.MUNICIPALITY, a.BARANGAY, a.CONTACT, a.EMAIL,
        a.IS_4PS_BENEFICIARY, a.IS_INDIGENOUS,
        a.SCHOOL, a.COURSE, a.YEARLEVEL, a.GPA,
        a.STATUS as APPLICANT_STATUS,
        u.FULLNAME as AWARDED_BY_NAME
    FROM tbl_scholarship_awards sa
    INNER JOIN tbl_applicants a ON sa.APPLICANTID = a.APPLICANTID
    LEFT JOIN tblusers u ON sa.AWARDED_BY = u.USERID
    WHERE sa.AWARD_ID = $id
";

$mydb->setQuery($sql);
$mydb->executeQuery();
$scholar = $mydb->loadSingleResult();

if (!$scholar) {
    message("Scholar record not found!", "error");
    redirect("index.php");
}

// Get renewal history
$mydb->setQuery("
    SELECT r.*, u.FULLNAME as REVIEWED_BY_NAME
    FROM tbl_renewal_applications r
    LEFT JOIN tblusers u ON r.REVIEWED_BY = u.USERID
    WHERE r.APPLICANTID = " . $scholar->APPLICANTID . "
    ORDER BY r.SCHOOL_YEAR DESC, r.SEMESTER DESC
");
$mydb->executeQuery();
$renewals = $mydb->loadResultList();

// Get full scholarship history
$mydb->setQuery("
    SELECT h.*, u.FULLNAME as UPDATED_BY_NAME
    FROM tbl_scholarship_history h
    LEFT JOIN tblusers u ON h.UPDATED_BY = u.USERID
    WHERE h.APPLICANTID = " . $scholar->APPLICANTID . "
    ORDER BY h.UPDATED_AT DESC
");
$mydb->executeQuery();
$history = $mydb->loadResultList();
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Scholar Details</h1>
    </div>
</div>

<!-- Action Buttons -->
<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-12">
        <a href="index.php" class="btn btn-primary">
            <i class="fa fa-arrow-left"></i> Back to List
        </a>
        
        <?php if ($scholar->APPLICANT_STATUS == 'Scholar'): ?>
            <a href="index.php?view=renew&id=<?= $scholar->AWARD_ID ?>" class="btn btn-warning">
                <i class="fa fa-refresh"></i> Process Renewal
            </a>
            
            <!-- Graduation Button - Only for 4th/5th Year Scholars -->
            <?php if (in_array($scholar->YEARLEVEL, ['4th Year', '5th Year'])): ?>
            <a href="index.php?view=graduate&id=<?= $scholar->AWARD_ID ?>" class="btn btn-success">
                <i class="fa fa-graduation-cap"></i> Mark as Graduated
            </a>
            <?php endif; ?>
        <?php endif; ?>
        
        
        <?php if ($_SESSION['ADMIN_ROLE'] == 'Super Admin' && $scholar->APPLICANT_STATUS == 'Scholar'): ?>
            <button type="button" class="btn btn-danger" onclick="confirmTerminate(<?= $scholar->AWARD_ID ?>)">
                <i class="fa fa-ban"></i> Terminate
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Status Alert for Non-Eligible Scholars -->
<?php if ($scholar->APPLICANT_STATUS == 'Scholar' && !in_array($scholar->YEARLEVEL, ['4th Year', '5th Year'])): ?>
<div class="alert alert-info">
    <i class="fa fa-info-circle"></i>
    <strong>Note:</strong> This scholar is in <?= $scholar->YEARLEVEL ?>. 
    Graduation button will appear when they reach 4th Year or 5th Year.
</div>
<?php endif; ?>

<!-- Eligibility Alert for Graduation -->
<?php if (in_array($scholar->YEARLEVEL, ['4th Year', '5th Year']) && $scholar->APPLICANT_STATUS == 'Scholar'): ?>
<div class="alert alert-success">
    <i class="fa fa-graduation-cap"></i>
    <strong>Eligible for Graduation:</strong> This scholar is in their final year and can be marked as graduated.
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-info">
            <div class="panel-heading">
                <i class="fa fa-user"></i> Personal Information
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Full Name:</th>
                        <td><?= htmlspecialchars($scholar->LASTNAME . ', ' . $scholar->FIRSTNAME . ' ' . ($scholar->MIDDLENAME ?? '') . ' ' . ($scholar->SUFFIX ?? '')) ?></td>
                    </tr>
                    <tr>
                        <th>Municipality:</th>
                        <td><?= htmlspecialchars($scholar->MUNICIPALITY ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Barangay:</th>
                        <td><?= htmlspecialchars($scholar->BARANGAY ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Contact No.:</th>
                        <td><?= htmlspecialchars($scholar->CONTACT ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?= htmlspecialchars($scholar->EMAIL ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>4Ps Beneficiary:</th>
                        <td><?= $scholar->IS_4PS_BENEFICIARY == 'Yes' ? '<span class="label label-success">Yes</span>' : 'No' ?></td>
                    </tr>
                    <tr>
                        <th>Indigenous People:</th>
                        <td><?= $scholar->IS_INDIGENOUS == 'Yes' ? '<span class="label label-info">Yes</span>' : 'No' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading">
                <i class="fa fa-graduation-cap"></i> Educational Information
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">School:</th>
                        <td><?= htmlspecialchars($scholar->SCHOOL ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Course:</th>
                        <td><?= htmlspecialchars($scholar->COURSE ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Year Level:</th>
                        <td>
                            <?= htmlspecialchars($scholar->YEARLEVEL ?? 'N/A') ?>
                            <?php if (in_array($scholar->YEARLEVEL, ['4th Year', '5th Year'])): ?>
                                <span class="label label-success pull-right">Final Year</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>GPA:</th>
                        <td><strong><?= $scholar->GPA ? $scholar->GPA . '%' : 'N/A' ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <i class="fa fa-trophy"></i> Scholarship Award
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Award ID:</th>
                        <td><strong>SCH-<?= str_pad($scholar->AWARD_ID, 5, '0', STR_PAD_LEFT) ?></strong></td>
                    </tr>
                    <tr>
                        <th>School Year:</th>
                        <td><?= $scholar->SCHOOL_YEAR ?></td>
                    </tr>
                    <tr>
                        <th>Semester:</th>
                        <td><?= $scholar->SEMESTER ?></td>
                    </tr>
                    <tr>
                        <th>Award Date:</th>
                        <td><?= date('F d, Y', strtotime($scholar->AWARD_DATE)) ?></td>
                    </tr>
                    <tr>
                        <th>Award Amount:</th>
                        <td><h4>₱ <?= number_format($scholar->AMOUNT, 2) ?></h4></td>
                    </tr>
                    <tr>
                        <th>Awarded By:</th>
                        <td><?= htmlspecialchars($scholar->AWARDED_BY_NAME ?? 'N/A') ?></td>
                    </tr>
                    <!-- DUAL STATUS DISPLAY -->
                    <tr>
                        <th>Scholar Status:</th>
                        <td>
                            <?php
                            $applicant_status_color = match($scholar->APPLICANT_STATUS) {
                                'Scholar' => 'label-success',
                                'Graduated' => 'label-primary',
                                'Qualified' => 'label-info',
                                'Rejected' => 'label-danger',
                                default => 'label-default'
                            };
                            ?>
                            <span class="label <?= $applicant_status_color ?>" style="font-size: 14px;"><?= $scholar->APPLICANT_STATUS ?></span>
                            <small class="text-muted">(from Applicant Record)</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Award Status:</th>
                        <td>
                            <?php
                            $award_status_color = match($scholar->STATUS) {
                                'Active' => 'label-success',
                                'Graduated' => 'label-primary',
                                'Terminated' => 'label-danger',
                                'Inactive' => 'label-warning',
                                default => 'label-default'
                            };
                            ?>
                            <span class="label <?= $award_status_color ?>" style="font-size: 14px;"><?= $scholar->STATUS ?></span>
                            <small class="text-muted">(from Scholarship Award)</small>
                        </td>
                    </tr>
                    <!-- END DUAL STATUS DISPLAY -->
                    <tr>
                        <th>Remarks:</th>
                        <td><?= nl2br(htmlspecialchars($scholar->REMARKS ?? 'No remarks')) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-history"></i> Renewal History
            </div>
            <div class="panel-body">
                <?php if (!empty($renewals)): ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>School Year</th>
                            <th>Semester</th>
                            <th>Previous GPA</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($renewals as $r): ?>
                        <tr>
                            <td><?= $r->SCHOOL_YEAR ?></td>
                            <td><?= $r->SEMESTER ?></td>
                            <td><?= $r->PREVIOUS_GPA ?>%</td>
                            <td>
                                <?php
                                $renewal_status = match($r->STATUS) {
                                    'Approved' => 'label-success',
                                    'Denied' => 'label-danger',
                                    default => 'label-warning'
                                };
                                ?>
                                <span class="label <?= $renewal_status ?>"><?= $r->STATUS ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="text-center">No renewal records found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-clock-o"></i> Scholarship History Timeline
            </div>
            <div class="panel-body">
                <?php if (!empty($history)): ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>School Year</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th>GPA</th>
                            <th>Remarks</th>
                            <th>Updated By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $h): ?>
                        <tr>
                            <td><?= date('M d, Y h:i A', strtotime($h->UPDATED_AT)) ?></td>
                            <td><?= $h->SCHOOL_YEAR ?></td>
                            <td><?= $h->SEMESTER ?></td>
                            <td>
                                <?php
                                $hist_status = match($h->STATUS) {
                                    'Awarded' => 'label-success',
                                    'Renewed' => 'label-info',
                                    'Graduated' => 'label-primary',
                                    default => 'label-default'
                                };
                                ?>
                                <span class="label <?= $hist_status ?>"><?= $h->STATUS ?></span>
                            </td>
                            <td><?= $h->GPA ?>%</td>
                            <td><?= htmlspecialchars($h->REMARKS ?? '') ?></td>
                            <td><?= htmlspecialchars($h->UPDATED_BY_NAME ?? 'N/A') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="text-center">No history records found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Termination Confirmation Script -->
<script>
function confirmTerminate(awardId) {
    if (confirm('Are you sure you want to terminate this scholarship? This action cannot be undone.')) {
        var reason = prompt('Please enter reason for termination:');
        if (reason) {
            $.post('controller.php?action=terminate', {
                id: awardId,
                reason: reason
            }, function(response) {
                if (response.status == 'success') {
                    alert('Scholarship terminated successfully.');
                    location.reload();
                } else {
                    alert('Error terminating scholarship.');
                }
            }, 'json');
        }
    }
}
</script>