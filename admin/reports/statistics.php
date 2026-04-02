<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

global $mydb;

// Get current school year
$current_sy = '2025-2026';

// Get counts for summary cards
$mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants");
$mydb->executeQuery();
$total_applicants = $mydb->loadSingleResult();

$mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE STATUS = 'Pending'");
$mydb->executeQuery();
$pending = $mydb->loadSingleResult();

$mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE EXAM_STATUS = 'Passed'");
$mydb->executeQuery();
$passed_exam = $mydb->loadSingleResult();

$mydb->setQuery("SELECT COUNT(*) as total FROM tbl_scholarship_awards WHERE STATUS = 'Active'");
$mydb->executeQuery();
$active_scholars = $mydb->loadSingleResult();

$mydb->setQuery("SELECT COUNT(*) as total FROM tbl_scholarship_history WHERE STATUS = 'Graduated'");
$mydb->executeQuery();
$graduates = $mydb->loadSingleResult();

$mydb->setQuery("SELECT SUM(AMOUNT) as total FROM tbl_scholarship_awards WHERE STATUS = 'Active'");
$mydb->executeQuery();
$total_amount = $mydb->loadSingleResult();
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Program Statistics</h1>
    </div>
</div>

<!-- School Year Filter -->
<div class="row" style="margin-bottom: 20px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-calendar"></i> Select School Year
            </div>
            <div class="panel-body">
                <form method="GET" action="index.php" class="form-inline">
                    <input type="hidden" name="view" value="statistics">
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>School Year:</label>
                        <select name="sy" class="form-control">
                            <option value="2024-2025" <?= isset($_GET['sy']) && $_GET['sy'] == '2024-2025' ? 'selected' : '' ?>>2024-2025</option>
                            <option value="2025-2026" <?= !isset($_GET['sy']) || $_GET['sy'] == '2025-2026' ? 'selected' : '' ?>>2025-2026</option>
                            <option value="2026-2027" <?= isset($_GET['sy']) && $_GET['sy'] == '2026-2027' ? 'selected' : '' ?>>2026-2027</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-filter"></i> Apply
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3><?= $total_applicants->total ?? 0 ?></h3>
                <p>Total Applicants</p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <a href="index.php?view=applicants" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3><?= $pending->total ?? 0 ?></h3>
                <p>Pending Applications</p>
            </div>
            <div class="icon">
                <i class="fa fa-clock-o"></i>
            </div>
            <a href="index.php?view=applicants&status=pending" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3><?= $passed_exam->total ?? 0 ?></h3>
                <p>Passed Examination</p>
            </div>
            <div class="icon">
                <i class="fa fa-check-circle"></i>
            </div>
            <a href="index.php?view=applicants&status=passed" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3><?= $active_scholars->total ?? 0 ?></h3>
                <p>Active Scholars</p>
            </div>
            <div class="icon">
                <i class="fa fa-graduation-cap"></i>
            </div>
            <a href="index.php?view=scholars" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Second Row -->
<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3><?= $graduates->total ?? 0 ?></h3>
                <p>Graduates</p>
            </div>
            <div class="icon">
                <i class="fa fa-star"></i>
            </div>
            <a href="../scholars/index.php?view=graduates" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-maroon">
            <div class="inner">
                <h3>₱ <?= number_format($total_amount->total ?? 0, 2) ?></h3>
                <p>Total Award Amount</p>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
            <a href="#" class="small-box-footer">
                <i class="fa fa-info-circle"></i> Current Disbursement
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE IS_4PS_BENEFICIARY = 'Yes'");
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
    
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-teal">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE IS_INDIGENOUS = 'Yes'");
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

<!-- Charts Row -->
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-pie-chart"></i> Application Status Distribution
            </div>
            <div class="panel-body">
                <canvas id="statusChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-bar-chart"></i> Monthly Applications
            </div>
            <div class="panel-body">
                <canvas id="monthlyChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- District Summary -->
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-info">
            <div class="panel-heading">
                <i class="fa fa-sun-o"></i> 1st District Summary
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Municipality</th>
                            <th>Applicants</th>
                            <th>Scholars</th>
                            <th>Graduates</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $mydb->setQuery("
                            SELECT 
                                m.MUNICIPALITY_NAME,
                                (SELECT COUNT(*) FROM tbl_applicants WHERE MUNICIPALITY = m.MUNICIPALITY_NAME) as applicants,
                                (SELECT COUNT(*) FROM tbl_applicants a 
                                 INNER JOIN tbl_scholarship_awards sa ON a.APPLICANTID = sa.APPLICANTID 
                                 WHERE a.MUNICIPALITY = m.MUNICIPALITY_NAME AND sa.STATUS = 'Active') as scholars,
                                (SELECT COUNT(*) FROM tbl_applicants a 
                                 INNER JOIN tbl_scholarship_history h ON a.APPLICANTID = h.APPLICANTID 
                                 WHERE a.MUNICIPALITY = m.MUNICIPALITY_NAME AND h.STATUS = 'Graduated') as graduates
                            FROM tbl_municipalities m
                            WHERE m.DISTRICT = '1st District' AND m.IS_ACTIVE = 'Yes'
                            ORDER BY m.MUNICIPALITY_NAME
                        ");
                        $mydb->executeQuery();
                        $first_district = $mydb->loadResultList();
                        
                        $first_total_apps = 0;
                        $first_total_scholars = 0;
                        $first_total_grads = 0;
                        
                        foreach ($first_district as $fd):
                            $first_total_apps += $fd->applicants;
                            $first_total_scholars += $fd->scholars;
                            $first_total_grads += $fd->graduates;
                        ?>
                        <tr>
                            <td><?= $fd->MUNICIPALITY_NAME ?></td>
                            <td><?= $fd->applicants ?></td>
                            <td><?= $fd->scholars ?></td>
                            <td><?= $fd->graduates ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="success">
                            <th>Total</th>
                            <th><?= $first_total_apps ?></th>
                            <th><?= $first_total_scholars ?></th>
                            <th><?= $first_total_grads ?></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <i class="fa fa-moon-o"></i> 2nd District Summary
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Municipality</th>
                            <th>Applicants</th>
                            <th>Scholars</th>
                            <th>Graduates</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $mydb->setQuery("
                            SELECT 
                                m.MUNICIPALITY_NAME,
                                (SELECT COUNT(*) FROM tbl_applicants WHERE MUNICIPALITY = m.MUNICIPALITY_NAME) as applicants,
                                (SELECT COUNT(*) FROM tbl_applicants a 
                                 INNER JOIN tbl_scholarship_awards sa ON a.APPLICANTID = sa.APPLICANTID 
                                 WHERE a.MUNICIPALITY = m.MUNICIPALITY_NAME AND sa.STATUS = 'Active') as scholars,
                                (SELECT COUNT(*) FROM tbl_applicants a 
                                 INNER JOIN tbl_scholarship_history h ON a.APPLICANTID = h.APPLICANTID 
                                 WHERE a.MUNICIPALITY = m.MUNICIPALITY_NAME AND h.STATUS = 'Graduated') as graduates
                            FROM tbl_municipalities m
                            WHERE m.DISTRICT = '2nd District' AND m.IS_ACTIVE = 'Yes'
                            ORDER BY m.MUNICIPALITY_NAME
                        ");
                        $mydb->executeQuery();
                        $second_district = $mydb->loadResultList();
                        
                        $second_total_apps = 0;
                        $second_total_scholars = 0;
                        $second_total_grads = 0;
                        
                        foreach ($second_district as $sd):
                            $second_total_apps += $sd->applicants;
                            $second_total_scholars += $sd->scholars;
                            $second_total_grads += $sd->graduates;
                        ?>
                        <tr>
                            <td><?= $sd->MUNICIPALITY_NAME ?></td>
                            <td><?= $sd->applicants ?></td>
                            <td><?= $sd->scholars ?></td>
                            <td><?= $sd->graduates ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="success">
                            <th>Total</th>
                            <th><?= $second_total_apps ?></th>
                            <th><?= $second_total_scholars ?></th>
                            <th><?= $second_total_grads ?></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Charts Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Status Distribution Chart
    var ctx1 = document.getElementById('statusChart').getContext('2d');
    var statusChart = new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: ['Pending', 'Approved', 'Rejected', 'For Interview', 'Qualified', 'Scholar'],
            datasets: [{
                data: [
                    <?php
                    $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE STATUS = 'Pending'");
                    $mydb->executeQuery();
                    $pending_count = $mydb->loadSingleResult();
                    echo $pending_count->total ?? 0;
                    ?>,
                    <?php
                    $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE STATUS = 'Approved'");
                    $mydb->executeQuery();
                    $approved_count = $mydb->loadSingleResult();
                    echo $approved_count->total ?? 0;
                    ?>,
                    <?php
                    $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE STATUS = 'Rejected'");
                    $mydb->executeQuery();
                    $rejected_count = $mydb->loadSingleResult();
                    echo $rejected_count->total ?? 0;
                    ?>,
                    <?php
                    $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE STATUS = 'For Interview'");
                    $mydb->executeQuery();
                    $interview_count = $mydb->loadSingleResult();
                    echo $interview_count->total ?? 0;
                    ?>,
                    <?php
                    $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE STATUS = 'Qualified'");
                    $mydb->executeQuery();
                    $qualified_count = $mydb->loadSingleResult();
                    echo $qualified_count->total ?? 0;
                    ?>,
                    <?php
                    $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE STATUS = 'Scholar'");
                    $mydb->executeQuery();
                    $scholar_count = $mydb->loadSingleResult();
                    echo $scholar_count->total ?? 0;
                    ?>
                ],
                backgroundColor: [
                    '#f39c12', '#00a65a', '#dd4b39', '#00c0ef', '#605ca8', '#3c8dbc'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
    
    // Monthly Applications Chart
    var ctx2 = document.getElementById('monthlyChart').getContext('2d');
    var monthlyChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Applications',
                data: [
                    <?php
                    for ($m = 1; $m <= 12; $m++) {
                        $month = str_pad($m, 2, '0', STR_PAD_LEFT);
                        $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE DATE_FORMAT(DATECREATED, '%m') = '$month'");
                        $mydb->executeQuery();
                        $month_count = $mydb->loadSingleResult();
                        echo ($month_count->total ?? 0) . ($m < 12 ? ',' : '');
                    }
                    ?>
                ],
                backgroundColor: '#00a65a'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>