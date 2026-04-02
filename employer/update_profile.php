<?php
require_once('./include/initialize.php');

if (isset($_POST['updateProfile'])) {
  $id = $_POST['employer_id'];
  $company = trim($_POST['companyname']);
  $contact = trim($_POST['contactperson']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $address = trim($_POST['address']);

  $logo = '';

  // Upload logo if provided
  if (!empty($_FILES['logo']['name'])) {
    $targetDir = "./uploads/";
    if (!is_dir($targetDir)) {
      mkdir($targetDir, 0777, true);
    }
    $logo = $targetDir . basename($_FILES['logo']['name']);
    move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
  }

  // Prepare SQL
  if ($logo != '') {
    $sql = "UPDATE tblemployers SET COMPANYNAME=?, CONTACTPERSON=?, EMAIL=?, PHONE=?, ADDRESS=?, LOGO=? WHERE EMPLOYERID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $company, $contact, $email, $phone, $address, $logo, $id);
  } else {
    $sql = "UPDATE tblemployers SET COMPANYNAME=?, CONTACTPERSON=?, EMAIL=?, PHONE=?, ADDRESS=? WHERE EMPLOYERID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $company, $contact, $email, $phone, $address, $id);
  }

  if ($stmt->execute()) {
    header("Location: index.php?success=1");
  } else {
    echo "Update failed: " . $conn->error;
  }
}
?>
