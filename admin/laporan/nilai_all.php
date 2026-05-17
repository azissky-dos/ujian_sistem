<?php
session_start();

// Tentukan BASE_PATH secara manual (AMAN)
$base_path = dirname(__DIR__, 2);  // naik 2 level dari admin/xxx/ ke root
require_once $base_path . '/config/config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

// Cek role
if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

// Koneksi database
require_once BASE_PATH . '/config/database.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$filter_kelas = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;
$filter_mk = isset($_GET['mk_induk_id']) ? (int)$_GET['mk_induk_id'] : 0;

$kelas_list = mysqli_query($conn, "SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas");
$mk_induk_list = mysqli_query($conn, "SELECT id, kode_mk, nama_mk FROM mata_kuliah_induk ORDER BY kode_mk");

$query = "SELECT 
            mhs.nim_nip as nim,
            mhs.nama_lengkap as nama_mahasiswa,
            k.nama_kelas,
            mki.kode_mk,
            mki.nama_mk as nama_mk_induk,
            u.nilai_akhir,
            u.jumlah_pindah_tab,
            u.selesai_ujian
          FROM ujian u
          JOIN enrollments e ON u.enrollment_id = e.id
          JOIN users mhs ON e.mahasiswa_id = mhs.id
          JOIN mata_kuliah mk ON u.mk_id = mk.id
          JOIN mata_kuliah_induk mki ON mk.mk_induk_id = mki.id
          JOIN kelas k ON mk.kelas_id = k.id
          WHERE u.status = 'selesai'";

if (!empty($search)) {
    $query .= " AND (mhs.nim_nip LIKE '%$search%' 
                    OR mhs.nama_lengkap LIKE '%$search%' 
                    OR mki.kode_mk LIKE '%$search%'
                    OR mki.nama_mk LIKE '%$search%'
                    OR k.nama_kelas LIKE '%$search%')";
}
if ($filter_kelas > 0) {
    $query .= " AND k.id = $filter_kelas";
}
if ($filter_mk > 0) {
    $query .= " AND mki.id = $filter_mk";
}
$query .= " ORDER BY u.selesai_ujian DESC";

$nilai = mysqli_query($conn, $query);

include BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Laporan Nilai Semua Mahasiswa</h1>
    <p class="page-subtitle">Rekap nilai seluruh mahasiswa dari semua kelas</p>
</div>

<div class="card-modern" style="margin-bottom: 20px;">
    <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Cari (NIM/Nama/MK/Kelas)</label>
            <input type="text" name="search" class="form-control" placeholder="Kata kunci..." value="<?= htmlspecialchars($search) ?>" style="width: 250px;">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>Filter Kelas</label>
            <select name="kelas_id" class="form-control" style="width: 200px;">
                <option value="">-- Semua Kelas --</option>
                <?php while($k = mysqli_fetch_assoc($kelas_list)): ?>
                    <option value="<?= $k['id'] ?>" <?= $filter_kelas == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>Filter Mata Kuliah</label>
            <select name="mk_induk_id" class="form-control" style="width: 250px;">
                <option value="">-- Semua MK --</option>
                <?php while($mk = mysqli_fetch_assoc($mk_induk_list)): ?>
                    <option value="<?= $mk['id'] ?>" <?= $filter_mk == $mk['id'] ? 'selected' : ?>><?= htmlspecialchars($mk['kode_mk']) ?> - <?= htmlspecialchars($mk['nama_mk']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Filter</button>
            <a href="<?= BASE_URL ?>/admin/laporan/nilai_all.php" class="btn-outline"><i class="fas fa-times"></i> Reset</a>
        </div>
    </form>
</div>

<?php 
$total_nilai = 0;
$count = mysqli_num_rows($nilai);
$nilai_array = [];
while($row = mysqli_fetch_assoc($nilai)) {
    $nilai_array[] = $row;
    $total_nilai += $row['nilai_akhir'];
}
$rata_rata = $count > 0 ? round($total_nilai / $count, 2) : 0;
$tertinggi = $count > 0 ? max(array_column($nilai_array, 'nilai_akhir')) : 0;
$terendah = $count > 0 ? min(array_column($nilai_array, 'nilai_akhir')) : 0;
?>

<div class="dashboard-grid" style="margin-bottom: 20px;">
    <div class="stat-card">
        <div class="stat-info"><h3><?= $count ?></h3><p>Total Ujian</p></div>
        <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info"><h3><?= $rata_rata ?></h3><p>Rata-rata Nilai</p></div>
        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info"><h3><?= $tertinggi ?></h3><p>Nilai Tertinggi</p></div>
        <div class="stat-icon"><i class="fas fa-trophy"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info"><h3><?= $terendah ?></h3><p>Nilai Terendah</p></div>
        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
    </div>
</div>

<div class="card-modern">
    <table class="table-modern">
        <thead>
            <tr><th>NIM</th><th>Nama Mahasiswa</th><th>Kelas</th><th>Mata Kuliah</th><th>Nilai</th><th>Pindah Tab</th><th>Tanggal Ujian</th></tr>
        </thead>
        <tbody>
            <?php if($count > 0): ?>
                <?php foreach($nilai_array as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nim']) ?></td>
                    <td><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                    <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                    <td><?= htmlspecialchars($row['kode_mk']) ?> - <?= htmlspecialchars(substr($row['nama_mk_induk'], 0, 30)) ?></td>
                    <td><strong><?= round($row['nilai_akhir'], 2) ?></strong></td>
                    <td><?= $row['jumlah_pindah_tab'] ?> x</td>
                    <td><?= date('d/m/Y H:i', strtotime($row['selesai_ujian'])) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center">Belum ada data ujian</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div style="margin-top: 20px; display: flex; justify-content: space-between;">
        <button onclick="window.print()" class="btn-outline"><i class="fas fa-print"></i> Cetak Laporan</button>
        <a href="<?= BASE_URL ?>/admin/laporan/export_excel.php?<?= http_build_query($_GET) ?>" class="btn-success" style="background:#10b981; color:white; padding:10px 20px; border-radius:12px; text-decoration:none;"><i class="fas fa-file-excel"></i> Export ke Excel</a>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>