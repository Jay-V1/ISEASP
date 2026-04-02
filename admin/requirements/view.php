<?php
if (!isset($_SESSION['ADMIN_USERID'])) redirect(web_root . "admin/index.php");

$id = $_GET['id'] ?? '';
if ($id=='') redirect("index.php");

$requirements = [
    1=>['name'=>'Birth Certificate','desc'=>'PSA-issued copy required','program'=>'All Programs','status'=>'Active'],
    2=>['name'=>'School Transcript of Records','desc'=>'Latest academic transcript','program'=>'Merit-Based & Educational Grants','status'=>'Active'],
];

$res = $requirements[$id] ?? null;
if (!$res) redirect("index.php");
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">View Requirement</h1>
    </div>
</div>

<div class="panel panel-default col-md-8">
    <div class="panel-body">
        <p><strong>Requirement Name:</strong> <?php echo $res['name']; ?></p>
        <p><strong>Description:</strong> <?php echo $res['desc']; ?></p>
        <p><strong>Required For:</strong> <?php echo $res['program']; ?></p>
        <p><strong>Status:</strong> <span class="label label-info"><?php echo $res['status']; ?></span></p>
        <a href="index.php" class="btn btn-secondary btn-sm"><span class="fa fa-arrow-left"></span> Back</a>
    </div>
</div>
