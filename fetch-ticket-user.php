<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];
$limit = 10; // Jumlah baris per halaman
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$start = ($page - 1) * $limit;

// 1. Hitung total record untuk pagination
$total_stmt = $conn->prepare("SELECT COUNT(id) FROM tickets WHERE user_id = ?");
$total_stmt->bind_param("i", $user_id);
$total_stmt->execute();
$total_res = $total_stmt->get_result()->fetch_row();
$total_pages = ceil($total_res[0] / $limit);

// 2. Ambil data dengan LIMIT
$query = "
    SELECT t.id, t.subject, t.category, t.priority, t.status, t.created_at, a.name AS admin_name
    FROM tickets t
    LEFT JOIN users a ON t.assigned_to = a.id
    WHERE t.user_id = ?
    ORDER BY 
        CASE 
            WHEN t.status = 'In Progress' THEN 1
            WHEN t.status = 'Open' THEN 2
            WHEN t.status = 'Closed' THEN 3
            ELSE 4
        END, t.created_at DESC
    LIMIT ?, ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $start, $limit);
$stmt->execute();
$tickets = $stmt->get_result();

if ($tickets->num_rows > 0): ?>
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Prioritas</th>
                <th>Status</th>
                <th>Dibuat</th>
                <th>Jam</th>
                <th>Checked By</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = $start + 1; while ($row = $tickets->fetch_assoc()): ?>
                <tr onclick="window.location='detail-ticket-user.php?id=<?= $row['id'] ?>'" style="cursor:pointer;">
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td><?= $row['category'] ?></td>
                    <td>
                        <span class="badge bg-<?= $row['priority'] == 'High' ? 'danger' : ($row['priority'] == 'Medium' ? 'warning' : 'secondary') ?>">
                            <?= $row['priority'] ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-<?= $row['status'] == 'Open' ? 'primary' : ($row['status'] == 'In Progress' ? 'info' : ($row['status'] == 'Solved' ? 'success' : 'dark')) ?>">
                            <?= $row['status'] ?>
                        </span>
                    </td>
                    <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                    <td><?= date('H:i', strtotime($row['created_at'])) ?></td>
                    <td>
                        <?= (!empty($row['admin_name']) && in_array($row['status'], ['Closed', 'In Progress'])) 
                            ? '<span class="badge bg-success">'.htmlspecialchars($row['admin_name']).'</span>' 
                            : '<span class="text-muted">-</span>' ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <nav>
        <ul class="pagination justify-content-end">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link btn-pagination" href="#" data-page="<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php else: ?>
    <div class="alert alert-info text-center">Anda belum memiliki tiket.</div>
<?php endif; ?>