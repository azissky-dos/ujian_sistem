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
    mysqli_query($conn, "DELETE FROM kelas WHERE id = $id");
    header('Location: ' . BASE_URL . '/admin/kelas/index.php');
    exit();
}

$query = "SELECT k.*, u.nama_lengkap as dosen_nama FROM kelas k LEFT JOIN users u ON k.dosen_id = u.id";
if (!empty($search)) {
    $query .= " WHERE (k.nama_kelas LIKE '%$search%' OR k.tahun_ajaran LIKE '%$search%' OR u.nama_lengkap LIKE '%$search%')";
}
$query .= " ORDER BY k.nama_kelas";

$kelas = mysqli_query($conn, $query);

require_once BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Kelola Kelas</h1>
    <div style="display: flex; gap: 10px;">
        <form method="GET" style="display: flex; gap: 5px;">
            <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>" style="width: 300px;">
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Cari</button>
            <?php if(!empty($search)): ?>
                <a href="<?= BASE_URL ?>/admin/kelas/index.php" class="btn-outline"><i class="fas fa-times"></i> Reset</a>
            <?php endif; ?>
        </form>
        <a href="<?= BASE_URL ?>/admin/kelas/tambah.php" class="btn-primary"><i class="fas fa-plus"></i> Tambah Kelas</a>
    </div>
</div>

<div class="card-modern">
    <table class="table-modern">
        <thead><tr><th>Nama Kelas</th><th>Tahun Ajaran</th><th>Dosen</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php while($k = mysqli_fetch_assoc($kelas)): ?>
            <tr>
                <td><?= htmlspecialchars($k['nama_kelas']) ?></td>
                <td><?= htmlspecialchars($k['tahun_ajaran']) ?></td>
                <td><?= htmlspecialchars($k['dosen_nama'] ?? '-') ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/admin/kelas/edit.php?id=<?= $k['id'] ?>" class="btn-primary" style="padding:4px 10px;">Edit</a>
                    <a href="?hapus=<?= $k['id'] ?>" class="btn-danger" style="padding:4px 10px;" onclick="return confirm('Yakin hapus?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>