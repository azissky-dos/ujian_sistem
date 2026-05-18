<?php
session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/fungsi.php';

if ($_SESSION['role'] != 'mahasiswa') {
    die("Akses ditolak!");
}

$mk_induk_id = $_GET['mk_induk_id'];
$mahasiswa_id = $_SESSION['user_id'];

// ======================================================
// CEK JADWAL UJIAN
// ======================================================

// Cari kelas_id untuk MK ini
$kelas = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT k.id as kelas_id, k.nama_kelas
    FROM mata_kuliah mk 
    JOIN kelas k ON mk.kelas_id = k.id 
    WHERE mk.mk_induk_id = $mk_induk_id 
    LIMIT 1
"));

if (!$kelas) {
    die("<script>alert('Mata kuliah tidak ditemukan!'); window.location='../dashboard.php';</script>");
}

$kelas_id = $kelas['kelas_id'];

// Cek apakah ada jadwal untuk MK dan kelas ini
$cek_jadwal = mysqli_query($conn, "
    SELECT * FROM jadwal_ujian 
    WHERE mk_induk_id = $mk_induk_id AND kelas_id = $kelas_id AND is_active = 1
");

if (mysqli_num_rows($cek_jadwal) == 0) {
    die("<script>alert('Ujian belum dijadwalkan oleh dosen!'); window.location='../dashboard.php';</script>");
}

$jadwal = mysqli_fetch_assoc($cek_jadwal);
$now = new DateTime();
$mulai = new DateTime($jadwal['tanggal_mulai']);
$selesai = new DateTime($jadwal['tanggal_selesai']);

if ($now < $mulai) {
    $msg = "Ujian akan dimulai pada " . $mulai->format('d/m/Y H:i') . "! Silakan tunggu.";
    die("<script>alert('$msg'); window.location='../dashboard.php';</script>");
}

if ($now > $selesai) {
    die("<script>alert('Maaf, jadwal ujian telah berakhir!'); window.location='../dashboard.php';</script>");
}

// Gunakan durasi dari jadwal, bukan dari mata_kuliah
$durasi_ujian = $jadwal['durasi_menit'];

// ======================================================
// LANJUTKAN KODE MULAI UJIAN
// ======================================================

// Cari mata_kuliah_id
$mk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM mata_kuliah WHERE mk_induk_id = $mk_induk_id LIMIT 1"));
$mk_id = $mk['id'];

// Dapatkan enrollment_id
$enroll = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT e.id 
    FROM enrollments e
    JOIN enrollment_mk em ON em.enrollment_id = e.id
    WHERE e.mahasiswa_id = $mahasiswa_id AND em.mk_induk_id = $mk_induk_id AND em.status = 'active'
    LIMIT 1
"));
$enrollment_id = $enroll['id'];

// Cek ujian sedang berlangsung
$ujian_aktif = cekUjianBerlangsung($enrollment_id, $mk_id, $conn);

if ($ujian_aktif) {
    $ujian_id = $ujian_aktif['id'];
    $soal_ids = json_decode($ujian_aktif['soal_yang_dikeluarkan'], true);
    $mulai_waktu = $ujian_aktif['mulai_ujian'];
} else {
    // Ambil soal dari MK Induk
    $soal_ids = ambilSoalAcakInduk($mk_induk_id, 5, $conn);
    
    mysqli_query($conn, "INSERT INTO ujian (enrollment_id, mk_id, mulai_ujian, status, soal_yang_dikeluarkan) 
                         VALUES ($enrollment_id, $mk_id, NOW(), 'sedang', '" . json_encode($soal_ids) . "')");
    $ujian_id = mysqli_insert_id($conn);
    $mulai_waktu = date('Y-m-d H:i:s');
}

$mulai_time = strtotime($mulai_waktu);
$waktu_habis = $mulai_time + ($durasi_ujian * 60);
$sisa_detik = max(0, $waktu_habis - time());

include '../../includes/header.php';
?>

<div class="ujian-header">
    <span>⏰ Waktu tersisa: <span id="timer" class="timer-box"><?= floor($sisa_detik/60) . ":" . str_pad($sisa_detik%60,2,'0',STR_PAD_LEFT) ?></span></span>
    <span class="warning-box"><i class="fas fa-exclamation-triangle"></i> Jangan pindah tab! Peringatan 3x = ujian dihentikan</span>
</div>

<form id="formUjian">
    <div id="soal_container">
        <?php $no = 1; foreach($soal_ids as $soal_id):
            $soal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM soal WHERE id=$soal_id"));
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
    document.getElementById('timer').innerHTML = `${menit.toString().padStart(2,'0')}:${detik.toString().padStart(2,'0')}`;
    sisaDetik--;
}
timerInterval = setInterval(updateTimer, 1000);

// Kirim ujian
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
        }
    }).catch(() => {
        isSubmitting = false;
    });
}

// Acak ulang soal
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

// Update container soal
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

// Ambil jumlah pindah tab
function getPindahCount(callback) {
    fetch('get_pindah_count.php?ujian_id=' + ujianId + '&t=' + Date.now())
        .then(res => res.json())
        .then(data => callback(data.count))
        .catch(() => callback(0));
}

// Deteksi pindah tab
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

<?php include '../../includes/footer.php'; ?>