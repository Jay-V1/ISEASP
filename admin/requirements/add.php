<?php
if (!isset($_SESSION['ADMIN_USERID'])) redirect(web_root . "admin/index.php");
?>

<form class="form-horizontal span6" action="controller.php?action=add" method="POST">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Add New Requirement</h1>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label">Requirement Name:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" name="NAME" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label">Description:</label>
            <div class="col-md-8">
                <textarea class="form-control input-sm" name="DESC" required></textarea>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label">Required For:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" name="PROGRAM" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label">Status:</label>
            <div class="col-md-8">
                <select class="form-control input-sm" name="STATUS">
                    <option>Active</option>
                    <option>Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <div class="col-md-offset-4 col-md-8">
                <button class="btn btn-primary btn-sm" type="submit"><span class="fa fa-save"></span> Save</button>
                <a href="index.php" class="btn btn-secondary btn-sm"><span class="fa fa-arrow-left"></span> Cancel</a>
            </div>
        </div>
    </div>
</form>
