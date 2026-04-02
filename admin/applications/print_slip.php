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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Examination Slip</title>
    <link rel="stylesheet" href="<?php echo web_root;?>bootstrap/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
        }
        .slip-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #27ae60;
            padding: 20px;
            border-radius: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #27ae60;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            color: #27ae60;
            margin: 5px 0;
        }
        .header h3 {
            margin: 5px 0;
        }
        .slip-number {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 20px;
        }
        .details-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .details-table td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .details-table td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .reminder-box {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #6c757d;
        }
        .print-btn {
            text-align: center;
            margin: 20px 0;
        }
        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-btn">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fa fa-print"></i> Print Slip
        </button>
        <a href="index.php" class="btn btn-default">Back</a>
    </div>

    <div class="slip-container">
        <div class="slip-number">
            Slip No: <?php echo $applicant->EXAM_SLIP_NUMBER; ?>
        </div>
        
        <div class="header">
            <h2>Republic of the Philippines</h2>
            <h3>Province of Ilocos Sur</h3>
            <h2>ILOCOS SUR EDUCATIONAL ASSISTANCE AND SCHOLARSHIP PROGRAM</h2>
            <h4>EXAMINATION SLIP</h4>
        </div>

        <table class="details-table">
            <tr>
                <td>Name:</td>
                <td><strong><?php echo strtoupper($applicant->LASTNAME . ', ' . $applicant->FIRSTNAME . ' ' . ($applicant->MIDDLENAME ?? '')); ?></strong></td>
            </tr>
            <!-- <tr>
                <td>Municipality:</td>
                <td><?php echo $applicant->MUNICIPALITY; ?></td>
            </tr> -->
            <tr>
                <td>School:</td>
                <td><?php echo $applicant->SCHOOL; ?></td>
            </tr>
            <tr>
                <td>Course/Program:</td>
                <td><?php echo $applicant->COURSE; ?></td>
            </tr>
            <tr>
                <td>Year Level:</td>
                <td><?php echo $applicant->YEARLEVEL; ?></td>
            </tr>
            <!-- <tr>
                <td>Examination Date:</td>
                <td><strong><?php echo date('F d, Y', strtotime($applicant->EXAM_DATE)); ?></strong></td>
            </tr>
            <tr>
                <td>Examination Time:</td>
                <td><strong><?php echo date('h:i A', strtotime($applicant->EXAM_TIME)); ?></strong></td>
            </tr>
            <tr>
                <td>Venue:</td>
                <td><strong><?php echo $applicant->EXAM_VENUE; ?></strong></td>
            </tr> -->
        </table>

        <div class="reminder-box">
            <h5><strong>IMPORTANT REMINDERS:</strong></h5>
            <ol>
                <li>Present this slip upon entry to the examination room.</li>
                <li>Bring the following:
                    <ul>
                        <li>Valid ID or Birth Certificate</li>
                        <li>Ballpen and Pencil</li>
                    </ul>
                </li>
                <li>Wear appropriate attire: White shirt and plain pants.</li>
                <li>Arrive at least 30 minutes before the scheduled time.</li>
                <li>Cell phones and other electronic devices are not allowed inside the examination room.</li>
                <li>Any form of cheating will result in automatic disqualification.</li>
            </ol>
            <p class="text-muted"><em><?php echo $applicant->EXAM_NOTES ?? 'Please follow all instructions strictly.'; ?></em></p>
        </div>

        <div class="footer">
            <p>This slip is valid only on the scheduled examination date. Not transferrable.</p>
            <!-- <p><?php echo date('F d, Y'); ?></p> -->
        </div>
    </div>

    <script>
        // Uncomment the line below if you want the print dialog to auto-open
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>