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

$dosen_list = mysqli_query($conn, "SELECT id, nama_lengkap FROM users WHERE role='dosen' ORDER BY nama_lengkap");

if (isset($_POST['simpan'])) {
    $nama_kelas = mysqli_real_escape_string($conn, $_POST['nama_kelas']);
    $tahun_ajaran = mysqli_real_escape_string($conn, $_POST['tahun_ajaran']);
    $dosen_id = $_POST['dosen_id'] ?: 'NULL';
    
    mysqli_query($conn, "INSERT INTO kelas (nama_kelas, tahun_ajaran, dosen_id) VALUES ('$nama_kelas', '$tahun_ajaran', $dosen_id)");
    header('Location: ' . BASE_URL . '/admin/kelas/index.php');
    exit();
}

require_once BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Tambah Kelas</h1>
    <a href="<?= BASE_URL ?>/admin/kelas/index.php" class="btn-outline">← Kembali</a>
</div>

<div class="card-modern" style="max-width:500px">
    <form method="POST">
        <div class="form-group"><label>Nama Kelas</label><input type="text" name="nama_kelas" class="form-control" required></div>
        <div class="form-group"><label>Tahun Ajaran</label><input type="text" name="tahun_ajaran" class="form-control" placeholder="2024/2025" required></div>
        <div class="form-group">
            <label>Dosen</label>
            <select name="dosen_id" class="form-control">
                <option value="">-- Pilih Dosen --</option>
                <?php while($d = mysqli_fetch_assoc($dosen_list)): ?>
                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nama_lengkap']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" name="simpan" class="btn-primary">Simpan</button>
    </form>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>