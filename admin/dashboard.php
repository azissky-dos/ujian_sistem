<?php
// admin/dashboard.php
// ======================================================
// DASHBOARD ADMIN
// ======================================================

session_start();
require_once __DIR__ . '/../includes/cek_login.php';
require_once __DIR__ . '/../config/database.php';

// Cek role admin
if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak! Anda bukan admin.");
}

// Hitung statistik
$total_admin_dosen = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role IN ('admin','dosen')"))['total'];
$total_mahasiswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='mahasiswa'"))['total'];
$total_kelas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kelas"))['total'];
$total_mk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM mata_kuliah"))['total'];
$total_soal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM soal"))['total'];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Dashboard Admin</h1>
    <p class="page-subtitle">Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?>! Anda memiliki akses penuh ke seluruh sistem.</p>
</div>

<!-- Statistik -->
<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $total_admin_dosen ?></h3>
            <p>Admin & Dosen</p>
        </div>
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $total_mahasiswa ?></h3>
            <p>Mahasiswa</p>
        </div>
        <div class="stat-icon">
            <i class="fas fa-user-graduate"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $total_kelas ?></h3>
            <p>Kelas</p>
        </div>
        <div class="stat-icon">
            <i class="fas fa-school"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $total_mk ?></h3>
            <p>Mata Kuliah</p>
        </div>
        <div class="stat-icon">
            <i class="fas fa-book"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $total_soal ?></h3>
            <p>Total Soal</p>
        </div>
        <div class="stat-icon">
            <i class="fas fa-question-circle"></i>
        </div>
    </div>
</div>

<!-- Menu Grid -->
<div class="menu-grid">
    <div class="card-modern">
        <i class="fas fa-users" style="font-size: 32px; color: #4f46e5;"></i>
        <h3 style="margin: 16px 0 8px 0;">Kelola Users</h3>
        <p style="color: #64748b;">Tambah/edit/hapus Admin & Dosen</p>
        <a href="users/index.php" class="btn-primary" style="display: inline-block; margin-top: 16px;">Kelola →</a>
    </div>
    <div class="card-modern">
        <i class="fas fa-key" style="font-size: 32px; color: #4f46e5;"></i>
        <h3 style="margin: 16px 0 8px 0;">Reset Password</h3>
        <p style="color: #64748b;">Reset password semua user ke 123456</p>
        <a href="reset_password/index.php" class="btn-primary" style="display: inline-block; margin-top: 16px;">Reset →</a>
    </div>
    <div class="card-modern">
        <i class="fas fa-school" style="font-size: 32px; color: #4f46e5;"></i>
        <h3 style="margin: 16px 0 8px 0;">Kelola Kelas</h3>
        <p style="color: #64748b;">Tambah/edit/hapus kelas</p>
        <a href="kelas/index.php" class="btn-primary" style="display: inline-block; margin-top: 16px;">Kelola →</a>
    </div>
    <div class="card-modern">
        <i class="fas fa-book" style="font-size: 32px; color: #4f46e5;"></i>
        <h3 style="margin: 16px 0 8px 0;">Kelola Mata Kuliah</h3>
        <p style="color: #64748b;">Tambah/edit/hapus mata kuliah per kelas</p>
        <a href="matakuliah/index.php" class="btn-primary" style="display: inline-block; margin-top: 16px;">Kelola →</a>
    </div>
    <div class="card-modern">
        <i class="fas fa-book" style="font-size: 32px; color: #4f46e5;"></i>
        <h3 style="margin: 16px 0 8px 0;">Master Mata Kuliah</h3>
        <p style="color: #64748b;">Kelola daftar master mata kuliah</p>
        <a href="matakuliah_induk/index.php" class="btn-primary" style="display: inline-block; margin-top: 16px;">Kelola →</a>
    </div>
    <div class="card-modern">
        <i class="fas fa-question-circle" style="font-size: 32px; color: #4f46e5;"></i>
        <h3 style="margin: 16px 0 8px 0;">Kelola Soal</h3>
        <p style="color: #64748b;">Lihat semua soal dari dosen</p>
        <a href="soal/index.php" class="btn-primary" style="display: inline-block; margin-top: 16px;">Lihat →</a>
    </div>
    <div class="card-modern">
        <i class="fas fa-chart-line" style="font-size: 32px; color: #4f46e5;"></i>
        <h3 style="margin: 16px 0 8px 0;">Laporan Nilai</h3>
        <p style="color: #64748b;">Rekap nilai seluruh mahasiswa</p>
        <a href="laporan/nilai_all.php" class="btn-primary" style="display: inline-block; margin-top: 16px;">Lihat →</a>
    </div>
    <div class="card-modern">
        <i class="fas fa-database" style="font-size: 32px; color: #4f46e5;"></i>
        <h3 style="margin: 16px 0 8px 0;">Backup & Restore</h3>
        <p style="color: #64748b;">Backup dan restore database</p>
        <a href="backup/index.php" class="btn-primary" style="display: inline-block; margin-top: 16px;">Kelola →</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>