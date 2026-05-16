<?php
// ======================================================
// FILE: includes/fungsi.php
// ======================================================

include __DIR__ . '/../config/config.php';

// Fungsi menghitung similarity 2 string (Essay Argument)
function similarity($str1, $str2) {
    similar_text(strtolower($str1), strtolower($str2), $percent);
    return round($percent, 2);
}

// Fungsi LAMA: ambil soal berdasarkan mk_id (per kelas)
function ambilSoalAcak($mk_id, $jumlah = 5, $conn) {
    $query = "SELECT id FROM soal WHERE mk_id = " . intval($mk_id);
    $result = mysqli_query($conn, $query);
    if (!$result) return [];
    
    $soal_ids = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $soal_ids[] = $row['id'];
    }
    shuffle($soal_ids);
    return array_slice($soal_ids, 0, $jumlah);
}

// Fungsi BARU: ambil soal berdasarkan mk_induk_id (master MK)
function ambilSoalAcakInduk($mk_induk_id, $jumlah = 5, $conn) {
    $query = "SELECT id FROM soal WHERE mk_induk_id = " . intval($mk_induk_id);
    $result = mysqli_query($conn, $query);
    if (!$result) return [];
    
    $soal_ids = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $soal_ids[] = $row['id'];
    }
    shuffle($soal_ids);
    return array_slice($soal_ids, 0, $jumlah);
}

// Cek apakah ujian sedang berlangsung
function cekUjianBerlangsung($enrollment_id, $mk_id, $conn) {
    $enrollment_id = intval($enrollment_id);
    $mk_id = intval($mk_id);
    $query = "SELECT id, mulai_ujian, soal_yang_dikeluarkan 
              FROM ujian 
              WHERE enrollment_id = $enrollment_id AND mk_id = $mk_id AND status = 'sedang'";
    $result = mysqli_query($conn, $query);
    if (!$result) return false;
    return mysqli_num_rows($result) > 0 ? mysqli_fetch_assoc($result) : false;
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

// Ambil daftar MK Induk yang tersedia di suatu kelas
function getMKIndukByKelas($kelas_id, $conn) {
    $kelas_id = intval($kelas_id);
    $query = "SELECT DISTINCT mki.id, mki.kode_mk, mki.nama_mk
              FROM mata_kuliah_induk mki
              JOIN mata_kuliah mk ON mk.mk_induk_id = mki.id
              WHERE mk.kelas_id = $kelas_id
              ORDER BY mki.kode_mk";
    $result = mysqli_query($conn, $query);
    if (!$result) return [];
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Ambil semua MK Induk yang ada
function getAllMKInduk($conn) {
    $query = "SELECT id, kode_mk, nama_mk FROM mata_kuliah_induk ORDER BY kode_mk";
    $result = mysqli_query($conn, $query);
    if (!$result) return [];
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Tambahan: fungsi untuk aman dari SQL injection
function escapeInput($conn, $input) {
    return mysqli_real_escape_string($conn, $input);
}
?>