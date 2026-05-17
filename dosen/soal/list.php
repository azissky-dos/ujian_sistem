<?php
session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'dosen') {
    die("Akses ditolak!");
}

$dosen_id = $_SESSION['user_id'];
$mk_induk_id = isset($_GET['mk_induk_id']) ? (int)$_GET['mk_induk_id'] : 0;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

$mk_induk_list = mysqli_query($conn, "
    SELECT DISTINCT mki.id, mki.kode_mk, mki.nama_mk
    FROM mata_kuliah_induk mki
    JOIN mata_kuliah mk ON mk.mk_induk_id = mki.id
    JOIN kelas k ON mk.kelas_id = k.id
    WHERE k.dosen_id = $dosen_id
    ORDER BY mki.kode_mk
");

if ($mk_induk_id > 0) {
    $mk_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mata_kuliah_induk WHERE id=$mk_induk_id"));
    
    $query = "SELECT * FROM soal WHERE mk_induk_id = $mk_induk_id";
    if (!empty($search)) {
        $query .= " AND (teks_soal LIKE '%$search%' OR kunci_jawaban LIKE '%$search%' OR tipe_soal LIKE '%$search%')";
    }
    $query .= " ORDER BY id";
    $soal_list = mysqli_query($conn, $query);
}

if (isset($_GET['hapus_soal'])) {
    $soal_id = $_GET['hapus_soal'];
    mysqli_query($conn, "DELETE FROM soal WHERE id=$soal_id");
    header("Location: list.php?mk_induk_id=$mk_induk_id");
    exit();
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Daftar Soal</h1>
    <p class="page-subtitle">Soal akan tersedia untuk semua kelas yang memiliki MK ini</p>
</div>

<div class="card-modern">
    <div class="form-group">
        <label>Pilih Mata Kuliah</label>
        <select id="mk_select" class="form-control" style="max-width:400px" onchange="window.location.href='?mk_induk_id='+this.value">
            <option value="">-- Pilih Mata Kuliah --</option>
            <?php while($mk = mysqli_fetch_assoc($mk_induk_list)): ?>
                <option value="<?= $mk['id'] ?>" <?= $mk_induk_id == $mk['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($mk['kode_mk']) ?> - <?= htmlspecialchars($mk['nama_mk']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    
    <?php if($mk_induk_id > 0 && isset($mk_info)): ?>
        <form method="GET" style="margin-top: 16px; display: flex; gap: 10px;">
            <input type="hidden" name="mk_induk_id" value="<?= $mk_induk_id ?>">
            <input type="text" name="search" class="form-control" placeholder="Cari teks soal atau kunci jawaban..." 
                   value="<?= htmlspecialchars($search) ?>" style="max-width: 350px;">
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Cari</button>
            <?php if(!empty($search)): ?>
                <a href="?mk_induk_id=<?= $mk_induk_id ?>" class="btn-outline"><i class="fas fa-times"></i> Reset</a>
            <?php endif; ?>
        </form>
        
        <?php $total_soal = isset($soal_list) ? mysqli_num_rows($soal_list) : 0; ?>
        <div class="alert info" style="margin:16px 0">
            <strong><?= htmlspecialchars($mk_info['kode_mk']) ?> - <?= htmlspecialchars($mk_info['nama_mk']) ?></strong><br>
            Minimal soal untuk pengacakan ujian: <strong>50 soal</strong> | Saat ini: <strong><?= $total_soal ?> soal</strong>
            <?php if($total_soal < 50): ?>
                <span style="color:#dc2626"> ⚠️ Masih kurang <?= 50 - $total_soal ?> soal lagi!</span>
            <?php else: ?>
                <span style="color:#10b981"> ✅ Sudah cukup untuk pengacakan ujian</span>
            <?php endif; ?>
        </div>
        
        <table class="table-modern">
            <thead><tr><th>ID</th><th>Tipe Soal</th><th>Soal & Pilihan</th><th>Kunci Jawaban</th><th>Bobot</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if($total_soal > 0): ?>
                    <?php while($soal = mysqli_fetch_assoc($soal_list)): ?>
                    <tr>
                        <td><?= $soal['id'] ?></td>
                        <td><?= $soal['tipe_soal'] ?></td>
                        <td style="max-width: 400px;">
                            <strong><?= htmlspecialchars(substr($soal['teks_soal'], 0, 80)) ?>...</strong>
                            <?php if($soal['tipe_soal'] == 'pg'): ?>
                                <div style="margin-top: 8px; font-size: 12px; color: #475569;">
                                    A. <?= htmlspecialchars(substr($soal['pilihan_A'] ?? '', 0, 40)) ?><br>
                                    B. <?= htmlspecialchars(substr($soal['pilihan_B'] ?? '', 0, 40)) ?><br>
                                    C. <?= htmlspecialchars(substr($soal['pilihan_C'] ?? '', 0, 40)) ?><br>
                                    D. <?= htmlspecialchars(substr($soal['pilihan_D'] ?? '', 0, 40)) ?><br>
                                    E. <?= htmlspecialchars(substr($soal['pilihan_E'] ?? '', 0, 40)) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars(substr($soal['kunci_jawaban'], 0, 50)) ?>...</td>
                        <td><?= $soal['bobot'] ?></td>
                        <td>
                            <a href="?mk_induk_id=<?= $mk_induk_id ?>&search=<?= urlencode($search) ?>&hapus_soal=<?= $soal['id'] ?>" 
                               class="btn-danger" style="padding:4px 10px;font-size:12px;background:#dc2626;color:white;border-radius:8px;text-decoration:none;" 
                               onclick="return confirm('Yakin hapus soal ini?')">Hapus</a>
                         </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center"><?= !empty($search) ? "Tidak ada soal yang cocok dengan '$search'" : "Belum ada soal untuk mata kuliah ini. <a href='tambah.php?mk_induk_id=$mk_induk_id'>Buat soal sekarang</a>" ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div style="margin-top:20px">
            <a href="tambah.php?mk_induk_id=<?= $mk_induk_id ?>" class="btn-primary"><i class="fas fa-plus"></i> Tambah Soal Lagi</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>