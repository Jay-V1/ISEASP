<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

global $mydb;

$sy = isset($_GET['sy']) ? $_GET['sy'] : '2025-2026';
$status = isset($_GET['status']) ? $_GET['status'] : 'Active';
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Scholars Report</h1>
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
                    <input type="hidden" name="view" value="scholars">
                    
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
                            <option value="Active" <?= $status == 'Active' ? 'selected' : '' ?>>Active</option>
                            <option value="Graduated" <?= $status == 'Graduated' ? 'selected' : '' ?>>Graduated</option>
                            <option value="Terminated" <?= $status == 'Terminated' ? 'selected' : '' ?>>Terminated</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> Generate Report
                    </button>
                    
                    <a href="export.php?type=scholars&sy=<?= $sy ?>&status=<?= $status ?>" class="btn btn-success">
                        <i class="fa fa-file-excel-o"></i> Export to Excel
                    </a>
                    
                    <a href="print.php?type=scholars&sy=<?= $sy ?>&status=<?= $status ?>" class="btn btn-default" target="_blank">
                        <i class="fa fa-print"></i> Print Report
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row">
    <div class="col-md-4">
        <div class="small-box bg-green">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total, SUM(AMOUNT) as amount FROM tbl_scholarship_awards WHERE STATUS = 'Active'");
                $mydb->executeQuery();
                $active = $mydb->loadSingleResult();
                ?>
                <h3><?= $active->total ?? 0 ?></h3>
                <p>Active Scholars</p>
                <p>₱ <?= number_format($active->amount ?? 0, 2) ?></p>
            </div>
            <div class="icon">
                <i class="fa fa-graduation-cap"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="small-box bg-aqua">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_scholarship_awards WHERE STATUS = 'Active' AND SCHOOL_YEAR = '$sy'");
                $mydb->executeQuery();
                $current = $mydb->loadSingleResult();
                ?>
                <h3><?= $current->total ?? 0 ?></h3>
                <p>Current School Year (<?= $sy ?>)</p>
            </div>
            <div class="icon">
                <i class="fa fa-calendar"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="small-box bg-yellow">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_renewal_applications WHERE STATUS = 'Approved'");
                $mydb->executeQuery();
                $renewals = $mydb->loadSingleResult();
                ?>
                <h3><?= $renewals->total ?? 0 ?></h3>
                <p>Total Renewals</p>
            </div>
            <div class="icon">
                <i class="fa fa-refresh"></i>
            </div>
        </div>
    </div>
</div>

<!-- Scholars Table -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list"></i> <?= $status ?> Scholars List
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="report-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Award ID</th>
                                <th>Name</th>
                                <th>Municipality</th>
                                <th>School</th>
                                <th>Course</th>
                                <th>Year Level</th>
                                <th>School Year</th>
                                <th>Semester</th>
                                <th>Amount</th>
                                <th>Award Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "
                                SELECT 
                                    sa.*,
                                    a.LASTNAME, a.FIRSTNAME, a.MIDDLENAME,
                                    a.MUNICIPALITY, a.SCHOOL, a.COURSE, a.YEARLEVEL
                                FROM tbl_scholarship_awards sa
                                INNER JOIN tbl_applicants a ON sa.APPLICANTID = a.APPLICANTID
                                WHERE sa.STATUS = '$status'
                                ORDER BY a.LASTNAME ASC
                            ";
                            
                            $mydb->setQuery($sql);
                            $mydb->executeQuery();
                            $scholars = $mydb->loadResultList();
                            
                            foreach ($scholars as $s):
                            ?>
                            <tr>
                                <td><strong>SCH-<?= str_pad($s->AWARD_ID, 5, '0', STR_PAD_LEFT) ?></strong></td>
                                <td><?= htmlspecialchars($s->LASTNAME . ', ' . $s->FIRSTNAME . ' ' . ($s->MIDDLENAME ?? '')) ?></td>
                                <td><?= htmlspecialchars($s->MUNICIPALITY ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($s->SCHOOL ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($s->COURSE ?? 'N/A') ?></td>
                                <td><?= $s->YEARLEVEL ?></td>
                                <td><?= $s->SCHOOL_YEAR ?></td>
                                <td><?= $s->SEMESTER ?></td>
                                <td><strong>₱ <?= number_format($s->AMOUNT, 2) ?></strong></td>
                                <td><?= date('M d, Y', strtotime($s->AWARD_DATE)) ?></td>
                                <td>
                                    <span class="label label-<?= $s->STATUS == 'Active' ? 'success' : ($s->STATUS == 'Graduated' ? 'primary' : 'danger') ?>">
                                        <?= $s->STATUS ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="success">
                                <th colspan="8" class="text-right">Total Amount:</th>
                                <th>
                                    <?php
                                    $total_sql = "SELECT SUM(AMOUNT) as total FROM tbl_scholarship_awards WHERE STATUS = '$status'";
                                    $mydb->setQuery($total_sql);
                                    $mydb->executeQuery();
                                    $total = $mydb->loadSingleResult();
                                    echo '₱ ' . number_format($total->total ?? 0, 2);
                                    ?>
                                </th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
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
        "order": [[1, "asc"]],
        "responsive": true
    });
});
</script>