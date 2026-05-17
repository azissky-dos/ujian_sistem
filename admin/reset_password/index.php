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

if (isset($_POST['reset'])) {
    $user_id = $_POST['user_id'];
    mysqli_query($conn, "UPDATE users SET password='" . md5('123456') . "' WHERE id=$user_id");
    $success = "Password berhasil direset ke 123456";
}

$users = mysqli_query($conn, "SELECT id, username, nama_lengkap, role, nim_nip FROM users ORDER BY role, nama_lengkap");

require_once BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Reset Password</h1>
</div>

<?php if(isset($success)): ?>
    <div class="alert success"><?= $success ?></div>
<?php endif; ?>

<div class="card-modern">
    <table class="table-modern">
        <thead><tr><th>Username</th><th>Nama</th><th>Role</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php while($user = mysqli_fetch_assoc($users)): ?>
            <tr>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['nama_lengkap']) ?></td>
                <td><?= $user['role'] ?></td>
                <td>
                    <form method="POST" onsubmit="return confirm('Reset password <?= $user['username'] ?>?')">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <button type="submit" name="reset" class="btn-primary" style="padding:6px 12px">Reset ke 123456</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>