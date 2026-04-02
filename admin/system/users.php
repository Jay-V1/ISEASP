<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

// Only Super Admin can access
if ($_SESSION['ADMIN_ROLE'] != 'Super Admin') {
    redirect(web_root . "admin/index.php");
}

global $mydb;
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Manage Users</h1>
    </div>
</div>

<!-- Action Buttons -->
<div class="row">
    <div class="col-lg-12" style="margin-bottom: 15px;">
        <a href="index.php?view=add_user" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New User
        </a>
        <a href="#" onclick="window.print()" class="btn btn-default">
            <i class="fa fa-print"></i> Print List
        </a>
    </div>
</div>

<!-- Users List -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-users"></i> System Users
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="users-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Profile</th>
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Date Created</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $mydb->setQuery("
                                SELECT u.*, COALESCE(a.STATUS, 'Active') as ADMIN_STATUS, a.CREATED_AT 
                                FROM tblusers u
                                LEFT JOIN tbl_admin a ON u.USERID = a.USERID
                                ORDER BY u.USERID ASC
                            ");
                            $mydb->executeQuery();
                            $users = $mydb->loadResultList();
                            
                            foreach ($users as $user):
                                // Special handling for Super Admin
                                if ($user->ROLE == 'Super Admin') {
                                    $status_color = 'label-success';
                                    $status_text = 'Active';
                                    // Ensure Super Admin is always Active in the database
                                    if ($user->USERID == 1) {
                                        $mydb->setQuery("UPDATE tbl_admin SET STATUS = 'Active' WHERE USERID = 1");
                                        $mydb->executeQuery();
                                    }
                                } else {
                                    $status_color = (isset($user->ADMIN_STATUS) && $user->ADMIN_STATUS == 'Active') ? 'label-success' : 'label-danger';
                                    $status_text = (isset($user->ADMIN_STATUS) && $user->ADMIN_STATUS == 'Active') ? 'Active' : 'Inactive';
                                }
                                
                                $profile_pic = !empty($user->PICLOCATION) ? web_root . 'admin/user/' . $user->PICLOCATION : web_root . 'admin/user/default-profile.png';
                                
                                // Check if file exists, otherwise use default
                                if (!empty($user->PICLOCATION) && !file_exists($_SERVER['DOCUMENT_ROOT'] . '/ISEASP1/admin/user/' . $user->PICLOCATION)) {
                                    $profile_pic = web_root . 'admin/user/default-profile.png';
                                }
                            ?>
                            <tr>
                                <td><?= $user->USERID ?></td>
                                <td class="text-center">
                                    <img src="<?= $profile_pic ?>" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" onerror="this.src='<?= web_root ?>admin/user/default-profile.png'">
                                </td>
                                <td><?= htmlspecialchars($user->FULLNAME) ?></td>
                                <td><?= htmlspecialchars($user->USERNAME) ?></td>
                                <td>
                                    <?php
                                    $role_color = match($user->ROLE) {
                                        'Super Admin' => 'label-danger',
                                        'Admin' => 'label-warning',
                                        'Evaluator' => 'label-info',
                                        'Staff' => 'label-success',
                                        default => 'label-default'
                                    };
                                    ?>
                                    <span class="label <?= $role_color ?>"><?= $user->ROLE ?></span>
                                </td>
                                <td><span class="label <?= $status_color ?>"><?= $status_text ?></span></td>
                                <td><?= isset($user->DATECREATED) ? date('M d, Y', strtotime($user->DATECREATED)) : 'N/A' ?></td>
                                <td class="text-center">
                                    <a href="index.php?view=edit_user&id=<?= $user->USERID ?>" class="btn btn-primary btn-xs" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <?php if ($user->USERID != $_SESSION['ADMIN_USERID'] && $user->ROLE != 'Super Admin'): ?>
                                    <a href="controller.php?action=delete&id=<?= $user->USERID ?>" 
                                       class="btn btn-danger btn-xs" 
                                       onclick="return confirm('Delete this user? This action cannot be undone.')"
                                       title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="#" onclick="resetPassword(<?= $user->USERID ?>)" class="btn btn-warning btn-xs" title="Reset Password">
                                        <i class="fa fa-key"></i>
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
</div>

<!-- User Statistics -->
<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Super Admin'");
                $mydb->executeQuery();
                $result = $mydb->loadSingleResult();
                $super_admin = $result ? $result->total : 0;
                ?>
                <h3><?= $super_admin ?></h3>
                <p>Super Admins</p>
            </div>
            <div class="icon">
                <i class="fa fa-shield"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Admin'");
                $mydb->executeQuery();
                $result = $mydb->loadSingleResult();
                $admins = $result ? $result->total : 0;
                ?>
                <h3><?= $admins ?></h3>
                <p>Admins</p>
            </div>
            <div class="icon">
                <i class="fa fa-user-md"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Evaluator'");
                $mydb->executeQuery();
                $result = $mydb->loadSingleResult();
                $evaluators = $result ? $result->total : 0;
                ?>
                <h3><?= $evaluators ?></h3>
                <p>Evaluators</p>
            </div>
            <div class="icon">
                <i class="fa fa-check-square"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <?php
                $mydb->setQuery("SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Staff'");
                $mydb->executeQuery();
                $result = $mydb->loadSingleResult();
                $staff = $result ? $result->total : 0;
                ?>
                <h3><?= $staff ?></h3>
                <p>Staff</p>
            </div>
            <div class="icon">
                <i class="fa fa-user"></i>
            </div>
        </div>
    </div>
</div>

<!-- Create default profile image if it doesn't exist -->
<?php
// Create default profile image if it doesn't exist
$default_profile = $_SERVER['DOCUMENT_ROOT'] . '/ISEASP1/admin/user/default-profile.png';
if (!file_exists($default_profile)) {
    // You can create a simple default image or use a base64 encoded image
    // For now, we'll just note that it doesn't exist
}
?>

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
    $('#users-table').DataTable({
        "pageLength": 25,
        "order": [[0, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": [1, 7] }
        ]
    });
});
</script>