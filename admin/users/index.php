<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

require_once BASE_PATH . '/config/database.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    header('Location: ' . BASE_URL . '/admin/users/index.php');
    exit();
}

$query = "SELECT * FROM users WHERE role IN ('admin','dosen')";
if (!empty($search)) {
    $query .= " AND (username LIKE '%$search%' OR nama_lengkap LIKE '%$search%' OR email LIKE '%$search%' OR nim_nip LIKE '%$search%')";
}
$query .= " ORDER BY role, nama_lengkap";

$users = mysqli_query($conn, $query);

require_once BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Kelola Users (Admin & Dosen)</h1>
    <div style="display: flex; gap: 10px;">
        <form method="GET" style="display: flex; gap: 5px;">
            <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>" style="width: 280px;">
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Cari</button>
            <?php if(!empty($search)): ?>
                <a href="<?= BASE_URL ?>/admin/users/index.php" class="btn-outline"><i class="fas fa-times"></i> Reset</a>
            <?php endif; ?>
        </form>
        <a href="<?= BASE_URL ?>/admin/users/tambah.php" class="btn-primary"><i class="fas fa-plus"></i> Tambah User</a>
    </div>
</div>

<div class="card-modern">
    <table class="table-modern">
        <thead><tr><th>Username</th><th>Nama Lengkap</th><th>Role</th><th>Email</th><th>NIP</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php while($user = mysqli_fetch_assoc($users)): ?>
            <tr>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['nama_lengkap']) ?></td>
                <td><span class="badge <?= $user['role']=='admin'?'badge-danger':'badge-warning' ?>"><?= $user['role'] ?></span></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['nim_nip']) ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/admin/users/edit.php?id=<?= $user['id'] ?>" class="btn-primary" style="padding:4px 10px;">Edit</a>
                    <a href="?hapus=<?= $user['id'] ?>" class="btn-danger" style="padding:4px 10px;" onclick="return confirm('Yakin hapus?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>