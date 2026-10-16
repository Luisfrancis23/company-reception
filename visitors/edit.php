<?php
$base_path = "../";
require_once '../includes/session_check.php';
require_once '../config/database.php';

$page_title = "Edit Visitor";
$active = "visitors";

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM visitors WHERE id = ?");
$stmt->execute([$id]);
$visitor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$visitor) {
    header("Location: list.php");
    exit();
}

$errors = [];
$form = $visitor;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach (['visitor_name','gender','company','phone','email','address','id_number','purpose','department','employee_to_meet'] as $key) {
        $form[$key] = trim($_POST[$key] ?? '');
    }
    $signature_data = $_POST['signature_data'] ?? '';

    if ($form['visitor_name'] === '') $errors[] = "Full Name is required.";
    if ($form['phone'] === '') $errors[] = "Phone Number is required.";
    if ($form['purpose'] === '') $errors[] = "Purpose of Visit is required.";
    if ($form['department'] === '') $errors[] = "Department is required.";
    if ($form['employee_to_meet'] === '') $errors[] = "Employee to Meet is required.";

    if ($form['phone'] !== '' && !preg_match('/^[0-9+\-\s]{7,20}$/', $form['phone'])) {
        $errors[] = "Please enter a valid phone number.";
    }

    if ($form['email'] !== '' && !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    $photo_path = $visitor['photo'];
    if (empty($errors) && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['photo']['type'], $allowed)) {
            if ($visitor['photo'] && file_exists('../' . $visitor['photo'])) {
                unlink('../' . $visitor['photo']);
            }
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $filename = 'photo_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], '../assets/photos/' . $filename);
            $photo_path = 'assets/photos/' . $filename;
        } else {
            $errors[] = "Photo must be JPG, PNG or GIF.";
        }
    }

    $signature_path = $visitor['signature_in'];
    if (empty($errors) && !empty($signature_data) && strpos($signature_data, 'data:image/png;base64,') === 0) {
        $base64_string = str_replace('data:image/png;base64,', '', $signature_data);
        $image_data = base64_decode($base64_string);
        if ($image_data !== false) {
            $filename = 'signature_' . time() . '_' . uniqid() . '.png';
            file_put_contents('../signatures/' . $filename, $image_data);
            $signature_path = 'signatures/' . $filename;
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "UPDATE visitors SET
                visitor_name = ?, gender = ?, company = ?, phone = ?, email = ?,
                address = ?, id_number = ?, purpose = ?, department = ?,
                employee_to_meet = ?, photo = ?, signature_in = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $form['visitor_name'], $form['gender'], $form['company'], $form['phone'], $form['email'],
            $form['address'], $form['id_number'], $form['purpose'], $form['department'],
            $form['employee_to_meet'], $photo_path, $signature_path,
            $id
        ]);

        header("Location: list.php?msg=updated");
        exit();
    }
}

include '../includes/header.php';
?>

<h4 class="mb-3">Edit Visitor - <?php echo htmlspecialchars($visitor['visitor_code']); ?></h4>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
                <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="edit.php?id=<?php echo $id; ?>" id="visitorForm" enctype="multipart/form-data">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Visitor Code</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($visitor['visitor_code']); ?>" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Visit Date</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($visitor['visit_date']); ?>" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($visitor['status']); ?>" disabled>
                </div>
            </div>

            <hr>
            <h6 class="mb-3">Personal Information</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="visitor_name" class="form-control" value="<?php echo htmlspecialchars($form['visitor_name']); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">Select</option>
                        <option value="Male" <?php echo $form['gender']==='Male'?'selected':''; ?>>Male</option>
                        <option value="Female" <?php echo $form['gender']==='Female'?'selected':''; ?>>Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">ID Card Number</label>
                    <input type="text" name="id_number" class="form-control" value="<?php echo htmlspecialchars($form['id_number']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Company / Organization</label>
                    <input type="text" name="company" class="form-control" value="<?php echo htmlspecialchars($form['company']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone Number *</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($form['phone']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($form['email']); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($form['address']); ?></textarea>
                </div>
            </div>

            <hr>
            <h6 class="mb-3">Visit Details</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Department *</label>
                    <input type="text" name="department" class="form-control" value="<?php echo htmlspecialchars($form['department']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Employee to Meet *</label>
                    <input type="text" name="employee_to_meet" class="form-control" value="<?php echo htmlspecialchars($form['employee_to_meet']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Purpose of Visit *</label>
                    <input type="text" name="purpose" class="form-control" value="<?php echo htmlspecialchars($form['purpose']); ?>" required>
                </div>
            </div>

            <hr>
            <h6 class="mb-3">Photo</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <?php if ($visitor['photo']): ?>
                        <img src="../<?php echo htmlspecialchars($visitor['photo']); ?>" class="img-thumbnail mb-2" style="max-height:150px;" alt="Visitor photo">
                    <?php endif; ?>
                    <label class="form-label">Change Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
            </div>

            <hr>
            <h6 class="mb-2"><i class="bi bi-pen"></i> Signature (Check-In)</h6>
            <?php if ($visitor['signature_in']): ?>
                <p class="text-muted small mb-1">Current signature:</p>
                <img src="../<?php echo htmlspecialchars($visitor['signature_in']); ?>" class="signature-preview mb-3" alt="Current signature">
            <?php endif; ?>
            <p class="text-muted small">Draw below only if you need to replace the signature.</p>
            <canvas id="signature-pad"></canvas>
            <input type="hidden" name="signature_data" id="signatureData">

            <div class="mt-2">
                <button type="button" id="clearSignature" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-eraser"></i> Clear
                </button>
                <button type="button" id="saveSignature" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-save"></i> Save Signature
                </button>
                <span id="signatureStatus" class="small ms-2"></span>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Update Visitor
                </button>
                <a href="list.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/signature_pad.js"></script>
