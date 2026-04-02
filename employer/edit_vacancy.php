<?php
require_once("./include/initialize.php");

if (!isset($_SESSION['EMPLOYER_ID'])) {
  redirect(web_root . "employer/login.php");
}

$jobID = $_GET['id'] ?? null;

$sql = "SELECT * FROM tbljob WHERE JOBID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $jobID);
$stmt->execute();
$job = $stmt->get_result()->fetch_assoc();
?>

<div class="container py-4">
  <h3 class="mb-4 text-primary"><i class="bi bi-pencil-square me-2"></i>Edit Vacancy</h3>
  <form action="process_edit_vacancy.php" method="POST">
    <input type="hidden" name="job_id" value="<?php echo $job['JOBID']; ?>">

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Occupation Title</label>
        <input type="text" name="occupation_title" class="form-control" value="<?php echo $job['OCCUPATIONTITLE']; ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control" value="<?php echo $job['CATEGORY']; ?>" required>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Required No. of Employees</label>
        <input type="number" name="required_employees" class="form-control" value="<?php echo $job['REQ_NO_EMPLOYEES']; ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Salaries (₱)</label>
        <input type="text" name="salaries" class="form-control" value="<?php echo $job['SALARIES']; ?>" required>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Duration of Employment</label>
      <input type="text" name="duration" class="form-control" value="<?php echo $job['DURATION_EMPLOYEMENT']; ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Qualification / Work Experience</label>
      <textarea name="qualification" class="form-control" rows="2" required><?php echo $job['QUALIFICATION_WORKEXPERIENCE']; ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Job Description</label>
      <textarea name="job_description" class="form-control" rows="3" required><?php echo $job['JOBDESCRIPTION']; ?></textarea>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Preferred Sex</label>
        <select name="preferred_sex" class="form-select" required>
          <option value="Male" <?php echo $job['PREFEREDSEX'] == 'Male' ? 'selected' : ''; ?>>Male</option>
          <option value="Female" <?php echo $job['PREFEREDSEX'] == 'Female' ? 'selected' : ''; ?>>Female</option>
          <option value="Any" <?php echo $job['PREFEREDSEX'] == 'Any' ? 'selected' : ''; ?>>Any</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Sector Vacancy</label>
        <input type="text" name="sector" class="form-control" value="<?php echo $job['SECTOR_VACANCY']; ?>" required>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Job Status</label>
        <select name="job_status" class="form-select" required>
          <option value="Open" <?php echo $job['JOBSTATUS'] == 'Open' ? 'selected' : ''; ?>>Open</option>
          <option value="Closed" <?php echo $job['JOBSTATUS'] == 'Closed' ? 'selected' : ''; ?>>Closed</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Date Posted</label>
        <input type="date" name="date_posted" class="form-control" 
              value="<?php echo date('Y-m-d', strtotime($job['DATEPOSTED'])); ?>" 
              readonly required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Closing Date</label>
        <input type="date" name="closing_date" class="form-control" value="<?php echo $job['CLOSINGDATE']; ?>" required>
      </div>
    </div>

    <button type="submit" name="updateVacancy" class="btn btn-info"><i class="bi bi-save2 me-1"></i> Update Vacancy</button>
    <a href="index.php?view=vacancies" class="btn btn-secondary ms-2">Cancel</a>
  </form>
</div>
