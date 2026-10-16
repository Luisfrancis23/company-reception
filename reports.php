<?php
$base_path = "";
require_once 'includes/session_check.php';
require_once 'config/database.php';

$page_title = "Reports";
$active = "reports";

$report_type = $_GET['type'] ?? 'today';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

switch ($report_type) {
    case 'today':
        $date_from = date('Y-m-d');
        $date_to = date('Y-m-d');
        break;
    case 'yesterday':
        $date_from = date('Y-m-d', strtotime('-1 day'));
        $date_to = date('Y-m-d', strtotime('-1 day'));
        break;
    case 'weekly':
        $date_from = date('Y-m-d', strtotime('monday this week'));
        $date_to = date('Y-m-d');
        break;
    case 'monthly':
        $date_from = date('Y-m-01');
        $date_to = date('Y-m-t');
        break;
    case 'yearly':
        $date_from = date('Y-01-01');
        $date_to = date('Y-12-31');
        break;
    case 'custom':
        break;
    default:
        $date_from = date('Y-m-d');
        $date_to = date('Y-m-d');
}

$params = [$date_from, $date_to];
$sql = "SELECT v.*, u.fullname AS receptionist_name
        FROM visitors v
        LEFT JOIN users u ON v.created_by = u.id
        WHERE v.visit_date >= ? AND v.visit_date <= ?
        ORDER BY v.visit_date DESC, v.check_in ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<h4 class="mb-3">Reports</h4>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="reports.php" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label">Report Type</label>
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="today" <?php echo $report_type==='today'?'selected':''; ?>>Today</option>
                    <option value="yesterday" <?php echo $report_type==='yesterday'?'selected':''; ?>>Yesterday</option>
                    <option value="weekly" <?php echo $report_type==='weekly'?'selected':''; ?>>This Week</option>
                    <option value="monthly" <?php echo $report_type==='monthly'?'selected':''; ?>>This Month</option>
                    <option value="yearly" <?php echo $report_type==='yearly'?'selected':''; ?>>This Year</option>
                    <option value="custom" <?php echo $report_type==='custom'?'selected':''; ?>>Custom Range</option>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label">From</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from); ?>">
            </div>
            <div class="col-auto">
                <label class="form-label">To</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to); ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-dark-blue text-white">
                    <i class="bi bi-search"></i> Generate
                </button>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">
        Report:
        <?php echo date('M d, Y', strtotime($date_from)); ?>
        to
        <?php echo date('M d, Y', strtotime($date_to)); ?>
        (<?php echo count($visitors); ?> records)
    </h5>
    <div>
        <a href="print_report.php?type=<?php echo urlencode($report_type); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>" class="btn btn-outline-secondary btn-sm" target="_blank">
            <i class="bi bi-printer"></i> Print
        </a>
        <a href="export_pdf.php?type=<?php echo urlencode($report_type); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-file-pdf"></i> PDF
        </a>
        <a href="export_excel.php?type=<?php echo urlencode($report_type); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-excel"></i> Excel
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0 align-middle" id="reportTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Visitor Code</th>
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
                        <tr><td colspan="13" class="text-center text-muted py-4">No records found for this period.</td></tr>
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
                            <td>
                                <?php
                                    $badgeClass = 'bg-warning text-dark';
                                    if ($v['status'] === 'Approved') $badgeClass = 'bg-info';
                                    if ($v['status'] === 'In Meeting') $badgeClass = 'bg-primary';
                                    if ($v['status'] === 'Checked Out') $badgeClass = 'bg-success';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($v['status']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($v['receptionist_name'] ?: '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
