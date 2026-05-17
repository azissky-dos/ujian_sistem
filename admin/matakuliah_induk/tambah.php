<?php
// admin/matakuliah_induk/tambah.php
// ======================================================
// TAMBAH MASTER MATA KULIAH (INDUK)
// ======================================================

session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

if (isset($_POST['simpan'])) {
    $kode_mk = mysqli_real_escape_string($conn, $_POST['kode_mk']);
    $nama_mk = mysqli_real_escape_string($conn, $_POST['nama_mk']);
    
    $query = "INSERT INTO mata_kuliah_induk (kode_mk, nama_mk) VALUES ('$kode_mk', '$nama_mk')";
    
    if (mysqli_query($conn, $query)) {
        header('Location: index.php');
        exit();
    } else {
        $error = "Gagal menambahkan: " . mysqli_error($conn);
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Tambah Mata Kuliah Induk</h1>
    <a href="index.php" class="btn-outline">← Kembali</a>
</div>

<?php if(isset($error)): ?>
    <div class="alert error"><?= $error ?></div>
<?php endif; ?>

<div class="card-modern" style="max-width:500px; margin:0 auto;">
    <form method="POST">
        <div class="form-group">
            <label>Kode Mata Kuliah</label>
            <input type="text" name="kode_mk" class="form-control" placeholder="Contoh: IF101" required>
        </div>
        <div class="form-group">
            <label>Nama Mata Kuliah</label>
            <input type="text" name="nama_mk" class="form-control" placeholder="Contoh: Algoritma dan Pemrograman" required>
        </div>
        <button type="submit" name="simpan" class="btn-primary" style="width:100%">Simpan</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>