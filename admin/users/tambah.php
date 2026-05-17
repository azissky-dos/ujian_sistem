<?php
// admin/users/tambah.php
// ======================================================
// TAMBAH USER ADMIN / DOSEN
// ======================================================

session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$error = '';
$success = '';

if (isset($_POST['simpan'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $role = $_POST['role'];
    
    // Cek username sudah ada
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $query = "INSERT INTO users (username, password, role, nama_lengkap, email, nim_nip) 
                  VALUES ('$username', '$password', '$role', '$nama', '$email', '$nip')";
        if (mysqli_query($conn, $query)) {
            $success = "User berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan user: " . mysqli_error($conn);
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Tambah User</h1>
    <a href="index.php" class="btn-outline">← Kembali</a>
</div>

<?php if($error): ?>
    <div class="alert error"><?= $error ?></div>
<?php endif; ?>

<?php if($success): ?>
    <div class="alert success"><?= $success ?></div>
<?php endif; ?>

<div class="card-modern" style="max-width: 500px;">
    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="form-group">
            <label>NIP (untuk Dosen)</label>
            <input type="text" name="nip" class="form-control">
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control" required>
                <option value="admin">Admin</option>
                <option value="dosen">Dosen</option>
            </select>
        </div>
        <button type="submit" name="simpan" class="btn-primary">Simpan</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>