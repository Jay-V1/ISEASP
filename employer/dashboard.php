<?php
require_once("./include/initialize.php");

if (!isset($_SESSION['EMPLOYER_ID'])) {
    redirect(web_root . "employer/login.php");
}

$employerId = $_SESSION['EMPLOYER_ID'];

// Vacancies
$vacancyQuery = $conn->prepare("SELECT COUNT(*) as total FROM tbljob WHERE COMPANYID = ?");
$vacancyQuery->bind_param("i", $employerId);
$vacancyQuery->execute();
$totalVacancies = $vacancyQuery->get_result()->fetch_assoc()['total'];

// Applicants
$applicantQuery = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM tbljobregistration jr
    JOIN tbljob j ON jr.JOBID = j.JOBID
    WHERE j.COMPANYID = ?
");
$applicantQuery->bind_param("i", $employerId);
$applicantQuery->execute();
$totalApplicants = $applicantQuery->get_result()->fetch_assoc()['total'];

// Hired applicants (IS_ACCEPTED = 'yes')
$hiredQuery = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM tbljobregistration jr
    JOIN tbljob j ON jr.JOBID = j.JOBID
    WHERE j.COMPANYID = ? AND jr.IS_ACCEPTED = 'yes'
");
$hiredQuery->bind_param("i", $employerId);
$hiredQuery->execute();
$totalHired = $hiredQuery->get_result()->fetch_assoc()['total'];
?>

<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="card text-bg-success shadow">
      <div class="card-body">
        <h5 class="card-title">Vacancies</h5>
        <h3><?php echo $totalVacancies; ?></h3>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-bg-warning shadow">
      <div class="card-body">
        <h5 class="card-title">Applicants</h5>
        <h3><?php echo $totalApplicants; ?></h3>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-bg-danger shadow">
      <div class="card-body">
        <h5 class="card-title">Hired</h5>
        <h3><?php echo $totalHired; ?></h3>
      </div>
    </div>
  </div>
</div>
