<?php
require_once 'config/database.php';

$report_type = $_GET['type'] ?? 'today';
$date_from = $_GET['date_from'] ?? date('Y-m-d');
$date_to = $_GET['date_to'] ?? date('Y-m-d');

$params = [$date_from, $date_to];
$sql = "SELECT v.*, u.fullname AS receptionist_name
        FROM visitors v
        LEFT JOIN users u ON v.created_by = u.id
        WHERE v.visit_date >= ? AND v.visit_date <= ?
        ORDER BY v.visit_date DESC, v.check_in ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Report - Company Reception</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; padding: 20px; }
        .report-title { text-align: center; margin-bottom: 20px; }
        .report-title h3 { margin-bottom: 5px; }
        .report-title p { color: #666; }
        table { font-size: 12px; }
        .signature-img { max-width: 80px; max-height: 30px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="report-title">
        <h3><i class="bi bi-building"></i> Company Reception System</h3>
        <p>Visitor Report: <?php echo date('M d, Y', strtotime($date_from)); ?> to <?php echo date('M d, Y', strtotime($date_to)); ?></p>
        <p>Generated on: <?php echo date('F d, Y h:i A'); ?></p>
        <hr>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Name</th>
                <th>Company</th>
                <th>Employee to Meet</th>
                <th>Department</th>
                <th>Purpose</th>
                <th>Date</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Receptionist</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($visitors) === 0): ?>
                <tr><td colspan="13" class="text-center">No records found.</td></tr>
            <?php endif; ?>
            <?php $i = 1; foreach ($visitors as $v): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($v['visitor_code']); ?></td>
                    <td><?php echo htmlspecialchars($v['visitor_name']); ?></td>
                    <td><?php echo htmlspecialchars($v['company'] ?: '-'); ?></td>
                    <td><?php echo htmlspecialchars($v['employee_to_meet']); ?></td>
                    <td><?php echo htmlspecialchars($v['department']); ?></td>
                    <td><?php echo htmlspecialchars($v['purpose']); ?></td>
                    <td><?php echo htmlspecialchars($v['visit_date']); ?></td>
                    <td><?php echo $v['check_in'] ? date('h:i A', strtotime($v['check_in'])) : '-'; ?></td>
                    <td><?php echo $v['check_out'] ? date('h:i A', strtotime($v['check_out'])) : '-'; ?></td>
                    <td><?php echo htmlspecialchars($v['duration'] ?: '-'); ?></td>
                    <td><?php echo htmlspecialchars($v['status']); ?></td>
                    <td><?php echo htmlspecialchars($v['receptionist_name'] ?: '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print();" class="btn btn-primary">
            <i class="bi bi-printer"></i> Print
        </button>
        <button onclick="window.close();" class="btn btn-secondary">
            Close
        </button>
    </div>
</body>
</html>
