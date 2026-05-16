<?php
session_start();
include '../../includes/cek_login.php';
include '../../config/database.php';

if ($_SESSION['role'] != 'dosen') {
    die("Akses ditolak!");
}

$dosen_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

$query = "
    SELECT DISTINCT 
        mki.id as mk_induk_id,
        mki.kode_mk,
        mki.nama_mk,
        (SELECT COUNT(*) FROM soal WHERE mk_induk_id = mki.id) as jumlah_soal,
        GROUP_CONCAT(DISTINCT k.nama_kelas SEPARATOR ', ') as daftar_kelas
    FROM mata_kuliah_induk mki
    JOIN mata_kuliah mk ON mk.mk_induk_id = mki.id
    JOIN kelas k ON mk.kelas_id = k.id
    WHERE k.dosen_id = $dosen_id
";

if (!empty($search)) {
    $query .= " AND (mki.kode_mk LIKE '%$search%' OR mki.nama_mk LIKE '%$search%')";
}

$query .= " GROUP BY mki.id ORDER BY mki.kode_mk";

$matakuliah = mysqli_query($conn, $query);

include '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Mata Kuliah Yang Saya Ajar</h1>
    <p class="page-subtitle">Soal cukup dibuat 1 kali, otomatis tersedia untuk semua kelas</p>
    <div style="display: flex; gap: 10px; margin-top: 10px;">
        <form method="GET" style="display: flex; gap: 5px;">
            <input type="text" name="search" class="form-control" placeholder="Cari kode MK atau nama MK..." 
                   value="<?= htmlspecialchars($search) ?>" style="width: 300px;">
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Cari</button>
            <?php if(!empty($search)): ?>
                <a href="index.php" class="btn-outline"><i class="fas fa-times"></i> Reset</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if(!empty($search) && mysqli_num_rows($matakuliah) == 0): ?>
    <div class="alert info">Tidak ada mata kuliah yang cocok dengan "<?= htmlspecialchars($search) ?>"</div>
<?php endif; ?>

<div class="card-modern">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Kode MK</th>
                <th>Nama Mata Kuliah</th>
                <th>Diajarkan di Kelas</th>
                <th>Jumlah Soal</th>
                <th>Status Soal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($matakuliah) > 0): ?>
                <?php while($mk = mysqli_fetch_assoc($matakuliah)): ?>
                <tr>
                    <td><?= htmlspecialchars($mk['kode_mk']) ?> </td>
                    <td><?= htmlspecialchars($mk['nama_mk']) ?> </td>
                    <td><?= htmlspecialchars($mk['daftar_kelas']) ?> </td>
                    <td><?= $mk['jumlah_soal'] ?> soal </td>
                    <td>
                        <?php if($mk['jumlah_soal'] >= 50): ?>
                            <span class="badge badge-success">✅ Siap Ujian</span>
                        <?php else: ?>
                            <span class="badge badge-danger">⚠️ Kurang <?= 50 - $mk['jumlah_soal'] ?> soal</span>
                        <?php endif; ?>
                     ﹏
                    <td>
                        <a href="../soal/tambah.php?mk_induk_id=<?= $mk['mk_induk_id'] ?>" class="btn-primary" style="padding:4px 10px;font-size:12px;"><i class="fas fa-plus"></i> Soal</a>
                        <a href="../soal/list.php?mk_induk_id=<?= $mk['mk_induk_id'] ?>" class="btn-outline" style="padding:4px 10px;font-size:12px;"><i class="fas fa-list"></i> List</a>
                        <a href="../laporan/nilai_perkelas.php?mk_induk_id=<?= $mk['mk_induk_id'] ?>" class="btn-info" style="padding:4px 10px;font-size:12px;background:#06b6d4;color:white;border-radius:8px;text-decoration:none;"><i class="fas fa-chart-line"></i> Nilai</a>
                     ﹏
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center">Belum ada mata kuliah yang ditugaskan ﹏
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>