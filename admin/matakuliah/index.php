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

if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM mata_kuliah WHERE id = " . (int)$_GET['hapus']);
    header('Location: ' . BASE_URL . '/admin/matakuliah/index.php');
    exit();
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where = !empty($search) ? "WHERE (mk.kode_mk LIKE '%$search%' OR mk.nama_mk LIKE '%$search%' OR k.nama_kelas LIKE '%$search%')" : "";

$mk = mysqli_query($conn, "SELECT mk.*, k.nama_kelas, u.nama_lengkap as dosen_nama FROM mata_kuliah mk JOIN kelas k ON mk.kelas_id = k.id LEFT JOIN users u ON mk.dosen_id = u.id $where ORDER BY k.nama_kelas, mk.nama_mk");

require_once BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Kelola Mata Kuliah</h1>
    <div style="display: flex; gap: 10px;">
        <form method="GET" style="display: flex; gap: 5px;">
            <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>" style="width: 300px;">
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Cari</button>
            <?php if(!empty($search)): ?>
                <a href="<?= BASE_URL ?>/admin/matakuliah/index.php" class="btn-outline"><i class="fas fa-times"></i> Reset</a>
            <?php endif; ?>
        </form>
        <a href="<?= BASE_URL ?>/admin/matakuliah/tambah.php" class="btn-primary"><i class="fas fa-plus"></i> Tambah MK</a>
    </div>
</div>

<div class="card-modern">
    <table class="table-modern">
        <thead><tr><th>Kode MK</th><th>Nama MK</th><th>Kelas</th><th>Dosen</th><th>Durasi</th><th>Mode</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($mk)): ?>
            <tr>
                <td><?= htmlspecialchars($row['kode_mk']) ?></td>
                <td><?= htmlspecialchars($row['nama_mk']) ?></td>
                <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                <td><?= htmlspecialchars($row['dosen_nama'] ?? '-') ?></td>
                <td><?= $row['durasi_ujian'] ?> menit</td>
                <td><?= $row['is_latihan'] ? '🏋️ Latihan' : '📝 Ujian' ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/admin/matakuliah/edit.php?id=<?= $row['id'] ?>" class="btn-primary" style="padding:4px 10px;">Edit</a>
                    <a href="?hapus=<?= $row['id'] ?>" class="btn-danger" style="padding:4px 10px;" onclick="return confirm('Yakin hapus?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>