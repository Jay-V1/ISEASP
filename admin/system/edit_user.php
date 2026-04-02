<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

if ($_SESSION['ADMIN_ROLE'] != 'Super Admin') {
    redirect(web_root . "admin/index.php");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) redirect("index.php?view=users");

global $mydb;

// Get user details
$mydb->setQuery("SELECT u.*, a.STATUS as ADMIN_STATUS FROM tblusers u LEFT JOIN tbl_admin a ON u.USERID = a.USERID WHERE u.USERID = $id");
$mydb->executeQuery();
$user = $mydb->loadSingleResult();

if (!$user) {
    message("User not found!", "error");
    redirect("index.php?view=users");
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Edit User</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-edit"></i> Edit: <?= htmlspecialchars($user->FULLNAME) ?>
            </div>
            <div class="panel-body">
                <form method="POST" action="controller.php?action=edit" enctype="multipart/form-data" class="form-horizontal">
                    
                    <input type="hidden" name="USERID" value="<?= $user->USERID ?>">
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Full Name:</label>
                        <div class="col-md-7">
                            <input type="text" name="FULLNAME" class="form-control" value="<?= htmlspecialchars($user->FULLNAME) ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Username:</label>
                        <div class="col-md-7">
                            <input type="text" name="USERNAME" class="form-control" value="<?= htmlspecialchars($user->USERNAME) ?>" required>
                            <span class="help-block">Username must be unique</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">New Password:</label>
                        <div class="col-md-7">
                            <input type="password" name="PASS" class="form-control" placeholder="Leave blank to keep current password">
                            <span class="help-block">Only fill if you want to change password</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Confirm Password:</label>
                        <div class="col-md-7">
                            <input type="password" name="CONFIRM_PASS" class="form-control" placeholder="Confirm new password">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Role:</label>
                        <div class="col-md-7">
                            <select name="ROLE" class="form-control" required>
                                <option value="Super Admin" <?= $user->ROLE == 'Super Admin' ? 'selected' : '' ?>>Super Admin</option>
                                <option value="Admin" <?= $user->ROLE == 'Admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="Evaluator" <?= $user->ROLE == 'Evaluator' ? 'selected' : '' ?>>Evaluator</option>
                                <option value="Staff" <?= $user->ROLE == 'Staff' ? 'selected' : '' ?>>Staff</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Current Profile:</label>
                        <div class="col-md-7">
                            <?php if (!empty($user->PICLOCATION)): ?>
                                <img src="<?= web_root . 'admin/user/' . $user->PICLOCATION ?>" style="max-width: 100px; max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                            <?php else: ?>
                                <p>No profile picture</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Change Picture:</label>
                        <div class="col-md-7">
                            <input type="file" name="PICLOCATION" class="form-control" accept="image/*">
                            <span class="help-block">Leave blank to keep current picture</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Status:</label>
                        <div class="col-md-7">
                            <select name="STATUS" class="form-control" required>
                                <option value="Active" <?= ($user->ADMIN_STATUS ?? 'Active') == 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="Inactive" <?= ($user->ADMIN_STATUS ?? '') == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-md-offset-3 col-md-7">
                            <button type="submit" name="save" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update User
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