<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

global $mydb;
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Graduated Scholars</h1>
    </div>
</div>

<!-- Graduation Statistics -->
<div class="row" style="margin-bottom: 20px;">
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-trophy"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">With Honors</span>
                <span class="info-box-number">
                    <?php
                    $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_scholarship_history 
                                     WHERE STATUS = 'Graduated' AND (REMARKS LIKE '%Cum Laude%' OR REMARKS LIKE '%Honors%')");
                    $mydb->executeQuery();
                    $honors = $mydb->loadSingleResult();
                    echo $honors->total ?? 0;
                    ?>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">This Year</span>
                <span class="info-box-number">
                    <?php
                    $current_year = date('Y') . '-' . (date('Y') + 1);
                    $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_scholarship_history 
                                     WHERE STATUS = 'Graduated' AND SCHOOL_YEAR = '$current_year'");
                    $mydb->executeQuery();
                    $this_year = $mydb->loadSingleResult();
                    echo $this_year->total ?? 0;
                    ?>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-line-chart"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Avg. GPA</span>
                <span class="info-box-number">
                    <?php
                    $mydb->setQuery("SELECT AVG(GPA) as avg FROM tbl_scholarship_history WHERE STATUS = 'Graduated'");
                    $mydb->executeQuery();
                    $avg_gpa = $mydb->loadSingleResult();
                    echo round($avg_gpa->avg ?? 0, 2) . '%';
                    ?>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Graduates</span>
                <span class="info-box-number">
                    <?php
                    $mydb->setQuery("SELECT COUNT(DISTINCT APPLICANTID) as total FROM tbl_scholarship_history WHERE STATUS = 'Graduated'");
                    $mydb->executeQuery();
                    $total = $mydb->loadSingleResult();
                    echo $total->total ?? 0;
                    ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-filter"></i> Filter Graduates
            </div>
            <div class="panel-body">
                <form method="GET" action="index.php" class="form-inline">
                    <input type="hidden" name="view" value="graduates">
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>School Year:</label>
                        <select name="school_year" class="form-control input-sm">
                            <option value="">All Years</option>
                            <option value="2023-2024" <?= isset($_GET['school_year']) && $_GET['school_year'] == '2023-2024' ? 'selected' : '' ?>>2023-2024</option>
                            <option value="2024-2025" <?= isset($_GET['school_year']) && $_GET['school_year'] == '2024-2025' ? 'selected' : '' ?>>2024-2025</option>
                            <option value="2025-2026" <?= isset($_GET['school_year']) && $_GET['school_year'] == '2025-2026' ? 'selected' : '' ?>>2025-2026</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Municipality:</label>
                        <input type="text" name="municipality" class="form-control input-sm" 
                               value="<?= isset($_GET['municipality']) ? $_GET['municipality'] : '' ?>" 
                               placeholder="Enter municipality">
                    </div>
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Honors:</label>
                        <select name="honors" class="form-control input-sm">
                            <option value="">All</option>
                            <option value="honors" <?= isset($_GET['honors']) && $_GET['honors'] == 'honors' ? 'selected' : '' ?>>With Honors</option>
                            <option value="none" <?= isset($_GET['honors']) && $_GET['honors'] == 'none' ? 'selected' : '' ?>>No Honors</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-search"></i> Apply Filter
                    </button>
                    <a href="index.php?view=graduates" class="btn btn-default btn-sm">
                        <i class="fa fa-refresh"></i> Reset
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Export Buttons -->
<div class="row">
    <div class="col-lg-12" style="margin-bottom: 10px;">
        <a href="#" onclick="window.print()" class="btn btn-default">
            <i class="fa fa-print"></i> Print List
        </a>
    </div>
</div>

<!-- Graduates List -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-success">
            <div class="panel-heading">
                <i class="fa fa-graduation-cap"></i> Graduated Scholars List
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="graduates-table" class="table table-striped table-bordered table-hover" style="font-size:13px">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Municipality</th>
                                <th>School</th>
                                <th>Course</th>
                                <th>Year Graduated</th>
                                <th>Final GPA</th>
                                <th>Honors</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Build query with filters
                            $where = array();
                            $where[] = "h.STATUS = 'Graduated'";
                            
                            if (isset($_GET['school_year']) && !empty($_GET['school_year'])) {
                                $where[] = "h.SCHOOL_YEAR = '" . $_GET['school_year'] . "'";
                            }
                            
                            if (isset($_GET['municipality']) && !empty($_GET['municipality'])) {
                                $municipality = $_GET['municipality'];
                                $where[] = "a.MUNICIPALITY LIKE '%$municipality%'";
                            }
                            
                            if (isset($_GET['honors']) && $_GET['honors'] == 'honors') {
                                $where[] = "(h.REMARKS LIKE '%Cum Laude%' OR h.REMARKS LIKE '%Honors%')";
                            } elseif (isset($_GET['honors']) && $_GET['honors'] == 'none') {
                                $where[] = "(h.REMARKS NOT LIKE '%Cum Laude%' AND h.REMARKS NOT LIKE '%Honors%')";
                            }
                            
                            $where_clause = "WHERE " . implode(" AND ", $where);
                            
                            $sql = "
                                SELECT 
                                    a.APPLICANTID,
                                    a.LASTNAME, a.FIRSTNAME, a.MIDDLENAME, a.SUFFIX,
                                    a.MUNICIPALITY, a.SCHOOL, a.COURSE,
                                    h.SCHOOL_YEAR,
                                    h.GPA,
                                    h.REMARKS,
                                    h.UPDATED_AT
                                FROM tbl_scholarship_history h
                                INNER JOIN tbl_applicants a ON h.APPLICANTID = a.APPLICANTID
                                $where_clause
                                ORDER BY h.UPDATED_AT DESC
                            ";
                            
                            $mydb->setQuery($sql);
                            $mydb->executeQuery();
                            $graduates = $mydb->loadResultList();
                            
                            $counter = 1;
                            foreach ($graduates as $g):
                                // Extract honors from remarks
                                $honors_text = 'None';
                                $honors_class = '';
                                if (strpos($g->REMARKS, 'Summa Cum Laude') !== false) {
                                    $honors_text = 'Summa Cum Laude';
                                    $honors_class = 'label-success';
                                } elseif (strpos($g->REMARKS, 'Magna Cum Laude') !== false) {
                                    $honors_text = 'Magna Cum Laude';
                                    $honors_class = 'label-info';
                                } elseif (strpos($g->REMARKS, 'Cum Laude') !== false) {
                                    $honors_text = 'Cum Laude';
                                    $honors_class = 'label-primary';
                                } elseif (strpos($g->REMARKS, 'With Highest Honors') !== false) {
                                    $honors_text = 'With Highest Honors';
                                    $honors_class = 'label-success';
                                } elseif (strpos($g->REMARKS, 'With High Honors') !== false) {
                                    $honors_text = 'With High Honors';
                                    $honors_class = 'label-info';
                                } elseif (strpos($g->REMARKS, 'With Honors') !== false) {
                                    $honors_text = 'With Honors';
                                    $honors_class = 'label-primary';
                                }
                            ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <td><?= htmlspecialchars($g->LASTNAME . ', ' . $g->FIRSTNAME . ' ' . ($g->MIDDLENAME ?? '')) ?></td>
                                <td><?= htmlspecialchars($g->MUNICIPALITY ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($g->SCHOOL ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($g->COURSE ?? 'N/A') ?></td>
                                <td><?= $g->SCHOOL_YEAR ?></td>
                                <td><strong><?= $g->GPA ?>%</strong></td>
                                <td>
                                    <?php if ($honors_class): ?>
                                        <span class="label <?= $honors_class ?>"><?= $honors_text ?></span>
                                    <?php else: ?>
                                        <span class="label label-default">None</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="../applications/index.php?view=view&id=<?= $g->APPLICANTID ?>" 
                                       class="btn btn-info btn-xs" title="View Details">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                    <a href="#" onclick="generateCertificate(<?= $g->APPLICANTID ?>)" 
                                       class="btn btn-success btn-xs" title="Generate Certificate">
                                        <i class="fa fa-certificate"></i> Certificate
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($graduates)): ?>
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="alert alert-info" style="margin: 20px;">
                                        <i class="fa fa-info-circle"></i> No graduated scholars found.
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

<!-- Alumni Highlights Section -->
<!-- <div class="row" style="margin-top: 20px;">
    <div class="col-lg-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <i class="fa fa-star"></i> Alumni Highlights
            </div>
            <div class="panel-body">
                <div class="row">
                    <?php
                    // Get top graduates
                    $mydb->setQuery("
                        SELECT 
                            a.LASTNAME, a.FIRSTNAME, a.MIDDLENAME,
                            a.SCHOOL, a.COURSE,
                            h.GPA,
                            h.REMARKS
                        FROM tbl_scholarship_history h
                        INNER JOIN tbl_applicants a ON h.APPLICANTID = a.APPLICANTID
                        WHERE h.STATUS = 'Graduated'
                        ORDER BY h.GPA DESC
                        LIMIT 3
                    ");
                    $mydb->executeQuery();
                    $top_graduates = $mydb->loadResultList();
                    
                    foreach ($top_graduates as $top):
                    ?>
                    <div class="col-md-4">
                        <div class="well text-center">
                            <i class="fa fa-user-circle fa-4x text-success"></i>
                            <h4><?= htmlspecialchars($top->FIRSTNAME . ' ' . $top->LASTNAME) ?></h4>
                            <p><?= htmlspecialchars($top->SCHOOL) ?></p>
                            <p><?= htmlspecialchars($top->COURSE) ?></p>
                            <h3><span class="label label-success">GPA: <?= $top->GPA ?>%</span></h3>
                            <p><em><?= htmlspecialchars($top->REMARKS) ?></em></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div> -->

<script>
function exportToExcel() {
    window.location.href = 'export_graduates.php';
}

function generateCertificate(applicantId) {
    window.open('../certificates/generate.php?type=graduation&id=' + applicantId, '_blank');
}

$(document).ready(function() {
    $('#graduates-table').DataTable({
        "pageLength": 25,
        "order": [[5, "desc"], [6, "desc"]],
        "language": {
            "emptyTable": "No graduated scholars found",
            "info": "Showing _START_ to _END_ of _TOTAL_ graduates",
            "infoEmpty": "Showing 0 to 0 of 0 graduates",
            "infoFiltered": "(filtered from _MAX_ total graduates)"
        },
        "columnDefs": [
            { "orderable": false, "targets": [8] }
        ]
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
</style>