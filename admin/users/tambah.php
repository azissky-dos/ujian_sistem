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

if (isset($_POST['simpan'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $role = $_POST['role'];
    
    $query = "INSERT INTO users (username, password, role, nama_lengkap, email, nim_nip) 
              VALUES ('$username', '$password', '$role', '$nama', '$email', '$nip')";
    mysqli_query($conn, $query);
    header('Location: ' . BASE_URL . '/admin/users/index.php');
    exit();
}

include BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Tambah User</h1>
    <a href="<?= BASE_URL ?>/admin/users/index.php" class="btn-outline">← Kembali</a>
</div>

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
            <small style="color: #64748b;">Mahasiswa registrasi mandiri</small>
        </div>
        <button type="submit" name="simpan" class="btn-primary">Simpan</button>
    </form>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>