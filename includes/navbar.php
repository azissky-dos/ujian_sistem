<?php
if (!isset($_SESSION['user_id'])) return;
?>
<aside class="sidebar glass-dark">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i>
            <span>Ujian<span class="dot">.</span>id</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="<?= BASE_URL ?>/admin/users/index.php" class="nav-item"><i class="fas fa-users"></i> Kelola Users</a>
            <a href="<?= BASE_URL ?>/admin/reset_password/index.php" class="nav-item"><i class="fas fa-key"></i> Reset Password</a>
            <a href="<?= BASE_URL ?>/admin/kelas/index.php" class="nav-item"><i class="fas fa-school"></i> Kelola Kelas</a>
            <a href="<?= BASE_URL ?>/admin/matakuliah/index.php" class="nav-item"><i class="fas fa-book"></i> Kelola MK</a>
            <a href="<?= BASE_URL ?>/admin/matakuliah_induk/index.php" class="nav-item"><i class="fas fa-book"></i> Master MK</a>
            <a href="<?= BASE_URL ?>/admin/soal/index.php" class="nav-item"><i class="fas fa-question-circle"></i> Kelola Soal</a>
            <a href="<?= BASE_URL ?>/admin/laporan/nilai_all.php" class="nav-item"><i class="fas fa-chart-line"></i> Laporan Nilai</a>
            <a href="<?= BASE_URL ?>/admin/backup/index.php" class="nav-item"><i class="fas fa-database"></i> Backup & Restore</a>
        <?php elseif ($_SESSION['role'] == 'dosen'): ?>
            <a href="<?= BASE_URL ?>/dosen/dashboard.php" class="nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="<?= BASE_URL ?>/dosen/kelas/index.php" class="nav-item"><i class="fas fa-school"></i> Kelas Saya</a>
            <a href="<?= BASE_URL ?>/dosen/matakuliah/index.php" class="nav-item"><i class="fas fa-book"></i> Mata Kuliah</a>
            <a href="<?= BASE_URL ?>/dosen/mahasiswa_terdaftar/index.php" class="nav-item"><i class="fas fa-user-graduate"></i> Mahasiswa</a>
            <a href="<?= BASE_URL ?>/dosen/reset_password/index.php" class="nav-item"><i class="fas fa-key"></i> Reset Password Mhs</a>
            <a href="<?= BASE_URL ?>/dosen/soal/tambah.php" class="nav-item"><i class="fas fa-pen-alt"></i> Buat Soal</a>
            <a href="<?= BASE_URL ?>/dosen/laporan/nilai_perkelas.php" class="nav-item"><i class="fas fa-chart-line"></i> Laporan Nilai</a>
            <a href="<?= BASE_URL ?>/dosen/profil.php" class="nav-item"><i class="fas fa-user-edit"></i> Edit Profil</a>
        <?php elseif ($_SESSION['role'] == 'mahasiswa'): ?>
            <a href="<?= BASE_URL ?>/mahasiswa/dashboard.php" class="nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="<?= BASE_URL ?>/mahasiswa/riwayat/nilai.php" class="nav-item"><i class="fas fa-history"></i> Riwayat Nilai</a>
            <a href="<?= BASE_URL ?>/mahasiswa/profil.php" class="nav-item"><i class="fas fa-key"></i> Ganti Password</a>
        <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <div>
                <div class="user-name"><?= $_SESSION['nama'] ?></div>
                <div class="user-role"><?= $_SESSION['role'] ?></div>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</aside>