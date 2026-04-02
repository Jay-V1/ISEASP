<?php
require_once("./include/initialize.php");

if (isset($_POST['saveVacancy'])) {
  $company_id = $_POST['company_id'];
  $occupation_title = $_POST['occupation_title'];
  $category = $_POST['category'];
  $required_employees = $_POST['required_employees'];
  $salaries = $_POST['salaries'];
  $duration = $_POST['duration_employment'];
  $qualification = $_POST['qualification'];
  $job_description = $_POST['job_description'];
  $preferred_sex = $_POST['preferred_sex'];
  $sector = $_POST['sector'];
  $date_posted = $_POST['date_posted'];
  $closing_date = $_POST['closing_date'];
  $job_status = $_POST['job_status'];

  // Server-side date validation
  if (strtotime($closing_date) <= strtotime($date_posted)) {
    echo "<script>
            alert('Closing date must be later than the date posted.');
            window.history.back();
          </script>";
    exit();
  }

  $sql = "INSERT INTO tbljob (
            COMPANYID, CATEGORY, OCCUPATIONTITLE, REQ_NO_EMPLOYEES, SALARIES, 
            DURATION_EMPLOYEMENT, QUALIFICATION_WORKEXPERIENCE, JOBDESCRIPTION, 
            PREFEREDSEX, SECTOR_VACANCY, JOBSTATUS, DATEPOSTED, CLOSINGDATE
          ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param(
    "issidssssssss",
    $company_id, $category, $occupation_title, $required_employees,
    $salaries, $duration, $qualification, $job_description,
    $preferred_sex, $sector, $job_status, $date_posted, $closing_date
  );

  if ($stmt->execute()) {
    header("Location: index.php?view=vacancies&msg=added");
    exit();
  } else {
    echo "Error: " . $stmt->error;
  }
}
?>
