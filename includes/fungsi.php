<?php
// includes/fungsi.php
// ======================================================
// FUNGSI-FUNGSI UMUM UNTUK APLIKASI UJIAN ONLINE
// ======================================================

// ========== FUNGSI SIMILARITY UNTUK ESSAY ARGUMEN ==========
function similarity($str1, $str2) {
    similar_text(strtolower($str1), strtolower($str2), $percent);
    return round($percent, 2);
}

// ========== AMBIL SOAL ACAK BERDASARKAN MK_INDUK_ID ==========
// URUTAN PARAMETER: mk_induk_id, conn, jumlah
function ambilSoalAcakInduk($mk_induk_id, $conn, $jumlah = 5) {
    $mk_induk_id = intval($mk_induk_id);
    $jumlah = intval($jumlah);
    
    $query = "SELECT id FROM soal WHERE mk_induk_id = $mk_induk_id";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return [];
    }
    
    $soal_ids = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $soal_ids[] = $row['id'];
    }
    
    if (count($soal_ids) < $jumlah) {
        return $soal_ids; // return semua soal jika kurang dari jumlah yang diminta
    }
    
    shuffle($soal_ids);
    return array_slice($soal_ids, 0, $jumlah);
}

// ========== AMBIL SOAL ACAK BERDASARKAN MK_ID (PER KELAS) ==========
// URUTAN PARAMETER: mk_id, conn, jumlah
function ambilSoalAcak($mk_id, $conn, $jumlah = 5) {
    $mk_id = intval($mk_id);
    $jumlah = intval($jumlah);
    
    $query = "SELECT id FROM soal WHERE mk_id = $mk_id";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return [];
    }
    
    $soal_ids = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $soal_ids[] = $row['id'];
    }
    
    if (count($soal_ids) < $jumlah) {
        return $soal_ids;
    }
    
    shuffle($soal_ids);
    return array_slice($soal_ids, 0, $jumlah);
}

// ========== CEK UJIAN SEDANG BERLANGSUNG ==========
// URUTAN PARAMETER: enrollment_id, mk_id, conn
function cekUjianBerlangsung($enrollment_id, $mk_id, $conn) {
    $enrollment_id = intval($enrollment_id);
    $mk_id = intval($mk_id);
    
    $query = "SELECT id, mulai_ujian, soal_yang_dikeluarkan 
              FROM ujian 
              WHERE enrollment_id = $enrollment_id AND mk_id = $mk_id AND status = 'sedang'";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return false;
    }
    
    return mysqli_num_rows($result) > 0 ? mysqli_fetch_assoc($result) : false;
}

// ========== CEK APAKAH MAHASISWA TERDAFTAR DI MK TERTENTU ==========
function cekTerdaftarDiMK($mahasiswa_id, $mk_induk_id, $conn) {
    $mahasiswa_id = intval($mahasiswa_id);
    $mk_induk_id = intval($mk_induk_id);
    
    $query = "SELECT em.id 
              FROM enrollment_mk em
              JOIN enrollments e ON em.enrollment_id = e.id
              WHERE e.mahasiswa_id = $mahasiswa_id 
                AND em.mk_induk_id = $mk_induk_id 
                AND em.status = 'active'
                AND e.status = 'active'";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return false;
    }
    
    return mysqli_num_rows($result) > 0;
}

// ========== AMBIL DAFTAR MK INDUK BERDASARKAN KELAS ==========
function getMKIndukByKelas($kelas_id, $conn) {
    $kelas_id = intval($kelas_id);
    
    $query = "SELECT DISTINCT mki.id, mki.kode_mk, mki.nama_mk
              FROM mata_kuliah_induk mki
              JOIN mata_kuliah mk ON mk.mk_induk_id = mki.id
              WHERE mk.kelas_id = $kelas_id
              ORDER BY mki.kode_mk";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return [];
    }
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// ========== AMBIL SEMUA MK INDUK ==========
function getAllMKInduk($conn) {
    $query = "SELECT id, kode_mk, nama_mk FROM mata_kuliah_induk ORDER BY kode_mk";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return [];
    }
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// ========== HITUNG NILAI AKHIR UJIAN ==========
function hitungNilaiAkhir($jawaban, $conn) {
    $total_skor = 0;
    $total_bobot = 0;
    
    foreach ($jawaban as $jwb) {
        $soal_id = intval($jwb['soal_id']);
        $jawaban_mhs = $jwb['jawaban'];
        
        $query = "SELECT * FROM soal WHERE id = $soal_id";
        $result = mysqli_query($conn, $query);
        $soal = mysqli_fetch_assoc($result);
        
        if ($soal) {
            $kunci = $soal['kunci_jawaban'];
            $bobot = intval($soal['bobot']);
            $tipe = $soal['tipe_soal'];
            $total_bobot += $bobot;
            
            if ($tipe == 'pg') {
                $skor = ($jawaban_mhs == $kunci) ? $bobot : 0;
            } elseif ($tipe == 'essay_mutlak') {
                $skor = (trim(strtolower($jawaban_mhs)) == trim(strtolower($kunci))) ? $bobot : 0;
            } else {
                $similarity = similarity($jawaban_mhs, $kunci);
                $skor = ($similarity / 100) * $bobot;
            }
            
            $total_skor += $skor;
        }
    }
    
    return ($total_bobot > 0) ? ($total_skor / $total_bobot) * 100 : 0;
}

// ========== CATAT PINDAH TAB ==========
function catatPindahTab($ujian_id, $conn) {
    $ujian_id = intval($ujian_id);
    
    // Increment jumlah_pindah_tab
    $query = "UPDATE ujian SET jumlah_pindah_tab = jumlah_pindah_tab + 1 WHERE id = $ujian_id";
    mysqli_query($conn, $query);
    
    // Ambil nilai terbaru
    $query = "SELECT jumlah_pindah_tab FROM ujian WHERE id = $ujian_id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    
    return $row ? intval($row['jumlah_pindah_tab']) : 0;
}

// ========== AMBIL JUMLAH PINDAH TAB ==========
function getPindahTabCount($ujian_id, $conn) {
    $ujian_id = intval($ujian_id);
    
    $query = "SELECT jumlah_pindah_tab FROM ujian WHERE id = $ujian_id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    
    return $row ? intval($row['jumlah_pindah_tab']) : 0;
}
?>