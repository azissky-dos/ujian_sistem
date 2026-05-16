<?php
session_start();
include '../../includes/cek_login.php';
include '../../config/database.php';

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

if (isset($_POST['reset'])) {
    $user_id = $_POST['user_id'];
    $new_password = md5('123456');
    mysqli_query($conn, "UPDATE users SET password='$new_password' WHERE id=$user_id");
    $success = "Password berhasil direset ke 123456";
}

$users = mysqli_query($conn, "SELECT id, username, nama_lengkap, role, nim_nip FROM users ORDER BY role, nama_lengkap");

include '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Reset Password User</h1>
    <p class="page-subtitle">Admin dapat reset password siapa saja (admin, dosen, mahasiswa)</p>
</div>

<?php if(isset($success)): ?>
    <div class="alert success"><?= $success ?></div>
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
                    <form method="POST" onsubmit="return confirm('Reset password <?= $user['username'] ?> ke 123456?')">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <button type="submit" name="reset" class="btn-primary" style="padding:6px 12px">Reset ke 123456</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <div class="alert info" style="margin-top:16px">
        <i class="fas fa-info-circle"></i> Password akan direset menjadi <strong>123456</strong>. User bisa mengganti sendiri setelah login.
    </div>
</div>

<?php include '../../includes/footer.php'; ?>