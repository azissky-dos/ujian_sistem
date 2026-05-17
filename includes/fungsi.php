<?php
// includes/fungsi.php
// ======================================================
// FUNGSI-FUNGSI UMUM
// ======================================================

// Fungsi menghitung similarity 2 string (Essay)
function similarity($str1, $str2) {
    similar_text(strtolower($str1), strtolower($str2), $percent);
    return round($percent, 2);
}

// Ambil soal acak berdasarkan mk_induk_id
function ambilSoalAcakInduk($mk_induk_id, $jumlah = 5, $conn) {
    $mk_induk_id = intval($mk_induk_id);
    $query = "SELECT id FROM soal WHERE mk_induk_id = $mk_induk_id";
    $result = mysqli_query($conn, $query);
    if (!$result) return [];
    
    $soal_ids = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $soal_ids[] = $row['id'];
    }
    shuffle($soal_ids);
    return array_slice($soal_ids, 0, $jumlah);
}

// Cek apakah mahasiswa terdaftar di MK tertentu
function cekTerdaftarDiMK($mahasiswa_id, $mk_induk_id, $conn) {
    $mahasiswa_id = intval($mahasiswa_id);
    $mk_induk_id = intval($mk_induk_id);
    $query = "SELECT em.id 
              FROM enrollment_mk em
              JOIN enrollments e ON em.enrollment_id = e.id
              WHERE e.mahasiswa_id = $mahasiswa_id AND em.mk_induk_id = $mk_induk_id AND em.status = 'active'";
    $result = mysqli_query($conn, $query);
    if (!$result) return false;
    return mysqli_num_rows($result) > 0;
}
?>