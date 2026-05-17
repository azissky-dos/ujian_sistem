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
            <a href="../admin/dashboard.php" class="nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="../admin/users/index.php" class="nav-item"><i class="fas fa-users"></i> Kelola Users</a>
            <a href="../admin/reset_password/index.php" class="nav-item"><i class="fas fa-key"></i> Reset Password</a>
            <a href="../admin/kelas/index.php" class="nav-item"><i class="fas fa-school"></i> Kelola Kelas</a>
            <a href="../admin/matakuliah/index.php" class="nav-item"><i class="fas fa-book"></i> Kelola MK</a>
            <a href="../admin/matakuliah_induk/index.php" class="nav-item"><i class="fas fa-book"></i> Master MK</a>
            <a href="../admin/soal/index.php" class="nav-item"><i class="fas fa-question-circle"></i> Kelola Soal</a>
            <a href="../admin/laporan/nilai_all.php" class="nav-item"><i class="fas fa-chart-line"></i> Laporan Nilai</a>
            <a href="../admin/backup/index.php" class="nav-item"><i class="fas fa-database"></i> Backup & Restore</a>
        <?php elseif ($_SESSION['role'] == 'dosen'): ?>
            <a href="../dosen/dashboard.php" class="nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="../dosen/kelas/index.php" class="nav-item"><i class="fas fa-school"></i> Kelas Saya</a>
            <a href="../dosen/matakuliah/index.php" class="nav-item"><i class="fas fa-book"></i> Mata Kuliah</a>
            <a href="../dosen/mahasiswa_terdaftar/index.php" class="nav-item"><i class="fas fa-user-graduate"></i> Mahasiswa</a>
            <a href="../dosen/reset_password/index.php" class="nav-item"><i class="fas fa-key"></i> Reset Password Mhs</a>
            <a href="../dosen/soal/tambah.php" class="nav-item"><i class="fas fa-pen-alt"></i> Buat Soal</a>
            <a href="../dosen/laporan/nilai_perkelas.php" class="nav-item"><i class="fas fa-chart-line"></i> Laporan Nilai</a>
            <a href="../dosen/profil.php" class="nav-item"><i class="fas fa-user-edit"></i> Edit Profil</a>
        <?php elseif ($_SESSION['role'] == 'mahasiswa'): ?>
            <a href="../mahasiswa/dashboard.php" class="nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="../mahasiswa/riwayat/nilai.php" class="nav-item"><i class="fas fa-history"></i> Riwayat Nilai</a>
            <a href="../mahasiswa/profil.php" class="nav-item"><i class="fas fa-key"></i> Ganti Password</a>
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
        <!-- PERBAIKAN: Pakai path relatif -->
        <a href="../auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</aside>