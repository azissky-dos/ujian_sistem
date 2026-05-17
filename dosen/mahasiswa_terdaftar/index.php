<?php
session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'dosen') {
    die("Akses ditolak!");
}

$dosen_id = $_SESSION['user_id'];
$kelas_id = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

$kelas_list = mysqli_query($conn, "SELECT id, nama_kelas FROM kelas WHERE dosen_id=$dosen_id ORDER BY nama_kelas");

if ($kelas_id > 0) {
    // PERBAIKAN QUERY - Fix ONLY_FULL_GROUP_BY
    $query = "
        SELECT 
            u.id, 
            u.username, 
            u.nama_lengkap, 
            u.email, 
            u.nim_nip, 
            e.tanggal_daftar,
            (SELECT GROUP_CONCAT(DISTINCT mki.nama_mk SEPARATOR ', ') 
             FROM enrollment_mk em2 
             JOIN mata_kuliah_induk mki ON em2.mk_induk_id = mki.id 
             WHERE em2.enrollment_id = e.id AND em2.status = 'active') as mk_terpilih
        FROM users u
        JOIN enrollments e ON u.id = e.mahasiswa_id
        WHERE e.kelas_id = $kelas_id AND u.role = 'mahasiswa'
    ";
    
    if (!empty($search)) {
        $query .= " AND (u.nim_nip LIKE '%$search%' 
                        OR u.nama_lengkap LIKE '%$search%' 
                        OR u.username LIKE '%$search%'
                        OR u.email LIKE '%$search%')";
    }
    
    $query .= " ORDER BY u.nama_lengkap";
    $mahasiswa = mysqli_query($conn, $query);
}

// Proses reset password
if (isset($_POST['reset_password'])) {
    $user_id = $_POST['user_id'];
    $new_password = md5('123456');
    mysqli_query($conn, "UPDATE users SET password='$new_password' WHERE id=$user_id");
    $success = "Password mahasiswa berhasil direset ke 123456";
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Mahasiswa Terdaftar</h1>
    <p class="page-subtitle">Lihat mahasiswa dan mata kuliah yang dipilih</p>
</div>

<?php if(isset($success)): ?>
    <div class="alert success"><?= $success ?></div>
<?php endif; ?>

<div class="card-modern">
    <div class="form-group">
        <label>Pilih Kelas</label>
        <select id="kelas_select" class="form-control" style="max-width:300px" onchange="window.location.href='?kelas_id='+this.value">
            <option value="">-- Pilih Kelas --</option>
            <?php while($k = mysqli_fetch_assoc($kelas_list)): ?>
                <option value="<?= $k['id'] ?>" <?= $kelas_id == $k['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($k['nama_kelas']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    
    <?php if($kelas_id > 0): ?>
        <form method="GET" style="margin-top: 16px; display: flex; gap: 10px;">
            <input type="hidden" name="kelas_id" value="<?= $kelas_id ?>">
            <input type="text" name="search" class="form-control" placeholder="Cari NIM, nama, username, atau email..." 
                   value="<?= htmlspecialchars($search) ?>" style="max-width: 350px;">
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Cari</button>
            <?php if(!empty($search)): ?>
                <a href="?kelas_id=<?= $kelas_id ?>" class="btn-outline"><i class="fas fa-times"></i> Reset</a>
            <?php endif; ?>
        </form>
        
        <table class="table-modern" style="margin-top:20px">
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Mata Kuliah Dipilih</th>
                    <th>Tanggal Daftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($mahasiswa) && mysqli_num_rows($mahasiswa) > 0): ?>
                    <?php while($m = mysqli_fetch_assoc($mahasiswa)): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['nim_nip']) ?></td>
                        <td><?= htmlspecialchars($m['nama_lengkap']) ?></td>
                        <td><?= htmlspecialchars($m['username']) ?></td>
                        <td><?= htmlspecialchars($m['email']) ?></td>
                        <td style="max-width:250px">
                            <span class="badge" style="background:#e0e7ff; color:#4338ca; padding:4px 12px; border-radius:20px; display:inline-block; max-width:100%; overflow-x:auto;">
                                <?= htmlspecialchars($m['mk_terpilih'] ?? '-') ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($m['tanggal_daftar'])) ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Reset password <?= $m['nama_lengkap'] ?> ke 123456?')">
                                <input type="hidden" name="user_id" value="<?= $m['id'] ?>">
                                <button type="submit" name="reset_password" class="btn-primary" style="padding:4px 10px;font-size:12px"><i class="fas fa-key"></i> Reset</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center">
                            <?= !empty($search) ? "Tidak ada mahasiswa yang cocok dengan '$search'" : "Belum ada mahasiswa terdaftar di kelas ini" ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>