<?php
session_start();
require 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status'=>'error','message'=>'Unauthorized']);
    exit;
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['status'=>'error','message'=>'ID tidak valid']);
    exit;
}

$id = (int) $_POST['id'];

// Cegah hapus diri sendiri
if ($id === (int)$_SESSION['user_id']) {
    echo json_encode(['status'=>'error','message'=>'Tidak bisa menghapus akun sendiri']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i",$id);

if($stmt->execute()){
    echo json_encode(['status'=>'success','message'=>'User dan seluruh datanya berhasil dihapus']);
}else{
    echo json_encode(['status'=>'error','message'=>'Gagal menghapus user']);
}
