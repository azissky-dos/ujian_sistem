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
    // Ambil soal langsung dari database
    $query_soal = "SELECT id FROM soal WHERE mk_induk_id = $mk_induk_id ORDER BY RAND() LIMIT 5";
    $result_soal = mysqli_query($conn, $query_soal);
    
    $soal_ids = [];
    while ($row = mysqli_fetch_assoc($result_soal)) {
        $soal_ids[] = $row['id'];
    }
    
    if (count($soal_ids) < 5) {
        die("Soal belum mencukupi. Tersedia " . count($soal_ids) . " soal, minimal 5 soal.");
    }
    
    $soal_baru_json = mysqli_real_escape_string($conn, json_encode($soal_ids));
    $query = "INSERT INTO ujian (enrollment_id, mk_id, mulai_ujian, status, soal_yang_dikeluarkan) 
              VALUES ($enrollment_id, $mk_id, NOW(), 'sedang', '$soal_baru_json')";
    
    if (!mysqli_query($conn, $query)) {
        die("Error: " . mysqli_error($conn));
    }
    
    $ujian_id = mysqli_insert_id($conn);
    $mulai = date('Y-m-d H:i:s');
}

$mulai_time = strtotime($mulai);
$waktu_habis = $mulai_time + ($durasi_ujian * 60);
$sisa_detik = max(0, $waktu_habis - time());

// Ambil data soal untuk ditampilkan
$soal_data = [];
foreach ($soal_ids as $soal_id) {
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM soal WHERE id=$soal_id"));
    if ($row) $soal_data[] = $row;
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="ujian-header">
    <span>⏰ Waktu tersisa: <span id="timer" class="timer-box"><?= floor($sisa_detik/60) . ":" . str_pad($sisa_detik%60,2,'0',STR_PAD_LEFT) ?></span></span>
    <span class="warning-box"><i class="fas fa-exclamation-triangle"></i> Jangan pindah tab! Peringatan 3x = ujian dihentikan</span>
</div>

<form id="formUjian">
    <div id="soal_container">
        <?php $no = 1; foreach($soal_data as $soal): ?>
        <div class="soal-card">
            <div><span class="soal-number"><?= $no++ ?></span> <strong><?= htmlspecialchars($soal['teks_soal']) ?></strong></div>
            
            <?php if($soal['tipe_soal'] == 'pg'): ?>
                <div class="radio-group" style="margin-top: 12px; margin-left: 20px;">
                    <label style="display: block; margin-bottom: 8px;">
                        <input type="radio" name="jawaban_<?= $soal['id'] ?>" value="A" class="jawaban-item" data-soal-id="<?= $soal['id'] ?>"> 
                        A. <?= htmlspecialchars($soal['pilihan_A']) ?>
                    </label>
                    <label style="display: block; margin-bottom: 8px;">
                        <input type="radio" name="jawaban_<?= $soal['id'] ?>" value="B" class="jawaban-item" data-soal-id="<?= $soal['id'] ?>"> 
                        B. <?= htmlspecialchars($soal['pilihan_B']) ?>
                    </label>
                    <label style="display: block; margin-bottom: 8px;">
                        <input type="radio" name="jawaban_<?= $soal['id'] ?>" value="C" class="jawaban-item" data-soal-id="<?= $soal['id'] ?>"> 
                        C. <?= htmlspecialchars($soal['pilihan_C']) ?>
                    </label>
                    <label style="display: block; margin-bottom: 8px;">
                        <input type="radio" name="jawaban_<?= $soal['id'] ?>" value="D" class="jawaban-item" data-soal-id="<?= $soal['id'] ?>"> 
                        D. <?= htmlspecialchars($soal['pilihan_D']) ?>
                    </label>
                    <label style="display: block; margin-bottom: 8px;">
                        <input type="radio" name="jawaban_<?= $soal['id'] ?>" value="E" class="jawaban-item" data-soal-id="<?= $soal['id'] ?>"> 
                        E. <?= htmlspecialchars($soal['pilihan_E']) ?>
                    </label>
                </div>
            <?php else: ?>
                <textarea class="jawaban-item form-control" data-soal-id="<?= $soal['id'] ?>" rows="3" style="margin-top:12px" placeholder="Tulis jawaban Anda di sini..."></textarea>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="btn-primary" id="btnKirim" style="width:100%;padding:15px; margin-top:20px;">Selesai & Kirim</button>
</form>

<script>
let sisaDetik = <?= $sisa_detik ?>;
let ujianId = <?= $ujian_id ?>;
let mkIndukId = <?= $mk_induk_id ?>;
let timerInterval;
let isSubmitting = false;
let isProcessing = false;

// Timer
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

// Kirim ujian
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
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.href = 'selesai.php?id=' + ujianId;
        } else {
            alert('Error: ' + (data.message || 'Gagal mengirim ujian'));
            isSubmitting = false;
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Terjadi kesalahan koneksi: ' + error.message);
        isSubmitting = false;
    });
}

// Fungsi catat pindah dan proses acak
function prosesPindah() {
    fetch('catat_pindah.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ujian_id: ujianId })
    })
    .then(res => res.json())
    .then(data => {
        let pindahCount = data.count || 0;
        
        if (pindahCount >= 3) {
            alert('❌ Anda telah pindah tab sebanyak 3 kali! Ujian akan dikirim.');
            kirimUjian();
        } else {
            alert(`⚠️ PERINGATAN ${pindahCount}/3! Jangan pindah tab. Soal akan diacak ulang.`);
            // Acak soal
            fetch('acak_ulang.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ujian_id: ujianId, mk_induk_id: mkIndukId })
            })
            .then(res => res.json())
            .then(() => {
                location.reload();
            })
            .catch(err => console.error('Acak error:', err));
        }
    })
    .catch(err => console.error('Error:', err));
}

// Deteksi pindah tab
document.addEventListener('visibilitychange', function() {
    if (document.hidden && !isProcessing) {
        isProcessing = true;
        prosesPindah();
        setTimeout(() => { isProcessing = false; }, 2000);
    }
});

// Tombol kirim
document.getElementById('btnKirim').addEventListener('click', function(e) {
    e.preventDefault();
    if (confirm('Apakah Anda yakin ingin mengirim ujian?')) {
        kirimUjian();
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>