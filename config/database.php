<?php
// 1. Cek apakah aplikasi sedang berjalan di Railway
if (getenv('MYSQLHOST')) {
    // === KONFIGURASI UNTUK RAILWAY ===
    $host = getenv('MYSQLHOST');
    $user = getenv('MYSQLUSER');
    $pass = getenv('MYSQLPASSWORD');
    $db   = getenv('MYSQLDATABASE');
    $port = getenv('MYSQLPORT');
} else {
    // === KONFIGURASI UNTUK LOCALHOST (XAMPP) ===
    $host = 'localhost';
    $user = 'root';      // Default XAMPP
    $pass = '';          // Default XAMPP biasanya kosong
    $db   = 'ujian_system'; // Sesuaikan dengan nama DB di phpMyAdmin laptop
    $port = '3306';      // Default port MySQL XAMPP
}

// 2. Eksekusi koneksi menggunakan data di atas
$conn = new mysqli($host, $user, $pass, $db, $port);

// 3. Cek Koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>