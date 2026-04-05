<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

global $mydb;
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Municipalities of Ilocos Sur</h1>
    </div>
</div>

<!-- Summary Cards -->
<div class="row" style="margin-bottom: 20px;">
    <div class="col-md-4">
        <div class="small-box bg-aqua">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_municipalities");
                $mydb->executeQuery();
                $total = $mydb->loadSingleResult();
                ?>
                <h3><?= $total->total ?? 0 ?></h3>
                <p>Total Municipalities</p>
            </div>
            <div class="icon">
                <i class="fa fa-map-marker"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="small-box bg-green">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_municipalities WHERE DISTRICT = '1st District'");
                $mydb->executeQuery();
                $first_district = $mydb->loadSingleResult();
                ?>
                <h3><?= $first_district->total ?? 0 ?></h3>
                <p>1st District</p>
            </div>
            <div class="icon">
                <i class="fa fa-sun-o"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="small-box bg-yellow">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_municipalities WHERE DISTRICT = '2nd District'");
                $mydb->executeQuery();
                $second_district = $mydb->loadSingleResult();
                ?>
                <h3><?= $second_district->total ?? 0 ?></h3>
                <p>2nd District</p>
            </div>
            <div class="icon">
                <i class="fa fa-moon-o"></i>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row">
    <div class="col-lg-12" style="margin-bottom: 10px;">
        <a href="index.php?view=add" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Municipality
        </a>
        <!-- <a href="#" onclick="window.print()" class="btn btn-default">
            <i class="fa fa-print"></i> Print List
        </a> -->
    </div>
</div>

<!-- Filter Tabs -->
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#all">All Municipalities</a></li>
    <li><a data-toggle="tab" href="#first">1st District</a></li>
    <li><a data-toggle="tab" href="#second">2nd District</a></li>
    <li><a data-toggle="tab" href="#inactive">Inactive</a></li>
</ul>

<div class="tab-content">
    <!-- All Municipalities Tab -->
    <div id="all" class="tab-pane fade in active">
        <div class="panel panel-default" style="margin-top: 15px;">
            <div class="panel-heading">
                <i class="fa fa-list"></i> Complete Municipalities List
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="all-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Municipality</th>
                                <th>District</th>
                                <th>Status</th>
                                <th>Applicants</th>
                                <th>Scholars</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $mydb->setQuery("
                                SELECT 
                                    m.*,
                                    (SELECT COUNT(*) FROM tbl_applicants WHERE MUNICIPALITY = m.MUNICIPALITY_NAME) as applicant_count,
                                    (SELECT COUNT(*) FROM tbl_applicants a 
                                     INNER JOIN tbl_scholarship_awards sa ON a.APPLICANTID = sa.APPLICANTID 
                                     WHERE a.MUNICIPALITY = m.MUNICIPALITY_NAME AND sa.STATUS = 'Active') as scholar_count
                                FROM tbl_municipalities m
                                ORDER BY m.DISTRICT, m.MUNICIPALITY_NAME
                            ");
                            $mydb->executeQuery();
                            $municipalities = $mydb->loadResultList();
                            
                            foreach ($municipalities as $m):
                                $status_color = $m->IS_ACTIVE == 'Yes' ? 'label-success' : 'label-danger';
                                $status_text = $m->IS_ACTIVE == 'Yes' ? 'Active' : 'Inactive';
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($m->MUNICIPALITY_NAME) ?></strong></td>
                                <td><?= $m->DISTRICT ?></td>
                                <td><span class="label <?= $status_color ?>"><?= $status_text ?></span></td>
                                <td><span class="label label-info"><?= $m->applicant_count ?></span></td>
                                <td><span class="label label-success"><?= $m->scholar_count ?></span></td>
                                <td class="text-center">
                                    <a href="index.php?view=edit&id=<?= $m->MUNICIPALITY_ID ?>" 
                                       class="btn btn-primary btn-xs" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <?php if ($_SESSION['ADMIN_ROLE'] == 'Super Admin'): ?>
                                        <a href="controller.php?action=delete&id=<?= $m->MUNICIPALITY_ID ?>" 
                                           class="btn btn-danger btn-xs" 
                                           onclick="return confirm('Delete this municipality? This may affect applicant records.')"
                                           title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 1st District Tab -->
    <div id="first" class="tab-pane fade">
        <div class="panel panel-default" style="margin-top: 15px;">
            <div class="panel-heading">
                <i class="fa fa-sun-o"></i> 1st District Municipalities
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Municipality</th>
                                <th>Status</th>
                                <th>Applicants</th>
                                <th>Scholars</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $mydb->setQuery("
                                SELECT 
                                    m.*,
                                    (SELECT COUNT(*) FROM tbl_applicants WHERE MUNICIPALITY = m.MUNICIPALITY_NAME) as applicant_count,
                                    (SELECT COUNT(*) FROM tbl_applicants a 
                                     INNER JOIN tbl_scholarship_awards sa ON a.APPLICANTID = sa.APPLICANTID 
                                     WHERE a.MUNICIPALITY = m.MUNICIPALITY_NAME AND sa.STATUS = 'Active') as scholar_count
                                FROM tbl_municipalities m
                                WHERE m.DISTRICT = '1st District'
                                ORDER BY m.MUNICIPALITY_NAME
                            ");
                            $mydb->executeQuery();
                            $first_district = $mydb->loadResultList();
                            
                            foreach ($first_district as $m):
                                $status_color = $m->IS_ACTIVE == 'Yes' ? 'label-success' : 'label-danger';
                                $status_text = $m->IS_ACTIVE == 'Yes' ? 'Active' : 'Inactive';
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($m->MUNICIPALITY_NAME) ?></strong></td>
                                <td><span class="label <?= $status_color ?>"><?= $status_text ?></span></td>
                                <td><span class="label label-info"><?= $m->applicant_count ?></span></td>
                                <td><span class="label label-success"><?= $m->scholar_count ?></span></td>
                                <td>
                                    <a href="index.php?view=edit&id=<?= $m->MUNICIPALITY_ID ?>" 
                                       class="btn btn-primary btn-xs">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 2nd District Tab -->
    <div id="second" class="tab-pane fade">
        <div class="panel panel-default" style="margin-top: 15px;">
            <div class="panel-heading">
                <i class="fa fa-moon-o"></i> 2nd District Municipalities
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Municipality</th>
                                <th>Status</th>
                                <th>Applicants</th>
                                <th>Scholars</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $mydb->setQuery("
                                SELECT 
                                    m.*,
                                    (SELECT COUNT(*) FROM tbl_applicants WHERE MUNICIPALITY = m.MUNICIPALITY_NAME) as applicant_count,
                                    (SELECT COUNT(*) FROM tbl_applicants a 
                                     INNER JOIN tbl_scholarship_awards sa ON a.APPLICANTID = sa.APPLICANTID 
                                     WHERE a.MUNICIPALITY = m.MUNICIPALITY_NAME AND sa.STATUS = 'Active') as scholar_count
                                FROM tbl_municipalities m
                                WHERE m.DISTRICT = '2nd District'
                                ORDER BY m.MUNICIPALITY_NAME
                            ");
                            $mydb->executeQuery();
                            $second_district = $mydb->loadResultList();
                            
                            foreach ($second_district as $m):
                                $status_color = $m->IS_ACTIVE == 'Yes' ? 'label-success' : 'label-danger';
                                $status_text = $m->IS_ACTIVE == 'Yes' ? 'Active' : 'Inactive';
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($m->MUNICIPALITY_NAME) ?></strong></td>
                                <td><span class="label <?= $status_color ?>"><?= $status_text ?></span></td>
                                <td><span class="label label-info"><?= $m->applicant_count ?></span></td>
                                <td><span class="label label-success"><?= $m->scholar_count ?></span></td>
                                <td>
                                    <a href="index.php?view=edit&id=<?= $m->MUNICIPALITY_ID ?>" 
                                       class="btn btn-primary btn-xs">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Inactive Tab -->
    <div id="inactive" class="tab-pane fade">
        <div class="panel panel-default" style="margin-top: 15px;">
            <div class="panel-heading">
                <i class="fa fa-ban"></i> Inactive Municipalities
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Municipality</th>
                                <th>District</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $mydb->setQuery("
                                SELECT * FROM tbl_municipalities 
                                WHERE IS_ACTIVE = 'No'
                                ORDER BY DISTRICT, MUNICIPALITY_NAME
                            ");
                            $mydb->executeQuery();
                            $inactive = $mydb->loadResultList();
                            
                            foreach ($inactive as $m):
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($m->MUNICIPALITY_NAME) ?></td>
                                <td><?= $m->DISTRICT ?></td>
                                <td>
                                    <a href="index.php?view=edit&id=<?= $m->MUNICIPALITY_ID ?>" 
                                       class="btn btn-primary btn-xs">
                                        <i class="fa fa-edit"></i> Activate
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($inactive)): ?>
                            <tr>
                                <td colspan="3" class="text-center">No inactive municipalities found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#all-table').DataTable({
        "pageLength": 25,
        "order": [[1, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": [6] }
        ]
    });
});
</script>