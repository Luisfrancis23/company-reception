<?php
$base_path = "";
require_once 'includes/session_check.php';
require_once 'config/database.php';

$page_title = "Profile";
$active = "profile";

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['update_profile'])) {
        $fullname = trim($_POST['fullname'] ?? '');
        $security_question = trim($_POST['security_question'] ?? '');
        $security_answer = trim($_POST['security_answer'] ?? '');

        if ($fullname === '') {
            $error = "Full name is required.";
        } else {
            if ($security_answer !== '') {
                $hashed_answer = password_hash(strtolower($security_answer), PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET fullname = ?, security_question = ?, security_answer = ? WHERE id = ?");
                $stmt->execute([$fullname, $security_question, $hashed_answer, $user_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET fullname = ?, security_question = ? WHERE id = ?");
                $stmt->execute([$fullname, $security_question, $user_id]);
            }
            $_SESSION['fullname'] = $fullname;
            $success = "Profile updated successfully.";
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (!password_verify($current_password, $user['password'])) {
            $error = "Current password is incorrect.";
        } elseif (strlen($new_password) < 6) {
            $error = "New password must be at least 6 characters.";
        } elseif ($new_password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $user_id]);
            $success = "Password changed successfully.";
        }
    }
}

include 'includes/header.php';
?>

<h4 class="mb-3">My Profile</h4>

<?php if ($success): ?>
    <div class="alert alert-success auto-alert"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger auto-alert"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-person"></i> Edit Profile</div>
            <div class="card-body">
                <form method="POST" action="profile.php">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Security Question</label>
                        <input type="text" name="security_question" class="form-control" value="<?php echo htmlspecialchars($user['security_question']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Security Answer <span class="text-muted">(leave blank to keep current)</span></label>
                        <input type="text" name="security_answer" class="form-control" placeholder="Enter new answer">
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-shield-lock"></i> Change Password</div>
            <div class="card-body">
                <form method="POST" action="profile.php">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
