<?php
session_start();
include '../../includes/cek_login.php';
include '../../config/database.php';

if ($_SESSION['role'] != 'mahasiswa') {
    die("Akses ditolak!");
}

$ujian_id = $_GET['id'];
$ujian = mysqli_fetch_assoc(mysqli_query($conn, "SELECT u.*, mk.nama_mk FROM ujian u JOIN mata_kuliah mk ON u.mk_id=mk.id WHERE u.id=$ujian_id"));

include '../../includes/header.php';
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

<?php include '../../includes/footer.php'; ?>