<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$keyword = $_GET['keyword'] ?? '';

$sql = "
    SELECT id, name, email, role, created_at
    FROM users
    WHERE name LIKE ?
    ORDER BY name ASC
";

$stmt = $conn->prepare($sql);
$search = "%$keyword%";
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<tr>
            <td colspan="6" class="text-center text-muted">
                Data user tidak ditemukan
            </td>
          </tr>';
    exit;
}

$no = 1;
while ($row = $result->fetch_assoc()):
?>

<tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td>
        <span class="badge bg-<?= $row['role']=='admin'?'danger':'primary' ?>">
            <?= ucfirst($row['role']) ?>
        </span>
    </td>
    <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
    <td class="text-center">

        <a href="edit-data-user.php?id=<?= $row['id'] ?>"
           class="btn btn-sm btn-outline-primary">
           <i class="bi bi-pencil-square"></i> Edit
        </a>

        <button type="button"
                class="btn btn-sm btn-outline-danger btn-delete"
                data-id="<?= $row['id'] ?>"
                data-name="<?= htmlspecialchars($row['name']) ?>">
            <i class="bi bi-trash"></i> Hapus
        </button>

    </td>
</tr>

<?php endwhile; ?>
