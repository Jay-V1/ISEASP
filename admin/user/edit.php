<?php  
if (!isset($_SESSION['ADMIN_USERID'])){
    redirect(web_root."admin/index.php");
}

// Only Super Admin can edit users (except own profile)
$USERID = isset($_GET['id']) ? $_GET['id'] : '';
if($USERID==''){
    redirect("index.php");
}

$user = New User();
$singleuser = $user->single_user($USERID);

if(!$singleuser){
    message("User not found!", "error");
    redirect("index.php");
}

// Get status from tbl_admin
global $mydb;
$mydb->setQuery("SELECT STATUS FROM tbl_admin WHERE USERID = ".$USERID);
$mydb->executeQuery();
$status_result = $mydb->loadSingleResult();
$current_status = $status_result ? $status_result->STATUS : 'Active';
?> 

<div class="col-lg-12">
    <h1 class="page-header">Edit User: <?php echo htmlspecialchars($singleuser->FULLNAME); ?></h1>
</div>

<form class="form-horizontal span6" action="controller.php?action=edit" method="POST" enctype="multipart/form-data">
    
    <input id="USERID" name="USERID" type="hidden" value="<?php echo $singleuser->USERID; ?>">
    <input id="old_picture" name="old_picture" type="hidden" value="<?php echo $singleuser->PICLOCATION; ?>">
    
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="FULLNAME">Full Name:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="FULLNAME" name="FULLNAME" 
                       placeholder="Full Name" type="text" value="<?php echo $singleuser->FULLNAME; ?>" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="USERNAME">Username:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="USERNAME" name="USERNAME" 
                       placeholder="Username" type="text" value="<?php echo $singleuser->USERNAME; ?>" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="PASS">New Password:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="PASS" name="PASS" 
                       placeholder="Leave blank to keep current" type="password">
                <small class="text-muted">Minimum 8 characters if changing</small>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="CONFIRM_PASS">Confirm Password:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="CONFIRM_PASS" name="CONFIRM_PASS" 
                       placeholder="Confirm new password" type="password">
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="ROLE">Role:</label>
            <div class="col-md-8">
                <?php if($_SESSION['ADMIN_ROLE'] == 'Super Admin'): ?>
                    <select class="form-control input-sm" name="ROLE" id="ROLE" required>
                        <option value="Super Admin" <?php echo ($singleuser->ROLE=='Super Admin') ? 'selected' : ''; ?>>Super Admin</option>
                        <option value="Admin" <?php echo ($singleuser->ROLE=='Admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="Evaluator" <?php echo ($singleuser->ROLE=='Evaluator') ? 'selected' : ''; ?>>Evaluator</option>
                        <option value="Staff" <?php echo ($singleuser->ROLE=='Staff') ? 'selected' : ''; ?>>Staff</option>
                    </select>
                <?php else: ?>
                    <input class="form-control input-sm" type="text" value="<?php echo $singleuser->ROLE; ?>" readonly disabled>
                    <small class="text-muted">Only Super Admin can change roles</small>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Status - Only for Super Admin and not editing own account -->
    <?php if($_SESSION['ADMIN_ROLE'] == 'Super Admin' && $singleuser->USERID != $_SESSION['ADMIN_USERID']): ?>
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="STATUS">Status:</label>
            <div class="col-md-8">
                <select class="form-control input-sm" name="STATUS" id="STATUS" required>
                    <option value="Active" <?php echo ($current_status=='Active') ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo ($current_status=='Inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Profile Picture -->
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label">Current Picture:</label>
            <div class="col-md-8">
                <?php if(!empty($singleuser->PICLOCATION)): ?>
                    <img src="<?php echo web_root.'admin/user/photos/'. $singleuser->PICLOCATION; ?>" 
                         style="max-width: 100px; max-height: 100px; border: 1px solid #ddd; padding: 5px;"
                         onerror="this.src='<?php echo web_root;?>admin/user/photos/default-profile.png'">
                <?php else: ?>
                    <p>No profile picture</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="PICLOCATION">Change Picture:</label>
            <div class="col-md-8">
                <input type="file" class="form-control input-sm" id="PICLOCATION" name="PICLOCATION" 
                       accept="image/*">
                <small class="text-muted">Leave blank to keep current picture</small>
            </div>
        </div>
    </div>
    
    <!-- Email (optional) -->
    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="EMAIL">Email:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="EMAIL" name="EMAIL" 
                       placeholder="Email Address" type="email" 
                       value="<?php echo isset($singleuser->EMAIL) ? $singleuser->EMAIL : ''; ?>">
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <div class="col-md-offset-4 col-md-8">
                <button class="btn btn-primary btn-sm" name="save" type="submit">
                    <span class="fa fa-save"></span> Update User
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
    
    if (pass != '' && pass != confirm) {
        e.preventDefault();
        alert('Passwords do not match!');
    } else if (pass != '' && pass.length < 8) {
        e.preventDefault();
        alert('Password must be at least 8 characters long!');
    }
});
</script>