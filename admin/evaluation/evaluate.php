<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
	redirect(web_root . "admin/index.php");
}

$id = $_GET['id'] ?? 0;
if (!$id) redirect("../applications/index.php");

$mydb->setQuery("
	SELECT 
		APPLICANTID,
		FIRSTNAME,
		MIDDLENAME,
		LASTNAME,
		SCHOOL,
		COURSE,
		YEARLEVEL,
		STATUS
	FROM tbl_applicants
	WHERE APPLICANTID = '{$id}'
");

$applicant = $mydb->loadSingleResult();
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Evaluate Applicant</h1>
	</div>
</div>

<form method="POST" action="controller.php?action=save">

<input type="hidden" name="APPLICANTID" value="<?= $applicant->APPLICANTID ?>">

<div class="panel panel-default">
<div class="panel-body">

<h4><b>
<?= $applicant->FIRSTNAME . " " . $applicant->MIDDLENAME . " " . $applicant->LASTNAME ?>
</b></h4>

<p><b>School:</b> <?= $applicant->SCHOOL ?></p>
<p><b>Course:</b> <?= $applicant->COURSE ?></p>
<p><b>Year Level:</b> <?= $applicant->YEARLEVEL ?></p>

<hr>

<div class="form-group">
<label>Final Evaluation Status</label>
<select name="FINAL_STATUS" class="form-control" required>
	<option value="">Select Status</option>
	<option>For Verification</option>
	<option>Pending Requirement</option>
	<option>For Interview</option>
	<option>Qualified</option>
	<option>Not Qualified</option>
</select>
</div>

<div class="form-group">
<label>Evaluator Feedback</label>
<textarea name="FEEDBACK" class="form-control" rows="4"></textarea>
</div>

<button type="submit" class="btn btn-success">
	Save Evaluation
</button>

<a href="../applications/index.php" class="btn btn-default">
	Back
</a>

</div>
</div>

</form>
