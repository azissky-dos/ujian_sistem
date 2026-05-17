<?php
// admin/users/edit.php
// ======================================================
// EDIT USER ADMIN / DOSEN
// ======================================================

session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$id = (int)$_GET['id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $id"));

if (!$user || $user['role'] == 'mahasiswa') {
    header('Location: index.php');
    exit();
}

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $role = $_POST['role'];
    
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        $query = "UPDATE users SET password='$password', nama_lengkap='$nama', email='$email', nim_nip='$nip', role='$role' WHERE id=$id";
    } else {
        $query = "UPDATE users SET nama_lengkap='$nama', email='$email', nim_nip='$nip', role='$role' WHERE id=$id";
    }
    mysqli_query($conn, $query);
    header('Location: index.php');
    exit();
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Edit User: <?= htmlspecialchars($user['username']) ?></h1>
    <a href="index.php" class="btn-outline">← Kembali</a>
</div>

<div class="card-modern" style="max-width: 500px;">
    <form method="POST">
        <div class="form-group">
            <label>Username (tidak bisa diubah)</label>
            <input type="text" value="<?= htmlspecialchars($user['username']) ?>" class="form-control" disabled>
        </div>
        <div class="form-group">
            <label>Password (kosongkan jika tidak diubah)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>">
        </div>
        <div class="form-group">
            <label>NIP</label>
            <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($user['nim_nip']) ?>">
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control">
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="dosen" <?= $user['role'] == 'dosen' ? 'selected' : '' ?>>Dosen</option>
            </select>
        </div>
        <button type="submit" name="update" class="btn-primary">Update</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>