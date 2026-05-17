<?php
// admin/reset_password/index.php
// ======================================================
// RESET PASSWORD USER (ADMIN, DOSEN, MAHASISWA)
// ======================================================

session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$success = '';
$error = '';

// Proses reset password
if (isset($_POST['reset'])) {
    $user_id = (int)$_POST['user_id'];
    $new_password = md5('123456');
    
    if (mysqli_query($conn, "UPDATE users SET password='$new_password' WHERE id=$user_id")) {
        $success = "Password berhasil direset ke 123456";
    } else {
        $error = "Gagal mereset password!";
    }
}

// Ambil semua user
$users = mysqli_query($conn, "SELECT id, username, nama_lengkap, role, nim_nip FROM users ORDER BY role, nama_lengkap");

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Reset Password User</h1>
    <p class="page-subtitle">Reset password user (admin, dosen, mahasiswa) menjadi <strong>123456</strong></p>
</div>

<?php if($success): ?>
    <div class="alert success"><?= $success ?></div>
<?php endif; ?>

<?php if($error): ?>
    <div class="alert error"><?= $error ?></div>
<?php endif; ?>

<div class="card-modern">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Role</th>
                <th>NIM/NIP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($user = mysqli_fetch_assoc($users)): ?>
            <tr>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['nama_lengkap']) ?></td>
                <td>
                    <span class="badge <?= $user['role']=='admin'?'badge-danger':($user['role']=='dosen'?'badge-warning':'badge-success') ?>">
                        <?= $user['role'] ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($user['nim_nip'] ?? '-') ?></td>
                <td>
                    <form method="POST" onsubmit="return confirm('Reset password <?= $user['username'] ?> menjadi 123456?')">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <button type="submit" name="reset" class="btn-primary" style="padding:6px 12px">Reset ke 123456</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>