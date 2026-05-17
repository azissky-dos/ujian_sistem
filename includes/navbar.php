<?php
// includes/navbar.php
// ======================================================
// NAVIGATION SIDEBAR - PAKAI PATH RELATIF
// ======================================================

if (!isset($_SESSION['user_id'])) return;
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i>
            <span>Ujian<span class="dot">.</span>id</span>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <!-- MENU ADMIN -->
            <a href="../admin/dashboard.php" class="nav-item">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
            <a href="../admin/users/index.php" class="nav-item">
                <i class="fas fa-users"></i> <span>Kelola Users</span>
            </a>
            <a href="../admin/reset_password/index.php" class="nav-item">
                <i class="fas fa-key"></i> <span>Reset Password</span>
            </a>
            <a href="../admin/kelas/index.php" class="nav-item">
                <i class="fas fa-school"></i> <span>Kelola Kelas</span>
            </a>
            <a href="../admin/matakuliah/index.php" class="nav-item">
                <i class="fas fa-book"></i> <span>Kelola MK</span>
            </a>
            <a href="../admin/matakuliah_induk/index.php" class="nav-item">
                <i class="fas fa-book"></i> <span>Master MK</span>
            </a>
            <a href="../admin/soal/index.php" class="nav-item">
                <i class="fas fa-question-circle"></i> <span>Kelola Soal</span>
            </a>
            <a href="../admin/laporan/nilai_all.php" class="nav-item">
                <i class="fas fa-chart-line"></i> <span>Laporan Nilai</span>
            </a>
            <a href="../admin/backup/index.php" class="nav-item">
                <i class="fas fa-database"></i> <span>Backup & Restore</span>
            </a>
            
        <?php elseif ($_SESSION['role'] == 'dosen'): ?>
            <!-- MENU DOSEN -->
            <a href="../dosen/dashboard.php" class="nav-item">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
            <a href="../dosen/kelas/index.php" class="nav-item">
                <i class="fas fa-school"></i> <span>Kelas Saya</span>
            </a>
            <a href="../dosen/matakuliah/index.php" class="nav-item">
                <i class="fas fa-book"></i> <span>Mata Kuliah</span>
            </a>
            <a href="../dosen/mahasiswa_terdaftar/index.php" class="nav-item">
                <i class="fas fa-user-graduate"></i> <span>Mahasiswa</span>
            </a>
            <a href="../dosen/reset_password/index.php" class="nav-item">
                <i class="fas fa-key"></i> <span>Reset Password Mhs</span>
            </a>
            <a href="../dosen/soal/tambah.php" class="nav-item">
                <i class="fas fa-pen-alt"></i> <span>Buat Soal</span>
            </a>
            <a href="../dosen/laporan/nilai_perkelas.php" class="nav-item">
                <i class="fas fa-chart-line"></i> <span>Laporan Nilai</span>
            </a>
            <a href="../dosen/profil.php" class="nav-item">
                <i class="fas fa-user-edit"></i> <span>Edit Profil</span>
            </a>
            
        <?php elseif ($_SESSION['role'] == 'mahasiswa'): ?>
            <!-- MENU MAHASISWA -->
            <a href="../mahasiswa/dashboard.php" class="nav-item">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
            <a href="../mahasiswa/riwayat/nilai.php" class="nav-item">
                <i class="fas fa-history"></i> <span>Riwayat Nilai</span>
            </a>
            <a href="../mahasiswa/profil.php" class="nav-item">
                <i class="fas fa-key"></i> <span>Ganti Password</span>
            </a>
        <?php endif; ?>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <div>
                <div class="user-name"><?= htmlspecialchars($_SESSION['nama']) ?></div>
                <div class="user-role"><?= $_SESSION['role'] ?></div>
            </div>
        </div>
        <a href="../auth/logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
        </a>
    </div>
</aside>