<?php
session_start();
require_once 'config/database.php';

$error = "";
$step = 1;
$username = "";
$security_question = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['find_user'])) {
        $username = trim($_POST['username'] ?? '');

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $security_question = $user['security_question'];
            $step = 2;
        } else {
            $error = "Username not found.";
        }
    }

    if (isset($_POST['check_answer'])) {
        $username = trim($_POST['username'] ?? '');
        $answer   = trim($_POST['security_answer'] ?? '');

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify(strtolower($answer), $user['security_answer'])) {
            $_SESSION['reset_user_id'] = $user['id'];
            header("Location: reset_password.php");
            exit();
        } else {
            $error = "Incorrect answer. Please try again.";
            $security_question = $user['security_question'] ?? '';
            $step = 2;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Company Reception</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="text-center mb-3">
            <i class="bi bi-key login-icon"></i>
            <h4 class="mt-2">Forgot Password</h4>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger auto-alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <form method="POST" action="forgot_password.php">
                <div class="mb-3">
                    <label class="form-label">Enter your username</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <button type="submit" name="find_user" class="btn btn-primary w-100">
                    Continue <i class="bi bi-arrow-right"></i>
                </button>
            </form>
        <?php else: ?>
            <form method="POST" action="forgot_password.php">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
                <div class="mb-3">
                    <label class="form-label">Security Question</label>
                    <p class="fw-semibold"><?php echo htmlspecialchars($security_question); ?></p>
                    <input type="text" name="security_answer" class="form-control" placeholder="Your answer" required autofocus>
                </div>
                <button type="submit" name="check_answer" class="btn btn-primary w-100">
                    Verify Answer <i class="bi bi-check-circle"></i>
                </button>
            </form>
        <?php endif; ?>

        <div class="text-center mt-3">
            <a href="login.php" class="small"><i class="bi bi-arrow-left"></i> Back to Login</a>
        </div>

        <hr>
        <p class="text-muted small text-center mb-0">
            Alternatively, ask your administrator to reset your password directly in the database.
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
