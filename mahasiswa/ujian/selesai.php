<?php
session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'mahasiswa') {
    die("Akses ditolak!");
}

$ujian_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$ujian = mysqli_fetch_assoc(mysqli_query($conn, "SELECT u.*, mk.nama_mk FROM ujian u JOIN mata_kuliah mk ON u.mk_id=mk.id WHERE u.id=$ujian_id"));

if (!$ujian) {
    header('Location: ../dashboard.php');
    exit();
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Ujian Selesai!</h1>
</div>

<div class="card-modern" style="text-align:center">
    <i class="fas fa-check-circle" style="font-size:64px;color:#10b981"></i>
    <h2>Nilai Anda: <?= round($ujian['nilai_akhir'], 2) ?></h2>
    <p>Mata Kuliah: <?= htmlspecialchars($ujian['nama_mk']) ?></p>
    <a href="../dashboard.php" class="btn-primary">Kembali ke Dashboard</a>
    <a href="../riwayat/nilai.php" class="btn-outline">Lihat Riwayat Nilai</a>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>