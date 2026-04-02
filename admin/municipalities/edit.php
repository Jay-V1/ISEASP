<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) redirect("index.php");

global $mydb;

$mydb->setQuery("SELECT * FROM tbl_municipalities WHERE MUNICIPALITY_ID = $id");
$mydb->executeQuery();
$municipality = $mydb->loadSingleResult();

if (!$municipality) {
    message("Municipality not found!", "error");
    redirect("index.php");
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Edit Municipality</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-edit"></i> Edit: <?= htmlspecialchars($municipality->MUNICIPALITY_NAME) ?>
            </div>
            <div class="panel-body">
                <form method="POST" action="controller.php?action=edit" class="form-horizontal">
                    
                    <input type="hidden" name="MUNICIPALITY_ID" value="<?= $municipality->MUNICIPALITY_ID ?>">
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Municipality Name:</label>
                        <div class="col-md-7">
                            <input type="text" name="MUNICIPALITY_NAME" class="form-control" 
                                   value="<?= htmlspecialchars($municipality->MUNICIPALITY_NAME) ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">District:</label>
                        <div class="col-md-7">
                            <select name="DISTRICT" class="form-control" required>
                                <option value="1st District" <?= $municipality->DISTRICT == '1st District' ? 'selected' : '' ?>>1st District</option>
                                <option value="2nd District" <?= $municipality->DISTRICT == '2nd District' ? 'selected' : '' ?>>2nd District</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Status:</label>
                        <div class="col-md-7">
                            <select name="IS_ACTIVE" class="form-control" required>
                                <option value="Yes" <?= $municipality->IS_ACTIVE == 'Yes' ? 'selected' : '' ?>>Active</option>
                                <option value="No" <?= $municipality->IS_ACTIVE == 'No' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-md-offset-3 col-md-7">
                            <button type="submit" name="save" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Municipality
                            </button>
                            <a href="index.php" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Statistics for this municipality -->
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-info">
            <div class="panel-heading">
                <i class="fa fa-bar-chart"></i> Municipality Statistics
            </div>
            <div class="panel-body">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants WHERE MUNICIPALITY = '" . $municipality->MUNICIPALITY_NAME . "'");
                $mydb->executeQuery();
                $applicants = $mydb->loadSingleResult();
                
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants a 
                                 INNER JOIN tbl_scholarship_awards sa ON a.APPLICANTID = sa.APPLICANTID 
                                 WHERE a.MUNICIPALITY = '" . $municipality->MUNICIPALITY_NAME . "' AND sa.STATUS = 'Active'");
                $mydb->executeQuery();
                $scholars = $mydb->loadSingleResult();
                
                $mydb->setQuery("SELECT COUNT(*) as total FROM tbl_applicants a 
                                 INNER JOIN tbl_scholarship_history h ON a.APPLICANTID = h.APPLICANTID 
                                 WHERE a.MUNICIPALITY = '" . $municipality->MUNICIPALITY_NAME . "' AND h.STATUS = 'Graduated'");
                $mydb->executeQuery();
                $graduates = $mydb->loadSingleResult();
                ?>
                
                <div class="row text-center">
                    <div class="col-md-4">
                        <h3><?= $applicants->total ?? 0 ?></h3>
                        <small>Total Applicants</small>
                    </div>
                    <div class="col-md-4">
                        <h3><?= $scholars->total ?? 0 ?></h3>
                        <small>Active Scholars</small>
                    </div>
                    <div class="col-md-4">
                        <h3><?= $graduates->total ?? 0 ?></h3>
                        <small>Graduates</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>