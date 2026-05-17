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

// Cek ujian sedang berlangsung (urutan parameter: enrollment_id, mk_id, conn)
$ujian_aktif = cekUjianBerlangsung($enrollment_id, $mk_id, $conn);

if ($ujian_aktif) {
    $ujian_id = $ujian_aktif['id'];
    $soal_ids = json_decode($ujian_aktif['soal_yang_dikeluarkan'], true);
    $mulai = $ujian_aktif['mulai_ujian'];
} else {
    // Ambil soal dari MK Induk (urutan parameter: mk_induk_id, conn, jumlah)
    $soal_ids = ambilSoalAcakInduk($mk_induk_id, $conn, 5);
    if (empty($soal_ids)) {
        die("Soal belum tersedia untuk mata kuliah ini! Minimal 5 soal.");
    }
    
    mysqli_query($conn, "INSERT INTO ujian (enrollment_id, mk_id, mulai_ujian, status, soal_yang_dikeluarkan) 
                         VALUES ($enrollment_id, $mk_id, NOW(), 'sedang', '" . json_encode($soal_ids) . "')");
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
        <div class="soal-card" data-soal-id="<?= $soal_id ?>" data-soal-no="<?= $no ?>">
            <div><span class="soal-number"><?= $no++ ?></span> <strong class="soal-teks"><?= htmlspecialchars($soal['teks_soal']) ?></strong></div>
            
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
                <textarea class="jawaban-item form-control" data-soal-id="<?= $soal_id ?>" rows="3" style="margin-top:12px"></textarea>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="btn-primary" onclick="kirimUjian()" style="width:100%;padding:15px; margin-top:20px;">Selesai & Kirim</button>
</form>

<script>
let sisaDetik = <?= $sisa_detik ?>;
let ujianId = <?= $ujian_id ?>;
let mkId = <?= $mk_id ?>;
let mkIndukId = <?= $mk_induk_id ?>;
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
    document.getElementById('timer').innerHTML = `${menit.toString().padStart(2,'0')}:${detik.toString().padStart(2,'0')}`;
    sisaDetik--;
}
timerInterval = setInterval(updateTimer, 1000);

function kirimUjian() {
    if (isSubmitting) return;
    isSubmitting = true;
    
    let jawaban = [];
    document.querySelectorAll('.jawaban-item').forEach(el => {
        jawaban.push({ soal_id: el.dataset.soalId, jawaban: el.value });
    });
    
    fetch('proses.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ujian_id: ujianId, jawaban: jawaban })
    }).then(res => res.json()).then(data => {
        if (data.status === 'success') {
            window.location.href = 'selesai.php?id=' + ujianId;
        } else {
            isSubmitting = false;
        }
    }).catch(() => {
        isSubmitting = false;
    });
}

function acakUlangSoal() {
    fetch('acak_ulang.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ujian_id: ujianId, mk_induk_id: mkIndukId })
    }).then(res => res.json()).then(data => {
        if (data.status === 'success' && data.soal_baru) {
            fetch('get_soal_details.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ soal_ids: data.soal_baru })
            }).then(res => res.json()).then(soal_data => {
                updateSoalContainer(soal_data);
                alert('⚠️ Soal telah diacak ulang!');
            });
        }
    });
}

function updateSoalContainer(soalData) {
    let container = document.getElementById('soal_container');
    let html = '';
    let no = 1;
    
    for (let i = 0; i < soalData.length; i++) {
        let soal = soalData[i];
        html += `<div class="soal-card" data-soal-id="${soal.id}" data-soal-no="${no}">`;
        html += `<div><span class="soal-number">${no++}</span> <strong class="soal-teks">${escapeHtml(soal.teks_soal)}</strong></div>`;
        
        if (soal.tipe_soal === 'pg') {
            html += `<div class="radio-group" style="margin-top: 12px; margin-left: 20px;">`;
            html += `<label style="display:block;margin-bottom:8px;"><input type="radio" name="jawaban_${soal.id}" value="A" class="jawaban-item" data-soal-id="${soal.id}"> A. ${escapeHtml(soal.pilihan_A)}</label>`;
            html += `<label style="display:block;margin-bottom:8px;"><input type="radio" name="jawaban_${soal.id}" value="B" class="jawaban-item" data-soal-id="${soal.id}"> B. ${escapeHtml(soal.pilihan_B)}</label>`;
            html += `<label style="display:block;margin-bottom:8px;"><input type="radio" name="jawaban_${soal.id}" value="C" class="jawaban-item" data-soal-id="${soal.id}"> C. ${escapeHtml(soal.pilihan_C)}</label>`;
            html += `<label style="display:block;margin-bottom:8px;"><input type="radio" name="jawaban_${soal.id}" value="D" class="jawaban-item" data-soal-id="${soal.id}"> D. ${escapeHtml(soal.pilihan_D)}</label>`;
            html += `<label style="display:block;margin-bottom:8px;"><input type="radio" name="jawaban_${soal.id}" value="E" class="jawaban-item" data-soal-id="${soal.id}"> E. ${escapeHtml(soal.pilihan_E)}</label>`;
            html += `</div>`;
        } else {
            html += `<textarea class="jawaban-item form-control" data-soal-id="${soal.id}" rows="3" style="margin-top:12px"></textarea>`;
        }
        html += `</div>`;
    }
    
    container.innerHTML = html;
}

function escapeHtml(text) {
    if (!text) return '';
    let div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

let isProcessing = false;

document.addEventListener('visibilitychange', function() {
    if (document.hidden && !isProcessing) {
        isProcessing = true;
        
        fetch('catat_pindah.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ujian_id: ujianId })
        }).then(() => {
            return fetch('get_pindah_count.php?ujian_id=' + ujianId + '&t=' + Date.now());
        }).then(res => res.json()).then(data => {
            let currentCount = data.count;
            if (currentCount >= 3) {
                alert('❌ Anda telah pindah tab sebanyak 3 kali! Ujian akan dikirim.');
                kirimUjian();
            } else {
                alert(`⚠️ PERINGATAN ${currentCount}/3! Jangan pindah tab. Soal akan diacak ulang.`);
                acakUlangSoal();
            }
            isProcessing = false;
        }).catch(err => {
            console.log('Error:', err);
            isProcessing = false;
        });
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>