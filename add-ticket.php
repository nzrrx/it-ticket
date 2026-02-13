<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error   = "";

// FLASH MESSAGE
$success = $_SESSION['success'] ?? "";
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $subject     = trim($_POST['subject']);
    $category    = $_POST['category'];
    $priority    = $_POST['priority'];
    $description = trim($_POST['description']);
    $attachment  = NULL;

    if ($subject === "" || $description === "") {
        $error = "Judul dan deskripsi wajib diisi.";
    }

    // VALIDASI FILE
    if (!$error && !empty($_FILES['attachment']['name'])) {

        $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'docx'];
        $ext = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $error = "Format file tidak diizinkan.";
        } elseif ($_FILES['attachment']['size'] > 2 * 1024 * 1024) {
            $error = "Ukuran file maksimal 2MB.";
        } else {
            $folder = "uploads/";
            if (!is_dir($folder)) mkdir($folder, 0777, true);

            $filename = uniqid('ticket_') . "." . $ext;
            $target = $folder . $filename;

            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
                $attachment = $filename;
            } else {
                $error = "Upload file gagal.";
            }
        }
    }

    if (!$error) {

        $stmt = $conn->prepare(
            "INSERT INTO tickets 
            (user_id, subject, category, priority, description, status, assigned_to, attachment)
            VALUES (?, ?, ?, ?, ?, 'Open', NULL, ?)"
        );

        $stmt->bind_param(
            "isssss",
            $user_id,
            $subject,
            $category,
            $priority,
            $description,
            $attachment
        );

        if ($stmt->execute()) {
            $_SESSION['success'] = "🎉 Tiket berhasil dibuat.";
            header("Location: add-ticket.php"); // redirect profesional
            exit;
        } else {
            $error = "Gagal menyimpan tiket.";
        }
    }
}
?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Buat Tiket | MICSTIX</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
        <a href="add-ticket.php" class="active">
            <i class="bi bi-plus-circle me-2"></i> Buat Tiket
        </a>
        <a href="ticket-user.php">
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
    <a href="dashboard.php" class="nav-item active">
        <i class="bi bi-house-door"></i>
        <span>Home</span>
    </a>
    <a href="ticket-user.php" class="nav-item">
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

    <!-- CONTENT -->
    <div class="content">

        <!-- TOP BAR
    <div class="topbar d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Buat Tiket</h5>
            <small class="text-muted">Laporkan kendala IT Anda</small>
        </div>
        <span class="badge bg-primary">User</span>
    </div> -->

        <h4 class="mb-4">
            <i class="bi bi-person-plus"></i> Tambah Tiket
        </h4>

        <!-- FORM CARD -->
        <div class="row justify-content-center">
            <div class="col-lg-6">

                <div class="card card-form p-4">
                    <h5 class="mb-3">
                        <i class="bi bi-plus-circle me-2"></i> Form Tiket Baru
                    </h5>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Tiket</label>
                            <input type="text" name="subject" class="form-control"
                                placeholder="Contoh: Laptop tidak bisa menyala" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select name="category" class="form-select" required>
                                <option value="Hardware">Hardware</option>
                                <option value="Software">Software</option>
                                <option value="Network">Network</option>
                                <option value="Email">Email</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Prioritas</label>
                            <select name="priority" class="form-select" required>
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi Masalah</label>
                            <textarea name="description" rows="5" class="form-control"
                                placeholder="Jelaskan masalah secara detail..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Lampiran (Opsional)</label>
                            <input type="file" name="attachment" class="form-control"
                                accept=".jpg,.png,.pdf,.docx">
                            <small class="text-muted">Format: JPG, PNG, PDF, DOCX</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                            <i id="btnText"
                                 class="bi bi-send"></i> Kirim Tiket
                    </i>
                            <span id="btnLoading" class="d-none">
                                <span class="spinner-border spinner-border-sm"></span> Mengirim...
                            </span>
                        </button>
                        

                    </form>


                </div>

            </div>
        </div>

    </div>
    <script>
        document.querySelector("form").addEventListener("submit", function() {
            document.getElementById("submitBtn").disabled = true;
            document.getElementById("btnText").classList.add("d-none");
            document.getElementById("btnLoading").classList.remove("d-none");
        });
    </script>


</body>

</html>