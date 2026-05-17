<?php
session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/fungsi.php';

if ($_SESSION['role'] != 'mahasiswa') {
    die("Akses ditolak!");
}

$mk_induk_id = isset($_GET['mk_induk_id']) ? (int)$_GET['mk_induk_id'] : 0;
$mahasiswa_id = $_SESSION['user_id'];

// Cari mata_kuliah_id berdasarkan mk_induk_id
$mk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, durasi_ujian FROM mata_kuliah WHERE mk_induk_id = $mk_induk_id LIMIT 1"));
if (!$mk) {
    die("Mata kuliah tidak ditemukan!");
}
$mk_id = $mk['id'];
$durasi_ujian = $mk['durasi_ujian'];

// Dapatkan enrollment_id
$enroll = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT e.id 
    FROM enrollments e
    JOIN enrollment_mk em ON em.enrollment_id = e.id
    WHERE e.mahasiswa_id = $mahasiswa_id AND em.mk_induk_id = $mk_induk_id AND em.status = 'active'
    LIMIT 1
"));
if (!$enroll) {
    die("Anda tidak terdaftar di mata kuliah ini!");
}
$enrollment_id = $enroll['id'];

// Cek ujian sedang berlangsung
$ujian_aktif = cekUjianBerlangsung($enrollment_id, $mk_id, $conn);

if ($ujian_aktif) {
    $ujian_id = $ujian_aktif['id'];
    $soal_ids = json_decode($ujian_aktif['soal_yang_dikeluarkan'], true);
    $mulai = $ujian_aktif['mulai_ujian'];
} else {
    // Ambil soal dari MK Induk
    $soal_ids = ambilSoalAcakInduk($mk_induk_id, $conn, 5);
    
    // DEBUG: Cek apakah soal tersedia
    if (empty($soal_ids)) {
        $query_cek = "SELECT COUNT(*) as total FROM soal WHERE mk_induk_id = $mk_induk_id";
        $cek_result = mysqli_query($conn, $query_cek);
        $cek_row = mysqli_fetch_assoc($cek_result);
        die("Soal belum tersedia! Total soal untuk MK ini: " . ($cek_row['total'] ?? 0) . ". Minimal 5 soal.");
    }
    
    $soal_baru_json = mysqli_real_escape_string($conn, json_encode($soal_ids));
    $query = "INSERT INTO ujian (enrollment_id, mk_id, mulai_ujian, status, soal_yang_dikeluarkan) 
              VALUES ($enrollment_id, $mk_id, NOW(), 'sedang', '$soal_baru_json')";
    
    if (!mysqli_query($conn, $query)) {
        die("Error saat memulai ujian: " . mysqli_error($conn));
    }
    
    $ujian_id = mysqli_insert_id($conn);
    $mulai = date('Y-m-d H:i:s');
}

$mulai_time = strtotime($mulai);
$waktu_habis = $mulai_time + ($durasi_ujian * 60);
$sisa_detik = max(0, $waktu_habis - time());

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="ujian-header">
    <span>⏰ Waktu tersisa: <span id="timer" class="timer-box"><?= floor($sisa_detik/60) . ":" . str_pad($sisa_detik%60,2,'0',STR_PAD_LEFT) ?></span></span>
    <span class="warning-box"><i class="fas fa-exclamation-triangle"></i> Jangan pindah tab! Peringatan 3x = ujian dihentikan</span>
</div>

<form id="formUjian">
    <div id="soal_container">
        <?php $no = 1; foreach($soal_ids as $soal_id):
            $soal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM soal WHERE id=$soal_id"));
            if (!$soal) continue;
        ?>
        <div class="soal-card" data-soal-id="<?= $soal_id ?>">
            <div><span class="soal-number"><?= $no++ ?></span> <strong><?= htmlspecialchars($soal['teks_soal']) ?></strong></div>
            
            <?php if($soal['tipe_soal'] == 'pg'): ?>
                <div class="radio-group" style="margin-top: 12px; margin-left: 20px;">
                    <label style="display: block; margin-bottom: 8px;">
                        <input type="radio" name="jawaban_<?= $soal_id ?>" value="A" class="jawaban-item" data-soal-id="<?= $soal_id ?>"> 
                        A. <?= htmlspecialchars($soal['pilihan_A']) ?>
                    </label>
                    <label style="display: block; margin-bottom: 8px;">
                        <input type="radio" name="jawaban_<?= $soal_id ?>" value="B" class="jawaban-item" data-soal-id="<?= $soal_id ?>"> 
                        B. <?= htmlspecialchars($soal['pilihan_B']) ?>
                    </label>
                    <label style="display: block; margin-bottom: 8px;">
                        <input type="radio" name="jawaban_<?= $soal_id ?>" value="C" class="jawaban-item" data-soal-id="<?= $soal_id ?>"> 
                        C. <?= htmlspecialchars($soal['pilihan_C']) ?>
                    </label>
                    <label style="display: block; margin-bottom: 8px;">
                        <input type="radio" name="jawaban_<?= $soal_id ?>" value="D" class="jawaban-item" data-soal-id="<?= $soal_id ?>"> 
                        D. <?= htmlspecialchars($soal['pilihan_D']) ?>
                    </label>
                    <label style="display: block; margin-bottom: 8px;">
                        <input type="radio" name="jawaban_<?= $soal_id ?>" value="E" class="jawaban-item" data-soal-id="<?= $soal_id ?>"> 
                        E. <?= htmlspecialchars($soal['pilihan_E']) ?>
                    </label>
                </div>
            <?php else: ?>
                <textarea class="jawaban-item form-control" data-soal-id="<?= $soal_id ?>" rows="3" style="margin-top:12px" placeholder="Tulis jawaban Anda di sini..."></textarea>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="btn-primary" id="btnKirim" style="width:100%;padding:15px; margin-top:20px;">Selesai & Kirim</button>
</form>

<script>
let sisaDetik = <?= $sisa_detik ?>;
let ujianId = <?= $ujian_id ?>;
let timerInterval;
let isSubmitting = false;

function updateTimer() {
    if (sisaDetik <= 0) {
        clearInterval(timerInterval);
        if (!isSubmitting) {
            alert('Waktu habis! Ujian akan dikirim otomatis.');
            kirimUjian();
        }
        return;
    }
    let menit = Math.floor(sisaDetik/60);
    let detik = sisaDetik%60;
    let timerEl = document.getElementById('timer');
    if (timerEl) timerEl.innerHTML = `${menit.toString().padStart(2,'0')}:${detik.toString().padStart(2,'0')}`;
    sisaDetik--;
}
timerInterval = setInterval(updateTimer, 1000);

function kirimUjian() {
    if (isSubmitting) return;
    isSubmitting = true;
    
    let jawaban = [];
    document.querySelectorAll('.jawaban-item').forEach(el => {
        let value = '';
        if (el.tagName === 'TEXTAREA') {
            value = el.value;
        } else if (el.tagName === 'INPUT' && el.type === 'radio') {
            if (el.checked) {
                value = el.value;
            } else {
                return;
            }
        }
        jawaban.push({ soal_id: el.dataset.soalId, jawaban: value });
    });
    
    fetch('proses.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ujian_id: ujianId, jawaban: jawaban })
    }).then(res => res.json()).then(data => {
        if (data.status === 'success') {
            window.location.href = 'selesai.php?id=' + ujianId;
        } else {
            alert('Terjadi kesalahan: ' + (data.message || 'Unknown error'));
            isSubmitting = false;
        }
    }).catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan saat mengirim ujian.');
        isSubmitting = false;
    });
}

document.getElementById('btnKirim').addEventListener('click', function(e) {
    e.preventDefault();
    if (confirm('Apakah Anda yakin ingin mengirim ujian?')) {
        kirimUjian();
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>