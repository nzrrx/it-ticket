<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    exit;
}

if (!isset($_POST['message_id'])) {
    http_response_code(400);
    exit;
}

$message_id = (int) $_POST['message_id'];
$admin_id = (int) $_SESSION['user_id'];

// 🔒 Verifikasi message_id valid
$verify = $conn->prepare("SELECT id FROM ticket_replies WHERE id = ?");
$verify->bind_param("i", $message_id);
$verify->execute();
if ($verify->get_result()->num_rows === 0) {
    http_response_code(404);
    exit;
}

// ✅ Insert atau update read status untuk admin specific
$stmt = $conn->prepare("
    INSERT INTO admin_message_reads (message_id, admin_id, read_at)
    VALUES (?, ?, NOW())
    ON DUPLICATE KEY UPDATE read_at = NOW()
");
$stmt->bind_param("ii", $message_id, $admin_id);
$success = $stmt->execute();

if ($success) {
    echo 'OK';
} else {
    http_response_code(500);
    echo 'Error';
}
