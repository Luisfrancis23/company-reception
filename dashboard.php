<?php
$base_path = "";
require_once 'includes/session_check.php';
require_once 'config/database.php';

$page_title = "Dashboard";
$active = "dashboard";

$today = date('Y-m-d');

$stmt = $pdo->prepare("SELECT COUNT(*) FROM visitors WHERE visit_date = ?");
$stmt->execute([$today]);
$visitors_today = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM visitors WHERE visit_date = ? AND check_in IS NOT NULL");
$stmt->execute([$today]);
$checked_in = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM visitors WHERE visit_date = ? AND status = 'Checked Out'");
$stmt->execute([$today]);
$checked_out = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM visitors WHERE status = 'Waiting'");
$stmt->execute();
$visitors_waiting = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM visitors WHERE status = 'Approved' OR status = 'In Meeting'");
$stmt->execute();
$currently_inside = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM visitors WHERE MONTH(visit_date) = MONTH(CURDATE()) AND YEAR(visit_date) = YEAR(CURDATE())");
$stmt->execute();
$monthly_total = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT * FROM visitors ORDER BY id DESC LIMIT 5");
$stmt->execute();
$recent = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?></h4>
        <p class="text-muted mb-0" id="currentDateTime"></p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4 col-lg-2">
        <div class="stat-card">
            <p class="mb-1">Visitors Today</p>
            <h3><?php echo $visitors_today; ?></h3>
            <i class="bi bi-people stat-icon"></i>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="stat-card">
            <p class="mb-1">Checked In</p>
            <h3><?php echo $checked_in; ?></h3>
            <i class="bi bi-box-arrow-in-right stat-icon"></i>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="stat-card">
            <p class="mb-1">Checked Out</p>
            <h3><?php echo $checked_out; ?></h3>
            <i class="bi bi-box-arrow-right stat-icon"></i>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="stat-card">
            <p class="mb-1">Waiting</p>
            <h3><?php echo $visitors_waiting; ?></h3>
            <i class="bi bi-hourglass-split stat-icon"></i>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="stat-card">
            <p class="mb-1">Currently Inside</p>
            <h3><?php echo $currently_inside; ?></h3>
            <i class="bi bi-building stat-icon"></i>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="stat-card">
            <p class="mb-1">This Month</p>
            <h3><?php echo $monthly_total; ?></h3>
            <i class="bi bi-calendar-range stat-icon"></i>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Recent Visitors</span>
        <a href="visitors/list.php" class="btn btn-sm btn-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Department</th>
                        <th>Time In</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recent) === 0): ?>
                        <tr><td colspan="7" class="text-center text-muted py-3">No visitors yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($recent as $v): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($v['visitor_code']); ?></td>
                            <td><?php echo htmlspecialchars($v['visitor_name']); ?></td>
                            <td><?php echo htmlspecialchars($v['company'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($v['department']); ?></td>
                            <td><?php echo $v['check_in'] ? date('h:i A', strtotime($v['check_in'])) : '-'; ?></td>
                            <td>
                                <?php
                                    $badgeClass = 'bg-warning text-dark';
                                    if ($v['status'] === 'Approved') $badgeClass = 'bg-info';
                                    if ($v['status'] === 'In Meeting') $badgeClass = 'bg-primary';
                                    if ($v['status'] === 'Checked Out') $badgeClass = 'bg-success';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($v['status']); ?></span>
                            </td>
                            <td>
                                <a href="visitors/view.php?id=<?php echo $v['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>
function updateDateTime() {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
    document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
}
updateDateTime();
setInterval(updateDateTime, 1000);
</script>
