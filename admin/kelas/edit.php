<?php
session_start();
include '../../includes/cek_login.php';
include '../../config/database.php';

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$id = $_GET['id'];
$kelas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kelas WHERE id=$id"));
$dosen_list = mysqli_query($conn, "SELECT id, nama_lengkap FROM users WHERE role='dosen' ORDER BY nama_lengkap");

if (isset($_POST['update'])) {
    $nama_kelas = mysqli_real_escape_string($conn, $_POST['nama_kelas']);
    $tahun_ajaran = mysqli_real_escape_string($conn, $_POST['tahun_ajaran']);
    $dosen_id = $_POST['dosen_id'] ?: 'NULL';
    
    $query = "UPDATE kelas SET nama_kelas='$nama_kelas', tahun_ajaran='$tahun_ajaran', dosen_id=$dosen_id WHERE id=$id";
    mysqli_query($conn, $query);
    header('Location: index.php');
    exit();
}

include '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Edit Kelas</h1>
    <a href="index.php" class="btn-outline">← Kembali</a>
</div>

<div class="card-modern" style="max-width:500px">
    <form method="POST">
        <div class="form-group">
            <label>Nama Kelas</label>
            <input type="text" name="nama_kelas" class="form-control" value="<?= htmlspecialchars($kelas['nama_kelas']) ?>" required>
        </div>
        <div class="form-group">
            <label>Tahun Ajaran</label>
            <input type="text" name="tahun_ajaran" class="form-control" value="<?= htmlspecialchars($kelas['tahun_ajaran']) ?>" required>
        </div>
        <div class="form-group">
            <label>Dosen Pengajar</label>
            <select name="dosen_id" class="form-control">
                <option value="">-- Pilih Dosen --</option>
                <?php while($d = mysqli_fetch_assoc($dosen_list)): ?>
                    <option value="<?= $d['id'] ?>" <?= $kelas['dosen_id']==$d['id']?'selected':'' ?>>
                        <?= htmlspecialchars($d['nama_lengkap']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" name="update" class="btn-primary">Update</button>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>