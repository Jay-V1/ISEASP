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

global $mydb;
?>

<!DOCTYPE html>
<html>
<head>
    <title>ISEASP Report - <?= ucfirst($type) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #27ae60;
            text-align: center;
            border-bottom: 2px solid #27ae60;
            padding-bottom: 10px;
        }
        h2 {
            color: #333;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #27ae60;
            color: white;
            padding: 10px;
            text-align: left;
        }
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .summary {
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #27ae60;
        }
        .total-row {
            font-weight: bold;
            background-color: #e8f5e9 !important;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()">Print Report</button>
        <button onclick="window.close()">Close</button>
    </div>
    
    <h1>ILOCOS SUR EDUCATIONAL ASSISTANCE AND SCHOLARSHIP PROGRAM</h1>
    
    <?php
    switch ($type) {
        case 'applicants':
            ?>
            <h2>Applicants Report - School Year <?= $sy ?></h2>
            <?php if (!empty($status)): ?>
            <div class="summary">Status Filter: <?= $status ?></div>
            <?php endif; ?>
            
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Municipality</th>
                        <th>School</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Exam Status</th>
                        <th>Application Status</th>
                        <th>Date Applied</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $where = "WHERE SCHOOL_YEAR = '$sy'";
                    if (!empty($status)) {
                        $where .= " AND STATUS = '$status'";
                    }
                    
                    $sql = "SELECT * FROM tbl_applicants $where ORDER BY LASTNAME ASC";
                    $mydb->setQuery($sql);
                    $mydb->executeQuery();
                    $applicants = $mydb->loadResultList();
                    
                    foreach ($applicants as $a):
                    ?>
                    <tr>
                        <td><?= $a->LASTNAME . ', ' . $a->FIRSTNAME . ' ' . ($a->MIDDLENAME ?? '') ?></td>
                        <td><?= $a->MUNICIPALITY ?? 'N/A' ?></td>
                        <td><?= $a->SCHOOL ?? 'N/A' ?></td>
                        <td><?= $a->COURSE ?? 'N/A' ?></td>
                        <td><?= $a->YEARLEVEL ?></td>
                        <td><?= $a->EXAM_STATUS ?></td>
                        <td><?= $a->STATUS ?></td>
                        <td><?= date('M d, Y', strtotime($a->DATECREATED)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php
            break;
            
        case 'scholars':
            ?>
            <h2><?= $status ?> Scholars Report - School Year <?= $sy ?></h2>
            
            <table>
                <thead>
                    <tr>
                        <th>Award ID</th>
                        <th>Name</th>
                        <th>Municipality</th>
                        <th>School</th>
                        <th>Course</th>
                        <th>Year Level</th>
                        <th>Amount</th>
                        <th>Award Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
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
                    foreach ($scholars as $s):
                        $total_amount += $s->AMOUNT;
                    ?>
                    <tr>
                        <td>SCH-<?= str_pad($s->AWARD_ID, 5, '0', STR_PAD_LEFT) ?></td>
                        <td><?= $s->LASTNAME . ', ' . $s->FIRSTNAME . ' ' . ($s->MIDDLENAME ?? '') ?></td>
                        <td><?= $s->MUNICIPALITY ?? 'N/A' ?></td>
                        <td><?= $s->SCHOOL ?? 'N/A' ?></td>
                        <td><?= $s->COURSE ?? 'N/A' ?></td>
                        <td><?= $s->YEARLEVEL ?></td>
                        <td>₱ <?= number_format($s->AMOUNT, 2) ?></td>
                        <td><?= date('M d, Y', strtotime($s->AWARD_DATE)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="6" align="right"><strong>TOTAL AMOUNT:</strong></td>
                        <td colspan="2"><strong>₱ <?= number_format($total_amount, 2) ?></strong></td>
                    </tr>
                </tbody>
            </table>
            <?php
            break;
            
        case 'district':
            ?>
            <h2><?= $district ?> Report - School Year <?= $sy ?></h2>
            
            <table>
                <thead>
                    <tr>
                        <th>Municipality</th>
                        <th>Applicants</th>
                        <th>Passed Exam</th>
                        <th>Qualified</th>
                        <th>Active Scholars</th>
                        <th>Graduates</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
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
                             WHERE a.MUNICIPALITY = m.MUNICIPALITY_NAME AND h.STATUS = 'Graduated') as graduates
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
                    
                    foreach ($municipalities as $m):
                        $total_apps += $m->applicants;
                        $total_passed += $m->passed;
                        $total_qualified += $m->qualified;
                        $total_scholars += $m->scholars;
                        $total_graduates += $m->graduates;
                    ?>
                    <tr>
                        <td><strong><?= $m->MUNICIPALITY_NAME ?></strong></td>
                        <td><?= $m->applicants ?></td>
                        <td><?= $m->passed ?></td>
                        <td><?= $m->qualified ?></td>
                        <td><?= $m->scholars ?></td>
                        <td><?= $m->graduates ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td><strong>TOTAL</strong></td>
                        <td><?= $total_apps ?></td>
                        <td><?= $total_passed ?></td>
                        <td><?= $total_qualified ?></td>
                        <td><?= $total_scholars ?></td>
                        <td><?= $total_graduates ?></td>
                    </tr>
                </tbody>
            </table>
            <?php
            break;
            
        case 'municipality':
            ?>
            <h2><?= $municipality ?> Report - School Year <?= $sy ?></h2>
            
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>School</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Exam Status</th>
                        <th>Application Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM tbl_applicants WHERE MUNICIPALITY = '$municipality' AND SCHOOL_YEAR = '$sy' ORDER BY LASTNAME ASC";
                    $mydb->setQuery($sql);
                    $mydb->executeQuery();
                    $applicants = $mydb->loadResultList();
                    
                    foreach ($applicants as $a):
                    ?>
                    <tr>
                        <td><?= $a->LASTNAME . ', ' . $a->FIRSTNAME . ' ' . ($a->MIDDLENAME ?? '') ?></td>
                        <td><?= $a->SCHOOL ?? 'N/A' ?></td>
                        <td><?= $a->COURSE ?? 'N/A' ?></td>
                        <td><?= $a->YEARLEVEL ?></td>
                        <td><?= $a->EXAM_STATUS ?></td>
                        <td><?= $a->STATUS ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php
            break;
    }
    ?>
    
    <div class="footer">
        <p>Report generated on <?= date('F d, Y h:i A') ?> | Ilocos Sur Educational Assistance and Scholarship Program</p>
        <p>Provincial Government of Ilocos Sur</p>
    </div>
</body>
</html>
<?php exit; ?>