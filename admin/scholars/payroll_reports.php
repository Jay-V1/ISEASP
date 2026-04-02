<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

global $mydb;

$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$school_year_id = isset($_GET['school_year_id']) ? intval($_GET['school_year_id']) : 0;

// Get all school years for filter
$mydb->setQuery("SELECT * FROM tbl_school_years ORDER BY school_year DESC");
$mydb->executeQuery();
$school_years = $mydb->loadResultList();

// Get selected school year
if($school_year_id > 0) {
    $mydb->setQuery("SELECT * FROM tbl_school_years WHERE id = $school_year_id");
    $mydb->executeQuery();
    $selected_sy = $mydb->loadSingleResult();
} else {
    $mydb->setQuery("SELECT * FROM tbl_school_years WHERE is_active = 1 LIMIT 1");
    $mydb->executeQuery();
    $selected_sy = $mydb->loadSingleResult();
    $school_year_id = $selected_sy ? $selected_sy->id : 0;
}

function getPayrollSummary($school_year_id) {
    global $mydb;
    $mydb->setQuery("
        SELECT 
            semester,
            COUNT(*) as payroll_count,
            SUM(total_amount) as total_amount,
            SUM(CASE WHEN status = 'disbursed' THEN total_amount ELSE 0 END) as disbursed_amount
        FROM tbl_payroll 
        WHERE school_year_id = '$school_year_id'
        GROUP BY semester
        ORDER BY FIELD(semester, '1st Semester', '2nd Semester', 'Summer')
    ");
    $mydb->executeQuery();
    $results = $mydb->loadResultList();
    return $results ? $results : [];
}

function getTopScholarsByStipend($school_year_id) {
    global $mydb;
    $mydb->setQuery("
        SELECT 
            CONCAT(a.LASTNAME, ', ', a.FIRSTNAME, ' ', IFNULL(a.MIDDLENAME, '')) as FULLNAME,
            a.MUNICIPALITY, 
            a.COURSE, 
            SUM(pd.amount) as total_received,
            COUNT(pd.id) as payment_count
        FROM tbl_payroll_details pd
        INNER JOIN tbl_payroll p ON pd.payroll_id = p.id
        INNER JOIN tbl_applicants a ON pd.scholar_id = a.APPLICANTID
        WHERE p.school_year_id = '$school_year_id' AND p.status = 'disbursed'
        GROUP BY pd.scholar_id
        ORDER BY total_received DESC
        LIMIT 10
    ");
    $mydb->executeQuery();
    $scholars = $mydb->loadResultList();
    return $scholars ? $scholars : [];
}

function getMunicipalitySummary($school_year_id) {
    global $mydb;
    $mydb->setQuery("
        SELECT 
            a.MUNICIPALITY, 
            COUNT(DISTINCT pd.scholar_id) as scholar_count,
            SUM(pd.amount) as total_amount
        FROM tbl_payroll_details pd
        INNER JOIN tbl_payroll p ON pd.payroll_id = p.id
        INNER JOIN tbl_applicants a ON pd.scholar_id = a.APPLICANTID
        WHERE p.school_year_id = '$school_year_id' AND p.status = 'disbursed'
        GROUP BY a.MUNICIPALITY
        ORDER BY total_amount DESC
    ");
    $mydb->executeQuery();
    $municipalities = $mydb->loadResultList();
    return $municipalities ? $municipalities : [];
}

$payroll_summary = getPayrollSummary($school_year_id);
$top_scholars = getTopScholarsByStipend($school_year_id);
$municipality_summary = getMunicipalitySummary($school_year_id);
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Payroll Reports</h1>
    </div>
</div>

<!-- Filter -->
<div class="row">
    <div class="col-lg-12" style="margin-bottom: 15px;">
        <form method="GET" class="form-inline">
            <input type="hidden" name="view" value="payroll_reports">
            <div class="form-group">
                <label>Select School Year: </label>
                <select name="school_year_id" class="form-control">
                    <option value="0">-- Select School Year --</option>
                    <?php foreach($school_years as $sy): ?>
                    <option value="<?php echo $sy->id; ?>" <?php echo $school_year_id == $sy->id ? 'selected' : ''; ?>>
                        <?php echo $sy->school_year; ?>
                        <?php echo $sy->is_active ? '(Active)' : ''; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Generate Report</button>
            <a href="#" onclick="window.print()" class="btn btn-default">Print Report</a>
        </form>
    </div>
</div>

<?php if($selected_sy): ?>

<!-- Summary by Semester -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-calendar"></i> Payroll Summary - <?php echo $selected_sy->school_year; ?>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Semester</th>
                                <th>Payroll Count</th>
                                <th>Total Amount</th>
                                <th>Disbursed Amount</th>
                                <th>Remaining</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $grand_total = 0;
                            $grand_disbursed = 0;
                            foreach($payroll_summary as $row):
                                $remaining = $row->total_amount - $row->disbursed_amount;
                                $grand_total += $row->total_amount;
                                $grand_disbursed += $row->disbursed_amount;
                            ?>
                            <tr>
                                <td><strong><?php echo $row->semester; ?></strong></td>
                                <td><?php echo $row->payroll_count; ?></td>
                                <td>₱ <?php echo number_format($row->total_amount, 2); ?></td>
                                <td>₱ <?php echo number_format($row->disbursed_amount, 2); ?></td>
                                <td>₱ <?php echo number_format($remaining, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if(empty($payroll_summary)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No payroll records found for <?php echo $selected_sy->school_year; ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if(!empty($payroll_summary)): ?>
                        <tfoot>
                            <tr class="bg-gray" style="font-weight: bold;">
                                <td><strong>GRAND TOTAL</strong></td>
                                <td>-</td>
                                <td><strong>₱ <?php echo number_format($grand_total, 2); ?></strong></td>
                                <td><strong>₱ <?php echo number_format($grand_disbursed, 2); ?></strong></td>
                                <td><strong>₱ <?php echo number_format($grand_total - $grand_disbursed, 2); ?></strong></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Scholars -->
    <div class="col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading">
                <i class="fa fa-trophy"></i> Top 10 Scholars by Stipend Received - <?php echo $selected_sy->school_year; ?>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Scholar Name</th>
                                <th>Municipality</th>
                                <th>Course</th>
                                <th>Payments</th>
                                <th>Total Received</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($top_scholars as $scholar): ?>
                            <tr>
                                <td><?php echo $scholar->FULLNAME; ?></td>
                                <td><?php echo $scholar->MUNICIPALITY; ?></td>
                                <td><?php echo $scholar->COURSE; ?></td>
                                <td><?php echo $scholar->payment_count; ?></td>
                                <td>₱ <?php echo number_format($scholar->total_received, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if(empty($top_scholars)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No data available for <?php echo $selected_sy->school_year; ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Municipality Summary -->
    <div class="col-md-6">
        <div class="panel panel-info">
            <div class="panel-heading">
                <i class="fa fa-map-marker"></i> Municipality Summary - <?php echo $selected_sy->school_year; ?>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Municipality</th>
                                <th>No. of Scholars</th>
                                <th>Total Stipend</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($municipality_summary as $mun): ?>
                            <tr>
                                <td><?php echo $mun->MUNICIPALITY; ?></td>
                                <td><?php echo $mun->scholar_count; ?></td>
                                <td>₱ <?php echo number_format($mun->total_amount, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if(empty($municipality_summary)): ?>
                            <tr>
                                <td colspan="3" class="text-center">No data available for <?php echo $selected_sy->school_year; ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<div class="alert alert-warning">
    <i class="fa fa-warning"></i> No school year selected or no active school year found.
</div>
<?php endif; ?>