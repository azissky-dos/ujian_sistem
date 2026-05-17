<?php
session_start();
include __DIR__ . '/../config/config.php';
include BASE_PATH . '/includes/cek_login.php';
include BASE_PATH . '/config/database.php';

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

if (isset($_POST['simpan'])) {
    $kode_mk = mysqli_real_escape_string($conn, $_POST['kode_mk']);
    $nama_mk = mysqli_real_escape_string($conn, $_POST['nama_mk']);
    
    $query = "INSERT INTO mata_kuliah_induk (kode_mk, nama_mk) VALUES ('$kode_mk', '$nama_mk')";
    mysqli_query($conn, $query);
    header('Location: ' . BASE_URL . '/admin/matakuliah_induk/index.php');
    exit();
}

include BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Tambah Mata Kuliah Induk</h1>
    <a href="<?= BASE_URL ?>/admin/matakuliah_induk/index.php" class="btn-outline">← Kembali</a>
</div>

<div class="card-modern" style="max-width:500px">
    <form method="POST">
        <div class="form-group">
            <label>Kode Mata Kuliah</label>
            <input type="text" name="kode_mk" class="form-control" placeholder="Contoh: IF101" required>
        </div>
        <div class="form-group">
            <label>Nama Mata Kuliah</label>
            <input type="text" name="nama_mk" class="form-control" placeholder="Contoh: Algoritma dan Pemrograman" required>
        </div>
        <button type="submit" name="simpan" class="btn-primary">Simpan</button>
    </form>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>