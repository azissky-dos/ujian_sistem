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
    mysqli_query($conn, "DELETE FROM mata_kuliah_induk WHERE id = " . (int)$_GET['hapus']);
    header('Location: ' . BASE_URL . '/admin/matakuliah_induk/index.php');
    exit();
}

$query = "SELECT * FROM mata_kuliah_induk";
if (!empty($search)) $query .= " WHERE (kode_mk LIKE '%$search%' OR nama_mk LIKE '%$search%')";
$query .= " ORDER BY kode_mk";

$mk_induk = mysqli_query($conn, $query);

require_once BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Master Mata Kuliah</h1>
    <div style="display: flex; gap: 10px;">
        <form method="GET" style="display: flex; gap: 5px;">
            <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>" style="width: 300px;">
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Cari</button>
            <?php if(!empty($search)): ?>
                <a href="<?= BASE_URL ?>/admin/matakuliah_induk/index.php" class="btn-outline"><i class="fas fa-times"></i> Reset</a>
            <?php endif; ?>
        </form>
        <a href="<?= BASE_URL ?>/admin/matakuliah_induk/tambah.php" class="btn-primary"><i class="fas fa-plus"></i> Tambah MK</a>
    </div>
</div>

<div class="card-modern">
    <table class="table-modern">
        <thead><tr><th>Kode MK</th><th>Nama MK</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($mk_induk)): ?>
            <tr>
                <td><?= htmlspecialchars($row['kode_mk']) ?></td>
                <td><?= htmlspecialchars($row['nama_mk']) ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/admin/matakuliah_induk/edit.php?id=<?= $row['id'] ?>" class="btn-primary" style="padding:4px 10px;">Edit</a>
                    <a href="?hapus=<?= $row['id'] ?>" class="btn-danger" style="padding:4px 10px;" onclick="return confirm('Yakin hapus?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>