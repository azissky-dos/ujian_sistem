<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

require_once BASE_PATH . '/config/database.php';

$id = $_GET['id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $id"));

if ($user['role'] == 'mahasiswa') {
    header('Location: ' . BASE_URL . '/admin/users/index.php');
    exit();
}

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $role = $_POST['role'];
    
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        mysqli_query($conn, "UPDATE users SET password='$password', nama_lengkap='$nama', email='$email', nim_nip='$nip', role='$role' WHERE id=$id");
    } else {
        mysqli_query($conn, "UPDATE users SET nama_lengkap='$nama', email='$email', nim_nip='$nip', role='$role' WHERE id=$id");
    }
    header('Location: ' . BASE_URL . '/admin/users/index.php');
    exit();
}

require_once BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Edit User: <?= $user['username'] ?></h1>
    <a href="<?= BASE_URL ?>/admin/users/index.php" class="btn-outline">← Kembali</a>
</div>

<div class="card-modern" style="max-width:500px">
    <form method="POST">
        <div class="form-group"><label>Username</label><input type="text" value="<?= $user['username'] ?>" class="form-control" disabled></div>
        <div class="form-group"><label>Password (kosongkan jika tidak diubah)</label><input type="password" name="password" class="form-control"></div>
        <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama_lengkap" class="form-control" value="<?= $user['nama_lengkap'] ?>" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="<?= $user['email'] ?>"></div>
        <div class="form-group"><label>NIP</label><input type="text" name="nip" class="form-control" value="<?= $user['nim_nip'] ?>"></div>
        <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control">
                <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
                <option value="dosen" <?= $user['role']=='dosen'?'selected':'' ?>>Dosen</option>
            </select>
        </div>
        <button type="submit" name="update" class="btn-primary">Update</button>
    </form>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>