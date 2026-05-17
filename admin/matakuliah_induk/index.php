<?php
// admin/matakuliah_induk/index.php
// ======================================================
// DAFTAR MASTER MATA KULIAH (INDUK)
// ======================================================

session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

// Hapus master MK
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM mata_kuliah_induk WHERE id = $id");
    header('Location: index.php');
    exit();
}

// Pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

$query = "SELECT * FROM mata_kuliah_induk";
if (!empty($search)) {
    $query .= " WHERE (kode_mk LIKE '%$search%' OR nama_mk LIKE '%$search%')";
}
$query .= " ORDER BY kode_mk";

$mk_induk = mysqli_query($conn, $query);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Kelola Mata Kuliah Induk (Master)</h1>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <form method="GET" style="display: flex; gap: 5px;">
            <input type="text" name="search" class="form-control" placeholder="Cari kode MK atau nama MK..." 
                   value="<?= htmlspecialchars($search) ?>" style="width: 300px;">
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Cari</button>
            <?php if(!empty($search)): ?>
                <a href="index.php" class="btn-outline"><i class="fas fa-times"></i> Reset</a>
            <?php endif; ?>
        </form>
        <a href="tambah.php" class="btn-primary"><i class="fas fa-plus"></i> Tambah MK Induk</a>
    </div>
</div>

<?php if(!empty($search) && mysqli_num_rows($mk_induk) == 0): ?>
    <div class="alert info">Tidak ada MK yang cocok dengan "<?= htmlspecialchars($search) ?>"</div>
<?php endif; ?>

<div class="card-modern">
    <div style="overflow-x: auto;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Kode MK</th>
                    <th>Nama Mata Kuliah</th>
                    <th>Digunakan di Kelas</th>
                    <th>Jumlah Soal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($mk_induk) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($mk_induk)): 
                        $kelas_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM mata_kuliah WHERE mk_induk_id = {$row['id']}"))['total'];
                        $soal_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM soal WHERE mk_induk_id = {$row['id']}"))['total'];
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['kode_mk']) ?></td>
                        <td><?= htmlspecialchars($row['nama_mk']) ?></td>
                        <td><?= $kelas_count ?> kelas</td>
                        <td><?= $soal_count ?> soal</td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn-primary" style="padding:4px 10px; font-size:12px;">Edit</a>
                            <a href="?hapus=<?= $row['id'] ?>" class="btn-danger" style="padding:4px 10px; font-size:12px;" onclick="return confirm('Yakin hapus MK induk ini? Semua soal akan ikut terhapus!')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center">Belum ada data MK induk</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>