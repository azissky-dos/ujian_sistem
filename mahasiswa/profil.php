<?php
session_start();
require_once __DIR__ . '/../includes/cek_login.php';
require_once __DIR__ . '/../config/database.php';

if ($_SESSION['role'] != 'mahasiswa') {
    die("Akses ditolak!");
}

$user_id = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id"));
$error = $success = '';

if (isset($_POST['ganti_password'])) {
    $password_lama = md5($_POST['password_lama']);
    $password_baru = md5($_POST['password_baru']);
    $konfirmasi = md5($_POST['konfirmasi']);
    
    if ($user['password'] != $password_lama) {
        $error = "Password lama salah!";
    } elseif ($password_baru != $konfirmasi) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        mysqli_query($conn, "UPDATE users SET password='$password_baru' WHERE id=$user_id");
        $success = "Password berhasil diubah! Silakan login kembali.";
        session_destroy();
        echo "<script>alert('$success'); window.location='../auth/login.php';</script>";
        exit();
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Ganti Password</h1>
    <p class="page-subtitle">Halo, <?= $_SESSION['nama'] ?></p>
</div>

<div class="card-modern" style="max-width:500px">
    <div class="alert info">
        📌 Data lain (nama, NIM, email, kelas) tidak dapat diubah. Hubungi admin/dosen jika ada perubahan data.
    </div>
    
    <?php if($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" readonly>
        </div>
        <div class="form-group">
            <label>NIM</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['nim_nip']) ?>" readonly>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
        </div>
        <hr>
        <div class="form-group">
            <label>Password Lama</label>
            <input type="password" name="password_lama" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password Baru</label>
            <input type="password" name="password_baru" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="konfirmasi" class="form-control" required>
        </div>
        <button type="submit" name="ganti_password" class="btn-primary">Ganti Password</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>