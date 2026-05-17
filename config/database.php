<?php
// config/database.php
// ======================================================
// FILE KONFIGURASI DATABASE - SATU-SATUNYA FILE CONFIG!
// ======================================================

// Deteksi apakah di Railway
$is_railway = (getenv('RAILWAY_ENVIRONMENT') !== false);

if ($is_railway) {
    // === KONFIGURASI UNTUK RAILWAY ===
    $db_host = getenv('MYSQLHOST') ?: 'localhost';
    $db_user = getenv('MYSQLUSER') ?: 'root';
    $db_pass = getenv('MYSQLPASSWORD') ?: '';
    $db_name = getenv('MYSQLDATABASE') ?: 'ujian_system';
    $db_port = getenv('MYSQLPORT') ?: 3306;
    
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
} else {
    // === KONFIGURASI UNTUK LOCAL (XAMPP) ===
    $conn = mysqli_connect('localhost', 'root', '', 'ujian_system');
}

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset ke UTF-8
mysqli_set_charset($conn, "utf8");

// Buat konstanta untuk environment (opsional, tapi berguna)
define('IS_RAILWAY', $is_railway);
?>