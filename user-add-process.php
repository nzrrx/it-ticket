<?php
session_start();
require 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status'=>'error','message'=>'Unauthorized']);
    exit;
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? '';

if (!$name || !$email || !$password || !$role) {
    echo json_encode(['status'=>'error','message'=>'Semua field wajib diisi.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status'=>'error','message'=>'Format email tidak valid.']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['status'=>'error','message'=>'Password minimal 6 karakter.']);
    exit;
}

$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(['status'=>'error','message'=>'Email sudah terdaftar.']);
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
    INSERT INTO users (name, email, password, role, created_at)
    VALUES (?, ?, ?, ?, NOW())
");

$stmt->bind_param("ssss", $name, $email, $passwordHash, $role);

if ($stmt->execute()) {
    echo json_encode(['status'=>'success','message'=>'User berhasil ditambahkan.']);
} else {
    echo json_encode(['status'=>'error','message'=>'Gagal menyimpan data.']);
}
