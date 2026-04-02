<?php 
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Scholarship Requirements</h1>
    </div>
</div>

<!-- ADD BUTTON -->
<div class="row mb-3">
    <div class="col-lg-12" style="margin-bottom: 10px;">
        <a href="index.php?view=add" class="btn btn-primary mb-3">
            <i class="fa fa-plus"></i> New Requirement
        </a>
    </div>
</div>

<form action="controller.php?action=delete" method="POST">  
    <div class="table-responsive">					
        <table id="dash-table" class="table table-striped table-bordered table-hover" style="font-size:13px" cellspacing="0">
            <thead>
                <tr>
                    <th width="3%"><input type="checkbox" id="chkAll"></th>
                    <th>Requirement Name</th> 
                    <th>Description</th> 
                    <th>Required For</th> 
                    <th>Status</th>
                    <th width="15%" class="text-center">Action</th>
                </tr>	
            </thead> 
            <tbody>
                <?php 
                $requirements = [
                    ['id'=>1,'name'=>'Birth Certificate','desc'=>'PSA-issued copy required','program'=>'All Programs','status'=>'Active'],
                    ['id'=>2,'name'=>'School Transcript of Records','desc'=>'Latest academic transcript','program'=>'Merit-Based & Educational Grants','status'=>'Active'],
                    ['id'=>3,'name'=>'Good Moral Certificate','desc'=>'Issued by school','program'=>'All Programs','status'=>'Active'],
                    ['id'=>4,'name'=>'Barangay Clearance','desc'=>'Proof of residency','program'=>'All Programs','status'=>'Active'],
                    ['id'=>5,'name'=>'Certificate of Income','desc'=>'For financial eligibility verification','program'=>'Educational Assistance Grant','status'=>'Active'],
                    ['id'=>6,'name'=>'2x2 Photo','desc'=>'Recent passport-size photo','program'=>'All Programs','status'=>'Active'],
                ];

                foreach ($requirements as $req) {
                    $statusColor = ($req['status'] == 'Active') ? 'label-success' : 'label-default';
                    echo '<tr>';
                    echo '<td><input type="checkbox" name="selector[]" value="'. $req['id'] .'"></td>';
                    echo '<td>' . $req['name'] . '</td>';
                    echo '<td>' . $req['desc'] . '</td>';
                    echo '<td>' . $req['program'] . '</td>';
                    echo '<td><span class="label ' . $statusColor . '">' . $req['status'] . '</span></td>';
                    echo '<td class="text-center">
                            
                            <a href="index.php?view=edit&id='. $req['id'] .'" class="btn btn-warning btn-xs" title="Edit"><span class="fa fa-edit"></span></a>
                            <a href="controller.php?action=delete&id='. $req['id'] .'" class="btn btn-danger btn-xs" title="Delete" onclick="return confirm(\'Delete this requirement?\')"><span class="fa fa-trash"></span></a>
                          </td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</form>

<script>
document.getElementById('chkAll').onclick = function() {
    document.querySelectorAll('input[name="selector[]"]').forEach(cb => cb.checked = this.checked);
};
</script>
