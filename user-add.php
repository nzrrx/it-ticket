<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah User | MICS IT</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="assets/css/hp.css" rel="stylesheet">

<style>
body{
    background:#f0f9ff;
    font-family:'Segoe UI',sans-serif;
}
.sidebar{
    width:250px;
    height:100vh;
    background:linear-gradient(180deg,#0ea5e9,#0284c7);
    position:fixed;
    color:#fff;
}
.sidebar h4{
    font-weight:700;
}
.sidebar a{
    color:#e0f2fe;
    text-decoration:none;
    display:block;
    padding:12px 20px;
    border-radius:10px;
    margin-bottom:5px;
    transition:.3s;
}
.sidebar a:hover,
.sidebar a.active{
    background:rgba(255,255,255,.2);
}
.content{
    margin-left:260px;
    padding:30px;
}
.card{
    border:none;
    border-radius:18px;
    box-shadow:0 10px 25px rgba(14,165,233,.2);
}
.card card-form p-4 {
    padding: 30px;
    text-align: center;
}
</style>
</head>

<body>

    <!-- SIDEBAR -->
<div class="sidebar p-4">
<h4 class="mb-4">🛠️ MICS IT</h4>

<a href="ticket.php">
<i class="bi bi-speedometer2 me-2"></i> Dashboard
</a>

<a href="ticket-admin.php">
<i class="bi bi-ticket-detailed me-2"></i> Semua Tiket
</a>

<a href="data-user.php" class="active">
<i class="bi bi-people me-2"></i> Data User
</a>

<a href="#">
<i class="bi bi-gear me-2"></i> Pengaturan
</a>

<a href="../logout.php">
<i class="bi bi-box-arrow-right me-2"></i> Logout
</a>
</div>

   <div class="mobile-nav-bar d-md-none">
    <a href="ticket.php" class="nav-item">
        <i class="bi bi-house-door"></i>
        <span>Home</span>
    </a>
    <a href="ticket-admin.php" class="nav-item">
        <i class="bi bi-ticket-perforated"></i>
        <span>Semua Tiket</span>
    </a>
    <a href="data-user.php" class="nav-item active">
        <i class="bi bi-people me-2"></i>
        <span>Data User</span>
    </a>
    <a href="#" class="nav-item">
        <i class="bi bi-gear"></i>
        <span>Pengaturan</span>
    </a>
    <a href="logout.php" class="nav-item">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
    </a>
</div>

<!-- CONTENT -->
<div class="content">


<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="bi bi-person-plus"></i> Tambah User
    </h4>

    <a href="data-user.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
        <div class="col-lg-5">

            <div class="card card-form p-4">

<form method="POST" action="user-add-process.php" id="addUserForm">

    <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-4">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
    </div>

    <button class="btn btn-primary w-100">
        <i class="bi bi-save"></i> Simpan User
    </button>

</form>

</div>
</div>
</div>
<script>
document.getElementById('addUserForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const password = document.querySelector('input[name="password"]').value;

    // VALIDASI PANJANG PASSWORD
    if (password.length < 6) {
        Swal.fire({
            icon: 'error',
            title: 'Password Terlalu Pendek',
            text: 'Password harus minimal 6 karakter!'
        });
        return;
    }

    Swal.fire({
        title: 'Tambah User?',
        text: 'Pastikan data sudah benar sebelum disimpan.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#0d6efd'
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
    });
});
</script>


</body>
</html>
