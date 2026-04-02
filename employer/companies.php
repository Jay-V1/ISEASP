<?php
require_once("./include/initialize.php");

// Handle form submission
if (isset($_POST['save_company'])) {
    $companyName = trim($_POST['companyname']);
    $companyAddress = trim($_POST['companyaddress']);
    $companyContact = trim($_POST['companycontact']);
    $companyStatus = trim($_POST['companystatus']);
    $companyMission = trim($_POST['companymission']);

    $stmt = $conn->prepare("INSERT INTO tblcompany (COMPANYNAME, COMPANYADDRESS, COMPANYCONTACTNO, COMPANYSTATUS, COMPANYMISSION) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $companyName, $companyAddress, $companyContact, $companyStatus, $companyMission);
    
    if ($stmt->execute()) {
        $success = "Company added successfully!";
    } else {
        $error = "Failed to add company.";
    }
}

// Fetch all companies
$result = $conn->query("SELECT * FROM tblcompany ORDER BY COMPANYNAME ASC");
?>

<div class="container">
    <h2>Manage Companies</h2>

    <?php if (isset($error)) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } elseif (isset($success)) { ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php } ?>

    <form method="POST" class="card p-4 shadow mb-4">
        <h4 class="mb-3">Add New Company</h4>

        <div class="mb-3">
            <label>Company Name</label>
            <input type="text" name="companyname" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Company Address</label>
            <input type="text" name="companyaddress" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Company Contact No.</label>
            <input type="text" name="companycontact" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Company Status</label>
            <input type="text" name="companystatus" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Company Mission</label>
            <textarea name="companymission" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" name="save_company" class="btn btn-primary">Save Company</button>
    </form>

    <h4 class="mb-3">Existing Companies</h4>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Company Name</th>
                <th>Address</th>
                <th>Contact No.</th>
                <th>Status</th>
                <th>Mission</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['COMPANYNAME']; ?></td>
                <td><?php echo $row['COMPANYADDRESS']; ?></td>
                <td><?php echo $row['COMPANYCONTACTNO']; ?></td>
                <td><?php echo $row['COMPANYSTATUS']; ?></td>
                <td><?php echo $row['COMPANYMISSION']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
