<?php 
if (!isset($_SESSION['ADMIN_USERID'])){
    redirect(web_root."admin/index.php");
}

// Only Super Admin can add users
if ($_SESSION['ADMIN_ROLE'] != 'Super Admin') {
    message("Access denied. Super Admin only.", "error");
    redirect("index.php");
}

$autonum = New Autonumber();
$res = $autonum->set_autonumber('userid');
?> 

<div class="col-lg-12">
    <h1 class="page-header">Add New User</h1>
</div>

<form class="form-horizontal span6" action="controller.php?action=add" method="POST" enctype="multipart/form-data">
    
    <input id="user_id" name="user_id" type="hidden" value="<?php echo $res->AUTO; ?>">
    
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="FULLNAME">Full Name:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="FULLNAME" name="FULLNAME" 
                       placeholder="Full Name" type="text" value="" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="USERNAME">Username:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="USERNAME" name="USERNAME" 
                       placeholder="Username" type="text" value="" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="PASS">Password:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="PASS" name="PASS" 
                       placeholder="Password" type="password" value="" required minlength="8">
                <small class="text-muted">Minimum 8 characters</small>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="CONFIRM_PASS">Confirm Password:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="CONFIRM_PASS" name="CONFIRM_PASS" 
                       placeholder="Confirm Password" type="password" value="" required>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="ROLE">Role:</label>
            <div class="col-md-8">
                <select class="form-control input-sm" name="ROLE" id="ROLE" required>
                    <option value="">-- Select Role --</option>
                    <option value="Super Admin">Super Admin</option>
                    <option value="Admin">Admin</option>
                    <option value="Evaluator">Evaluator</option>
                    <option value="Staff">Staff</option>
                </select>
                <small class="text-muted">
                    <strong>Super Admin:</strong> Full access<br>
                    <strong>Admin:</strong> Manage most modules<br>
                    <strong>Evaluator:</strong> Can evaluate applicants<br>
                    <strong>Staff:</strong> Basic data entry
                </small>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="STATUS">Status:</label>
            <div class="col-md-8">
                <select class="form-control input-sm" name="STATUS" id="STATUS" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="PICLOCATION">Profile Picture:</label>
            <div class="col-md-8">
                <input type="file" class="form-control input-sm" id="PICLOCATION" name="PICLOCATION" 
                       accept="image/*">
                <small class="text-muted">Optional. Allowed: JPG, PNG, GIF, WEBP. Max: 2MB</small>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <div class="col-md-offset-4 col-md-8">
                <button class="btn btn-primary btn-sm" name="save" type="submit">
                    <span class="fa fa-save"></span> Save User
                </button>
                <a href="index.php" class="btn btn-default btn-sm">
                    <span class="fa fa-arrow-left"></span> Cancel
                </a>
            </div>
        </div>
    </div>
  
</form>

<script>
// Password match validation
document.querySelector('form').addEventListener('submit', function(e) {
    var pass = document.getElementById('PASS').value;
    var confirm = document.getElementById('CONFIRM_PASS').value;
    
    if (pass != confirm) {
        e.preventDefault();
        alert('Passwords do not match!');
    } else if (pass.length < 8) {
        e.preventDefault();
        alert('Password must be at least 8 characters long!');
    }
});
</script>