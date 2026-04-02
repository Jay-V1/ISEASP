<?php
require_once("../../include/initialize.php");

if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

global $mydb;

// Handle Add/Edit/Delete operations
if (isset($_POST['add_requirement'])) {
    $name = trim($_POST['requirement_name']);
    $description = trim($_POST['description']);
    $required = $_POST['required'];
    $category = $_POST['category'];
    $display_order = intval($_POST['display_order']);
    $created_by = $_SESSION['ADMIN_USERID'];
    
    // Escape special characters to prevent SQL errors
    $name = addslashes($name);
    $description = addslashes($description);
    
    $sql = "INSERT INTO tbl_requirement (REQUIREMENT_NAME, DESCRIPTION, REQUIRED, CATEGORY, DISPLAY_ORDER, CREATED_BY, CREATED_AT)
            VALUES ('$name', '$description', '$required', '$category', $display_order, $created_by, NOW())";
    
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    
    message("Requirement added successfully!", "success");
    redirect("index.php?view=manage_req");
}

if (isset($_POST['edit_requirement'])) {
    $id = intval($_POST['requirement_id']);
    $name = trim($_POST['requirement_name']);
    $description = trim($_POST['description']);
    $required = $_POST['required'];
    $category = $_POST['category'];
    $display_order = intval($_POST['display_order']);
    $updated_by = $_SESSION['ADMIN_USERID'];
    
    // Escape special characters to prevent SQL errors
    $name = addslashes($name);
    $description = addslashes($description);
    
    $sql = "UPDATE tbl_requirement SET 
            REQUIREMENT_NAME = '$name',
            DESCRIPTION = '$description',
            REQUIRED = '$required',
            CATEGORY = '$category',
            DISPLAY_ORDER = $display_order,
            UPDATED_BY = $updated_by,
            UPDATED_AT = NOW()
            WHERE REQUIREMENT_ID = $id";
    
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    
    message("Requirement updated successfully!", "success");
    redirect("index.php?view=manage_req");
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Check if requirement is being used
    $check_sql = "SELECT COUNT(*) as count FROM tbl_applicant_requirement_checklist WHERE REQUIREMENT_ID = $id";
    $mydb->setQuery($check_sql);
    $mydb->executeQuery();
    $result = $mydb->loadSingleResult();
    
    if ($result->count > 0) {
        message("Cannot delete requirement because it is already being used by applicants.", "error");
    } else {
        $sql = "DELETE FROM tbl_requirement WHERE REQUIREMENT_ID = $id";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        message("Requirement deleted successfully!", "success");
    }
    redirect("index.php?view=manage_req");
}

// Get all requirements
$mydb->setQuery("SELECT * FROM tbl_requirement ORDER BY CATEGORY, DISPLAY_ORDER");
$mydb->executeQuery();
$requirements = $mydb->loadResultList();

// Get requirement for editing if ID is provided
$edit_requirement = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $mydb->setQuery("SELECT * FROM tbl_requirement WHERE REQUIREMENT_ID = $edit_id");
    $mydb->executeQuery();
    $edit_requirement = $mydb->loadSingleResult();
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Manage Requirements</h1>
    </div>
</div>

<div class="row">
    <!-- Add/Edit Form -->
    <div class="col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-plus"></i> <?php echo $edit_requirement ? 'Edit' : 'Add'; ?> Requirement
            </div>
            <div class="panel-body">
                <form method="POST" action="index.php?view=manage_req">
                    <?php if ($edit_requirement): ?>
                        <input type="hidden" name="requirement_id" value="<?= $edit_requirement->REQUIREMENT_ID ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Requirement Name</label>
                        <input type="text" name="requirement_name" class="form-control" required 
                               value="<?= $edit_requirement ? htmlspecialchars($edit_requirement->REQUIREMENT_NAME) : '' ?>">
                        <small class="text-muted">Use proper spelling (e.g., Parent's Income Tax Return)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= $edit_requirement ? htmlspecialchars($edit_requirement->DESCRIPTION) : '' ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" class="form-control" required>
                            <option value="Personal" <?= $edit_requirement && $edit_requirement->CATEGORY == 'Personal' ? 'selected' : '' ?>>Personal</option>
                            <option value="Academic" <?= $edit_requirement && $edit_requirement->CATEGORY == 'Academic' ? 'selected' : '' ?>>Academic</option>
                            <option value="Financial" <?= $edit_requirement && $edit_requirement->CATEGORY == 'Financial' ? 'selected' : '' ?>>Financial</option>
                            <option value="Other" <?= $edit_requirement && $edit_requirement->CATEGORY == 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Required</label>
                        <select name="required" class="form-control" required>
                            <option value="Yes" <?= $edit_requirement && $edit_requirement->REQUIRED == 'Yes' ? 'selected' : '' ?>>Yes (Required)</option>
                            <option value="No" <?= $edit_requirement && $edit_requirement->REQUIRED == 'No' ? 'selected' : '' ?>>No (Optional)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Display Order</label>
                        <input type="number" name="display_order" class="form-control" value="<?= $edit_requirement ? $edit_requirement->DISPLAY_ORDER : '0' ?>" required>
                        <small class="text-muted">Lower numbers appear first</small>
                    </div>
                    
                    <button type="submit" name="<?= $edit_requirement ? 'edit_requirement' : 'add_requirement' ?>" class="btn btn-primary">
                        <i class="fa fa-save"></i> <?= $edit_requirement ? 'Update' : 'Save' ?>
                    </button>
                    
                    <?php if ($edit_requirement): ?>
                        <a href="index.php?view=manage_req" class="btn btn-default">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Requirements List -->
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list"></i> Requirements List
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="requirements-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Category</th>
                                <th>Requirement</th>
                                <th>Description</th>
                                <th>Required</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requirements as $req): ?>
                            <tr>
                                <td><?= $req->DISPLAY_ORDER ?></td>
                                <td><span class="label label-info"><?= $req->CATEGORY ?></span></td>
                                <td><strong><?= htmlspecialchars($req->REQUIREMENT_NAME) ?></strong></td>
                                <td><?= htmlspecialchars($req->DESCRIPTION) ?></td>
                                <td>
                                    <?php if ($req->REQUIRED == 'Yes'): ?>
                                        <span class="label label-danger">Required</span>
                                    <?php else: ?>
                                        <span class="label label-default">Optional</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="index.php?view=manage_req&edit=<?= $req->REQUIREMENT_ID ?>" class="btn btn-primary btn-xs">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <a href="index.php?view=manage_req&delete=<?= $req->REQUIREMENT_ID ?>" 
                                       class="btn btn-danger btn-xs" 
                                       onclick="return confirm('Are you sure you want to delete this requirement?')">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($requirements)): ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="alert alert-info" style="margin: 20px;">
                                        <i class="fa fa-info-circle"></i> No requirements added yet. Use the form to add your first requirement.
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#requirements-table').DataTable({
        "pageLength": 25,
        "order": [[0, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": [5] }
        ]
    });
});
</script>