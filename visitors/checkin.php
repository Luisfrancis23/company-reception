<?php
$base_path = "../";
require_once '../includes/session_check.php';
require_once '../config/database.php';

$page_title = "Check In";
$active = "visitors";

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM visitors WHERE id = ?");
$stmt->execute([$id]);
$visitor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$visitor) {
    header("Location: list.php");
    exit();
}

if ($visitor['check_in']) {
    header("Location: view.php?id=" . $id);
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $signature_data = $_POST['signature_data'] ?? '';

    $signature_path = null;
    if (!empty($signature_data) && strpos($signature_data, 'data:image/png;base64,') === 0) {
        $base64_string = str_replace('data:image/png;base64,', '', $signature_data);
        $image_data = base64_decode($base64_string);
        if ($image_data !== false) {
            $filename = 'signature_' . time() . '_' . uniqid() . '.png';
            file_put_contents('../signatures/' . $filename, $image_data);
            $signature_path = 'signatures/' . $filename;
        }
    }

    if (!$signature_path) {
        $errors[] = "Signature is required for check-in.";
    }

    if (empty($errors)) {
        $now = date('Y-m-d H:i:s');
        $stmt = $pdo->prepare("UPDATE visitors SET check_in = ?, signature_in = ?, status = 'Approved' WHERE id = ?");
        $stmt->execute([$now, $signature_path, $id]);

        header("Location: list.php?msg=checked_in");
        exit();
    }
}

include '../includes/header.php';
?>

<h4 class="mb-3">Check In - <?php echo htmlspecialchars($visitor['visitor_name']); ?></h4>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
                <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">Visitor Information</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Code:</strong> <?php echo htmlspecialchars($visitor['visitor_code']); ?></p>
                        <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($visitor['visitor_name']); ?></p>
                        <p class="mb-1"><strong>Company:</strong> <?php echo htmlspecialchars($visitor['company'] ?: '-'); ?></p>
                        <p class="mb-1"><strong>Department:</strong> <?php echo htmlspecialchars($visitor['department']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Employee to Meet:</strong> <?php echo htmlspecialchars($visitor['employee_to_meet']); ?></p>
                        <p class="mb-1"><strong>Purpose:</strong> <?php echo htmlspecialchars($visitor['purpose']); ?></p>
                        <p class="mb-1"><strong>Date:</strong> <?php echo htmlspecialchars($visitor['visit_date']); ?></p>
                        <p class="mb-1"><strong>Status:</strong> <?php echo htmlspecialchars($visitor['status']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Check-In</div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Date:</strong> <?php echo date('Y-m-d'); ?><br>
                    <strong>Time:</strong> <?php echo date('h:i A'); ?>
                </div>

                <form method="POST" action="checkin.php?id=<?php echo $id; ?>" id="visitorForm">
                    <h6 class="mb-2"><i class="bi bi-pen"></i> Visitor Signature</h6>
                    <p class="text-muted small">Visitor must sign below to confirm check-in.</p>
                    <canvas id="signature-pad"></canvas>
                    <input type="hidden" name="signature_data" id="signatureData">

                    <div class="mt-2 mb-3">
                        <button type="button" id="clearSignature" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-eraser"></i> Clear
                        </button>
                        <button type="button" id="saveSignature" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-save"></i> Save Signature
                        </button>
                        <span id="signatureStatus" class="small ms-2"></span>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-box-arrow-in-right"></i> Confirm Check In
                    </button>
                    <a href="view.php?id=<?php echo $id; ?>" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/signature_pad.js"></script>
