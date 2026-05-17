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

$query = "SELECT s.*, mki.kode_mk as mk_induk_kode, mki.nama_mk as mk_induk_nama FROM soal s JOIN mata_kuliah_induk mki ON s.mk_induk_id = mki.id";
if (!empty($search)) {
    $query .= " WHERE (mki.kode_mk LIKE '%$search%' OR mki.nama_mk LIKE '%$search%' OR s.teks_soal LIKE '%$search%' OR s.tipe_soal LIKE '%$search%')";
}
$query .= " ORDER BY mki.kode_mk, s.id LIMIT 200";

$soal = mysqli_query($conn, $query);

require_once BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Kelola Soal</h1>
    <form method="GET" style="display: flex; gap: 5px;">
        <input type="text" name="search" class="form-control" placeholder="Cari soal..." value="<?= htmlspecialchars($search) ?>" style="width: 350px;">
        <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Cari</button>
        <?php if(!empty($search)): ?>
            <a href="<?= BASE_URL ?>/admin/soal/index.php" class="btn-outline"><i class="fas fa-times"></i> Reset</a>
        <?php endif; ?>
    </form>
</div>

<div class="card-modern" style="overflow-x: auto;">
    <table class="table-modern">
        <thead><tr><th>MK</th><th>Tipe</th><th>Soal</th><th>Kunci</th><th>Bobot</th></tr></thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($soal)): ?>
            <tr>
                <td><?= htmlspecialchars($row['mk_induk_kode']) ?></td>
                <td><?= $row['tipe_soal'] ?></td>
                <td style="max-width:400px"><?= htmlspecialchars(substr($row['teks_soal'], 0, 100)) ?>...</td>
                <td><?= htmlspecialchars(substr($row['kunci_jawaban'], 0, 50)) ?></td>
                <td><?= $row['bobot'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>