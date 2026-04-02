<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

global $mydb;

$sy = isset($_GET['sy']) ? $_GET['sy'] : '2025-2026';
$status = isset($_GET['status']) ? $_GET['status'] : '';
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Applicants Report</h1>
    </div>
</div>

<!-- Filter Section -->
<div class="row" style="margin-bottom: 20px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-filter"></i> Filter Report
            </div>
            <div class="panel-body">
                <form method="GET" action="index.php" class="form-inline">
                    <input type="hidden" name="view" value="applicants">
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>School Year:</label>
                        <select name="sy" class="form-control">
                            <option value="2024-2025" <?= $sy == '2024-2025' ? 'selected' : '' ?>>2024-2025</option>
                            <option value="2025-2026" <?= $sy == '2025-2026' ? 'selected' : '' ?>>2025-2026</option>
                            <option value="2026-2027" <?= $sy == '2026-2027' ? 'selected' : '' ?>>2026-2027</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Status:</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="Pending" <?= $status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Approved" <?= $status == 'Approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="Rejected" <?= $status == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                            <option value="For Interview" <?= $status == 'For Interview' ? 'selected' : '' ?>>For Interview</option>
                            <option value="Qualified" <?= $status == 'Qualified' ? 'selected' : '' ?>>Qualified</option>
                            <option value="Scholar" <?= $status == 'Scholar' ? 'selected' : '' ?>>Scholar</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> Generate Report
                    </button>
                    
                    <a href="export.php?type=applicants&sy=<?= $sy ?>&status=<?= $status ?>" class="btn btn-success">
                        <i class="fa fa-file-excel-o"></i> Export to Excel
                    </a>
                    
                    <a href="print.php?type=applicants&sy=<?= $sy ?>&status=<?= $status ?>" class="btn btn-default" target="_blank">
                        <i class="fa fa-print"></i> Print Report
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-aqua">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE SCHOOL_YEAR = '$sy'");
                $mydb->executeQuery();
                $total = $mydb->loadSingleResult();
                ?>
                <h3><?= $total->total ?? 0 ?></h3>
                <p>Total Applicants (<?= $sy ?>)</p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="small-box bg-green">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE SCHOOL_YEAR = '$sy' AND EXAM_STATUS = 'Passed'");
                $mydb->executeQuery();
                $passed = $mydb->loadSingleResult();
                ?>
                <h3><?= $passed->total ?? 0 ?></h3>
                <p>Passed Exam</p>
            </div>
            <div class="icon">
                <i class="fa fa-check-circle"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="small-box bg-yellow">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE SCHOOL_YEAR = '$sy' AND STATUS = 'Pending'");
                $mydb->executeQuery();
                $pending = $mydb->loadSingleResult();
                ?>
                <h3><?= $pending->total ?? 0 ?></h3>
                <p>Pending</p>
            </div>
            <div class="icon">
                <i class="fa fa-clock-o"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="small-box bg-red">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE SCHOOL_YEAR = '$sy' AND STATUS = 'Rejected'");
                $mydb->executeQuery();
                $rejected = $mydb->loadSingleResult();
                ?>
                <h3><?= $rejected->total ?? 0 ?></h3>
                <p>Rejected</p>
            </div>
            <div class="icon">
                <i class="fa fa-times-circle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Applicants Table -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list"></i> Applicants List
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="report-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Municipality</th>
                                <th>School</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Application Type</th>
                                <th>Exam Status</th>
                                <th>Application Status</th>
                                <th>Date Applied</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $where = "WHERE SCHOOL_YEAR = '$sy'";
                            if (!empty($status)) {
                                $where .= " AND STATUS = '$status'";
                            }
                            
                            $sql = "SELECT * FROM tbl_applicants $where ORDER BY DATECREATED DESC";
                            $mydb->setQuery($sql);
                            $mydb->executeQuery();
                            $applicants = $mydb->loadResultList();
                            
                            foreach ($applicants as $a):
                            ?>
                            <tr>
                                <td><?= $a->APPLICANTID ?></td>
                                <td><?= htmlspecialchars($a->LASTNAME . ', ' . $a->FIRSTNAME . ' ' . ($a->MIDDLENAME ?? '')) ?></td>
                                <td><?= htmlspecialchars($a->MUNICIPALITY ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($a->SCHOOL ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($a->COURSE ?? 'N/A') ?></td>
                                <td><?= $a->YEARLEVEL ?></td>
                                <td><?= $a->APPLICATION_TYPE ?></td>
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
                                <td><?= date('M d, Y', strtotime($a->DATECREATED)) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#report-table').DataTable({
        "pageLength": 25,
        "order": [[9, "desc"]],
        "responsive": true
    });
});
</script>