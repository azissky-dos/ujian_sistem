<?php
session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'dosen') {
    die("Akses ditolak!");
}

$dosen_id = $_SESSION['user_id'];

if (isset($_POST['reset'])) {
    $user_id = $_POST['user_id'];
    $new_password = md5('123456');
    mysqli_query($conn, "UPDATE users SET password='$new_password' WHERE id=$user_id");
    $success = "Password mahasiswa berhasil direset ke 123456";
}

$mahasiswa = mysqli_query($conn, "
    SELECT DISTINCT u.id, u.username, u.nama_lengkap, u.nim_nip, k.nama_kelas
    FROM users u
    JOIN enrollments e ON u.id = e.mahasiswa_id
    JOIN kelas k ON e.kelas_id = k.id
    WHERE k.dosen_id = $dosen_id AND u.role = 'mahasiswa'
    ORDER BY k.nama_kelas, u.nama_lengkap
");

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Reset Password Mahasiswa</h1>
    <p class="page-subtitle">Dosen dapat reset password mahasiswa di kelas yang Anda ajar</p>
</div>

<?php if(isset($success)): ?>
    <div class="alert success"><?= $success ?></div>
<?php endif; ?>

<div class="card-modern">
    <table class="table-modern">
        <thead><tr><th>NIM</th><th>Nama Mahasiswa</th><th>Kelas</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php if(mysqli_num_rows($mahasiswa) > 0): ?>
                <?php while($m = mysqli_fetch_assoc($mahasiswa)): ?>
                <tr>
                    <td><?= htmlspecialchars($m['nim_nip']) ?></td>
                    <td><?= htmlspecialchars($m['nama_lengkap']) ?></td>
                    <td><?= htmlspecialchars($m['nama_kelas']) ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Reset password <?= $m['nama_lengkap'] ?> ke 123456?')">
                            <input type="hidden" name="user_id" value="<?= $m['id'] ?>">
                            <button type="submit" name="reset" class="btn-primary" style="padding:6px 12px">Reset ke 123456</button>
                        </form>
                     </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center">Belum ada mahasiswa terdaftar di kelas Anda</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>