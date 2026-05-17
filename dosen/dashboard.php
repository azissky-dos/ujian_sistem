<?php
session_start();
require_once __DIR__ . '/../includes/cek_login.php';
require_once __DIR__ . '/../config/database.php';

if ($_SESSION['role'] != 'dosen') {
    die("Akses ditolak!");
}

$dosen_id = $_SESSION['user_id'];

$total_kelas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kelas WHERE dosen_id=$dosen_id"))['total'];
$total_mk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM mata_kuliah mk JOIN kelas k ON mk.kelas_id=k.id WHERE k.dosen_id=$dosen_id"))['total'];
$total_mahasiswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT e.mahasiswa_id) as total FROM enrollments e JOIN kelas k ON e.kelas_id=k.id WHERE k.dosen_id=$dosen_id"))['total'];
$total_soal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM soal s JOIN mata_kuliah mk ON s.mk_id=mk.id JOIN kelas k ON mk.kelas_id=k.id WHERE k.dosen_id=$dosen_id"))['total'];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Dashboard Dosen</h1>
    <p class="page-subtitle">Selamat datang, <?= $_SESSION['nama'] ?></p>
</div>

<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $total_kelas ?></h3>
            <p>Kelas Saya</p>
        </div>
        <div class="stat-icon"><i class="fas fa-school"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $total_mk ?></h3>
            <p>Mata Kuliah</p>
        </div>
        <div class="stat-icon"><i class="fas fa-book"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $total_mahasiswa ?></h3>
            <p>Mahasiswa</p>
        </div>
        <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $total_soal ?></h3>
            <p>Total Soal</p>
        </div>
        <div class="stat-icon"><i class="fas fa-question-circle"></i></div>
    </div>
</div>

<div class="menu-grid">
    <div class="card-modern">
        <i class="fas fa-school" style="font-size:32px;color:#4f46e5"></i>
        <h3>Kelas Saya</h3>
        <a href="kelas/index.php" class="btn-primary">Kelola →</a>
    </div>
    <div class="card-modern">
        <i class="fas fa-key" style="font-size:32px;color:#4f46e5"></i>
        <h3>Reset Password Mhs</h3>
        <a href="reset_password/index.php" class="btn-primary">Reset →</a>
    </div>
    <div class="card-modern">
        <i class="fas fa-pen-alt" style="font-size:32px;color:#4f46e5"></i>
        <h3>Buat Soal</h3>
        <a href="soal/tambah.php" class="btn-primary">Buat →</a>
    </div>
    <div class="card-modern">
        <i class="fas fa-chart-line" style="font-size:32px;color:#4f46e5"></i>
        <h3>Laporan Nilai</h3>
        <a href="laporan/nilai_perkelas.php" class="btn-primary">Lihat →</a>
    </div>
    <div class="card-modern">
        <i class="fas fa-user-edit" style="font-size:32px;color:#4f46e5"></i>
        <h3>Edit Profil</h3>
        <a href="profil.php" class="btn-primary">Edit →</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>