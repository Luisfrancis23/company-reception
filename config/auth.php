<?php

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function getVisitorCode($pdo) {
    $stmt = $pdo->query("SELECT MAX(id) FROM visitors");
    $max_id = $stmt->fetchColumn();
    $next_id = ($max_id ? $max_id : 0) + 1;
    return 'VIS-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
}
