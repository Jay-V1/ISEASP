<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

global $mydb;

$sy = isset($_GET['sy']) ? $_GET['sy'] : '2025-2026';
$municipality = isset($_GET['municipality']) ? $_GET['municipality'] : '';

// Get list of municipalities for dropdown
$mydb->setQuery("SELECT MUNICIPALITY_NAME FROM tbl_municipalities WHERE IS_ACTIVE = 'Yes' ORDER BY MUNICIPALITY_NAME");
$mydb->executeQuery();
$municipalities = $mydb->loadResultList();

// If no municipality selected and there are municipalities, select the first one
if (empty($municipality) && !empty($municipalities)) {
    $municipality = $municipalities[0]->MUNICIPALITY_NAME;
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Municipality Report</h1>
    </div>
</div>

<!-- Filter Section -->
<div class="row" style="margin-bottom: 20px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-filter"></i> Select Municipality
            </div>
            <div class="panel-body">
                <form method="GET" action="index.php" class="form-inline">
                    <input type="hidden" name="view" value="municipality">
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>School Year:</label>
                        <select name="sy" class="form-control">
                            <option value="2024-2025" <?= $sy == '2024-2025' ? 'selected' : '' ?>>2024-2025</option>
                            <option value="2025-2026" <?= $sy == '2025-2026' ? 'selected' : '' ?>>2025-2026</option>
                            <option value="2026-2027" <?= $sy == '2026-2027' ? 'selected' : '' ?>>2026-2027</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Municipality:</label>
                        <select name="municipality" class="form-control" required>
                            <option value="">-- Select Municipality --</option>
                            <?php foreach ($municipalities as $m): ?>
                            <option value="<?= htmlspecialchars($m->MUNICIPALITY_NAME) ?>" <?= $municipality == $m->MUNICIPALITY_NAME ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m->MUNICIPALITY_NAME) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> Generate Report
                    </button>
                    
                    <a href="export.php?type=municipality&sy=<?= $sy ?>&municipality=<?= urlencode($municipality) ?>" class="btn btn-success">
                        <i class="fa fa-file-excel-o"></i> Export to Excel
                    </a>
                    
                    <a href="print.php?type=municipality&sy=<?= $sy ?>&municipality=<?= urlencode($municipality) ?>" class="btn btn-default" target="_blank">
                        <i class="fa fa-print"></i> Print Report
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($municipality)): ?>
<!-- Municipality Header -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <i class="fa fa-map-marker"></i> <?= htmlspecialchars($municipality) ?> - School Year <?= $sy ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Applicants</span>
                                <span class="info-box-number">
                                    <?php
                                    $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE MUNICIPALITY = '$municipality' AND SCHOOL_YEAR = '$sy'");
                                    $mydb->executeQuery();
                                    $apps = $mydb->loadSingleResult();
                                    echo $apps->total ?? 0;
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Passed Exam</span>
                                <span class="info-box-number">
                                    <?php
                                    $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE MUNICIPALITY = '$municipality' AND EXAM_STATUS = 'Passed' AND SCHOOL_YEAR = '$sy'");
                                    $mydb->executeQuery();
                                    $passed = $mydb->loadSingleResult();
                                    echo $passed->total ?? 0;
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-yellow"><i class="fa fa-star"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Qualified</span>
                                <span class="info-box-number">
                                    <?php
                                    $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE MUNICIPALITY = '$municipality' AND STATUS = 'Qualified' AND SCHOOL_YEAR = '$sy'");
                                    $mydb->executeQuery();
                                    $qualified = $mydb->loadSingleResult();
                                    echo $qualified->total ?? 0;
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-red"><i class="fa fa-graduation-cap"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Active Scholars</span>
                                <span class="info-box-number">
                                    <?php
                                    $mydb->setQuery("
                                        SELECT COUNT(*) as total 
                                        FROM tbl_scholarship_awards sa 
                                        INNER JOIN tbl_applicants a ON sa.APPLICANTID = a.APPLICANTID 
                                        WHERE a.MUNICIPALITY = '$municipality' 
                                        AND sa.STATUS = 'Active'
                                    ");
                                    $mydb->executeQuery();
                                    $scholars = $mydb->loadSingleResult();
                                    echo $scholars->total ?? 0;
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Applicants List -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list"></i> Applicants from <?= htmlspecialchars($municipality) ?> (School Year <?= $sy ?>)
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="report-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>School</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Exam Status</th>
                                <th>Application Status</th>
                                <th>4Ps</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "
                                SELECT * FROM tbl_applicants 
                                WHERE MUNICIPALITY = '$municipality' 
                                AND SCHOOL_YEAR = '$sy'
                                ORDER BY LASTNAME ASC
                            ";
                            
                            $mydb->setQuery($sql);
                            $mydb->executeQuery();
                            $applicants = $mydb->loadResultList();
                            
                            if (!empty($applicants)):
                                foreach ($applicants as $a):
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($a->LASTNAME . ', ' . $a->FIRSTNAME . ' ' . ($a->MIDDLENAME ?? '')) ?></td>
                                <td><?= htmlspecialchars($a->SCHOOL ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($a->COURSE ?? 'N/A') ?></td>
                                <td><?= $a->YEARLEVEL ?></td>
                                <td>
                                    <span class="label label-<?= $a->EXAM_STATUS == 'Passed' ? 'success' : ($a->EXAM_STATUS == 'Failed' ? 'danger' : 'default') ?>">
                                        <?= $a->EXAM_STATUS ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="label label-<?= 
                                        $a->STATUS == 'Scholar' ? 'success' : 
                                        ($a->STATUS == 'Qualified' ? 'primary' : 
                                        ($a->STATUS == 'For Interview' ? 'info' : 
                                        ($a->STATUS == 'Pending' ? 'warning' : 
                                        ($a->STATUS == 'Rejected' ? 'danger' : 'default')))) ?>">
                                        <?= $a->STATUS ?>
                                    </span>
                                </td>
                                <td><?= $a->IS_4PS_BENEFICIARY == 'Yes' ? 'Yes' : 'No' ?></td>
                                <td><?= $a->IS_INDIGENOUS == 'Yes' ? 'Yes' : 'No' ?></td>
                            </tr>
                            <?php 
                                endforeach;
                            else:
                            ?>
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="alert alert-warning" style="margin: 10px;">
                                        <i class="fa fa-info-circle"></i> No applicants found for this municipality in school year <?= $sy ?>.
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

<!-- Summary by Status -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-success">
            <div class="panel-heading">
                <i class="fa fa-pie-chart"></i> Summary by Status
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <?php
                                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE MUNICIPALITY = '$municipality' AND SCHOOL_YEAR = '$sy' AND STATUS = 'Pending'");
                                $mydb->executeQuery();
                                $pending = $mydb->loadSingleResult();
                                ?>
                                <h3><?= $pending->total ?? 0 ?></h3>
                                <p>Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <?php
                                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE MUNICIPALITY = '$municipality' AND SCHOOL_YEAR = '$sy' AND STATUS = 'For Interview'");
                                $mydb->executeQuery();
                                $for_interview = $mydb->loadSingleResult();
                                ?>
                                <h3><?= $for_interview->total ?? 0 ?></h3>
                                <p>For Interview</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <?php
                                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE MUNICIPALITY = '$municipality' AND SCHOOL_YEAR = '$sy' AND STATUS = 'Qualified'");
                                $mydb->executeQuery();
                                $qualified = $mydb->loadSingleResult();
                                ?>
                                <h3><?= $qualified->total ?? 0 ?></h3>
                                <p>Qualified</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> Please select a municipality to view the report.
        </div>
    </div>
</div>
<?php endif; ?>

<script>
$(document).ready(function() {
    $('#report-table').DataTable({
        "pageLength": 25,
        "order": [[0, "asc"]],
        "responsive": true
    });
});
</script>

<style>
.info-box {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 2px;
    margin-bottom: 15px;
}
.info-box-icon {
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
    display: block;
    float: left;
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
}
.info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
}
.info-box-text {
    display: block;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-transform: uppercase;
}
.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 18px;
}
.small-box {
    border-radius: 2px;
    position: relative;
    display: block;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
}
.small-box > .inner {
    padding: 10px;
}
.small-box h3 {
    font-size: 38px;
    font-weight: bold;
    margin: 0 0 10px 0;
    white-space: nowrap;
    padding: 0;
}
.small-box p {
    font-size: 15px;
}
.small-box .icon {
    position: absolute;
    top: -10px;
    right: 10px;
    z-index: 0;
    font-size: 70px;
    color: rgba(0,0,0,0.15);
}
</style>