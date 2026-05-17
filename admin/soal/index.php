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

$query = "SELECT s.*, mki.kode_mk as mk_induk_kode, mki.nama_mk as mk_induk_nama
          FROM soal s 
          JOIN mata_kuliah_induk mki ON s.mk_induk_id = mki.id";
if (!empty($search)) {
    $query .= " WHERE (mki.kode_mk LIKE '%$search%' 
                    OR mki.nama_mk LIKE '%$search%' 
                    OR s.teks_soal LIKE '%$search%'
                    OR s.tipe_soal LIKE '%$search%')";
}
$query .= " ORDER BY mki.kode_mk, s.id LIMIT 200";

$soal = mysqli_query($conn, $query);

include BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Kelola Soal (Semua Dosen)</h1>
    <div style="display: flex; gap: 10px;">
        <form method="GET" style="display: flex; gap: 5px;">
            <input type="text" name="search" class="form-control" placeholder="Cari kode MK, nama MK, teks soal..." value="<?= htmlspecialchars($search) ?>" style="width: 350px;">
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Cari</button>
            <?php if(!empty($search)): ?>
                <a href="<?= BASE_URL ?>/admin/soal/index.php" class="btn-outline"><i class="fas fa-times"></i> Reset</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card-modern" style="overflow-x: auto;">
    <table class="table-modern" style="min-width: 800px;">
        <thead>
            <tr><th>MK Induk</th><th>Tipe</th><th>Soal & Pilihan</th><th>Kunci Jawaban</th><th>Bobot</th></tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($soal) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($soal)): ?>
                <tr>
                    <td style="vertical-align: top;"><?= htmlspecialchars($row['mk_induk_kode']) ?><br><small><?= htmlspecialchars(substr($row['mk_induk_nama'], 0, 25)) ?>...</small></td>
                    <td style="vertical-align: top;"><?= $row['tipe_soal'] ?></td>
                    <td style="max-width: 400px; vertical-align: top;">
                        <strong><?= htmlspecialchars(substr($row['teks_soal'], 0, 100)) ?>...</strong>
                        <?php if($row['tipe_soal'] == 'pg'): ?>
                            <div style="margin-top: 8px; font-size: 12px; color: #475569;">
                                <div>A. <?= htmlspecialchars(substr($row['pilihan_A'] ?? '', 0, 50)) ?></div>
                                <div>B. <?= htmlspecialchars(substr($row['pilihan_B'] ?? '', 0, 50)) ?></div>
                                <div>C. <?= htmlspecialchars(substr($row['pilihan_C'] ?? '', 0, 50)) ?></div>
                                <div>D. <?= htmlspecialchars(substr($row['pilihan_D'] ?? '', 0, 50)) ?></div>
                                <div>E. <?= htmlspecialchars(substr($row['pilihan_E'] ?? '', 0, 50)) ?></div>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td style="vertical-align: top;"><?= htmlspecialchars(substr($row['kunci_jawaban'], 0, 50)) ?>...</td>
                    <td style="vertical-align: top;"><?= $row['bobot'] ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center">Belum ada data soal</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>