<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil tiket milik user ini saja
$stmt = $conn->prepare("
    SELECT 
    t.id,
    t.subject,
    t.category,
    t.priority,
    t.status,
    t.created_at,
    t.attachment,
    u.name AS user_name,
    u.email,
    a.name AS admin_name
FROM tickets t
JOIN users u ON t.user_id = u.id
LEFT JOIN users a ON t.assigned_to = a.id
WHERE t.user_id = ?
ORDER BY 
    CASE 
        WHEN t.status = 'In Progress' THEN 1
        WHEN t.status = 'Open' THEN 2
        WHEN t.status = 'Closed' THEN 3
        ELSE 4
    END,
    t.created_at DESC;
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tickets = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tiket Saya | MICS IT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="assets/css/hp.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
    <!-- SIDEBAR -->
    <div class="sidebar p-4">
        <h4 class="mb-4">🎫 MICS IT</h4>

        <a href="dashboard.php">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        <a href="add-ticket.php">
            <i class="bi bi-plus-circle me-2"></i> Buat Tiket
        </a>
        <a href="ticket-user.php" class="active">
            <i class="bi bi-ticket-detailed me-2"></i> Tiket Saya
        </a>
        <a href="profil.php">
            <i class="bi bi-person me-2"></i> Profil
        </a>
        <a href="logout.php">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
    </div>

    <div class="mobile-nav-bar d-md-none">
    <a href="dashboard.php" class="nav-item">
        <i class="bi bi-house-door"></i>
        <span>Home</span>
    </a>
    <a href="ticket-user.php" class="nav-item active">
        <i class="bi bi-ticket-perforated"></i>
        <span>Tiket Saya</span>
    </a>
    <a href="add-ticket.php" class="nav-item">
        <i class="bi bi-plus-circle"></i>
        <span>Buat Tiket</span>
    </a>
    <a href="profil.php" class="nav-item">
        <i class="bi bi-person"></i>
        <span>Profil</span>
    </a>
    <a href="logout.php" class="nav-item">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
    </a>
</div>

    <div class="content">
    <h4 class="mb-4"><i class="bi bi-ticket-perforated"></i> Tiket Saya</h4>
    
    <div id="ticket-container">
        <div class="text-center p-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p>Memuat data...</p>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Fungsi untuk memuat data
    function loadTickets(page) {
        $.ajax({
            url: "fetch-ticket-user.php",
            method: "POST",
            data: { page: page },
            success: function(data) {
                $('#ticket-container').html(data);
            },
            error: function() {
                $('#ticket-container').html('<div class="alert alert-danger">Gagal memuat data.</div>');
            }
        });
    }

    // Load halaman pertama saat pertama kali buka
    loadTickets(1);

    // Handle klik pada tombol pagination
    $(document).on('click', '.btn-pagination', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        loadTickets(page);
        
        // Scroll ke atas sedikit agar user tahu konten berubah
        $('html, body').animate({ scrollTop: 0 }, 'fast');
    });
});
</script>