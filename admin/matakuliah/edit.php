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

$id = $_GET['id'];
$mk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mata_kuliah WHERE id=$id"));
$kelas_list = mysqli_query($conn, "SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas");
$dosen_list = mysqli_query($conn, "SELECT id, nama_lengkap FROM users WHERE role='dosen' ORDER BY nama_lengkap");

if (isset($_POST['update'])) {
    $kelas_id = $_POST['kelas_id'];
    $dosen_id = !empty($_POST['dosen_id']) ? $_POST['dosen_id'] : 'NULL';
    $durasi_ujian = $_POST['durasi_ujian'];
    $is_latihan = isset($_POST['is_latihan']) ? 1 : 0;
    
    mysqli_query($conn, "UPDATE mata_kuliah SET kelas_id='$kelas_id', dosen_id=$dosen_id, durasi_ujian='$durasi_ujian', is_latihan='$is_latihan' WHERE id='$id'");
    header('Location: ' . BASE_URL . '/admin/matakuliah/index.php');
    exit();
}

require_once BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Edit Mata Kuliah</h1>
    <a href="<?= BASE_URL ?>/admin/matakuliah/index.php" class="btn-outline">← Kembali</a>
</div>

<div class="card-modern" style="max-width:550px">
    <form method="POST">
        <div class="form-group"><label>Kode MK</label><input type="text" class="form-control" value="<?= htmlspecialchars($mk['kode_mk']) ?>" readonly></div>
        <div class="form-group"><label>Nama MK</label><input type="text" class="form-control" value="<?= htmlspecialchars($mk['nama_mk']) ?>" readonly></div>
        <div class="form-group">
            <label>Kelas</label>
            <select name="kelas_id" class="form-control" required>
                <?php while($k = mysqli_fetch_assoc($kelas_list)): ?>
                    <option value="<?= $k['id'] ?>" <?= $mk['kelas_id']==$k['id']?'selected':'' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Dosen</label>
            <select name="dosen_id" class="form-control">
                <option value="">-- Pilih --</option>
                <?php while($d = mysqli_fetch_assoc($dosen_list)): ?>
                    <option value="<?= $d['id'] ?>" <?= $mk['dosen_id']==$d['id']?'selected':'' ?>><?= htmlspecialchars($d['nama_lengkap']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group"><label>Durasi (menit)</label><input type="number" name="durasi_ujian" class="form-control" value="<?= $mk['durasi_ujian'] ?>" required></div>
        <div class="form-group"><label><input type="checkbox" name="is_latihan" <?= $mk['is_latihan'] ? 'checked' : '' ?>> Mode Latihan</label></div>
        <button type="submit" name="update" class="btn-primary">Update</button>
    </form>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>