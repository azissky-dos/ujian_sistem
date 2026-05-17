<?php
// admin/kelas/index.php
// ======================================================
// DAFTAR KELAS
// ======================================================

session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

// Hapus kelas
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM kelas WHERE id = $id");
    header('Location: index.php');
    exit();
}

// Pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

$query = "SELECT k.*, u.nama_lengkap as dosen_nama 
          FROM kelas k 
          LEFT JOIN users u ON k.dosen_id = u.id";
if (!empty($search)) {
    $query .= " WHERE (k.nama_kelas LIKE '%$search%' 
                    OR k.tahun_ajaran LIKE '%$search%' 
                    OR u.nama_lengkap LIKE '%$search%')";
}
$query .= " ORDER BY k.nama_kelas";

$kelas = mysqli_query($conn, $query);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Kelola Kelas</h1>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <form method="GET" style="display: flex; gap: 5px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama kelas, tahun ajaran, dosen..." 
                   value="<?= htmlspecialchars($search) ?>" style="width: 300px;">
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Cari</button>
            <?php if(!empty($search)): ?>
                <a href="index.php" class="btn-outline"><i class="fas fa-times"></i> Reset</a>
            <?php endif; ?>
        </form>
        <a href="tambah.php" class="btn-primary"><i class="fas fa-plus"></i> Tambah Kelas</a>
    </div>
</div>

<?php if(!empty($search) && mysqli_num_rows($kelas) == 0): ?>
    <div class="alert info">Tidak ada kelas yang cocok dengan "<?= htmlspecialchars($search) ?>"</div>
<?php endif; ?>

<div class="card-modern">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Nama Kelas</th>
                <th>Tahun Ajaran</th>
                <th>Dosen Pengajar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($kelas) > 0): ?>
                <?php while($k = mysqli_fetch_assoc($kelas)): ?>
                <tr>
                    <td><?= htmlspecialchars($k['nama_kelas']) ?></td>
                    <td><?= htmlspecialchars($k['tahun_ajaran']) ?></td>
                    <td><?= htmlspecialchars($k['dosen_nama'] ?? '-') ?></td>
                    <td>
                        <a href="edit.php?id=<?= $k['id'] ?>" class="btn-primary" style="padding:4px 10px;font-size:12px;">Edit</a>
                        <a href="?hapus=<?= $k['id'] ?>" class="btn-danger" style="padding:4px 10px;font-size:12px;" onclick="return confirm('Yakin hapus kelas ini? Semua data MK akan ikut terhapus!')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align:center">Belum ada data kelas</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>