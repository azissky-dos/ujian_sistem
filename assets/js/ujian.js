// assets/js/ujian.js
// ======================================================
// JAVASCRIPT UNTUK UJIAN ONLINE
// ======================================================

let timerInterval;
let sisaDetik = 0;
let ujianId = null;
let pindahCount = 0;

// Fungsi untuk memulai timer
function startTimer(detik, ujian_id) {
    sisaDetik = detik;
    ujianId = ujian_id;
    
    if (timerInterval) clearInterval(timerInterval);
    
    timerInterval = setInterval(function() {
        if (sisaDetik <= 0) {
            clearInterval(timerInterval);
            alert('Waktu habis! Ujian akan dikirim otomatis.');
            kirimUjian();
            return;
        }
        
        let menit = Math.floor(sisaDetik / 60);
        let detik = sisaDetik % 60;
        let timerEl = document.getElementById('timer');
        
        if (timerEl) {
            timerEl.innerHTML = `${menit.toString().padStart(2,'0')}:${detik.toString().padStart(2,'0')}`;
        }
        
        sisaDetik--;
    }, 1000);
}

// Fungsi untuk mengirim ujian
function kirimUjian() {
    let jawaban = [];
    
    document.querySelectorAll('.jawaban-item').forEach(function(el) {
        jawaban.push({ 
            soal_id: el.dataset.soalId, 
            jawaban: el.value 
        });
    });
    
    fetch('proses_ujian.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ujian_id: ujianId, jawaban: jawaban })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.href = 'selesai.php?id=' + ujianId;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengirim ujian.');
    });
}

// Deteksi pindah tab (untuk mencegah kecurangan)
document.addEventListener('visibilitychange', function() {
    if (document.hidden && window.location.pathname.includes('mulai_ujian.php')) {
        pindahCount++;
        alert(`⚠️ Peringatan! Jangan pindah tab (${pindahCount}x)`);
        
        fetch('catat_pindah.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ujian_id: ujianId })
        })
        .catch(error => console.error('Error:', error));
        
        if (pindahCount >= 3) {
            alert('Ujian dihentikan karena sering pindah tab!');
            kirimUjian();
        }
    }
});

// Fungsi untuk konfirmasi sebelum keluar halaman
window.addEventListener('beforeunload', function(e) {
    if (ujianId && sisaDetik > 0) {
        e.preventDefault();
        e.returnValue = 'Ujian belum selesai! Yakin ingin keluar?';
        return 'Ujian belum selesai! Yakin ingin keluar?';
    }
});