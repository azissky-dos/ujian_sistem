<?php
session_start();
require_once __DIR__ . '/../includes/cek_login.php';
require_once __DIR__ . '/../config/database.php';

if ($_SESSION['role'] != 'mahasiswa') {
    die("Akses ditolak!");
}

$mahasiswa_id = $_SESSION['user_id'];

// PERBAIKAN QUERY - Fix ONLY_FULL_GROUP_BY
$ujian_list = mysqli_query($conn, "
    SELECT 
        mki.id as mk_induk_id,
        mki.kode_mk,
        mki.nama_mk,
        (SELECT MIN(k.nama_kelas) 
         FROM mata_kuliah mk2 
         JOIN kelas k ON mk2.kelas_id = k.id 
         WHERE mk2.mk_induk_id = mki.id) as nama_kelas,
        (SELECT MIN(mk2.durasi_ujian) 
         FROM mata_kuliah mk2 
         WHERE mk2.mk_induk_id = mki.id) as durasi_ujian,
        (SELECT MIN(mk2.is_latihan) 
         FROM mata_kuliah mk2 
         WHERE mk2.mk_induk_id = mki.id) as is_latihan,
        (SELECT COUNT(*) FROM soal WHERE mk_induk_id = mki.id) as total_soal,
        (SELECT COUNT(*) FROM ujian u 
         JOIN mata_kuliah mk2 ON u.mk_id = mk2.id
         WHERE mk2.mk_induk_id = mki.id 
         AND u.enrollment_id IN (SELECT id FROM enrollments WHERE mahasiswa_id = $mahasiswa_id) 
         AND u.status = 'selesai') as sudah_ujian
    FROM mata_kuliah_induk mki
    WHERE EXISTS (
        SELECT 1 
        FROM enrollment_mk em 
        JOIN enrollments e ON em.enrollment_id = e.id 
        WHERE e.mahasiswa_id = $mahasiswa_id 
        AND em.mk_induk_id = mki.id 
        AND em.status = 'active'
        AND e.status = 'active'
    )
    ORDER BY mki.nama_mk
");

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Dashboard Mahasiswa</h1>
    <p class="page-subtitle">Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?></p>
</div>

<?php if(mysqli_num_rows($ujian_list) == 0): ?>
    <div class="card-modern">
        <div class="alert info">
            <i class="fas fa-info-circle"></i> 
            Anda belum terdaftar di mata kuliah manapun. Silakan hubungi admin atau dosen untuk pendaftaran.
        </div>
    </div>
<?php else: ?>
    <div class="menu-grid">
        <?php while($ujian = mysqli_fetch_assoc($ujian_list)): ?>
        <div class="card-modern">
            <i class="fas fa-book" style="font-size:32px;color:#4f46e5"></i>
            <h3><?= htmlspecialchars($ujian['kode_mk']) ?> - <?= htmlspecialchars($ujian['nama_mk']) ?></h3>
            <p>
                Kelas: <?= htmlspecialchars($ujian['nama_kelas']) ?><br>
                Durasi: <?= $ujian['durasi_ujian'] ?> menit<br>
                Status: <?= $ujian['sudah_ujian'] > 0 ? '✅ Selesai' : '⏳ Belum' ?>
            </p>
            <?php if($ujian['sudah_ujian'] == 0): ?>
                <?php if($ujian['total_soal'] >= 5): ?>
                    <a href="ujian/mulai.php?mk_induk_id=<?= $ujian['mk_induk_id'] ?>" class="btn-primary">Mulai Ujian</a>
                <?php else: ?>
                    <p class="alert info" style="margin-top:12px; background:#fed7aa; color:#ea580c;">
                        ⚠️ Soal belum mencukupi (minimal 5 soal)
                    </p>
                <?php endif; ?>
            <?php else: ?>
                <a href="riwayat/nilai.php" class="btn-outline">Lihat Nilai</a>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>