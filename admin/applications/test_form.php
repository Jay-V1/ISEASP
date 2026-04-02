<?php
require_once("../../include/initialize.php");

if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/login.php");
}

global $mydb;
?>

<h2>Simple Test Form</h2>

<form method="POST" action="test_insert.php">
    <input type="text" name="FIRSTNAME" placeholder="First Name" required><br>
    <input type="text" name="LASTNAME" placeholder="Last Name" required><br>
    <input type="text" name="COURSE" placeholder="Course" required><br>
    <input type="text" name="SCHOOL" placeholder="School" required><br>
    <select name="YEARLEVEL" required>
        <option>1st Year</option>
        <option>2nd Year</option>
        <option>3rd Year</option>
        <option>4th Year</option>
    </select><br>
    <input type="text" name="CONTACT" placeholder="Contact" required><br>
    <input type="email" name="EMAIL" placeholder="Email" required><br>
    <select name="DISTRICT" required>
        <option>1st District</option>
        <option>2nd District</option>
    </select><br>
    <select name="MUNICIPALITY" required>
        <?php
        $mydb->setQuery("SELECT * FROM tbl_municipalities WHERE IS_ACTIVE = 'Yes'");
        $mydb->executeQuery();
        $municipalities = $mydb->loadResultList();
        foreach($municipalities as $town):
        ?>
        <option value="<?= $town->MUNICIPALITY_NAME ?>"><?= $town->MUNICIPALITY_NAME ?></option>
        <?php endforeach; ?>
    </select><br>
    <button type="submit">Submit</button>
</form>