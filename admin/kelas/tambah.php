<?php
// admin/kelas/tambah.php
// ======================================================
// TAMBAH KELAS BARU
// ======================================================

session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

// Ambil daftar dosen untuk dropdown
$dosen_list = mysqli_query($conn, "SELECT id, nama_lengkap FROM users WHERE role='dosen' ORDER BY nama_lengkap");

if (isset($_POST['simpan'])) {
    $nama_kelas = mysqli_real_escape_string($conn, $_POST['nama_kelas']);
    $tahun_ajaran = mysqli_real_escape_string($conn, $_POST['tahun_ajaran']);
    $dosen_id = !empty($_POST['dosen_id']) ? $_POST['dosen_id'] : 'NULL';
    
    $query = "INSERT INTO kelas (nama_kelas, tahun_ajaran, dosen_id) VALUES ('$nama_kelas', '$tahun_ajaran', $dosen_id)";
    
    if (mysqli_query($conn, $query)) {
        header('Location: index.php');
        exit();
    } else {
        $error = "Gagal menambahkan kelas: " . mysqli_error($conn);
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Tambah Kelas</h1>
    <a href="index.php" class="btn-outline">← Kembali</a>
</div>

<?php if(isset($error)): ?>
    <div class="alert error"><?= $error ?></div>
<?php endif; ?>

<div class="card-modern" style="max-width:500px">
    <form method="POST">
        <div class="form-group">
            <label>Nama Kelas</label>
            <input type="text" name="nama_kelas" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Tahun Ajaran</label>
            <input type="text" name="tahun_ajaran" class="form-control" placeholder="Contoh: 2024/2025" required>
        </div>
        <div class="form-group">
            <label>Dosen Pengajar (opsional)</label>
            <select name="dosen_id" class="form-control">
                <option value="">-- Pilih Dosen --</option>
                <?php while($d = mysqli_fetch_assoc($dosen_list)): ?>
                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nama_lengkap']) ?></option>
                <?php endwhile; ?>
            </select>
            <small style="color: #64748b;">Dosen dapat dipilih nanti melalui edit kelas</small>
        </div>
        <button type="submit" name="simpan" class="btn-primary">Simpan</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>