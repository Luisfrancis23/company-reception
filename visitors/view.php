<?php
$base_path = "../";
require_once '../includes/session_check.php';
require_once '../config/database.php';

$page_title = "View Visitor";
$active = "visitors";

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT v.*, u.fullname AS receptionist_name FROM visitors v LEFT JOIN users u ON v.created_by = u.id WHERE v.id = ?");
$stmt->execute([$id]);
$visitor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$visitor) {
    header("Location: list.php");
    exit();
}

$badgeClass = 'bg-warning text-dark';
if ($visitor['status'] === 'Approved') $badgeClass = 'bg-info';
if ($visitor['status'] === 'In Meeting') $badgeClass = 'bg-primary';
if ($visitor['status'] === 'Checked Out') $badgeClass = 'bg-success';

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Visitor Details - <?php echo htmlspecialchars($visitor['visitor_code']); ?></h4>
    <div>
        <a href="checkin.php?id=<?php echo $visitor['id']; ?>" class="btn btn-success btn-sm <?php echo $visitor['check_in'] ? 'disabled' : ''; ?>">
            <i class="bi bi-box-arrow-in-right"></i> Check In
        </a>
        <a href="checkout.php?id=<?php echo $visitor['id']; ?>" class="btn btn-warning btn-sm <?php echo (!$visitor['check_in'] || $visitor['check_out']) ? 'disabled' : ''; ?>">
            <i class="bi bi-box-arrow-right"></i> Check Out
        </a>
        <a href="edit.php?id=<?php echo $visitor['id']; ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="list.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Visitor Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <p class="text-muted mb-1 small">Visitor Code</p>
                        <p class="fw-semibold"><?php echo htmlspecialchars($visitor['visitor_code']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1 small">Status</p>
                        <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($visitor['status']); ?></span>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1 small">Full Name</p>
                        <p class="fw-semibold"><?php echo htmlspecialchars($visitor['visitor_name']); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p class="text-muted mb-1 small">Gender</p>
                        <p class="fw-semibold"><?php echo htmlspecialchars($visitor['gender'] ?: '-'); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p class="text-muted mb-1 small">ID Card Number</p>
                        <p class="fw-semibold"><?php echo htmlspecialchars($visitor['id_number'] ?: '-'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1 small">Company / Organization</p>
                        <p class="fw-semibold"><?php echo htmlspecialchars($visitor['company'] ?: '-'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1 small">Phone Number</p>
                        <p class="fw-semibold"><?php echo htmlspecialchars($visitor['phone']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1 small">Email</p>
                        <p class="fw-semibold"><?php echo htmlspecialchars($visitor['email'] ?: '-'); ?></p>
                    </div>
                    <div class="col-12">
                        <p class="text-muted mb-1 small">Address</p>
                        <p class="fw-semibold"><?php echo htmlspecialchars($visitor['address'] ?: '-'); ?></p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-1 small">Department</p>
                        <p class="fw-semibold"><?php echo htmlspecialchars($visitor['department']); ?></p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-1 small">Employee to Meet</p>
                        <p class="fw-semibold"><?php echo htmlspecialchars($visitor['employee_to_meet']); ?></p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-1 small">Purpose of Visit</p>
                        <p class="fw-semibold"><?php echo htmlspecialchars($visitor['purpose']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">Time Records</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <p class="text-muted mb-1 small">Visit Date</p>
                        <p class="fw-semibold"><?php echo htmlspecialchars($visitor['visit_date']); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p class="text-muted mb-1 small">Check In</p>
                        <p class="fw-semibold"><?php echo $visitor['check_in'] ? date('h:i A', strtotime($visitor['check_in'])) : '-'; ?></p>
                    </div>
                    <div class="col-md-3">
                        <p class="text-muted mb-1 small">Check Out</p>
                        <p class="fw-semibold"><?php echo $visitor['check_out'] ? date('h:i A', strtotime($visitor['check_out'])) : '-'; ?></p>
                    </div>
                    <div class="col-md-3">
                        <p class="text-muted mb-1 small">Duration</p>
                        <p class="fw-semibold"><?php echo htmlspecialchars($visitor['duration'] ?: '-'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">Signatures</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 text-center">
                        <p class="text-muted small mb-2">Check-In Signature</p>
                        <?php if ($visitor['signature_in'] && file_exists('../' . $visitor['signature_in'])): ?>
                            <img src="../<?php echo htmlspecialchars($visitor['signature_in']); ?>" class="signature-preview" alt="Signature In">
                        <?php else: ?>
                            <p class="text-muted fst-italic">No signature</p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 text-center">
                        <p class="text-muted small mb-2">Check-Out Signature</p>
                        <?php if ($visitor['signature_out'] && file_exists('../' . $visitor['signature_out'])): ?>
                            <img src="../<?php echo htmlspecialchars($visitor['signature_out']); ?>" class="signature-preview" alt="Signature Out">
                        <?php else: ?>
                            <p class="text-muted fst-italic">No signature</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Photo</div>
            <div class="card-body text-center">
                <?php if ($visitor['photo'] && file_exists('../' . $visitor['photo'])): ?>
                    <img src="../<?php echo htmlspecialchars($visitor['photo']); ?>" class="img-fluid rounded" style="max-height:250px;" alt="Visitor photo">
                <?php else: ?>
                    <div class="py-5 text-muted">
                        <i class="bi bi-person" style="font-size:4rem;"></i>
                        <p class="mt-2">No photo</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">Record Info</div>
            <div class="card-body">
                <p class="text-muted mb-1 small">Created By</p>
                <p class="fw-semibold"><?php echo htmlspecialchars($visitor['receptionist_name'] ?: '-'); ?></p>
                <p class="text-muted mb-1 small">Created At</p>
                <p class="fw-semibold"><?php echo htmlspecialchars($visitor['created_at']); ?></p>
            </div>
        </div>

        <div class="mt-3">
            <a href="javascript:void(0);" onclick="window.open('print_pass.php?id=<?php echo $visitor['id']; ?>','popup','width=600,height=800');" class="btn btn-outline-info w-100 mb-2">
                <i class="bi bi-printer"></i> Print Visitor Pass
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
