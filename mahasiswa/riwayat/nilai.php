<?php
session_start();
include '../../includes/cek_login.php';
include '../../config/database.php';

if ($_SESSION['role'] != 'mahasiswa') {
    die("Akses ditolak!");
}

$mahasiswa_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$filter_mk = isset($_GET['mk_induk_id']) ? (int)$_GET['mk_induk_id'] : 0;

// Ambil daftar MK yang pernah diikuti untuk filter
$mk_list = mysqli_query($conn, "
    SELECT DISTINCT mki.id, mki.kode_mk, mki.nama_mk
    FROM ujian u
    JOIN mata_kuliah mk ON u.mk_id = mk.id
    JOIN mata_kuliah_induk mki ON mk.mk_induk_id = mki.id
    JOIN enrollments e ON u.enrollment_id = e.id
    WHERE e.mahasiswa_id = $mahasiswa_id AND u.status = 'selesai'
    ORDER BY mki.kode_mk
");

// Query riwayat
$query = "
    SELECT u.nilai_akhir, u.mulai_ujian, u.selesai_ujian, 
           mki.kode_mk, mki.nama_mk, k.nama_kelas
    FROM ujian u
    JOIN mata_kuliah mk ON u.mk_id = mk.id
    JOIN mata_kuliah_induk mki ON mk.mk_induk_id = mki.id
    JOIN kelas k ON mk.kelas_id = k.id
    JOIN enrollments e ON u.enrollment_id = e.id
    WHERE e.mahasiswa_id = $mahasiswa_id AND u.status = 'selesai'
";

if (!empty($search)) {
    $query .= " AND (mki.kode_mk LIKE '%$search%' 
                    OR mki.nama_mk LIKE '%$search%' 
                    OR k.nama_kelas LIKE '%$search%')";
}

if ($filter_mk > 0) {
    $query .= " AND mki.id = $filter_mk";
}

$query .= " ORDER BY u.selesai_ujian DESC";

$riwayat = mysqli_query($conn, $query);

// Hitung statistik
$total_nilai = 0;
$count = mysqli_num_rows($riwayat);
$nilai_array = [];
while($row = mysqli_fetch_assoc($riwayat)) {
    $nilai_array[] = $row;
    $total_nilai += $row['nilai_akhir'];
}
$rata_rata = $count > 0 ? round($total_nilai / $count, 2) : 0;

include '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Riwayat Nilai</h1>
    <p class="page-subtitle">Halo, <?= htmlspecialchars($_SESSION['nama']) ?></p>
</div>

<!-- Statistik -->
<div class="dashboard-grid" style="margin-bottom: 20px;">
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $count ?></h3>
            <p>Total Ujian</p>
        </div>
        <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $rata_rata ?></h3>
            <p>Rata-rata Nilai</p>
        </div>
        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
    </div>
</div>

<!-- Filter dan Search -->
<div class="card-modern" style="margin-bottom: 20px;">
    <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Cari (MK / Kelas)</label>
            <input type="text" name="search" class="form-control" placeholder="Kata kunci..." 
                   value="<?= htmlspecialchars($search) ?>" style="width: 250px;">
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <label>Filter Mata Kuliah</label>
            <select name="mk_induk_id" class="form-control" style="width: 250px;">
                <option value="">-- Semua MK --</option>
                <?php 
                // Reset pointer hasil query untuk digunakan lagi
                if(mysqli_num_rows($mk_list) > 0) {
                    mysqli_data_seek($mk_list, 0);
                    while($mk = mysqli_fetch_assoc($mk_list)): 
                ?>
                    <option value="<?= $mk['id'] ?>" <?= $filter_mk == $mk['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($mk['kode_mk']) ?> - <?= htmlspecialchars($mk['nama_mk']) ?>
                    </option>
                <?php 
                    endwhile;
                }
                ?>
            </select>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Filter</button>
            <a href="nilai.php" class="btn-outline"><i class="fas fa-times"></i> Reset</a>
        </div>
    </form>
</div>

<div class="card-modern">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Mata Kuliah</th>
                <th>Kelas</th>
                <th>Nilai</th>
                <th>Tanggal Ujian</th>
            </tr>
        </thead>
        <tbody>
            <?php if($count > 0): ?>
                <?php foreach($nilai_array as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['kode_mk']) ?> - <?= htmlspecialchars($row['nama_mk']) ?> </td>
                    <td><?= htmlspecialchars($row['nama_kelas']) ?> </td>
                    <td><strong><?= round($row['nilai_akhir'], 2) ?></strong> </td>
                    <td><?= date('d/m/Y H:i', strtotime($row['selesai_ujian'])) ?> </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align:center">
                        <?= (!empty($search) || $filter_mk > 0) ? "Tidak ada data yang cocok" : "Belum ada riwayat ujian" ?>
                     ﹏
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if($count > 0): ?>
        <div style="margin-top: 20px;">
            <button onclick="window.print()" class="btn-outline"><i class="fas fa-print"></i> Cetak Riwayat</button>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>