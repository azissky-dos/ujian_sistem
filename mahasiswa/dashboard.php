<?php
session_start();
require_once __DIR__ . '/../includes/cek_login.php';
require_once __DIR__ . '/../config/database.php';

if ($_SESSION['role'] != 'mahasiswa') {
    die("Akses ditolak!");
}

$mahasiswa_id = $_SESSION['user_id'];

// PERBAIKAN QUERY - Fix ONLY_FULL_GROUP_BY dengan ANY_VALUE atau MIN/MAX
$ujian_list = mysqli_query($conn, "
    SELECT 
        mki.id as mk_induk_id,
        mki.kode_mk,
        mki.nama_mk,
        MIN(k.nama_kelas) as nama_kelas,
        MIN(j.durasi_menit) as durasi_ujian,
        MIN(j.tanggal_mulai) as tanggal_mulai,
        MIN(j.tanggal_selesai) as tanggal_selesai,
        (SELECT COUNT(*) FROM soal WHERE mk_induk_id = mki.id) as total_soal,
        (SELECT COUNT(*) FROM ujian u 
         JOIN mata_kuliah mk2 ON u.mk_id = mk2.id
         WHERE mk2.mk_induk_id = mki.id 
         AND u.enrollment_id IN (SELECT id FROM enrollments WHERE mahasiswa_id = $mahasiswa_id) 
         AND u.status = 'selesai') as sudah_ujian
    FROM mata_kuliah_induk mki
    JOIN mata_kuliah mk ON mk.mk_induk_id = mki.id
    JOIN kelas k ON mk.kelas_id = k.id
    JOIN jadwal_ujian j ON j.mk_induk_id = mki.id AND j.kelas_id = k.id
    JOIN enrollment_mk em ON em.mk_induk_id = mki.id
    JOIN enrollments e ON em.enrollment_id = e.id
    WHERE e.mahasiswa_id = $mahasiswa_id AND e.status = 'active' AND em.status = 'active'
      AND j.is_active = 1
    GROUP BY mki.id, mki.kode_mk, mki.nama_mk
    ORDER BY MIN(j.tanggal_mulai)
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
            Anda belum terdaftar di mata kuliah manapun atau belum ada jadwal ujian.
        </div>
    </div>
<?php else: ?>
    <div class="menu-grid">
        <?php while($ujian = mysqli_fetch_assoc($ujian_list)): 
            // Hitung status jadwal di PHP (bukan di SQL)
            $now = new DateTime();
            $mulai = new DateTime($ujian['tanggal_mulai']);
            $selesai = new DateTime($ujian['tanggal_selesai']);
            
            if ($now < $mulai) {
                $status_jadwal = 'belum';
            } elseif ($now >= $mulai && $now <= $selesai) {
                $status_jadwal = 'sedang';
            } else {
                $status_jadwal = 'selesai';
            }
        ?>
        <div class="card-modern">
            <i class="fas fa-book" style="font-size:32px;color:#4f46e5"></i>
            <h3><?= htmlspecialchars($ujian['kode_mk']) ?> - <?= htmlspecialchars($ujian['nama_mk']) ?></h3>
            <p>
                Kelas: <?= htmlspecialchars($ujian['nama_kelas']) ?><br>
                Durasi: <?= $ujian['durasi_ujian'] ?> menit<br>
                Jadwal: <?= date('d/m/Y H:i', strtotime($ujian['tanggal_mulai'])) ?> - <?= date('d/m/Y H:i', strtotime($ujian['tanggal_selesai'])) ?><br>
                Status: 
                <?php if($ujian['sudah_ujian'] > 0): ?>
                    ✅ Selesai
                <?php elseif($status_jadwal == 'belum'): ?>
                    ⏳ Belum dibuka
                <?php elseif($status_jadwal == 'sedang'): ?>
                    🟢 Sedang berlangsung
                <?php else: ?>
                    🔴 Telah berakhir
                <?php endif; ?>
            </p>
            <?php if($ujian['sudah_ujian'] == 0 && $status_jadwal == 'sedang'): ?>
                <?php if($ujian['total_soal'] >= 5): ?>
                    <a href="ujian/mulai.php?mk_induk_id=<?= $ujian['mk_induk_id'] ?>" class="btn-primary">Mulai Ujian</a>
                <?php else: ?>
                    <p class="alert info" style="margin-top:12px; background:#fed7aa; color:#ea580c;">
                        ⚠️ Soal belum mencukupi (minimal 5 soal)
                    </p>
                <?php endif; ?>
            <?php elseif($ujian['sudah_ujian'] > 0): ?>
                <a href="riwayat/nilai.php" class="btn-outline">Lihat Nilai</a>
            <?php elseif($status_jadwal == 'belum'): ?>
                <p class="alert info" style="margin-top:12px; background:#e0e7ff; color:#4338ca;">
                    <i class="fas fa-clock"></i> Ujian akan dibuka pada <?= date('d/m/Y H:i', strtotime($ujian['tanggal_mulai'])) ?>
                </p>
            <?php else: ?>
                <p class="alert error" style="margin-top:12px;">
                    <i class="fas fa-times-circle"></i> Jadwal ujian telah berakhir
                </p>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>