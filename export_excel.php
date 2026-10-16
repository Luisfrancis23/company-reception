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

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="visitor_report_' . $date_from . '_to_' . $date_to . '.xls"');

echo "Company Reception System - Visitor Report\n";
echo "Date Range: " . date('M d, Y', strtotime($date_from)) . " to " . date('M d, Y', strtotime($date_to)) . "\n";
echo "Generated: " . date('F d, Y h:i A') . "\n\n";

echo "#\tCode\tName\tCompany\tEmployee to Meet\tDepartment\tPurpose\tDate\tCheck In\tCheck Out\tDuration\tStatus\tReceptionist\n";

$i = 1;
foreach ($visitors as $v) {
    echo $i++ . "\t";
    echo $v['visitor_code'] . "\t";
    echo $v['visitor_name'] . "\t";
    echo ($v['company'] ?: '-') . "\t";
    echo $v['employee_to_meet'] . "\t";
    echo $v['department'] . "\t";
    echo $v['purpose'] . "\t";
    echo $v['visit_date'] . "\t";
    echo ($v['check_in'] ? date('h:i A', strtotime($v['check_in'])) : '-') . "\t";
    echo ($v['check_out'] ? date('h:i A', strtotime($v['check_out'])) : '-') . "\t";
    echo ($v['duration'] ?: '-') . "\t";
    echo $v['status'] . "\t";
    echo ($v['receptionist_name'] ?: '-') . "\n";
}
exit();
