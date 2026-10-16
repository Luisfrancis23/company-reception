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

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Export PDF - Company Reception</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; padding: 20px; }
        .report-title { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #999; padding: 5px; text-align: left; }
        th { background: #0d2b4e; color: #fff; }
        tr:nth-child(even) { background: #f4f6f9; }
    </style>
</head>
<body>
    <div class="report-title">
        <h2>Company Reception System</h2>
        <p>Visitor Report: <?php echo date('M d, Y', strtotime($date_from)); ?> to <?php echo date('M d, Y', strtotime($date_to)); ?></p>
        <p>Generated: <?php echo date('F d, Y h:i A'); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Name</th>
                <th>Company</th>
                <th>Employee</th>
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
                <tr><td colspan="13" style="text-align:center;">No records found.</td></tr>
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

    <p style="text-align:center;margin-top:20px;color:#666;font-size:11px;">
        Company Reception Management System &copy; <?php echo date('Y'); ?>
    </p>

    <script>
        window.print();
    </script>
</body>
</html>
