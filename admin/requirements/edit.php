<?php
if (!isset($_SESSION['ADMIN_USERID'])) redirect(web_root . "admin/index.php");

$id = $_GET['id'] ?? '';
if ($id=='') redirect("index.php");

$requirements = [
    1=>['name'=>'Birth Certificate','desc'=>'PSA-issued copy required','program'=>'All Programs','status'=>'Active'],
];

$res = $requirements[$id] ?? ['name'=>'','desc'=>'','program'=>'','status'=>'Active'];
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Edit Requirement</h1>
    </div>
</div>

<form class="form-horizontal span6" action="controller.php?action=edit" method="POST">
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label">Requirement Name:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" name="NAME" value="<?php echo $res['name']; ?>" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label">Description:</label>
            <div class="col-md-8">
                <textarea class="form-control input-sm" name="DESC" required><?php echo $res['desc']; ?></textarea>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label">Required For:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" name="PROGRAM" value="<?php echo $res['program']; ?>" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label">Status:</label>
            <div class="col-md-8">
                <select class="form-control input-sm" name="STATUS">
                    <option <?php if($res['status']=='Active') echo 'selected'; ?>>Active</option>
                    <option <?php if($res['status']=='Inactive') echo 'selected'; ?>>Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <div class="col-md-offset-4 col-md-8">
                <button class="btn btn-primary btn-sm" type="submit"><span class="fa fa-save"></span> Update</button>
                <a href="index.php" class="btn btn-secondary btn-sm"><span class="fa fa-arrow-left"></span> Cancel</a>
            </div>
        </div>
    </div>
</form>
