// assets/js/ujian.js
// Gunakan BASE_URL dari PHP atau hardcode path dengan /

let timerInterval;
let sisaDetik = 0;
let ujianId = null;
let pindahCount = 0;

// Deteksi BASE_URL dari PHP (disediakan via inline script di halaman)
let baseUrl = window.BASE_URL || '';

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
        if (timerEl) timerEl.innerHTML = `${menit.toString().padStart(2,'0')}:${detik.toString().padStart(2,'0')}`;
        sisaDetik--;
    }, 1000);
}

function kirimUjian() {
    let jawaban = [];
    document.querySelectorAll('.jawaban-item').forEach(function(el) {
        jawaban.push({ soal_id: el.dataset.soalId, jawaban: el.value });
    });
    
    // Gunakan BASE_URL untuk fetch
    fetch(baseUrl + '/mahasiswa/ujian/proses.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ujian_id: ujianId, jawaban: jawaban })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.href = baseUrl + '/mahasiswa/ujian/selesai.php?id=' + ujianId;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengirim ujian. Silakan coba lagi.');
    });
}

document.addEventListener('visibilitychange', function() {
    if (document.hidden && window.location.pathname.includes('mulai.php')) {
        pindahCount++;
        alert(`⚠️ Peringatan! Jangan pindah tab (${pindahCount}x)`);
        
        // Gunakan BASE_URL untuk fetch
        fetch(baseUrl + '/mahasiswa/ujian/catat_pindah.php', {
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