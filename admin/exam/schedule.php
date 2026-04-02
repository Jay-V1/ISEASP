<?php
require_once("../../include/initialize.php");

if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

global $mydb;
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Exam Schedule</h1>
    </div>
</div>

<!-- Summary Stats -->
<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE EXAM_SLIP_GENERATED IS NOT NULL AND EXAM_SLIP_GENERATED != '' AND EXAM_STATUS = 'Pending'");
                $mydb->executeQuery();
                $pending = $mydb->loadSingleResult();
                ?>
                <h3><?= $pending->total ?? 0 ?></h3>
                <p>Pending Exams</p>
            </div>
            <div class="icon">
                <i class="fa fa-clock-o"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE EXAM_STATUS = 'Passed'");
                $mydb->executeQuery();
                $passed = $mydb->loadSingleResult();
                ?>
                <h3><?= $passed->total ?? 0 ?></h3>
                <p>Passed</p>
            </div>
            <div class="icon">
                <i class="fa fa-check-circle"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE EXAM_STATUS = 'Failed'");
                $mydb->executeQuery();
                $failed = $mydb->loadSingleResult();
                ?>
                <h3><?= $failed->total ?? 0 ?></h3>
                <p>Failed</p>
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
                <i class="fa fa-filter"></i> Filter Schedule
            </div>
            <div class="panel-body">
                <form method="GET" action="index.php" class="form-inline">
                    <input type="hidden" name="view" value="schedule">
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Exam Date:</label>
                        <input type="date" name="exam_date" class="form-control input-sm" 
                               value="<?php echo isset($_GET['exam_date']) ? $_GET['exam_date'] : ''; ?>">
                    </div>
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Venue:</label>
                        <input type="text" name="venue" class="form-control input-sm" 
                               value="<?php echo isset($_GET['venue']) ? $_GET['venue'] : ''; ?>" 
                               placeholder="Enter venue">
                    </div>
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Show:</label>
                        <select name="show" class="form-control input-sm">
                            <option value="pending" <?= !isset($_GET['show']) || $_GET['show'] == 'pending' ? 'selected' : '' ?>>Pending Exams Only</option>
                            <option value="all" <?= isset($_GET['show']) && $_GET['show'] == 'all' ? 'selected' : '' ?>>All Exams (Including Completed)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-search"></i> Apply Filter
                    </button>
                    <a href="index.php?view=schedule" class="btn btn-default btn-sm">
                        <i class="fa fa-refresh"></i> Reset
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row">
    <div class="col-lg-12" style="margin-bottom: 10px;">
        <a href="index.php?view=results" class="btn btn-success">
            <i class="fa fa-list"></i> View Results
        </a>
        <!-- <a href="#" onclick="window.print()" class="btn btn-default">
            <i class="fa fa-print"></i> Print Schedule
        </a> -->
    </div>
</div>

<!-- Notice about pending exams -->
<div class="alert alert-info">
    <i class="fa fa-info-circle"></i> 
    <strong>Note:</strong> Only applicants with pending exams are shown by default. 
    Once an exam result is recorded, the applicant will be removed from this list.
</div>

<div class="table-responsive">
    <table id="dash-table" class="table table-striped table-bordered table-hover" style="font-size:13px">
        <thead>
            32
                <th>Exam Slip #</th>
                <th>Applicant Name</th>
                <th>Municipality</th>
                <th>School</th>
                <th>Course</th>
                <th>Exam Date</th>
                <th>Exam Time</th>
                <th>Venue</th>
                <th>Status</th>
                <th width="15%">Action</th>
            </thead>
        <tbody>
            <?php
            // Build query with filters
            $where = array();
            
            // Default to showing only pending exams
            $show_all = isset($_GET['show']) && $_GET['show'] == 'all';
            
            if (!$show_all) {
                $where[] = "a.EXAM_STATUS = 'Pending'";
            }
            
            $where[] = "a.EXAM_SLIP_GENERATED IS NOT NULL";
            $where[] = "a.EXAM_SLIP_GENERATED != ''";
            
            if (isset($_GET['exam_date']) && !empty($_GET['exam_date'])) {
                $exam_date = $_GET['exam_date'];
                $where[] = "a.EXAM_DATE = '$exam_date'";
            }
            
            if (isset($_GET['venue']) && !empty($_GET['venue'])) {
                $venue = $_GET['venue'];
                $where[] = "a.EXAM_VENUE LIKE '%$venue%'";
            }
            
            $where_clause = "WHERE " . implode(" AND ", $where);
            
            $sql = "
                SELECT a.*
                FROM tbl_applicants a
                $where_clause
                ORDER BY a.EXAM_DATE ASC, a.EXAM_TIME ASC
            ";
            
            $mydb->setQuery($sql);
            $mydb->executeQuery();
            $applicants = $mydb->loadResultList();
            
            foreach ($applicants as $a):
                $status_text = $a->EXAM_STATUS;
                $status_color = 'label-warning';
                if ($status_text == 'Passed') {
                    $status_color = 'label-success';
                } elseif ($status_text == 'Failed') {
                    $status_color = 'label-danger';
                }
            ?>
             <tr>
                <td><?= htmlspecialchars($a->EXAM_SLIP_NUMBER ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($a->LASTNAME . ', ' . $a->FIRSTNAME . ' ' . ($a->MIDDLENAME ?? '')) ?></td>
                <td><?= htmlspecialchars($a->MUNICIPALITY ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($a->SCHOOL ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($a->COURSE ?? 'N/A') ?></td>
                <td><?= $a->EXAM_DATE ? date('M d, Y', strtotime($a->EXAM_DATE)) : 'N/A' ?></td>
                <td><?= $a->EXAM_TIME ? date('h:i A', strtotime($a->EXAM_TIME)) : 'N/A' ?></td>
                <td><?= htmlspecialchars($a->EXAM_VENUE ?? 'N/A') ?></td>
                <td><span class="label <?= $status_color ?>"><?= $status_text ?></span></td>
                <td class="text-center">
                    <?php if ($a->EXAM_STATUS == 'Pending'): ?>
                        <a href="index.php?view=add&id=<?= $a->APPLICANTID ?>" 
                           class="btn btn-success btn-xs" title="Enter Result">
                            <i class="fa fa-pencil"></i> Enter Result
                        </a>
                    <?php endif; ?>
                    <a href="../applications/index.php?view=print_slip&id=<?= $a->APPLICANTID ?>" 
                       class="btn btn-info btn-xs" title="Print Exam Slip" target="_blank">
                        <i class="fa fa-print"></i> Print
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($applicants)): ?>
            <tr>
                <td colspan="10" class="text-center">
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="fa fa-info-circle"></i> No pending exams found.
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
        "order": [[5, "asc"], [6, "asc"]]
    });
});
</script>