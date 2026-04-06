<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
    exit();
}

if (!isset($_SESSION['ADMIN_ROLE']) || $_SESSION['ADMIN_ROLE'] != 'Super Admin') {
    redirect(web_root . "admin/index.php");
    exit();
}

global $mydb;

$super_admin = 0;
$admins = 0;
$staff = 0;

$mydb->setQuery("SELECT COUNT(*) AS total FROM tblusers WHERE ROLE = 'Super Admin'");
$result = $mydb->loadSingleResult();
$super_admin = $result ? (int)$result->total : 0;

$mydb->setQuery("SELECT COUNT(*) AS total FROM tblusers WHERE ROLE = 'Admin'");
$result = $mydb->loadSingleResult();
$admins = $result ? (int)$result->total : 0;

$mydb->setQuery("SELECT COUNT(*) AS total FROM tblusers WHERE ROLE = 'Staff'");
$result = $mydb->loadSingleResult();
$staff = $result ? (int)$result->total : 0;

$mydb->setQuery("
    SELECT 
        u.*,
        COALESCE(a.STATUS, 'Active') AS ADMIN_STATUS,
        a.CREATED_AT
    FROM tblusers u
    LEFT JOIN tbl_admin a ON u.USERID = a.USERID
    ORDER BY FIELD(u.ROLE, 'Super Admin', 'Admin', 'Staff'), u.USERID ASC
");
$users = $mydb->loadResultList();

function getUserProfileImage($user) {
    $default = web_root . 'admin/user/photos/default-profile.png';

    if (empty($user->PICLOCATION)) {
        return $default;
    }

    $relative = trim($user->PICLOCATION, '/\\');

    // remove duplicated folder prefix if already stored in DB
    $relative = preg_replace('#^(admin/user/photos/|photos/)#i', '', $relative);

    $absolute = $_SERVER['DOCUMENT_ROOT'] . '/ISEASP/admin/user/photos/' . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);

    if (file_exists($absolute) && is_file($absolute)) {
        return web_root . 'admin/user/photos/' . $relative;
    }

    return $default;
}

function getRoleLabelClass($role) {
    switch ($role) {
        case 'Super Admin': return 'danger';
        case 'Admin': return 'warning';
        case 'Staff': return 'success';
        default: return 'default';
    }
}

function getStatusData($user) {
    if ($user->ROLE === 'Super Admin') {
        return ['success', 'Active'];
    }

    $status = (isset($user->ADMIN_STATUS) && $user->ADMIN_STATUS === 'Active') ? 'Active' : 'Inactive';
    $class = ($status === 'Active') ? 'success' : 'danger';

    return [$class, $status];
}
?>

<style>
.small-box {
    border-radius: 5px;
    position: relative;
    display: block;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.small-box .inner {
    padding: 10px;
}

.small-box h3 {
    font-size: 38px;
    font-weight: bold;
    margin: 0 0 10px 0;
    white-space: nowrap;
    padding: 0;
}

.small-box p {
    font-size: 15px;
    margin: 0;
}

.small-box .icon {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 0;
    font-size: 70px;
    color: rgba(0,0,0,0.15);
}

.small-box.bg-red { background-color: #dd4b39; color: #fff; }
.small-box.bg-yellow { background-color: #f39c12; color: #fff; }
.small-box.bg-green { background-color: #00a65a; color: #fff; }

.panel-body.no-padding {
    padding: 0 !important;
}

#users-table {
    width: 100% !important;
    margin-bottom: 0 !important;
}

#users-table thead th,
#users-table tbody td {
    vertical-align: middle !important;
}

#users-table thead th {
    white-space: nowrap;
    background: #f9f9f9;
    border-bottom: 2px solid #ddd !important;
}

.profile-img {
    width: 40px;
    height: 40px;
    display: block;
    margin: 0 auto;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #ddd;
    background: #f4f4f4;
}

.action-buttons {
    white-space: nowrap;
}

.action-buttons .btn {
    margin: 2px;
}

.label {
    display: inline-block;
    min-width: 70px;
    text-align: center;
}

.dataTables_wrapper {
    padding: 10px;
}

#users-table.dataTable {
    opacity: 0;
    transition: opacity .15s ease-in-out;
}

#users-table.dataTable.dt-ready {
    opacity: 1;
}

@media (max-width: 991px) {
    .action-buttons {
        white-space: normal;
    }
}
</style>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Manage Users</h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12" style="margin-bottom: 15px;">
        <a href="index.php?view=add_user" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New User
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4 col-sm-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3><?php echo $super_admin; ?></h3>
                <p>Super Admins</p>
            </div>
            <div class="icon"><i class="fa fa-shield"></i></div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3><?php echo $admins; ?></h3>
                <p>Admins</p>
            </div>
            <div class="icon"><i class="fa fa-user-md"></i></div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3><?php echo $staff; ?></h3>
                <p>Staff</p>
            </div>
            <div class="icon"><i class="fa fa-user"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-users"></i> System Users
            </div>

            <div class="panel-body no-padding">
                <div id="users-table-shell" class="users-table-shell is-loading">
                    <!-- <div class="users-loader">
                        <i class="fa fa-spinner fa-spin"></i><br>
                        Loading users...
                    </div> -->

                    <div class="table-responsive">
                        <table id="users-table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Profile</th>
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Date Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)) : ?>
                                    <?php foreach ($users as $user) : ?>
                                        <?php
                                        list($status_class, $status_text) = getStatusData($user);
                                        $role_class = getRoleLabelClass($user->ROLE);
                                        $profile_pic = getUserProfileImage($user);
                                        $date_created = (!empty($user->DATECREATED) && $user->DATECREATED != '0000-00-00' && $user->DATECREATED != '0000-00-00 00:00:00')
                                            ? date('M d, Y', strtotime($user->DATECREATED))
                                            : 'N/A';
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo (int)$user->USERID; ?></td>
                                            <td class="text-center">
                                                <img src="<?php echo $profile_pic; ?>"
                                                     alt="Profile"
                                                     class="profile-img"
                                                     width="40"
                                                     height="40"
                                                     onerror="this.onerror=null;this.src='<?php echo web_root; ?>admin/user/photos/default-profile.png';">
                                            </td>
                                            <td><?php echo htmlspecialchars($user->FULLNAME); ?></td>
                                            <td><?php echo htmlspecialchars($user->USERNAME); ?></td>
                                            <td><span class="label label-<?php echo $role_class; ?>"><?php echo htmlspecialchars($user->ROLE); ?></span></td>
                                            <td><span class="label label-<?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                            <td><?php echo $date_created; ?></td>
                                            <td class="text-center">
                                                <div class="action-buttons">
                                                    <a href="index.php?view=edit_user&id=<?php echo (int)$user->USERID; ?>" class="btn btn-primary btn-xs">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </a>

                                                    <?php if ($user->USERID != $_SESSION['ADMIN_USERID'] && $user->ROLE != 'Super Admin') : ?>
                                                        <a href="controller.php?action=delete&id=<?php echo (int)$user->USERID; ?>"
                                                           class="btn btn-danger btn-xs"
                                                           onclick="return confirm('Delete this user? This action cannot be undone.');">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </a>
                                                    <?php endif; ?>

                                                    <button type="button"
                                                            class="btn btn-warning btn-xs btn-reset-password"
                                                            data-userid="<?php echo (int)$user->USERID; ?>">
                                                        <i class="fa fa-key"></i> Reset
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No users found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$page_script = '
<script>
(function ($) {
    "use strict";

    function resetPassword(userId) {
        if (!userId) {
            alert("Invalid user ID.");
            return;
        }

        if (!confirm("Reset password for this user? A temporary password will be generated.")) {
            return;
        }

        $.ajax({
            url: "controller.php?action=reset_password",
            type: "POST",
            data: { id: userId },
            dataType: "json",
            cache: false,
            success: function (response) {
                if (response && response.status === "success") {
                    alert("Password reset successfully!\\n\\nNew password: " + response.password);
                } else {
                    alert("Error resetting password.");
                }
            },
            error: function () {
                alert("An error occurred while resetting the password.");
            }
        });
    }

    $(function () {
        var $table = $("#users-table");

        $(document).on("click", ".btn-reset-password", function () {
            resetPassword($(this).data("userid"));
        });

        if (!$table.length) {
            return;
        }

        // If DataTables is missing, just show normal table
        if (!$.fn.DataTable) {
            $table.addClass("dt-ready");
            return;
        }

        try {
            if ($.fn.DataTable.isDataTable("#users-table")) {
                $table.DataTable().destroy();
            }

            $table.on("init.dt", function () {
                $table.addClass("dt-ready");
            });

            $table.DataTable({
                paging: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                ordering: true,
                info: true,
                searching: true,
                responsive: false,
                autoWidth: false,
                stateSave: false,
                processing: false,
                deferRender: false,
                order: [[0, "asc"]],
                columnDefs: [
                    { orderable: false, targets: [1, 7] },
                    { className: "text-center", targets: [0, 1, 7] }
                ],
                language: {
                    emptyTable: "No users found",
                    info: "Showing _START_ to _END_ of _TOTAL_ users",
                    infoEmpty: "Showing 0 to 0 of 0 users",
                    infoFiltered: "(filtered from _MAX_ total users)",
                    lengthMenu: "Show _MENU_ users",
                    search: "Search:",
                    zeroRecords: "No matching users found"
                },
                initComplete: function () {
                    $table.addClass("dt-ready");
                }
            });

            // Fallback in case init event does not fire properly
            setTimeout(function () {
                $table.addClass("dt-ready");
            }, 800);

        } catch (e) {
            console.log("DataTable init error:", e);
            $table.addClass("dt-ready");
        }
    });
})(jQuery);
</script>';
?>
?>