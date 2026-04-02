<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

global $mydb;

$sy = isset($_GET['sy']) ? $_GET['sy'] : '2025-2026';
$district = isset($_GET['district']) ? $_GET['district'] : '1st District';
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?= $district ?> Report</h1>
    </div>
</div>

<!-- Filter Section -->
<div class="row" style="margin-bottom: 20px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-filter"></i> Select District
            </div>
            <div class="panel-body">
                <form method="GET" action="index.php" class="form-inline">
                    <input type="hidden" name="view" value="district">
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>School Year:</label>
                        <select name="sy" class="form-control">
                            <option value="2024-2025" <?= $sy == '2024-2025' ? 'selected' : '' ?>>2024-2025</option>
                            <option value="2025-2026" <?= $sy == '2025-2026' ? 'selected' : '' ?>>2025-2026</option>
                            <option value="2026-2027" <?= $sy == '2026-2027' ? 'selected' : '' ?>>2026-2027</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>District:</label>
                        <select name="district" class="form-control">
                            <option value="1st District" <?= $district == '1st District' ? 'selected' : '' ?>>1st District</option>
                            <option value="2nd District" <?= $district == '2nd District' ? 'selected' : '' ?>>2nd District</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> Generate Report
                    </button>
                    
                    <a href="export.php?type=district&sy=<?= $sy ?>&district=<?= $district ?>" class="btn btn-success">
                        <i class="fa fa-file-excel-o"></i> Export to Excel
                    </a>
                    
                    <a href="print.php?type=district&sy=<?= $sy ?>&district=<?= $district ?>" class="btn btn-default" target="_blank">
                        <i class="fa fa-print"></i> Print Report
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- District Summary -->
<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-aqua">
            <div class="inner">
                <?php
                $mydb->setQuery("
                    SELECT COUNT(DISTINCT a.APPLICANTID) as total 
                    FROM tbl_applicants a
                    INNER JOIN tbl_municipalities m ON a.MUNICIPALITY = m.MUNICIPALITY_NAME
                    WHERE m.DISTRICT = '$district' AND a.SCHOOL_YEAR = '$sy'
                ");
                $mydb->executeQuery();
                $total_apps = $mydb->loadSingleResult();
                ?>
                <h3><?= $total_apps->total ?? 0 ?></h3>
                <p>Total Applicants</p>
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
                $mydb->setQuery("
                    SELECT COUNT(DISTINCT a.APPLICANTID) as total 
                    FROM tbl_applicants a
                    INNER JOIN tbl_municipalities m ON a.MUNICIPALITY = m.MUNICIPALITY_NAME
                    INNER JOIN tbl_scholarship_awards sa ON a.APPLICANTID = sa.APPLICANTID
                    WHERE m.DISTRICT = '$district' AND sa.STATUS = 'Active'
                ");
                $mydb->executeQuery();
                $active = $mydb->loadSingleResult();
                ?>
                <h3><?= $active->total ?? 0 ?></h3>
                <p>Active Scholars</p>
            </div>
            <div class="icon">
                <i class="fa fa-graduation-cap"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="small-box bg-yellow">
            <div class="inner">
                <?php
                $mydb->setQuery("
                    SELECT COUNT(DISTINCT a.APPLICANTID) as total 
                    FROM tbl_applicants a
                    INNER JOIN tbl_municipalities m ON a.MUNICIPALITY = m.MUNICIPALITY_NAME
                    WHERE m.DISTRICT = '$district' AND a.IS_4PS_BENEFICIARY = 'Yes'
                ");
                $mydb->executeQuery();
                $four_ps = $mydb->loadSingleResult();
                ?>
                <h3><?= $four_ps->total ?? 0 ?></h3>
                <p>4Ps Beneficiaries</p>
            </div>
            <div class="icon">
                <i class="fa fa-heart"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="small-box bg-purple">
            <div class="inner">
                <?php
                $mydb->setQuery("
                    SELECT COUNT(DISTINCT a.APPLICANTID) as total 
                    FROM tbl_applicants a
                    INNER JOIN tbl_municipalities m ON a.MUNICIPALITY = m.MUNICIPALITY_NAME
                    WHERE m.DISTRICT = '$district' AND a.IS_INDIGENOUS = 'Yes'
                ");
                $mydb->executeQuery();
                $ip = $mydb->loadSingleResult();
                ?>
                <h3><?= $ip->total ?? 0 ?></h3>
                <p>Indigenous People</p>
            </div>
            <div class="icon">
                <i class="fa fa-leaf"></i>
            </div>
        </div>
    </div>
</div>

<!-- Municipalities Table -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-map-marker"></i> Municipalities in <?= $district ?>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="report-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Municipality</th>
                                <th>Applicants</th>
                                <th>Passed Exam</th>
                                <th>Qualified</th>
                                <th>Active Scholars</th>
                                <th>Graduates</th>
                                <th>4Ps</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "
                                SELECT 
                                    m.MUNICIPALITY_NAME,
                                    (SELECT COUNT(*) FROM tbl_applicants WHERE MUNICIPALITY = m.MUNICIPALITY_NAME AND SCHOOL_YEAR = '$sy') as applicants,
                                    (SELECT COUNT(*) FROM tbl_applicants WHERE MUNICIPALITY = m.MUNICIPALITY_NAME AND EXAM_STATUS = 'Passed') as passed,
                                    (SELECT COUNT(*) FROM tbl_applicants WHERE MUNICIPALITY = m.MUNICIPALITY_NAME AND STATUS = 'Qualified') as qualified,
                                    (SELECT COUNT(*) FROM tbl_applicants a 
                                     INNER JOIN tbl_scholarship_awards sa ON a.APPLICANTID = sa.APPLICANTID 
                                     WHERE a.MUNICIPALITY = m.MUNICIPALITY_NAME AND sa.STATUS = 'Active') as scholars,
                                    (SELECT COUNT(*) FROM tbl_applicants a 
                                     INNER JOIN tbl_scholarship_history h ON a.APPLICANTID = h.APPLICANTID 
                                     WHERE a.MUNICIPALITY = m.MUNICIPALITY_NAME AND h.STATUS = 'Graduated') as graduates,
                                    (SELECT COUNT(*) FROM tbl_applicants WHERE MUNICIPALITY = m.MUNICIPALITY_NAME AND IS_4PS_BENEFICIARY = 'Yes') as four_ps,
                                    (SELECT COUNT(*) FROM tbl_applicants WHERE MUNICIPALITY = m.MUNICIPALITY_NAME AND IS_INDIGENOUS = 'Yes') as ip
                                FROM tbl_municipalities m
                                WHERE m.DISTRICT = '$district' AND m.IS_ACTIVE = 'Yes'
                                ORDER BY m.MUNICIPALITY_NAME
                            ";
                            
                            $mydb->setQuery($sql);
                            $mydb->executeQuery();
                            $municipalities = $mydb->loadResultList();
                            
                            $total_apps = 0;
                            $total_passed = 0;
                            $total_qualified = 0;
                            $total_scholars = 0;
                            $total_graduates = 0;
                            $total_four_ps = 0;
                            $total_ip = 0;
                            
                            foreach ($municipalities as $m):
                                $total_apps += $m->applicants;
                                $total_passed += $m->passed;
                                $total_qualified += $m->qualified;
                                $total_scholars += $m->scholars;
                                $total_graduates += $m->graduates;
                                $total_four_ps += $m->four_ps;
                                $total_ip += $m->ip;
                            ?>
                            <tr>
                                <td><strong><?= $m->MUNICIPALITY_NAME ?></strong></td>
                                <td><?= $m->applicants ?></td>
                                <td><?= $m->passed ?></td>
                                <td><?= $m->qualified ?></td>
                                <td><?= $m->scholars ?></td>
                                <td><?= $m->graduates ?></td>
                                <td><?= $m->four_ps ?></td>
                                <td><?= $m->ip ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="success">
                                <th><strong>TOTAL</strong></th>
                                <th><?= $total_apps ?></th>
                                <th><?= $total_passed ?></th>
                                <th><?= $total_qualified ?></th>
                                <th><?= $total_scholars ?></th>
                                <th><?= $total_graduates ?></th>
                                <th><?= $total_four_ps ?></th>
                                <th><?= $total_ip ?></th>
                            </tr>
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
        "order": [[0, "asc"]],
        "responsive": true
    });
});
</script>