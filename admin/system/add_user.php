<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

if ($_SESSION['ADMIN_ROLE'] != 'Super Admin') {
    redirect(web_root . "admin/index.php");
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add New User</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-user-plus"></i> User Information
            </div>
            <div class="panel-body">
                <form method="POST" action="controller.php?action=add" enctype="multipart/form-data" class="form-horizontal">
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Full Name:</label>
                        <div class="col-md-7">
                            <input type="text" name="FULLNAME" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Username:</label>
                        <div class="col-md-7">
                            <input type="text" name="USERNAME" class="form-control" required>
                            <span class="help-block">Username must be unique</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Password:</label>
                        <div class="col-md-7">
                            <input type="password" name="PASS" class="form-control" required>
                            <span class="help-block">Minimum 8 characters</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Confirm Password:</label>
                        <div class="col-md-7">
                            <input type="password" name="CONFIRM_PASS" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Role:</label>
                        <div class="col-md-7">
                            <select name="ROLE" class="form-control" required>
                                <option value="">-- Select Role --</option>
                                <option value="Super Admin">Super Admin</option>
                                <option value="Admin">Admin</option>
                                <option value="Evaluator">Evaluator</option>
                                <option value="Staff">Staff</option>
                            </select>
                            <span class="help-block">
                                <strong>Super Admin:</strong> Full system access<br>
                                <strong>Admin:</strong> Can manage most modules<br>
                                <strong>Evaluator:</strong> Can evaluate applicants<br>
                                <strong>Staff:</strong> Basic data entry
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Profile Picture:</label>
                        <div class="col-md-7">
                            <input type="file" name="PICLOCATION" class="form-control" accept="image/*">
                            <span class="help-block">Optional. Max size: 2MB</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Status:</label>
                        <div class="col-md-7">
                            <select name="STATUS" class="form-control" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-md-offset-3 col-md-7">
                            <button type="submit" name="save" class="btn btn-primary">
                                <i class="fa fa-save"></i> Create User
                            </button>
                            <a href="index.php?view=users" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>