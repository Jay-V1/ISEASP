<?php
require_once("./include/initialize.php");

$applicant_id = $_GET['applicant_id'] ?? 0;
$sql = "SELECT f.FEEDBACK, f.CREATED_AT, e.COMPANYNAME
        FROM tbl_applicant_feedback f
        JOIN tblemployers e ON f.EMPLOYERID = e.EMPLOYERID
        WHERE f.APPLICANTID = '$applicant_id'
        ORDER BY f.CREATED_AT DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<li class="list-group-item">';
        echo '<strong>' . htmlspecialchars($row['COMPANYNAME']) . '</strong>: ';
        echo htmlspecialchars($row['FEEDBACK']) . '<br>';
        echo '<small class="text-muted">' . $row['CREATED_AT'] . '</small>';
        echo '</li>';
    }
} else {
    echo '<li class="list-group-item text-muted">No feedback yet.</li>';
}
?>
