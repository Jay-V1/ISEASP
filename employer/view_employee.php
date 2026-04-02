<?php
require_once("./include/initialize.php");

if (!isset($_SESSION['EMPLOYER_ID'])) {
    redirect(web_root . "employer/login.php");
}

if (!isset($_GET['id'])) {
    redirect("index.php");
}

$employeeId = $_GET['id'];

// Fetch employee information
$sql = "SELECT a.*, f.*, jr.* 
        FROM tblapplicants a
        JOIN tblattachmentfile f ON a.APPLICANTID = f.USERATTACHMENTID
        JOIN tbljobregistration jr ON a.APPLICANTID = jr.APPLICANTID
        WHERE a.APPLICANTID = '$employeeId'";
$result = $conn->query($sql);

if ($result->num_rows != 1) {
    echo "<div class='container mt-5 alert alert-danger'>Employee not found.</div>";
    exit;
}

$row = $result->fetch_assoc();
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">
  <h2 class="fw-bold text-primary mb-4">👤 Employee Profile</h2>

  <div class="card shadow-lg border-0 rounded-4 mb-4 p-4" id="profile">
    <div class="row g-4">
      <div class="col-md-3 text-center">
        <img src="<?php echo '../applicant/' . $row['APPLICANTPHOTO']; ?>" class="rounded-circle shadow" alt="Applicant Photo" width="160" height="160">
      </div>
      <div class="col-md-9">
        <h4 class="mb-3"><?php echo $row['FNAME'] . ' ' . $row['MNAME'] . ' ' . $row['LNAME']; ?></h4>
        <div class="row">
          <div class="col-md-6">
            <p><strong>Degree:</strong> <?php echo $row['DEGREE']; ?></p>
            <p><strong>National ID:</strong> <?php echo $row['NATIONALID']; ?></p>
            <p><strong>Email:</strong> <?php echo $row['EMAILADDRESS']; ?></p>
            <p><strong>Contact:</strong> <?php echo $row['CONTACTNO']; ?></p>
            <p><strong>Address:</strong> <?php echo $row['ADDRESS']; ?></p>
            <p><strong>Date Hired:</strong> <?php echo substr($row['DATETIMEAPPROVED'], 0, 10); ?></p>
          </div>
          <div class="col-md-6">
            <p><strong>Sex:</strong> <?php echo $row['SEX']; ?></p>
            <p><strong>Civil Status:</strong> <?php echo $row['CIVILSTATUS']; ?></p>
            <p><strong>Birthdate:</strong> <?php echo $row['BIRTHDATE']; ?></p>
            <p><strong>Birthplace:</strong> <?php echo $row['BIRTHPLACE']; ?></p>
            <p><strong>Age:</strong> <?php echo $row['AGE']; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <button onclick="history.back()" class="btn btn-outline-secondary">← Go Back</button>
</div>
