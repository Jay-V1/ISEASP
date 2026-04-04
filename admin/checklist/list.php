<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

global $mydb;
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Requirements Checklist</h1>
    </div>
</div>

<!-- Summary Stats -->
<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(DISTINCT APPLICANTID) as total FROM tbl_applicant_requirement_checklist WHERE IS_VERIFIED = 1");
                $mydb->executeQuery();
                $complete = $mydb->loadSingleResult();
                ?>
                <h3><?= $complete->total ?? 0 ?></h3>
                <p>Complete Requirements</p>
            </div>
            <div class="icon">
                <i class="fa fa-check-circle"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(DISTINCT APPLICANTID) as total FROM tbl_applicant_requirement_checklist WHERE IS_SUBMITTED = 1 AND IS_VERIFIED = 0");
                $mydb->executeQuery();
                $pending = $mydb->loadSingleResult();
                ?>
                <h3><?= $pending->total ?? 0 ?></h3>
                <p>Pending Verification</p>
            </div>
            <div class="icon">
                <i class="fa fa-clock-o"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(DISTINCT APPLICANTID) as total FROM tbl_applicant_requirement_checklist WHERE IS_SUBMITTED = 0");
                $mydb->executeQuery();
                $missing = $mydb->loadSingleResult();
                ?>
                <h3><?= $missing->total ?? 0 ?></h3>
                <p>Missing Requirements</p>
            </div>
            <div class="icon">
                <i class="fa fa-times-circle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-filter"></i> Filter Applicants
            </div>
            <div class="panel-body">
                <form method="GET" action="index.php" class="form-inline">
                    <input type="hidden" name="view" value="list">
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Status:</label>
                        <select name="status" class="form-control input-sm">
                            <option value="">All Status</option>
                            <option value="complete" <?= isset($_GET['status']) && $_GET['status'] == 'complete' ? 'selected' : '' ?>>Complete</option>
                            <option value="pending" <?= isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : '' ?>>Pending Verification</option>
                            <option value="missing" <?= isset($_GET['status']) && $_GET['status'] == 'missing' ? 'selected' : '' ?>>Missing</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Municipality:</label>
                        <input type="text" name="municipality" class="form-control input-sm" 
                               value="<?= isset($_GET['municipality']) ? $_GET['municipality'] : '' ?>" 
                               placeholder="Enter municipality">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-search"></i> Apply Filter
                    </button>
                    <a href="index.php?view=list" class="btn btn-default btn-sm">
                        <i class="fa fa-refresh"></i> Reset
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table id="dash-table" class="table table-striped table-bordered table-hover" style="font-size:13px">
        <thead>
            <tr>
                <th>Applicant Name</th>
                <th>Municipality</th>
                <th>School</th>
                <th>Course</th>
                <th>Year</th>
                <th>Requirements Status</th>
                <!-- <th>Progress</th> -->
                <th width="15%">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Build query with filters
            $where = array();
            
            if (isset($_GET['status']) && !empty($_GET['status'])) {
                if ($_GET['status'] == 'complete') {
                    $where[] = "c.verified_count = c.total_required";
                } elseif ($_GET['status'] == 'pending') {
                    $where[] = "c.submitted_count > 0 AND c.verified_count < c.total_required";
                } elseif ($_GET['status'] == 'missing') {
                    $where[] = "c.submitted_count = 0";
                }
            }
            
            if (isset($_GET['municipality']) && !empty($_GET['municipality'])) {
                $municipality = trim($_GET['municipality']);
                $where[] = "a.MUNICIPALITY LIKE '%$municipality%'";
            }
            
            $where_clause = !empty($where) ? "HAVING " . implode(" AND ", $where) : "";
            
            $sql = "
                SELECT 
                    a.APPLICANTID,
                    a.LASTNAME, a.FIRSTNAME, a.MIDDLENAME,
                    a.MUNICIPALITY, a.SCHOOL, a.COURSE, a.YEARLEVEL,
                    COUNT(c.REQUIREMENT_ID) as total_required,
                    SUM(CASE WHEN c.IS_SUBMITTED = 1 THEN 1 ELSE 0 END) as submitted_count,
                    SUM(CASE WHEN c.IS_VERIFIED = 1 THEN 1 ELSE 0 END) as verified_count
                FROM tbl_applicants a
                INNER JOIN tbl_applicant_requirement_checklist c ON a.APPLICANTID = c.APPLICANTID
                GROUP BY a.APPLICANTID
                $where_clause
                ORDER BY a.LASTNAME ASC
            ";
            
            $mydb->setQuery($sql);
            $mydb->executeQuery();
            $applicants = $mydb->loadResultList();
            
            foreach ($applicants as $a):
                $percentage = ($a->total_required > 0) ? round(($a->verified_count / $a->total_required) * 100) : 0;
                
                if ($a->verified_count == $a->total_required) {
                    $status_label = '<span class="label label-success">Complete</span>';
                    $progress_class = 'progress-bar-success';
                } elseif ($a->submitted_count > 0) {
                    $status_label = '<span class="label label-warning">Pending Verification</span>';
                    $progress_class = 'progress-bar-warning';
                } else {
                    $status_label = '<span class="label label-danger">Missing</span>';
                    $progress_class = 'progress-bar-danger';
                }
            ?>
            <tr>
                <td><?= htmlspecialchars($a->LASTNAME . ', ' . $a->FIRSTNAME . ' ' . ($a->MIDDLENAME ?? '')) ?></td>
                <td><?= htmlspecialchars($a->MUNICIPALITY ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($a->SCHOOL ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($a->COURSE ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($a->YEARLEVEL ?? 'N/A') ?></td>
                <td><?= $status_label ?></td>
                <!-- <td style="width: 200px;">
                    <div class="progress progress-xs">
                        <div class="progress-bar <?= $progress_class ?>" style="width: <?= $percentage ?>%"></div>
                    </div>
                    <small><?= $a->verified_count ?>/<?= $a->total_required ?> verified</small>
                </td> -->
                <td class="text-center">
                    <a href="index.php?view=view&id=<?= $a->APPLICANTID ?>" 
                       class="btn btn-info btn-xs" title="View Requirements">
                        <i class="fa fa-list"></i> View Requirements
                    </a>
                    <a href="index.php?view=manage&id=<?= $a->APPLICANTID ?>" 
                       class="btn btn-primary btn-xs" title="Manage Requirements">
                        <i class="fa fa-check-square-o"></i> Manage
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($applicants)): ?>
            <tr>
                <td colspan="8" class="text-center">
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="fa fa-info-circle"></i> No applicants found.
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    $('#dash-table').DataTable({
        "pageLength": 25,
        "order": [[0, "asc"]]
    });
});
</script>