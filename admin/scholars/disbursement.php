<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

global $mydb;

function getDisbursementRecords() {
    global $mydb;
    $mydb->setQuery("
        SELECT p.*, sy.school_year,
            (SELECT COUNT(*) FROM tbl_payroll_details WHERE payroll_id = p.id AND payment_status = 'paid') as paid_count,
            (SELECT COUNT(*) FROM tbl_payroll_details WHERE payroll_id = p.id) as total_count,
            u.FULLNAME as disbursed_by_name
        FROM tbl_payroll p 
        LEFT JOIN tbl_school_years sy ON p.school_year_id = sy.id
        LEFT JOIN tblusers u ON p.disbursed_by = u.USERID
        WHERE p.status = 'disbursed'
        ORDER BY p.disbursement_date DESC
    ");
    $mydb->executeQuery();
    $records = $mydb->loadResultList();
    return $records ? $records : [];
}

$records = getDisbursementRecords();
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Disbursement Records</h1>
    </div>
</div>

<!-- Action Buttons -->
<div class="row">
    <div class="col-lg-12" style="margin-bottom: 10px;">
        <a href="index.php?view=payroll" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back to Payroll
        </a>
        <a href="#" onclick="window.print()" class="btn btn-default">
            <i class="fa fa-print"></i> Print List
        </a>
    </div>
</div>

<!-- Disbursement Records List -->
<div class="panel panel-success">
    <div class="panel-heading">
        <i class="fa fa-credit-card"></i> Disbursement Records
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table id="disbursement-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>School Year</th>
                        <th>Semester</th>
                        <th>Payment Date</th>
                        <th>Total Amount</th>
                        <th>Scholars Paid</th>
                        <th>Disbursement Date</th>
                        <th>Disbursed By</th>
                        <th>Reference No.</th>
                        <th width="15%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($records as $record): ?>
                    <tr>
                        <td><?php echo $record->school_year; ?></td>
                        <td><?php echo $record->semester; ?></td>
                        <td><?php echo date('F d, Y', strtotime($record->payment_date)); ?></td>
                        <td>₱ <?php echo number_format($record->total_amount, 2); ?></td>
                        <td><?php echo $record->paid_count; ?> / <?php echo $record->total_count; ?></td>
                        <td><?php echo date('M d, Y h:i A', strtotime($record->disbursement_date)); ?></td>
                        <td><?php echo $record->disbursed_by_name; ?></td>
                        <td><?php echo $record->reference_no ?? 'N/A'; ?></td>
                        <td class="text-center">
                            <a href="index.php?view=payroll_details&id=<?php echo $record->id; ?>" class="btn btn-info btn-xs">
                                <i class="fa fa-eye"></i> View
                            </a>
                            <a href="index.php?view=print_payroll&id=<?php echo $record->id; ?>" class="btn btn-default btn-xs" target="_blank">
                                <i class="fa fa-print"></i> Print
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($records)): ?>
                    <tr>
                        <td colspan="9" class="text-center">No disbursement records found.?>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#disbursement-table').DataTable({
        "pageLength": 25,
        "order": [[5, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": [8] }
        ]
    });
});
</script>