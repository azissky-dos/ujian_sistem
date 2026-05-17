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
$mk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mata_kuliah_induk WHERE id=$id"));

if (isset($_POST['update'])) {
    $kode_mk = mysqli_real_escape_string($conn, $_POST['kode_mk']);
    $nama_mk = mysqli_real_escape_string($conn, $_POST['nama_mk']);
    
    mysqli_query($conn, "UPDATE mata_kuliah_induk SET kode_mk='$kode_mk', nama_mk='$nama_mk' WHERE id=$id");
    header('Location: ' . BASE_URL . '/admin/matakuliah_induk/index.php');
    exit();
}

require_once BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Edit Master MK</h1>
    <a href="<?= BASE_URL ?>/admin/matakuliah_induk/index.php" class="btn-outline">← Kembali</a>
</div>

<div class="card-modern" style="max-width:500px">
    <form method="POST">
        <div class="form-group"><label>Kode MK</label><input type="text" name="kode_mk" class="form-control" value="<?= htmlspecialchars($mk['kode_mk']) ?>" required></div>
        <div class="form-group"><label>Nama MK</label><input type="text" name="nama_mk" class="form-control" value="<?= htmlspecialchars($mk['nama_mk']) ?>" required></div>
        <button type="submit" name="update" class="btn-primary">Update</button>
    </form>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>