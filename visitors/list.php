<?php
$base_path = "../";
require_once '../includes/session_check.php';
require_once '../config/database.php';

$page_title = "Visitors";
$active = "visitors";

$search    = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? '');
$date_from = trim($_GET['date_from'] ?? '');
$date_to   = trim($_GET['date_to'] ?? '');
$page      = max(1, (int)($_GET['page'] ?? 1));
$limit     = 10;
$offset    = ($page - 1) * $limit;

$where = [];
$params = [];

if ($search !== '') {
    $where[] = "(v.visitor_name LIKE ? OR v.company LIKE ? OR v.department LIKE ? OR v.employee_to_meet LIKE ? OR v.visitor_code LIKE ?)";
    $like = "%{$search}%";
    $params = array_merge($params, [$like, $like, $like, $like, $like]);
}

if ($status_filter !== '') {
    $where[] = "v.status = ?";
    $params[] = $status_filter;
}

if ($date_from !== '') {
    $where[] = "v.visit_date >= ?";
    $params[] = $date_from;
}

if ($date_to !== '') {
    $where[] = "v.visit_date <= ?";
    $params[] = $date_to;
}

$where_clause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

$count_sql = "SELECT COUNT(*) FROM visitors v $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

$sql = "SELECT v.* FROM visitors v $where_clause ORDER BY v.id DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);

$flash = $_GET['msg'] ?? '';

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Visitors List</h4>
    <a href="add.php" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Add Visitor
    </a>
</div>

<?php if ($flash === 'added'): ?>
    <div class="alert alert-success auto-alert">Visitor added successfully.</div>
<?php elseif ($flash === 'updated'): ?>
    <div class="alert alert-success auto-alert">Visitor updated successfully.</div>
<?php elseif ($flash === 'deleted'): ?>
    <div class="alert alert-success auto-alert">Visitor deleted successfully.</div>
<?php elseif ($flash === 'checked_in'): ?>
    <div class="alert alert-success auto-alert">Visitor checked in successfully.</div>
<?php elseif ($flash === 'checked_out'): ?>
    <div class="alert alert-success auto-alert">Visitor checked out successfully.</div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="list.php" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search by name, code, company..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="Waiting" <?php echo $status_filter==='Waiting'?'selected':''; ?>>Waiting</option>
                    <option value="Approved" <?php echo $status_filter==='Approved'?'selected':''; ?>>Approved</option>
                    <option value="In Meeting" <?php echo $status_filter==='In Meeting'?'selected':''; ?>>In Meeting</option>
                    <option value="Checked Out" <?php echo $status_filter==='Checked Out'?'selected':''; ?>>Checked Out</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from); ?>" placeholder="From">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to); ?>" placeholder="To">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-dark-blue text-white">
                    <i class="bi bi-search"></i> Search
                </button>
                <a href="list.php" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Department</th>
                        <th>Employee to Meet</th>
                        <th>Date</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($visitors) === 0): ?>
                        <tr><td colspan="11" class="text-center text-muted py-4">No visitors found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($visitors as $v): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($v['visitor_code']); ?></td>
                            <td><?php echo htmlspecialchars($v['visitor_name']); ?></td>
                            <td><?php echo htmlspecialchars($v['company'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($v['department']); ?></td>
                            <td><?php echo htmlspecialchars($v['employee_to_meet']); ?></td>
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
                            <td class="text-end">
                                <a href="view.php?id=<?php echo $v['id']; ?>" class="btn btn-sm btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="checkin.php?id=<?php echo $v['id']; ?>" class="btn btn-sm btn-outline-success <?php echo $v['check_in'] ? 'disabled' : ''; ?>" title="Check In">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                </a>
                                <a href="checkout.php?id=<?php echo $v['id']; ?>" class="btn btn-sm btn-outline-warning <?php echo (!$v['check_in'] || $v['check_out']) ? 'disabled' : ''; ?>" title="Check Out">
                                    <i class="bi bi-box-arrow-right"></i>
                                </a>
                                <a href="edit.php?id=<?php echo $v['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $v['id']; ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($total_pages > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="list.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>">
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
