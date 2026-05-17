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
$filter_kelas = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;
$filter_mk = isset($_GET['mk_induk_id']) ? (int)$_GET['mk_induk_id'] : 0;

$kelas_list = mysqli_query($conn, "SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas");
$mk_induk_list = mysqli_query($conn, "SELECT id, kode_mk, nama_mk FROM mata_kuliah_induk ORDER BY kode_mk");

$query = "SELECT mhs.nim_nip as nim, mhs.nama_lengkap as nama_mahasiswa, k.nama_kelas, mki.kode_mk, mki.nama_mk as nama_mk_induk, u.nilai_akhir, u.jumlah_pindah_tab, u.selesai_ujian
          FROM ujian u
          JOIN enrollments e ON u.enrollment_id = e.id
          JOIN users mhs ON e.mahasiswa_id = mhs.id
          JOIN mata_kuliah mk ON u.mk_id = mk.id
          JOIN mata_kuliah_induk mki ON mk.mk_induk_id = mki.id
          JOIN kelas k ON mk.kelas_id = k.id
          WHERE u.status = 'selesai'";

if (!empty($search)) $query .= " AND (mhs.nim_nip LIKE '%$search%' OR mhs.nama_lengkap LIKE '%$search%' OR mki.kode_mk LIKE '%$search%' OR mki.nama_mk LIKE '%$search%' OR k.nama_kelas LIKE '%$search%')";
if ($filter_kelas > 0) $query .= " AND k.id = $filter_kelas";
if ($filter_mk > 0) $query .= " AND mki.id = $filter_mk";
$query .= " ORDER BY u.selesai_ujian DESC";

$nilai = mysqli_query($conn, $query);

require_once BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Laporan Nilai</h1>
    <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
        <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>" style="width: 200px;">
        <select name="kelas_id" class="form-control" style="width: 150px;">
            <option value="">Semua Kelas</option>
            <?php while($k = mysqli_fetch_assoc($kelas_list)): ?>
                <option value="<?= $k['id'] ?>" <?= $filter_kelas==$k['id']?'selected':'' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
            <?php endwhile; ?>
        </select>
        <select name="mk_induk_id" class="form-control" style="width: 200px;">
            <option value="">Semua MK</option>
            <?php while($mk = mysqli_fetch_assoc($mk_induk_list)): ?>
                <option value="<?= $mk['id'] ?>" <?= $filter_mk==$mk['id']?'selected':'' ?>><?= htmlspecialchars($mk['kode_mk']) ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Filter</button>
        <a href="<?= BASE_URL ?>/admin/laporan/nilai_all.php" class="btn-outline">Reset</a>
        <a href="<?= BASE_URL ?>/admin/laporan/export_excel.php?<?= http_build_query($_GET) ?>" class="btn-success" style="background:#10b981; color:white; padding:8px 16px; border-radius:12px;">Export Excel</a>
    </form>
</div>

<div class="card-modern" style="overflow-x: auto;">
    <table class="table-modern">
        <thead><tr><th>NIM</th><th>Nama</th><th>Kelas</th><th>MK</th><th>Nilai</th><th>Pindah Tab</th><th>Tgl Ujian</th></tr></thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($nilai)): ?>
            <tr>
                <td><?= htmlspecialchars($row['nim']) ?></td>
                <td><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                <td><?= htmlspecialchars($row['kode_mk']) ?></td>
                <td><strong><?= round($row['nilai_akhir'], 2) ?></strong></td>
                <td><?= $row['jumlah_pindah_tab'] ?> x</td>
                <td><?= date('d/m/Y', strtotime($row['selesai_ujian'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>