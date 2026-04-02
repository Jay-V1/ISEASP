<?php
require_once("./include/initialize.php");

if (!isset($_SESSION['EMPLOYER_ID'])) {
  redirect(web_root . "employer/login.php");
}

if (isset($_POST['updateVacancy'])) {
  $jobID = $_POST['job_id'];
  $occupation = $_POST['occupation_title'];
  $category = $_POST['category'];
  $required_employees = $_POST['required_employees'];
  $salaries = $_POST['salaries'];
  $duration = $_POST['duration'];
  $qualification = $_POST['qualification'];
  $description = $_POST['job_description'];
  $preferred_sex = $_POST['preferred_sex'];
  $sector = $_POST['sector'];
  $job_status = $_POST['job_status'];
  $date_posted = $_POST['date_posted'];
  $closing_date = $_POST['closing_date'];

  // Prepare the SQL update statement
  $sql = "UPDATE tbljob SET 
            OCCUPATIONTITLE = ?, 
            CATEGORY = ?, 
            REQ_NO_EMPLOYEES = ?, 
            SALARIES = ?, 
            DURATION_EMPLOYEMENT = ?, 
            QUALIFICATION_WORKEXPERIENCE = ?, 
            JOBDESCRIPTION = ?, 
            PREFEREDSEX = ?, 
            SECTOR_VACANCY = ?, 
            JOBSTATUS = ?, 
            DATEPOSTED = ?, 
            CLOSINGDATE = ?
          WHERE JOBID = ?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssisssssssssi", 
    $occupation, 
    $category, 
    $required_employees, 
    $salaries, 
    $duration, 
    $qualification, 
    $description, 
    $preferred_sex, 
    $sector, 
    $job_status, 
    $date_posted, 
    $closing_date,
    $jobID
  );

  if ($stmt->execute()) {
    $_SESSION['message'] = "Vacancy updated successfully!";
    $_SESSION['msg_type'] = "success";
  } else {
    $_SESSION['message'] = "Error updating vacancy. Please try again.";
    $_SESSION['msg_type'] = "danger";
  }

  redirect(web_root . "employer/index.php?view=vacancies");
} else {
  redirect(web_root . "employer/index.php?view=vacancies");
}
