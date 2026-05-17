<?php
// admin/matakuliah_induk/edit.php
// ======================================================
// EDIT MASTER MATA KULIAH (INDUK)
// ======================================================

session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$id = (int)$_GET['id'];
$mk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mata_kuliah_induk WHERE id=$id"));

if (!$mk) {
    header('Location: index.php');
    exit();
}

if (isset($_POST['update'])) {
    $kode_mk = mysqli_real_escape_string($conn, $_POST['kode_mk']);
    $nama_mk = mysqli_real_escape_string($conn, $_POST['nama_mk']);
    
    $query = "UPDATE mata_kuliah_induk SET kode_mk='$kode_mk', nama_mk='$nama_mk' WHERE id=$id";
    mysqli_query($conn, $query);
    header('Location: index.php');
    exit();
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Edit Mata Kuliah Induk</h1>
    <a href="index.php" class="btn-outline">← Kembali</a>
</div>

<div class="card-modern" style="max-width:500px; margin:0 auto;">
    <form method="POST">
        <div class="form-group">
            <label>Kode Mata Kuliah</label>
            <input type="text" name="kode_mk" class="form-control" value="<?= htmlspecialchars($mk['kode_mk']) ?>" required>
        </div>
        <div class="form-group">
            <label>Nama Mata Kuliah</label>
            <input type="text" name="nama_mk" class="form-control" value="<?= htmlspecialchars($mk['nama_mk']) ?>" required>
        </div>
        <button type="submit" name="update" class="btn-primary" style="width:100%">Update</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>