<?php
session_start();
require_once __DIR__ . '/../includes/cek_login.php';
require_once __DIR__ . '/../config/database.php';

if ($_SESSION['role'] != 'dosen') {
    die("Akses ditolak!");
}

$user_id = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id"));
$error = $success = '';

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    if (!empty($_POST['password_lama']) && !empty($_POST['password_baru'])) {
        $password_lama = md5($_POST['password_lama']);
        $password_baru = md5($_POST['password_baru']);
        if ($user['password'] != $password_lama) {
            $error = "Password lama salah!";
        } else {
            mysqli_query($conn, "UPDATE users SET nama_lengkap='$nama', email='$email', password='$password_baru' WHERE id=$user_id");
            $success = "Profil dan password berhasil diupdate!";
        }
    } else {
        mysqli_query($conn, "UPDATE users SET nama_lengkap='$nama', email='$email' WHERE id=$user_id");
        $success = "Profil berhasil diupdate!";
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Edit Profil Dosen</h1>
</div>

<?php if($error): ?>
    <div class="alert error"><?= $error ?></div>
<?php endif; ?>
<?php if($success): ?>
    <div class="alert success"><?= $success ?></div>
<?php endif; ?>

<div class="card-modern" style="max-width:500px">
    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" readonly>
        </div>
        <div class="form-group">
            <label>NIP</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['nim_nip']) ?>" readonly>
        </div>
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>">
        </div>
        <hr>
        <h4>Ganti Password (opsional)</h4>
        <div class="form-group">
            <label>Password Lama</label>
            <input type="password" name="password_lama" class="form-control">
        </div>
        <div class="form-group">
            <label>Password Baru</label>
            <input type="password" name="password_baru" class="form-control">
        </div>
        <button type="submit" name="update" class="btn-primary">Simpan Perubahan</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>