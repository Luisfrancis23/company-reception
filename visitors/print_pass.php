<?php
require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT v.*, u.fullname AS receptionist_name FROM visitors v LEFT JOIN users u ON v.created_by = u.id WHERE v.id = ?");
$stmt->execute([$id]);
$visitor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$visitor) {
    die("Visitor not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Pass - <?php echo htmlspecialchars($visitor['visitor_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .pass-container {
            max-width: 400px;
            margin: 20px auto;
        }
        .pass-card {
            border: 2px solid #0d2b4e;
            border-radius: 16px;
            overflow: hidden;
        }
        .pass-header {
            background: #0d2b4e;
            color: #fff;
            padding: 15px;
            text-align: center;
        }
        .pass-body {
            padding: 20px;
        }
        .pass-photo {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #0d2b4e;
        }
        .pass-label {
            color: #6b7a8f;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .pass-value {
            font-weight: 600;
            margin-bottom: 10px;
        }
        @media print {
            .no-print { display: none; }
            body { background: #fff; }
        }
    </style>
</head>
<body>
    <div class="pass-container">
        <div class="text-center no-print mb-3">
            <button onclick="window.print();" class="btn btn-primary">
                <i class="bi bi-printer"></i> Print Pass
            </button>
            <button onclick="window.close();" class="btn btn-secondary">
                Close
            </button>
        </div>

        <div class="pass-card">
            <div class="pass-header">
                <h5 class="mb-0"><i class="bi bi-building"></i> Company Reception</h5>
                <small>Visitor Pass</small>
            </div>
            <div class="pass-body text-center">
                <?php if ($visitor['photo'] && file_exists('../' . $visitor['photo'])): ?>
                    <img src="../<?php echo htmlspecialchars($visitor['photo']); ?>" class="pass-photo mb-3" alt="Photo">
                <?php else: ?>
                    <div class="pass-photo d-inline-flex align-items-center justify-content-center bg-light mb-3">
                        <i class="bi bi-person" style="font-size:2.5rem;color:#999;"></i>
                    </div>
                <?php endif; ?>

                <h5 class="mb-1"><?php echo htmlspecialchars($visitor['visitor_name']); ?></h5>
                <span class="badge bg-primary mb-3"><?php echo htmlspecialchars($visitor['visitor_code']); ?></span>

                <hr>
                <div class="text-start">
                    <div class="row">
                        <div class="col-6">
                            <div class="pass-label">Company</div>
                            <div class="pass-value"><?php echo htmlspecialchars($visitor['company'] ?: '-'); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="pass-label">Phone</div>
                            <div class="pass-value"><?php echo htmlspecialchars($visitor['phone']); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="pass-label">Employee to Meet</div>
                            <div class="pass-value"><?php echo htmlspecialchars($visitor['employee_to_meet']); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="pass-label">Department</div>
                            <div class="pass-value"><?php echo htmlspecialchars($visitor['department']); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="pass-label">Purpose</div>
                            <div class="pass-value"><?php echo htmlspecialchars($visitor['purpose']); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="pass-label">Date</div>
                            <div class="pass-value"><?php echo htmlspecialchars($visitor['visit_date']); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="pass-label">Check In</div>
                            <div class="pass-value"><?php echo $visitor['check_in'] ? date('h:i A', strtotime($visitor['check_in'])) : '-'; ?></div>
                        </div>
                        <div class="col-6">
                            <div class="pass-label">Status</div>
                            <div class="pass-value"><?php echo htmlspecialchars($visitor['status']); ?></div>
                        </div>
                    </div>
                </div>

                <?php if ($visitor['signature_in'] && file_exists('../' . $visitor['signature_in'])): ?>
                    <hr>
                    <div class="pass-label">Signature</div>
                    <img src="../<?php echo htmlspecialchars($visitor['signature_in']); ?>" class="signature-preview mt-1" alt="Signature">
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
