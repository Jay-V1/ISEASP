<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "admin/index.php");
}

$id = $_GET['id'] ?? '';
if ($id == '') redirect("index.php");

// Dummy record for preview
$evaluation = [
  'FULLNAME' => 'Juan Dela Cruz',
  'PROGRAM' => 'Merit-Based Scholarship',
  'SCHOOL' => 'ISPSC',
  'YEARLEVEL' => '3rd Year',
  'GPA' => 85,
  'FINANCIAL' => 90,
  'DOCUMENTS' => 95,
  'OTHERS' => 80,
  'RECOMMENDATION' => 'Pending'
];
?>

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Edit Evaluation</h1>
  </div>
</div>

<form class="form-horizontal span6" action="controller.php?action=edit" method="POST">
  <input type="hidden" name="EVALUATIONID" value="<?php echo $id; ?>">

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label">Full Name:</label>
      <div class="col-md-8">
        <input class="form-control input-sm" name="FULLNAME" value="<?php echo $evaluation['FULLNAME']; ?>" type="text" readonly>
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label">Program Applied:</label>
      <div class="col-md-8">
        <input class="form-control input-sm" name="PROGRAM" value="<?php echo $evaluation['PROGRAM']; ?>" type="text" readonly>
      </div>
    </div>
  </div>

  <hr>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label">Academic Performance:</label>
      <div class="col-md-8">
        <input class="form-control input-sm" name="GPA" value="<?php echo $evaluation['GPA']; ?>" type="number">
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label">Financial Need:</label>
      <div class="col-md-8">
        <input class="form-control input-sm" name="FINANCIAL" value="<?php echo $evaluation['FINANCIAL']; ?>" type="number">
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label">Documents:</label>
      <div class="col-md-8">
        <input class="form-control input-sm" name="DOCUMENTS" value="<?php echo $evaluation['DOCUMENTS']; ?>" type="number">
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label">Other Considerations:</label>
      <div class="col-md-8">
        <input class="form-control input-sm" name="OTHERS" value="<?php echo $evaluation['OTHERS']; ?>" type="number">
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label">Recommendation:</label>
      <div class="col-md-8">
        <select class="form-control input-sm" name="RECOMMENDATION">
          <option value="Pending" <?php if($evaluation['RECOMMENDATION']=='Pending') echo 'selected'; ?>>Pending</option>
          <option value="Approve" <?php if($evaluation['RECOMMENDATION']=='Approve') echo 'selected'; ?>>Approve</option>
          <option value="Reject" <?php if($evaluation['RECOMMENDATION']=='Reject') echo 'selected'; ?>>Reject</option>
        </select>
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label"></label>
      <div class="col-md-8">
        <button class="btn btn-primary btn-sm" name="save" type="submit">
          <span class="fa fa-save fw-fa"></span> Update
        </button>
      </div>
    </div>
  </div>
</form>
