<?php
// admin/matakuliah/index.php
// ======================================================
// DAFTAR MATA KULIAH PER KELAS
// ======================================================

session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

// Hapus mata kuliah
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM mata_kuliah WHERE id = $id");
    header('Location: index.php');
    exit();
}

// Pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

$query = "SELECT mk.*, k.nama_kelas, u.nama_lengkap as dosen_nama
          FROM mata_kuliah mk 
          JOIN kelas k ON mk.kelas_id = k.id 
          LEFT JOIN users u ON mk.dosen_id = u.id";

if (!empty($search)) {
    $query .= " WHERE (mk.kode_mk LIKE '%$search%' 
                    OR mk.nama_mk LIKE '%$search%' 
                    OR k.nama_kelas LIKE '%$search%'
                    OR u.nama_lengkap LIKE '%$search%')";
}
$query .= " ORDER BY k.nama_kelas, mk.nama_mk";

$mk_list = mysqli_query($conn, $query);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Kelola Mata Kuliah (Per Kelas)</h1>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <form method="GET" style="display: flex; gap: 5px;">
            <input type="text" name="search" class="form-control" placeholder="Cari kode MK, nama MK, kelas, dosen..." 
                   value="<?= htmlspecialchars($search) ?>" style="width: 300px;">
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Cari</button>
            <?php if(!empty($search)): ?>
                <a href="index.php" class="btn-outline"><i class="fas fa-times"></i> Reset</a>
            <?php endif; ?>
        </form>
        <a href="tambah.php" class="btn-primary"><i class="fas fa-plus"></i> Tambah MK ke Kelas</a>
    </div>
</div>

<?php if(!empty($search) && mysqli_num_rows($mk_list) == 0): ?>
    <div class="alert info">Tidak ada mata kuliah yang cocok dengan "<?= htmlspecialchars($search) ?>"</div>
<?php endif; ?>

<div class="card-modern">
    <div style="overflow-x: auto;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Kode MK</th>
                    <th>Nama Mata Kuliah</th>
                    <th>Kelas</th>
                    <th>Dosen Pengajar</th>
                    <th>Durasi Ujian</th>
                    <th>Mode</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($mk_list) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($mk_list)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['kode_mk']) ?></td>
                        <td><?= htmlspecialchars($row['nama_mk']) ?></td>
                        <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                        <td><?= htmlspecialchars($row['dosen_nama'] ?? '-') ?></td>
                        <td><?= $row['durasi_ujian'] ?> menit</td>
                        <td><?= $row['is_latihan'] ? '🏋️ Latihan' : '📝 Ujian' ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn-primary" style="padding:4px 10px; font-size:12px;">Edit</a>
                            <a href="?hapus=<?= $row['id'] ?>" class="btn-danger" style="padding:4px 10px; font-size:12px;" onclick="return confirm('Yakin hapus MK ini dari kelas?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center">Belum ada data mata kuliah per kelas</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>