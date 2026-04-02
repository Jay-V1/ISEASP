<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) redirect("index.php");

global $mydb;

// Get applicant details
$mydb->setQuery("SELECT * FROM tbl_applicants WHERE APPLICANTID = $id");
$applicant = $mydb->loadSingleResult();

if (!$applicant) {
    message("Applicant not found!", "error");
    redirect("index.php");
}

// Handle conversion
if (isset($_POST['convert'])) {
    $amount = $_POST['amount'];
    $school_year = $_POST['school_year'];
    $semester = $_POST['semester'];
    $remarks = $_POST['remarks'];
    
    // Get current user info for logging
    $user_id = $_SESSION['ADMIN_USERID'];
    $mydb->setQuery("SELECT USERNAME, ROLE FROM tblusers WHERE USERID = $user_id");
    $mydb->executeQuery();
    $user = $mydb->loadSingleResult();
    
    // Update applicant status to Scholar
    $sql = "UPDATE tbl_applicants SET STATUS = 'Scholar' WHERE APPLICANTID = $id";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    
    // Insert into scholarship awards
    $award_sql = "INSERT INTO tbl_scholarship_awards 
                  (APPLICANTID, SCHOOL_YEAR, SEMESTER, AWARD_DATE, AWARDED_BY, AMOUNT, STATUS, REMARKS)
                  VALUES ($id, '$school_year', '$semester', NOW(), $user_id, '$amount', 'Active', '$remarks')";
    $mydb->setQuery($award_sql);
    $mydb->executeQuery();
    
    // Insert into scholarship history
    $history_sql = "INSERT INTO tbl_scholarship_history 
                    (APPLICANTID, SCHOOL_YEAR, SEMESTER, STATUS, GPA, REMARKS, UPDATED_BY)
                    VALUES ($id, '$school_year', '$semester', 'Awarded', '" . $applicant->GPA . "', 'Converted to scholar', $user_id)";
    $mydb->setQuery($history_sql);
    $mydb->executeQuery();
    
    // FIXED: Log the action with correct columns
    $log_sql = "INSERT INTO tbl_application_log 
                (APPLICANTID, USERID, USERNAME, USER_ROLE, ACTION, ACTION_TYPE, DETAILS)
                VALUES ($id, $user_id, '{$user->USERNAME}', '{$user->ROLE}', 
                        'Converted to Scholar', 'SCHOLAR', 
                        'Converted to scholar with amount: ₱$amount for $school_year $semester')";
    $mydb->setQuery($log_sql);
    $mydb->executeQuery();
    
    message("Applicant successfully converted to scholar!", "success");
    redirect("index.php?view=qualified");
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Convert to Scholar</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-success">
            <div class="panel-heading">
                <i class="fa fa-graduation-cap"></i> Convert Applicant to Scholar
            </div>
            <div class="panel-body">
                <div class="well">
                    <h4>Applicant Information</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Full Name:</th>
                            <td><?= htmlspecialchars($applicant->LASTNAME . ', ' . $applicant->FIRSTNAME . ' ' . ($applicant->MIDDLENAME ?? '')) ?></td>
                        </tr>
                        <tr>
                            <th>Municipality:</th>
                            <td><?= htmlspecialchars($applicant->MUNICIPALITY ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <th>School:</th>
                            <td><?= htmlspecialchars($applicant->SCHOOL) ?></td>
                        </tr>
                        <tr>
                            <th>Course:</th>
                            <td><?= htmlspecialchars($applicant->COURSE) ?></td>
                        </tr>
                        <tr>
                            <th>Year Level:</th>
                            <td><?= htmlspecialchars($applicant->YEARLEVEL) ?></td>
                        </tr>
                        <?php if($applicant->IS_4PS_BENEFICIARY == 'Yes'): ?>
                        <tr>
                            <th>4Ps Beneficiary:</th>
                            <td><span class="label label-success">Yes</span></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($applicant->IS_INDIGENOUS == 'Yes'): ?>
                        <tr>
                            <th>Indigenous People:</th>
                            <td><span class="label label-info">Yes</span></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label>School Year:</label>
                        <select name="school_year" class="form-control" required>
                            <option value="2024-2025">2024-2025</option>
                            <option value="2025-2026" selected>2025-2026</option>
                            <option value="2026-2027">2026-2027</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Semester:</label>
                        <select name="semester" class="form-control" required>
                            <option value="1st Semester">1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                            <option value="Summer">Summer</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Award Amount (₱):</label>
                        <input type="number" name="amount" class="form-control" value="10000.00" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Remarks / Notes:</label>
                        <textarea name="remarks" class="form-control" rows="3">Qualified scholar for the school year</textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" name="convert" class="btn btn-success" onclick="return confirm('Convert this applicant to scholar? This action cannot be undone.')">
                            <i class="fa fa-graduation-cap"></i> Confirm Conversion
                        </button>
                        <a href="index.php?view=qualified" class="btn btn-default">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>