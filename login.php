<?php
session_start();
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}
include 'includes/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

$email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = "Email dan password wajib diisi!";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    }
    elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    }
    else {

    // Prepared statement
    $stmt = $conn->prepare("SELECT id, email, password, role, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verifikasi password hash
        if (password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];   // ✅ WAJIB
    $_SESSION['email']   = $user['email'];
    $_SESSION['role']    = $user['role']; // jika ada role
    $_SESSION['login']   = true;

    session_regenerate_id(true);
    header("Location: index.php");
    exit;
}

    }

    $error = "Email atau password salah!";
}
}
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | MICS IT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/login.css" rel="stylesheet">
    <style>      
    </style>
</head>
<body>

<div class="card login-card p-4">
    <div class="card-header">
        <h4>🔐 Login Account</h4>
        <p class="text-muted mb-0">MICS IT Ticketing System</p>
    </div>

    <div class="card-body">
        <?php if ($error): ?>
            <div class="alert alert-danger text-center">
                <?= $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" name="email" class="form-control" placeholder="Masukkan email" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 text-white mt-3" id="loginBtn">
    <span>Login</span>
</button>

        </form>
    </div>

    <div class="text-center mt-3">
        <small class="text-muted">
            Belum punya akun?
            <a class="text-decoration-none fw-semibold">Hubungi Administrator</a>
        </small>
    </div>
</div>
<script>
    const form = document.querySelector("form");
    const btn = document.getElementById("loginBtn");

    form.addEventListener("submit", function () {
        btn.classList.add("loading");
        form.classList.add("submitting");
    });
</script>

</body>
</html>
