<?php
session_start();

// Tentukan BASE_PATH secara manual (AMAN)
$base_path = dirname(__DIR__, 2);  // naik 2 level dari admin/xxx/ ke root
require_once $base_path . '/config/config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

// Cek role
if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

// Koneksi database
require_once BASE_PATH . '/config/database.php';

$id = $_GET['id'];
$mk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mata_kuliah_induk WHERE id=$id"));

if (isset($_POST['update'])) {
    $kode_mk = mysqli_real_escape_string($conn, $_POST['kode_mk']);
    $nama_mk = mysqli_real_escape_string($conn, $_POST['nama_mk']);
    
    $query = "UPDATE mata_kuliah_induk SET kode_mk='$kode_mk', nama_mk='$nama_mk' WHERE id=$id";
    mysqli_query($conn, $query);
    header('Location: ' . BASE_URL . '/admin/matakuliah_induk/index.php');
    exit();
}

include BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Edit Mata Kuliah Induk</h1>
    <a href="<?= BASE_URL ?>/admin/matakuliah_induk/index.php" class="btn-outline">← Kembali</a>
</div>

<div class="card-modern" style="max-width:500px">
    <form method="POST">
        <div class="form-group">
            <label>Kode Mata Kuliah</label>
            <input type="text" name="kode_mk" class="form-control" value="<?= htmlspecialchars($mk['kode_mk']) ?>" required>
        </div>
        <div class="form-group">
            <label>Nama Mata Kuliah</label>
            <input type="text" name="nama_mk" class="form-control" value="<?= htmlspecialchars($mk['nama_mk']) ?>" required>
        </div>
        <button type="submit" name="update" class="btn-primary">Update</button>
    </form>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>