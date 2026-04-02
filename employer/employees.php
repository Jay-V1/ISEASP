<?php
require_once("./include/initialize.php");

if(!isset($_SESSION['EMPLOYER_ID'])){
    redirect(web_root."employer/login.php");
}

$employer_id = $_SESSION['EMPLOYER_ID'];

$sql = "SELECT DISTINCT a.`APPLICANTID`, a.`FNAME`, a.`LNAME`, a.`MNAME`, a.`ADDRESS`, a.`SEX`, a.`CIVILSTATUS`, 
               a.`BIRTHDATE`, a.`BIRTHPLACE`, a.`AGE`, a.`USERNAME`, a.`EMAILADDRESS`, a.`CONTACTNO`, 
               a.`DEGREE`, a.`APPLICANTPHOTO`, a.`NATIONALID`
        FROM `tblapplicants` a
        JOIN `tbljobregistration` jr ON a.`APPLICANTID` = jr.`APPLICANTID`
        JOIN `tbljob` j ON jr.`JOBID` = j.`JOBID`
        WHERE j.`COMPANYID` = '$employer_id' and jr.`IS_ACCEPTED` = 'yes'";

$result = $conn->query($sql);
$hasEmployees = ($result && $result->num_rows > 0);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container py-4">
  <h2 class="mb-4">Our Employees</h2>

  <?php if (!$hasEmployees): ?>
    <div class="text-center my-5">
      <img src="assets/img/empty-employees.svg" alt="No Employees" style="max-width: 250px;" class="mb-4">
      <h3>No employees found</h3>
      <p class="text-muted">You haven’t accepted any applicants as employees yet.</p>
      <a href="index.php?view=applicants" class="btn btn-primary">View Applicants to Add Employees</a>
    </div>
  <?php else: ?>
    <div class="row g-4">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card h-100 shadow-sm cursor-pointer" onclick="location.href='index.php?view=view_employee&id=<?php echo $row['APPLICANTID']; ?>'">
            <img src="<?php echo '../applicant/' . $row['APPLICANTPHOTO']; ?>" class="card-img-top rounded-circle mx-auto mt-3" alt="Photo" style="width: 120px; height: 120px; object-fit: cover;">
            <div class="card-body text-center">
              <h5 class="card-title mb-1"><?php echo htmlspecialchars($row['FNAME'] . ' ' . $row['MNAME'] . ' ' . $row['LNAME']); ?></h5>
              <p class="text-muted mb-1"><?php echo htmlspecialchars($row['DEGREE']); ?></p>
              <p class="mb-2"><small><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($row['CONTACTNO']); ?></small></p>
              <a href="index.php?view=view_employee&id=<?php echo $row['APPLICANTID']; ?>" class="btn btn-primary btn-sm">View Details</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Bootstrap icons and JS (for the telephone icon and responsive behavior) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
  .cursor-pointer { cursor: pointer; }
  .card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
    transform: translateY(-5px);
    transition: all 0.3s ease;
  }
</style>
