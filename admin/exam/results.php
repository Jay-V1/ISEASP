<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Exam Results</h1>
    </div>
</div>

<!-- Action Buttons -->
<div class="row">
    <div class="col-lg-12" style="margin-bottom: 10px;">
        <a href="index.php?view=schedule" class="btn btn-primary">
            <i class="fa fa-calendar"></i> View Schedule
        </a>
        <!-- <a href="#" onclick="window.print()" class="btn btn-default">
            <i class="fa fa-print"></i> Print Results
        </a> -->
    </div>
</div>

<!-- Summary Stats -->
<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <?php
                global $mydb;
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_exam_results");
                $mydb->executeQuery();
                $total = $mydb->loadSingleResult();
                ?>
                <h3><?= $total->total ?? 0 ?></h3>
                <p>Total Exam Taken</p>
            </div>
            <div class="icon">
                <i class="fa fa-pencil"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_exam_results WHERE TOTAL_SCORE >= PASSING_SCORE");
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
    
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_exam_results WHERE TOTAL_SCORE < PASSING_SCORE");
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
    
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT AVG(TOTAL_SCORE) as average FROM tbl_exam_results");
                $mydb->executeQuery();
                $avg = $mydb->loadSingleResult();
                ?>
                <h3><?= round($avg->average ?? 0) ?>%</h3>
                <p>Average Score</p>
            </div>
            <div class="icon">
                <i class="fa fa-line-chart"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-filter"></i> Filter Results
            </div>
            <div class="panel-body">
                <form method="GET" action="index.php" class="form-inline">
                    <input type="hidden" name="view" value="results">
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Result:</label>
                        <select name="result" class="form-control input-sm">
                            <option value="">All Results</option>
                            <option value="passed" <?= isset($_GET['result']) && $_GET['result'] == 'passed' ? 'selected' : '' ?>>Passed</option>
                            <option value="failed" <?= isset($_GET['result']) && $_GET['result'] == 'failed' ? 'selected' : '' ?>>Failed</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Date From:</label>
                        <input type="date" name="date_from" class="form-control input-sm" 
                               value="<?= isset($_GET['date_from']) ? $_GET['date_from'] : '' ?>">
                    </div>
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Date To:</label>
                        <input type="date" name="date_to" class="form-control input-sm" 
                               value="<?= isset($_GET['date_to']) ? $_GET['date_to'] : '' ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-search"></i> Apply Filter
                    </button>
                    <a href="index.php?view=results" class="btn btn-default btn-sm">
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
                <th>Exam Date</th>
                <th>Applicant Name</th>
                <th>Municipality</th>
                <th>School</th>
                <th>Course</th>
                <th>Total Score</th>
                <th>Passing Score</th>
                <th>Result</th>
                <th>Examined By</th>
                <th width="10%">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Build query with filters
            $where = array();
            
            if (isset($_GET['result']) && $_GET['result'] == 'passed') {
                $where[] = "er.TOTAL_SCORE >= er.PASSING_SCORE";
            } elseif (isset($_GET['result']) && $_GET['result'] == 'failed') {
                $where[] = "er.TOTAL_SCORE < er.PASSING_SCORE";
            }
            
            if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
                $date_from = $mydb->escape($_GET['date_from']);
                $where[] = "DATE(er.EXAM_DATE) >= '$date_from'";
            }
            
            if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
                $date_to = $mydb->escape($_GET['date_to']);
                $where[] = "DATE(er.EXAM_DATE) <= '$date_to'";
            }
            
            $where_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
            
            $sql = "
                SELECT 
                    er.*,
                    a.LASTNAME, a.FIRSTNAME, a.MIDDLENAME,
                    a.MUNICIPALITY, a.SCHOOL, a.COURSE,
                    u.FULLNAME as EXAMINER_NAME
                FROM tbl_exam_results er
                INNER JOIN tbl_applicants a ON er.APPLICANTID = a.APPLICANTID
                LEFT JOIN tblusers u ON er.EXAMINER_ID = u.USERID
                $where_clause
                ORDER BY er.EXAM_DATE DESC
            ";
            
            $mydb->setQuery($sql);
            $mydb->executeQuery();
            $results = $mydb->loadResultList();
            
            foreach ($results as $r):
                $result_label = ($r->TOTAL_SCORE >= $r->PASSING_SCORE) ? 
                    '<span class="label label-success">PASSED</span>' : 
                    '<span class="label label-danger">FAILED</span>';
            ?>
            <tr>
                <td><?= date('M d, Y', strtotime($r->EXAM_DATE)) ?></td>
                <td><?= htmlspecialchars($r->LASTNAME . ', ' . $r->FIRSTNAME . ' ' . ($r->MIDDLENAME ?? '')) ?></td>
                <td><?= htmlspecialchars($r->MUNICIPALITY ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($r->SCHOOL ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($r->COURSE ?? 'N/A') ?></td>
                <td><strong><?= $r->TOTAL_SCORE ?>%</strong></td>
                <td><?= $r->PASSING_SCORE ?>%</td>
                <td><?= $result_label ?></td>
                <td><?= htmlspecialchars($r->EXAMINER_NAME ?? 'N/A') ?></td>
                <td class="text-center">
                    <a href="index.php?view=view&id=<?= $r->EXAM_RESULT_ID ?>" 
                       class="btn btn-info btn-xs" title="View Details">
                        <i class="fa fa-eye"></i>
                    </a>
                    <a href="index.php?view=edit&id=<?= $r->EXAM_RESULT_ID ?>" 
                       class="btn btn-primary btn-xs" title="Edit">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a href="controller.php?action=delete&id=<?= $r->EXAM_RESULT_ID ?>" 
                       class="btn btn-danger btn-xs" 
                       onclick="return confirm('Delete this exam result?')"
                       title="Delete">
                        <i class="fa fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($results)): ?>
            <tr>
                <td colspan="10" class="text-center">
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="fa fa-info-circle"></i> No exam results found.
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
        "order": [[0, "desc"]]
    });
});
</script>