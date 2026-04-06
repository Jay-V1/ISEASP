<?php
require_once("../../include/initialize.php");

if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

global $mydb;

// Get all active scholars
$mydb->setQuery("SELECT sa.AWARD_ID, a.APPLICANTID, a.FIRSTNAME, a.LASTNAME, a.YEARLEVEL 
                 FROM tbl_scholarship_awards sa 
                 INNER JOIN tbl_applicants a ON sa.APPLICANTID = a.APPLICANTID 
                 WHERE sa.STATUS = 'Active'");
$mydb->executeQuery();
$scholars = $mydb->loadResultList();
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Test Graduation</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <i class="fa fa-bug"></i> Debug Graduation
            </div>
            <div class="panel-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Select Scholar:</label>
                        <select name="award_id" class="form-control" required>
                            <option value="">-- Select Scholar --</option>
                            <?php foreach($scholars as $s): ?>
                            <option value="<?= $s->AWARD_ID ?>">
                                <?= $s->LASTNAME . ', ' . $s->FIRSTNAME ?> (Year: <?= $s->YEARLEVEL ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Final GPA (%):</label>
                        <input type="number" name="final_gpa" class="form-control" step="0.01" value="89.00" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Honors:</label>
                        <select name="honors" class="form-control">
                            <option value="">-- No Honors --</option>
                            <option value="Cum Laude">Cum Laude</option>
                            <option value="Magna Cum Laude">Magna Cum Laude</option>
                            <option value="Summa Cum Laude">Summa Cum Laude</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="test_graduate" class="btn btn-danger">Test Graduate</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
if(isset($_POST['test_graduate'])) {
    $award_id = intval($_POST['award_id']);
    $final_gpa = floatval($_POST['final_gpa']);
    $honors = $_POST['honors'];
    
    echo '<div class="row"><div class="col-md-12"><div class="panel panel-info">';
    echo '<div class="panel-heading">Results:</div>';
    echo '<div class="panel-body">';
    
    // Get applicant ID
    $mydb->setQuery("SELECT APPLICANTID FROM tbl_scholarship_awards WHERE AWARD_ID = $award_id");
    $mydb->executeQuery();
    $result = $mydb->loadSingleResult();
    $applicant_id = $result->APPLICANTID;
    
    // Update scholarship award
    $sql = "UPDATE tbl_scholarship_awards SET STATUS = 'Graduated' WHERE AWARD_ID = $award_id";
    $mydb->setQuery($sql);
    if($mydb->executeQuery()) {
        echo "<p>✓ Updated tbl_scholarship_awards STATUS to 'Graduated'</p>";
    } else {
        echo "<p>✗ Failed to update tbl_scholarship_awards</p>";
    }
    
    // Update applicant
    $sql2 = "UPDATE tbl_applicants SET STATUS = 'Graduated', GPA = $final_gpa WHERE APPLICANTID = $applicant_id";
    $mydb->setQuery($sql2);
    if($mydb->executeQuery()) {
        echo "<p>✓ Updated tbl_applicants STATUS to 'Graduated'</p>";
    } else {
        echo "<p>✗ Failed to update tbl_applicants</p>";
    }
    
    // Verify the updates
    $mydb->setQuery("SELECT STATUS FROM tbl_scholarship_awards WHERE AWARD_ID = $award_id");
    $mydb->executeQuery();
    $check1 = $mydb->loadSingleResult();
    echo "<p><strong>Current tbl_scholarship_awards STATUS:</strong> " . ($check1 ? $check1->STATUS : 'Not found') . "</p>";
    
    $mydb->setQuery("SELECT STATUS FROM tbl_applicants WHERE APPLICANTID = $applicant_id");
    $mydb->executeQuery();
    $check2 = $mydb->loadSingleResult();
    echo "<p><strong>Current tbl_applicants STATUS:</strong> " . ($check2 ? $check2->STATUS : 'Not found') . "</p>";
    
    echo '<a href="index.php?view=graduates" class="btn btn-success">Go to Graduates List</a>';
    echo '</div></div></div>';
}
?>