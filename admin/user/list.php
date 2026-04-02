<?php
if (!isset($_SESSION['ADMIN_USERID'])){
    redirect(web_root."admin/index.php");
}

// Only Super Admin can see all users, others see limited view
$is_super_admin = ($_SESSION['ADMIN_ROLE'] == 'Super Admin');
?> 

<div class="col-lg-12">
    <h1 class="page-header">
        List of Users 
        <?php if($is_super_admin): ?>
            <a href="index.php?view=add" class="btn btn-primary btn-xs">
                <i class="fa fa-plus-circle"></i> Add User
            </a>
        <?php endif; ?>
    </h1>
</div>

<div class="col-lg-12"> 
    <table id="dash-table" class="table table-bordered table-hover table-responsive" style="font-size:12px;" cellspacing="0"> 
        <thead>
            <tr>
                <th>ID</th>
                <th>Photo</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Role</th>
                <th>Status</th>
                <th>Date Created</th>
                <th width="15%">Action</th>
            </tr>	
        </thead> 
        <tbody>
            <?php 
            // For non-super admin, only show their own account
            if($is_super_admin) {
                $mydb->setQuery("SELECT u.*, a.STATUS as ADMIN_STATUS 
                                FROM tblusers u 
                                LEFT JOIN tbl_admin a ON u.USERID = a.USERID
                                ORDER BY u.USERID ASC");
            } else {
                $mydb->setQuery("SELECT u.*, a.STATUS as ADMIN_STATUS 
                                FROM tblusers u 
                                LEFT JOIN tbl_admin a ON u.USERID = a.USERID
                                WHERE u.USERID = " . $_SESSION['ADMIN_USERID']);
            }
            $mydb->executeQuery();
            $cur = $mydb->loadResultList();

            foreach ($cur as $result) {
                $status_color = (isset($result->ADMIN_STATUS) && $result->ADMIN_STATUS == 'Active') ? 'label-success' : 'label-danger';
                $status_text = (isset($result->ADMIN_STATUS) && $result->ADMIN_STATUS == 'Active') ? 'Active' : 'Inactive';
                
                $profile_pic = !empty($result->PICLOCATION) ? web_root.'admin/user/'.$result->PICLOCATION : web_root.'admin/user/default-profile.png';
                
                // Check if user can be deleted (not self, not Super Admin for non-super admins)
                $can_delete = false;
                if($is_super_admin && $result->USERID != $_SESSION['ADMIN_USERID']) {
                    $can_delete = true;
                }
                
                // Check if user can be edited
                $can_edit = true;
                if(!$is_super_admin && $result->USERID != $_SESSION['ADMIN_USERID']) {
                    $can_edit = false;
                }
            ?>
            <tr>
                <td><?php echo $result->USERID; ?></td>
                <td align="center">
                    <img src="<?php echo $profile_pic; ?>" alt="Profile" 
                         style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"
                         onerror="this.src='<?php echo web_root;?>admin/user/default-profile.png'">
                </td>
                <td><?php echo htmlspecialchars($result->FULLNAME); ?></td>
                <td><?php echo htmlspecialchars($result->USERNAME); ?></td>
                <td>
                    <?php
                    $role_color = 'default';
                    if($result->ROLE == 'Super Admin') $role_color = 'danger';
                    elseif($result->ROLE == 'Admin') $role_color = 'warning';
                    elseif($result->ROLE == 'Evaluator') $role_color = 'info';
                    elseif($result->ROLE == 'Staff') $role_color = 'success';
                    ?>
                    <span class="label label-<?php echo $role_color; ?>"><?php echo $result->ROLE; ?></span>
                </td>
                <td><span class="label <?php echo $status_color; ?>"><?php echo $status_text; ?></span></td>
                <td><?php echo date('M d, Y', strtotime($result->DATECREATED)); ?></td>
                <td align="center">
                    <?php if($can_edit): ?>
                        <a title="Edit" href="index.php?view=edit&id=<?php echo $result->USERID; ?>" 
                           class="btn btn-primary btn-xs">
                            <span class="fa fa-edit"></span>
                        </a>
                    <?php endif; ?>
                    
                    <?php if($can_delete): ?>
                        <a title="Delete" href="controller.php?action=delete&id=<?php echo $result->USERID; ?>" 
                           class="btn btn-danger btn-xs" 
                           onclick="return confirm('Delete this user? This action cannot be undone.')">
                            <span class="fa fa-trash-o"></span>
                        </a>
                    <?php endif; ?>
                    
                    <?php if($is_super_admin): ?>
                        <a title="Reset Password" href="#" onclick="resetPassword(<?php echo $result->USERID; ?>)" 
                           class="btn btn-warning btn-xs">
                            <span class="fa fa-key"></span>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>  
</div>

<script>
function resetPassword(userId) {
    if (confirm('Reset password for this user? A temporary password will be generated.')) {
        $.post('controller.php?action=reset_password', {id: userId}, function(response) {
            if (response.status == 'success') {
                alert('Password reset successfully. New password: ' + response.password);
            } else {
                alert('Error resetting password.');
            }
        }, 'json');
    }
}

$(document).ready(function() {
    $('#dash-table').DataTable({
        "pageLength": 25,
        "order": [[0, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": [1, 7] }
        ]
    });
});
</script>