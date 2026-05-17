<?php
session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'dosen') {
    die("Akses ditolak!");
}

$dosen_id = $_SESSION['user_id'];
$mk_induk_id = isset($_GET['mk_induk_id']) ? (int)$_GET['mk_induk_id'] : 0;
$kelas_id = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

$mk_induk_list = mysqli_query($conn, "
    SELECT DISTINCT mki.id, mki.kode_mk, mki.nama_mk
    FROM mata_kuliah_induk mki
    JOIN mata_kuliah mk ON mk.mk_induk_id = mki.id
    JOIN kelas k ON mk.kelas_id = k.id
    WHERE k.dosen_id = $dosen_id
    ORDER BY mki.kode_mk
");

if ($mk_induk_id > 0) {
    $kelas_list = mysqli_query($conn, "
        SELECT DISTINCT k.id, k.nama_kelas
        FROM kelas k
        JOIN mata_kuliah mk ON mk.kelas_id = k.id
        WHERE mk.mk_induk_id = $mk_induk_id AND k.dosen_id = $dosen_id
        ORDER BY k.nama_kelas
    ");
}

if ($mk_induk_id > 0 && $kelas_id > 0) {
    $query = "
        SELECT u.nilai_akhir, u.mulai_ujian, u.selesai_ujian, u.jumlah_pindah_tab,
               mhs.nama_lengkap, mhs.nim_nip, mhs.email
        FROM ujian u
        JOIN enrollments e ON u.enrollment_id = e.id
        JOIN users mhs ON e.mahasiswa_id = mhs.id
        JOIN mata_kuliah mk ON u.mk_id = mk.id
        WHERE mk.mk_induk_id = $mk_induk_id 
          AND mk.kelas_id = $kelas_id
          AND u.status = 'selesai'
    ";
    
    if (!empty($search)) {
        $query .= " AND (mhs.nim_nip LIKE '%$search%' OR mhs.nama_lengkap LIKE '%$search%' OR mhs.email LIKE '%$search%')";
    }
    
    $query .= " ORDER BY u.nilai_akhir DESC";
    
    $nilai_list = mysqli_query($conn, $query);
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Laporan Nilai Per Kelas</h1>
    <p class="page-subtitle">Pilih mata kuliah, lalu pilih kelas</p>
</div>

<div class="card-modern">
    <div class="form-group">
        <label>Pilih Mata Kuliah</label>
        <select id="mk_induk_select" class="form-control" style="max-width:400px" onchange="window.location.href='?mk_induk_id='+this.value">
            <option value="">-- Pilih Mata Kuliah --</option>
            <?php while($mk = mysqli_fetch_assoc($mk_induk_list)): ?>
                <option value="<?= $mk['id'] ?>" <?= $mk_induk_id == $mk['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($mk['kode_mk']) ?> - <?= htmlspecialchars($mk['nama_mk']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    
    <?php if($mk_induk_id > 0): ?>
    <div class="form-group" style="margin-top:16px">
        <label>Pilih Kelas</label>
        <select id="kelas_select" class="form-control" style="max-width:300px" onchange="window.location.href='?mk_induk_id=<?= $mk_induk_id ?>&kelas_id='+this.value">
            <option value="">-- Pilih Kelas --</option>
            <?php if(isset($kelas_list)): ?>
                <?php while($k = mysqli_fetch_assoc($kelas_list)): ?>
                    <option value="<?= $k['id'] ?>" <?= $kelas_id == $k['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nama_kelas']) ?>
                    </option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>
    </div>
    <?php endif; ?>
    
    <?php if($mk_induk_id > 0 && $kelas_id > 0 && isset($nilai_list)): ?>
        <form method="GET" style="margin-top: 16px; display: flex; gap: 10px; align-items: flex-end;">
            <input type="hidden" name="mk_induk_id" value="<?= $mk_induk_id ?>">
            <input type="hidden" name="kelas_id" value="<?= $kelas_id ?>">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Cari Mahasiswa</label>
                <input type="text" name="search" class="form-control" placeholder="NIM, Nama, atau Email..." 
                       value="<?= htmlspecialchars($search) ?>" style="width: 280px;">
            </div>
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Cari</button>
            <?php if(!empty($search)): ?>
                <a href="?mk_induk_id=<?= $mk_induk_id ?>&kelas_id=<?= $kelas_id ?>" class="btn-outline"><i class="fas fa-times"></i> Reset</a>
            <?php endif; ?>
        </form>
        
        <?php 
        $total_nilai = 0;
        $count = 0;
        $nilai_array = [];
        while($row = mysqli_fetch_assoc($nilai_list)) {
            $nilai_array[] = $row;
            $total_nilai += $row['nilai_akhir'];
            $count++;
        }
        $rata_rata = $count > 0 ? round($total_nilai / $count, 2) : 0;
        $tertinggi = $count > 0 ? max(array_column($nilai_array, 'nilai_akhir')) : 0;
        $terendah = $count > 0 ? min(array_column($nilai_array, 'nilai_akhir')) : 0;
        ?>
        
        <div class="dashboard-grid" style="margin-top:20px">
            <div class="stat-card"><div class="stat-info"><h3><?= $count ?></h3><p>Jumlah Peserta</p></div><div class="stat-icon"><i class="fas fa-users"></i></div></div>
            <div class="stat-card"><div class="stat-info"><h3><?= $rata_rata ?></h3><p>Rata-rata Nilai</p></div><div class="stat-icon"><i class="fas fa-chart-line"></i></div></div>
            <div class="stat-card"><div class="stat-info"><h3><?= $tertinggi ?></h3><p>Nilai Tertinggi</p></div><div class="stat-icon"><i class="fas fa-trophy"></i></div></div>
            <div class="stat-card"><div class="stat-info"><h3><?= $terendah ?></h3><p>Nilai Terendah</p></div><div class="stat-icon"><i class="fas fa-chart-line"></i></div></div>
        </div>
        
        <table class="table-modern" style="margin-top:20px">
            <thead><tr><th>NIM</th><th>Nama Mahasiswa</th><th>Email</th><th>Nilai</th><th>Pindah Tab</th><th>Tanggal Ujian</th></tr></thead>
            <tbody>
                <?php if($count > 0): ?>
                    <?php foreach($nilai_array as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nim_nip']) ?></td>
                        <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><strong><?= round($row['nilai_akhir'], 2) ?></strong></td>
                        <td><?= $row['jumlah_pindah_tab'] ?> x</td>
                        <td><?= date('d/m/Y H:i', strtotime($row['selesai_ujian'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center"><?= !empty($search) ? "Tidak ada mahasiswa yang cocok dengan '$search'" : "Belum ada mahasiswa yang mengikuti ujian ini" ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div style="margin-top:20px">
            <button onclick="window.print()" class="btn-outline"><i class="fas fa-print"></i> Cetak Laporan</button>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>