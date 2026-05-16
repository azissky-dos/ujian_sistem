<?php
// 1. PENGATURAN KONEKSI DATABASE (OTOMATIS CLOUD / LOKAL)
if (getenv('MYSQLHOST') || isset($_ENV['MYSQLHOST'])) {
    $host = getenv('MYSQLHOST');
    $user = getenv('MYSQLUSER');
    $pass = getenv('MYSQLPASSWORD');
    $db   = getenv('MYSQLDATABASE');
    $port = getenv('MYSQLPORT');
} else {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'Ujian_Sistem'; // Sesuaikan dengan nama database di XAMPP Bapak
    $port = '3306';
}

$conn = @new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}


// 2. PUSAT PENGATURAN JALUR FOLDER (GANTI DI SINI, SEMUA HALAMAN IKUT)
if (getenv('MYSQLHOST') || isset($_ENV['MYSQLHOST'])) {
    // Di Cloud Railway: Langsung di root luar (/app/)
    $base_path = $_SERVER['DOCUMENT_ROOT'] . '/';
    $url_aset  = '/'; 
} else {
    // Di Laptop Bapak: Otomatis mendeteksi folder 'ujian_system' atau 'ujian_sistem'
    if (is_dir($_SERVER['DOCUMENT_ROOT'] . '/ujian_system/')) {
        $base_path = $_SERVER['DOCUMENT_ROOT'] . '/ujian_system/';
        $url_aset  = '/ujian_system/';
    } else {
        $base_path = $_SERVER['DOCUMENT_ROOT'] . '/ujian_sistem/';
        $url_aset  = '/ujian_sistem/';
    }
}
?>