<?php
require_once("../../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

$type = isset($_GET['type']) ? $_GET['type'] : '';
$sy = isset($_GET['sy']) ? $_GET['sy'] : '2025-2026';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$district = isset($_GET['district']) ? $_GET['district'] : '';
$municipality = isset($_GET['municipality']) ? $_GET['municipality'] : '';

// Set headers for Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=iseasp_report_" . $type . "_" . date('Ymd') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

global $mydb;

echo "<html>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<style>";
echo "th { background-color: #27ae60; color: white; font-weight: bold; }";
echo "td { border: 1px solid #ccc; }";
echo "</style>";
echo "</head>";
echo "<body>";

switch ($type) {
    case 'applicants':
        echo "<h2>ISEASP Applicants Report - School Year $sy</h2>";
        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Name</th>";
        echo "<th>Municipality</th>";
        echo "<th>School</th>";
        echo "<th>Course</th>";
        echo "<th>Year</th>";
        echo "<th>Application Type</th>";
        echo "<th>Exam Status</th>";
        echo "<th>Application Status</th>";
        echo "<th>Date Applied</th>";
        echo "</tr>";
        
        $where = "WHERE SCHOOL_YEAR = '$sy'";
        if (!empty($status)) {
            $where .= " AND STATUS = '$status'";
        }
        
        $sql = "SELECT * FROM tbl_applicants $where ORDER BY LASTNAME ASC";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        $applicants = $mydb->loadResultList();
        
        foreach ($applicants as $a) {
            echo "<tr>";
            echo "<td>" . $a->APPLICANTID . "</td>";
            echo "<td>" . $a->LASTNAME . ', ' . $a->FIRSTNAME . ' ' . $a->MIDDLENAME . "</td>";
            echo "<td>" . ($a->MUNICIPALITY ?? 'N/A') . "</td>";
            echo "<td>" . ($a->SCHOOL ?? 'N/A') . "</td>";
            echo "<td>" . ($a->COURSE ?? 'N/A') . "</td>";
            echo "<td>" . $a->YEARLEVEL . "</td>";
            echo "<td>" . $a->APPLICATION_TYPE . "</td>";
            echo "<td>" . $a->EXAM_STATUS . "</td>";
            echo "<td>" . $a->STATUS . "</td>";
            echo "<td>" . date('Y-m-d', strtotime($a->DATECREATED)) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        break;
        
    case 'scholars':
        echo "<h2>ISEASP Scholars Report - School Year $sy</h2>";
        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>Award ID</th>";
        echo "<th>Name</th>";
        echo "<th>Municipality</th>";
        echo "<th>School</th>";
        echo "<th>Course</th>";
        echo "<th>Year Level</th>";
        echo "<th>School Year</th>";
        echo "<th>Semester</th>";
        echo "<th>Amount</th>";
        echo "<th>Award Date</th>";
        echo "<th>Status</th>";
        echo "</tr>";
        
        $sql = "
            SELECT 
                sa.*,
                a.LASTNAME, a.FIRSTNAME, a.MIDDLENAME,
                a.MUNICIPALITY, a.SCHOOL, a.COURSE, a.YEARLEVEL
            FROM tbl_scholarship_awards sa
            INNER JOIN tbl_applicants a ON sa.APPLICANTID = a.APPLICANTID
            WHERE sa.STATUS = '$status'
            ORDER BY a.LASTNAME ASC
        ";
        
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        $scholars = $mydb->loadResultList();
        
        $total_amount = 0;
        foreach ($scholars as $s) {
            $total_amount += $s->AMOUNT;
            echo "<tr>";
            echo "<td>SCH-" . str_pad($s->AWARD_ID, 5, '0', STR_PAD_LEFT) . "</td>";
            echo "<td>" . $s->LASTNAME . ', ' . $s->FIRSTNAME . ' ' . $s->MIDDLENAME . "</td>";
            echo "<td>" . ($s->MUNICIPALITY ?? 'N/A') . "</td>";
            echo "<td>" . ($s->SCHOOL ?? 'N/A') . "</td>";
            echo "<td>" . ($s->COURSE ?? 'N/A') . "</td>";
            echo "<td>" . $s->YEARLEVEL . "</td>";
            echo "<td>" . $s->SCHOOL_YEAR . "</td>";
            echo "<td>" . $s->SEMESTER . "</td>";
            echo "<td>₱ " . number_format($s->AMOUNT, 2) . "</td>";
            echo "<td>" . date('Y-m-d', strtotime($s->AWARD_DATE)) . "</td>";
            echo "<td>" . $s->STATUS . "</td>";
            echo "</tr>";
        }
        echo "<tr>";
        echo "<td colspan='8' align='right'><strong>TOTAL AMOUNT:</strong></td>";
        echo "<td colspan='3'><strong>₱ " . number_format($total_amount, 2) . "</strong></td>";
        echo "</tr>";
        echo "</table>";
        break;
        
    case 'district':
        echo "<h2>ISEASP District Report - $district - School Year $sy</h2>";
        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>Municipality</th>";
        echo "<th>Applicants</th>";
        echo "<th>Passed Exam</th>";
        echo "<th>Qualified</th>";
        echo "<th>Active Scholars</th>";
        echo "<th>Graduates</th>";
        echo "<th>4Ps</th>";
        echo "<th>IP</th>";
        echo "</tr>";
        
        $sql = "
            SELECT 
                m.MUNICIPALITY_NAME,
                (SELECT COUNT(*) FROM tbl_applicants WHERE MUNICIPALITY = m.MUNICIPALITY_NAME AND SCHOOL_YEAR = '$sy') as applicants,
                (SELECT COUNT(*) FROM tbl_applicants WHERE MUNICIPALITY = m.MUNICIPALITY_NAME AND EXAM_STATUS = 'Passed') as passed,
                (SELECT COUNT(*) FROM tbl_applicants WHERE MUNICIPALITY = m.MUNICIPALITY_NAME AND STATUS = 'Qualified') as qualified,
                (SELECT COUNT(*) FROM tbl_applicants a 
                 INNER JOIN tbl_scholarship_awards sa ON a.APPLICANTID = sa.APPLICANTID 
                 WHERE a.MUNICIPALITY = m.MUNICIPALITY_NAME AND sa.STATUS = 'Active') as scholars,
                (SELECT COUNT(*) FROM tbl_applicants a 
                 INNER JOIN tbl_scholarship_history h ON a.APPLICANTID = h.APPLICANTID 
                 WHERE a.MUNICIPALITY = m.MUNICIPALITY_NAME AND h.STATUS = 'Graduated') as graduates,
                (SELECT COUNT(*) FROM tbl_applicants WHERE MUNICIPALITY = m.MUNICIPALITY_NAME AND IS_4PS_BENEFICIARY = 'Yes') as four_ps,
                (SELECT COUNT(*) FROM tbl_applicants WHERE MUNICIPALITY = m.MUNICIPALITY_NAME AND IS_INDIGENOUS = 'Yes') as ip
            FROM tbl_municipalities m
            WHERE m.DISTRICT = '$district' AND m.IS_ACTIVE = 'Yes'
            ORDER BY m.MUNICIPALITY_NAME
        ";
        
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        $municipalities = $mydb->loadResultList();
        
        $total_apps = 0;
        $total_passed = 0;
        $total_qualified = 0;
        $total_scholars = 0;
        $total_graduates = 0;
        $total_four_ps = 0;
        $total_ip = 0;
        
        foreach ($municipalities as $m) {
            $total_apps += $m->applicants;
            $total_passed += $m->passed;
            $total_qualified += $m->qualified;
            $total_scholars += $m->scholars;
            $total_graduates += $m->graduates;
            $total_four_ps += $m->four_ps;
            $total_ip += $m->ip;
            
            echo "<tr>";
            echo "<td><strong>" . $m->MUNICIPALITY_NAME . "</strong></td>";
            echo "<td>" . $m->applicants . "</td>";
            echo "<td>" . $m->passed . "</td>";
            echo "<td>" . $m->qualified . "</td>";
            echo "<td>" . $m->scholars . "</td>";
            echo "<td>" . $m->graduates . "</td>";
            echo "<td>" . $m->four_ps . "</td>";
            echo "<td>" . $m->ip . "</td>";
            echo "</tr>";
        }
        
        echo "<tr style='font-weight: bold; background-color: #27ae60; color: white;'>";
        echo "<td>TOTAL</td>";
        echo "<td>" . $total_apps . "</td>";
        echo "<td>" . $total_passed . "</td>";
        echo "<td>" . $total_qualified . "</td>";
        echo "<td>" . $total_scholars . "</td>";
        echo "<td>" . $total_graduates . "</td>";
        echo "<td>" . $total_four_ps . "</td>";
        echo "<td>" . $total_ip . "</td>";
        echo "</tr>";
        echo "</table>";
        break;
        
    case 'municipality':
        echo "<h2>ISEASP Municipality Report - $municipality - School Year $sy</h2>";
        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>Name</th>";
        echo "<th>School</th>";
        echo "<th>Course</th>";
        echo "<th>Year</th>";
        echo "<th>Exam Status</th>";
        echo "<th>Application Status</th>";
        echo "<th>4Ps</th>";
        echo "<th>IP</th>";
        echo "</tr>";
        
        $sql = "SELECT * FROM tbl_applicants WHERE MUNICIPALITY = '$municipality' AND SCHOOL_YEAR = '$sy' ORDER BY LASTNAME ASC";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        $applicants = $mydb->loadResultList();
        
        foreach ($applicants as $a) {
            echo "<tr>";
            echo "<td>" . $a->LASTNAME . ', ' . $a->FIRSTNAME . ' ' . $a->MIDDLENAME . "</td>";
            echo "<td>" . ($a->SCHOOL ?? 'N/A') . "</td>";
            echo "<td>" . ($a->COURSE ?? 'N/A') . "</td>";
            echo "<td>" . $a->YEARLEVEL . "</td>";
            echo "<td>" . $a->EXAM_STATUS . "</td>";
            echo "<td>" . $a->STATUS . "</td>";
            echo "<td>" . ($a->IS_4PS_BENEFICIARY == 'Yes' ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($a->IS_INDIGENOUS == 'Yes' ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        break;
}

echo "</body>";
echo "</html>";
exit;
?>