<?php 
if (!isset($_SESSION['ADMIN_USERID'])) {
    // disabled redirect for demo
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Applicant Details</h1>

    </div>
</div>

<?php
// ---------- STATIC APPLICANT INFORMATION (PLACEHOLDER) ----------
$applicant = [
    "name" => "Juan Dela Cruz",
    "address" => "Brgy. San Jose, Vigan City",
    "contact" => "09123456789",
    "email" => "juan.dc@email.com",
    "program" => "Merit-Based Scholarship",
    "school" => "Ilocos Sur Polytechnic State College",
    "year" => "3rd Year",
    "birthdate" => "March 15, 2005",
    "guardian" => "Maria Dela Cruz",
    "guardian_contact" => "09991234567",
    "status" => "Pending"
];

// Status badge color
$statusColor = [
    "Pending" => "label-warning",
    "For Verification" => "label-info",
    "Qualified" => "label-success",
    "Not Qualified" => "label-danger"
][$applicant['status']] ?? "label-default";

// ---------- STATIC DOCUMENTS ----------
$documents = [
    ["title" => "Birth Certificate", "file" => "birth.pdf"],
    ["title" => "Report Card / TOR", "file" => "grades.pdf"],
    ["title" => "Certificate of Residency", "file" => "residency.pdf"],
    ["title" => "Barangay Clearance", "file" => "brgy.pdf"],
    ["title" => "Good Moral Certificate", "file" => "moral.pdf"],
];
?>

<!-- Applicant Personal Info -->
<div class="panel panel-primary">
    <div class="panel-heading">Personal Information</div>
    <div class="panel-body">

        <div class="row">
            <div class="col-md-6">
                <p><strong>Name:</strong> <?= $applicant['name']; ?></p>
                <p><strong>Address:</strong> <?= $applicant['address']; ?></p>
                <p><strong>Birthdate:</strong> <?= $applicant['birthdate']; ?></p>
                <p><strong>Contact:</strong> <?= $applicant['contact']; ?></p>
            </div>

            <div class="col-md-6">
                <p><strong>Email:</strong> <?= $applicant['email']; ?></p>
                <p><strong>Applied Program:</strong> <?= $applicant['program']; ?></p>
                <p><strong>School:</strong> <?= $applicant['school']; ?> (<?= $applicant['year']; ?>)</p>
                <p><strong>Status:</strong> 
                    <span class="label <?= $statusColor; ?>"><?= $applicant['status']; ?></span>
                </p>
            </div>
        </div>

    </div>
</div>

<!-- Guardian Information -->
<div class="panel panel-info">
    <div class="panel-heading">Parent / Guardian Information</div>
    <div class="panel-body">
        <p><strong>Guardian Name:</strong> <?= $applicant['guardian']; ?></p>
        <p><strong>Contact Number:</strong> <?= $applicant['guardian_contact']; ?></p>
    </div>
</div>

<!-- Submitted Documents -->
<div class="panel panel-success">
    <div class="panel-heading">Submitted Documents</div>
    <div class="panel-body">

        <table class="table table-bordered table-striped">
            <thead style="background:#3c8dbc; color:white;">
                <tr>
                    <th>Document</th>
                    <th width="15%">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
                <tr>
                    <td><?= $doc['title']; ?></td>
                    <td>
                        <a href="#" class="btn btn-info btn-xs">
                            <i class="fa fa-file"></i> View
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

<!-- Buttons -->
<div class="form-group">
    <a href="index.php?view=evaluation&id=1" class="btn btn-primary btn-lg">
        <i class="fa fa-search"></i> Evaluate Applicant
    </a>

    <a href="index.php?view=list" class="btn btn-default btn-lg">
        <i class="fa fa-arrow-left"></i> Return to List
    </a>
</div>
