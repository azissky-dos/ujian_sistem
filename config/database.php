<?php
// Cek apakah ada variabel host dari Railway
if (getenv('MYSQLHOST') || isset($_ENV['MYSQLHOST'])) {
    // === SETTINGAN ONLINE (RAILWAY) ===
    $host = getenv('MYSQLHOST');
    $user = getenv('MYSQLUSER');
    $pass = getenv('MYSQLPASSWORD');
    $db   = getenv('MYSQLDATABASE');
    $port = getenv('MYSQLPORT');
} else {
    // === SETTINGAN LOKAL (XAMPP LAPTOP) ===
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'Ujian_System'; // <-- Sesuaikan dengan nama DB di phpMyAdmin laptop Bapak
    $port = '3306';
}

// Koneksi ke server MySQL menggunakan @ untuk meredam crash di cloud jika DB belum siap
$conn = @new mysqli($host, $user, $pass, $db, $port);

// Jika gagal konek
if ($conn->connect_error) {
    die("Gagal terhubung ke database: " . $conn->connect_error);
}
?>