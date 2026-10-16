<?php
$base_path = "../";
require_once '../includes/session_check.php';
require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT photo, signature_in, signature_out FROM visitors WHERE id = ?");
    $stmt->execute([$id]);
    $visitor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($visitor) {
        foreach (['photo', 'signature_in', 'signature_out'] as $field) {
            if ($visitor[$field] && file_exists('../' . $visitor[$field])) {
                unlink('../' . $visitor[$field]);
            }
        }
    }

    $stmt = $pdo->prepare("DELETE FROM visitors WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: list.php?msg=deleted");
exit();
