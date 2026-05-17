<?php
session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'dosen') {
    die("Akses ditolak!");
}

$dosen_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

$query = "SELECT * FROM kelas WHERE dosen_id = $dosen_id";
if (!empty($search)) {
    $query .= " AND (nama_kelas LIKE '%$search%' OR tahun_ajaran LIKE '%$search%')";
}
$query .= " ORDER BY nama_kelas";

$kelas = mysqli_query($conn, $query);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Kelas Saya</h1>
    <div style="display: flex; gap: 10px;">
        <form method="GET" style="display: flex; gap: 5px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama kelas atau tahun ajaran..." 
                   value="<?= htmlspecialchars($search) ?>" style="width: 300px;">
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Cari</button>
            <?php if(!empty($search)): ?>
                <a href="index.php" class="btn-outline"><i class="fas fa-times"></i> Reset</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if(!empty($search) && mysqli_num_rows($kelas) == 0): ?>
    <div class="alert info">Tidak ada kelas yang cocok dengan "<?= htmlspecialchars($search) ?>"</div>
<?php endif; ?>

<div class="card-modern">
    <table class="table-modern">
        <thead>
            <tr><th>Nama Kelas</th><th>Tahun Ajaran</th><th>Jumlah MK</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($kelas) > 0): ?>
                <?php while($k = mysqli_fetch_assoc($kelas)): 
                    $total_mk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM mata_kuliah WHERE kelas_id={$k['id']}"))['total'];
                ?>
                <tr>
                    <td><?= htmlspecialchars($k['nama_kelas']) ?></td>
                    <td><?= htmlspecialchars($k['tahun_ajaran']) ?></td>
                    <td><?= $total_mk ?> Mata Kuliah</td>
                    <td>
                        <a href="../matakuliah/index.php?kelas_id=<?= $k['id'] ?>" class="btn-primary" style="padding:4px 10px;font-size:12px;">Lihat MK</a>
                        <a href="../mahasiswa_terdaftar/index.php?kelas_id=<?= $k['id'] ?>" class="btn-outline" style="padding:4px 10px;font-size:12px;">Lihat Mhs</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center">Belum ada kelas yang ditugaskan</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>