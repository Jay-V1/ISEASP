<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

if ($_SESSION['ADMIN_ROLE'] != 'Super Admin') {
    redirect(web_root . "admin/index.php");
}

global $mydb;

$filter_date = isset($_GET['date']) ? $_GET['date'] : '';
$filter_user = isset($_GET['user']) ? intval($_GET['user']) : '';
$filter_action = isset($_GET['action_type']) ? $_GET['action_type'] : '';
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Activity Logs</h1>
    </div>
</div>

<!-- Filter Section -->
<div class="row" style="margin-bottom: 20px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-filter"></i> Filter Logs
            </div>
            <div class="panel-body">
                <form method="GET" action="index.php" class="form-inline">
                    <input type="hidden" name="view" value="logs">
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Date:</label>
                        <input type="date" name="date" class="form-control" value="<?= $filter_date ?>">
                    </div>
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>User:</label>
                        <select name="user" class="form-control">
                            <option value="">All Users</option>
                            <?php
                            $mydb->setQuery("SELECT USERID, FULLNAME FROM tblusers ORDER BY FULLNAME");
                            $mydb->executeQuery();
                            $users = $mydb->loadResultList();
                            foreach ($users as $u):
                            ?>
                            <option value="<?= $u->USERID ?>" <?= $filter_user == $u->USERID ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u->FULLNAME) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Action:</label>
                        <select name="action_type" class="form-control">
                            <option value="">All Actions</option>
                            <option value="CREATE" <?= $filter_action == 'CREATE' ? 'selected' : '' ?>>Created</option>
                            <option value="UPDATE" <?= $filter_action == 'UPDATE' ? 'selected' : '' ?>>Updated</option>
                            <option value="DELETE" <?= $filter_action == 'DELETE' ? 'selected' : '' ?>>Deleted</option>
                            <option value="EXAM" <?= $filter_action == 'EXAM' ? 'selected' : '' ?>>Exam</option>
                            <option value="INTERVIEW" <?= $filter_action == 'INTERVIEW' ? 'selected' : '' ?>>Interview</option>
                            <option value="EVALUATION" <?= $filter_action == 'EVALUATION' ? 'selected' : '' ?>>Evaluation</option>
                            <option value="LOGIN" <?= $filter_action == 'LOGIN' ? 'selected' : '' ?>>Login</option>
                            <option value="LOGOUT" <?= $filter_action == 'LOGOUT' ? 'selected' : '' ?>>Logout</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> Apply Filter
                    </button>
                    
                    <a href="index.php?view=logs" class="btn btn-default">
                        <i class="fa fa-refresh"></i> Reset
                    </a>
                    
                    <button type="button" onclick="exportLogs()" class="btn btn-success">
                        <i class="fa fa-download"></i> Export Logs
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Logs Statistics -->
<div class="row" style="margin-bottom: 20px;">
    <div class="col-md-3">
        <div class="small-box bg-aqua">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_application_log WHERE DATE(LOG_DATE) = CURDATE()");
                $mydb->executeQuery();
                $today = $mydb->loadSingleResult();
                ?>
                <h3><?= $today->total ?? 0 ?></h3>
                <p>Today's Activities</p>
            </div>
            <div class="icon">
                <i class="fa fa-calendar"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="small-box bg-green">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_application_log WHERE WEEK(LOG_DATE) = WEEK(CURDATE())");
                $mydb->executeQuery();
                $week = $mydb->loadSingleResult();
                ?>
                <h3><?= $week->total ?? 0 ?></h3>
                <p>This Week</p>
            </div>
            <div class="icon">
                <i class="fa fa-calendar-check-o"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="small-box bg-yellow">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_application_log WHERE MONTH(LOG_DATE) = MONTH(CURDATE())");
                $mydb->executeQuery();
                $month = $mydb->loadSingleResult();
                ?>
                <h3><?= $month->total ?? 0 ?></h3>
                <p>This Month</p>
            </div>
            <div class="icon">
                <i class="fa fa-calendar-plus-o"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="small-box bg-red">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_application_log");
                $mydb->executeQuery();
                $total = $mydb->loadSingleResult();
                ?>
                <h3><?= $total->total ?? 0 ?></h3>
                <p>Total Activities</p>
            </div>
            <div class="icon">
                <i class="fa fa-history"></i>
            </div>
        </div>
    </div>
</div>

<!-- Logs Table -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list"></i> Activity Logs
                <div class="pull-right">
                    <button onclick="clearLogs()" class="btn btn-danger btn-xs">
                        <i class="fa fa-trash"></i> Clear Old Logs
                    </button>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="logs-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Action Type</th>
                                <th>Action</th>
                                <th>Applicant</th>
                                <th>Details</th>
                                <!-- <th>IP Address</th> -->
                             </thead>
                        <tbody>
                            <?php
                            $where = array();
                            
                            if (!empty($filter_date)) {
                                $where[] = "DATE(l.LOG_DATE) = '$filter_date'";
                            }
                            
                            if (!empty($filter_user)) {
                                $where[] = "l.USERID = $filter_user";
                            }
                            
                            if (!empty($filter_action)) {
                                $where[] = "l.ACTION_TYPE = '$filter_action'";
                            }
                            
                            $where_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
                            
                            // FIXED: Changed l.ACTION_BY to l.USERID in the JOIN
                            $sql = "
                                SELECT 
                                    l.*,
                                    u.FULLNAME as USER_NAME,
                                    u.ROLE as USER_ROLE,
                                    CONCAT(a.LASTNAME, ', ', a.FIRSTNAME) as APPLICANT_NAME
                                FROM tbl_application_log l
                                LEFT JOIN tblusers u ON l.USERID = u.USERID
                                LEFT JOIN tbl_applicants a ON l.APPLICANTID = a.APPLICANTID
                                $where_clause
                                ORDER BY l.LOG_DATE DESC
                                LIMIT 1000
                            ";
                            
                            $mydb->setQuery($sql);
                            $mydb->executeQuery();
                            $logs = $mydb->loadResultList();
                            
                            foreach ($logs as $log):
                                // Action type badge color
                                $action_colors = [
                                    'CREATE' => 'success',
                                    'UPDATE' => 'info',
                                    'DELETE' => 'danger',
                                    'EXAM' => 'primary',
                                    'INTERVIEW' => 'warning',
                                    'EVALUATION' => 'primary',
                                    'LOGIN' => 'success',
                                    'LOGOUT' => 'default',
                                    'SCHOLAR' => 'success',
                                    'REQUIREMENT' => 'info'
                                ];
                                $badge_color = $action_colors[$log->ACTION_TYPE] ?? 'default';
                            ?>
                             <tr>
                                <td><?= date('M d, Y h:i A', strtotime($log->LOG_DATE)) ?></td>
                                <td><?= htmlspecialchars($log->USER_NAME ?? 'System') ?></td>
                                <td><span class="label label-<?= strtolower(str_replace(' ', '', $log->USER_ROLE)) ?>"><?= $log->USER_ROLE ?? 'N/A' ?></span></td>
                                <td><span class="label label-<?= $badge_color ?>"><?= $log->ACTION_TYPE ?></span></td>
                                <td><?= htmlspecialchars($log->ACTION) ?></td>
                                <td>
                                    <?php if ($log->APPLICANTID): ?>
                                        <a href="../applications/index.php?view=view&id=<?= $log->APPLICANTID ?>">
                                            <?= htmlspecialchars($log->APPLICANT_NAME ?? 'Unknown') ?>
                                        </a>
                                    <?php else: ?>
                                        <em>System</em>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($log->DETAILS ?? '') ?></td>
                                <!-- <td><code><?= $log->IP_ADDRESS ?? 'N/A' ?></code></td> -->
                             </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($logs)): ?>
                             <tr>
                                <td colspan="8" class="text-center">
                                    <div class="alert alert-info" style="margin: 10px;">
                                        <i class="fa fa-info-circle"></i> No activity logs found.
                                    </div>
                                </td>
                             </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="text-muted" style="margin-top: 10px;">
                    <i class="fa fa-info-circle"></i> Showing last 1000 logs.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Timeline -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-clock-o"></i> Recent Activity Timeline
            </div>
            <div class="panel-body">
                <ul class="timeline">
                    <?php
                    // FIXED: Changed l.ACTION_BY to l.USERID
                    $mydb->setQuery("
                        SELECT l.*, u.FULLNAME as USER_NAME, CONCAT(a.LASTNAME, ', ', a.FIRSTNAME) as APPLICANT_NAME
                        FROM tbl_application_log l
                        LEFT JOIN tblusers u ON l.USERID = u.USERID
                        LEFT JOIN tbl_applicants a ON l.APPLICANTID = a.APPLICANTID
                        ORDER BY l.LOG_DATE DESC
                        LIMIT 10
                    ");
                    $mydb->executeQuery();
                    $recent = $mydb->loadResultList();
                    
                    foreach ($recent as $r):
                        $icon = 'fa-info-circle';
                        $bg = 'bg-aqua';
                        
                        if ($r->ACTION_TYPE == 'CREATE') {
                            $icon = 'fa-plus-circle';
                            $bg = 'bg-green';
                        } elseif ($r->ACTION_TYPE == 'UPDATE') {
                            $icon = 'fa-edit';
                            $bg = 'bg-yellow';
                        } elseif ($r->ACTION_TYPE == 'DELETE') {
                            $icon = 'fa-trash';
                            $bg = 'bg-red';
                        } elseif ($r->ACTION_TYPE == 'EXAM') {
                            $icon = 'fa-pencil';
                            $bg = 'bg-blue';
                        } elseif ($r->ACTION_TYPE == 'INTERVIEW') {
                            $icon = 'fa-users';
                            $bg = 'bg-purple';
                        } elseif ($r->ACTION_TYPE == 'LOGIN') {
                            $icon = 'fa-sign-in';
                            $bg = 'bg-green';
                        } elseif ($r->ACTION_TYPE == 'LOGOUT') {
                            $icon = 'fa-sign-out';
                            $bg = 'bg-gray';
                        }
                    ?>
                    <li>
                        <i class="fa <?= $icon ?> <?= $bg ?>"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fa fa-clock-o"></i> <?= date('h:i A', strtotime($r->LOG_DATE)) ?></span>
                            <h3 class="timeline-header"><?= htmlspecialchars($r->USER_NAME ?? 'System') ?></h3>
                            <div class="timeline-body">
                                <?= htmlspecialchars($r->ACTION) ?>
                                <?php if ($r->APPLICANT_NAME): ?>
                                    on <strong><?= htmlspecialchars($r->APPLICANT_NAME) ?></strong>
                                <?php endif; ?>
                            </div>
                            <div class="timeline-footer">
                                <small class="text-muted"><?= date('M d, Y', strtotime($r->LOG_DATE)) ?></small>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    
                    <li>
                        <i class="fa fa-clock-o bg-gray"></i>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function exportLogs() {
    window.location.href = 'export_logs.php';
}

function clearLogs() {
    if (confirm('Clear logs older than 30 days? This action cannot be undone.')) {
        $.post('controller.php?action=clear_logs', function(response) {
            if (response.status == 'success') {
                alert('Old logs cleared successfully.');
                location.reload();
            } else {
                alert('Error clearing logs.');
            }
        }, 'json');
    }
}

$(document).ready(function() {
    $('#logs-table').DataTable({
        "pageLength": 50,
        "order": [[0, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": [3, 4, 5, 6, 7] }
        ],
        "language": {
            "emptyTable": "No activity logs found"
        }
    });
});
</script>

<style>
.timeline {
    position: relative;
    margin: 0 0 30px 0;
    padding: 0;
    list-style: none;
}
.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 4px;
    background: #ddd;
    left: 31px;
    margin: 0;
    border-radius: 2px;
}
.timeline > li {
    position: relative;
    margin-right: 10px;
    margin-bottom: 15px;
}
.timeline > li:before,
.timeline > li:after {
    content: " ";
    display: table;
}
.timeline > li:after {
    clear: both;
}
.timeline > li > .timeline-item {
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 3px;
    margin-top: 0;
    background: #fff;
    color: #444;
    margin-left: 60px;
    margin-right: 15px;
    padding: 0;
    position: relative;
}
.timeline > li > .timeline-item > .time {
    color: #999;
    float: right;
    padding: 10px;
    font-size: 12px;
}
.timeline > li > .timeline-item > .timeline-header {
    margin: 0;
    color: #555;
    border-bottom: 1px solid #f4f4f4;
    padding: 10px;
    font-size: 16px;
    line-height: 1.1;
}
.timeline > li > .timeline-item > .timeline-body {
    padding: 10px;
}
.timeline > li > .timeline-item > .timeline-footer {
    padding: 10px;
    border-top: 1px solid #f4f4f4;
}
.timeline > li > .fa,
.timeline > li > .glyphicon,
.timeline > li > .ion {
    width: 30px;
    height: 30px;
    font-size: 15px;
    line-height: 30px;
    position: absolute;
    color: #fff;
    background: #d2d6de;
    border-radius: 50%;
    text-align: center;
    left: 18px;
    top: 0;
}
.bg-blue { background-color: #3c8dbc; }
.bg-green { background-color: #00a65a; }
.bg-yellow { background-color: #f39c12; }
.bg-red { background-color: #dd4b39; }
.bg-aqua { background-color: #00c0ef; }
.bg-purple { background-color: #605ca8; }
.bg-gray { background-color: #d2d6de; }
.label-SuperAdmin { background-color: #dd4b39; }
.label-Admin { background-color: #f39c12; }
.label-Evaluator { background-color: #00a65a; }
.label-Staff { background-color: #3c8dbc; }
</style>