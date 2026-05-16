<?php
// Cek apakah ada variabel host dari Railway
if (isset($_ENV['MYSQLHOST']) || getenv('MYSQLHOST')) {
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
    $db   = 'ujian_system'; // <-- PAK, GANTI INI SESUAI DI XAMPP
    $port = '3306';
}

// Koneksi ke server MySQL
$conn = @new mysqli($host, $user, $pass, $db, $port);

// Jika gagal konek
if ($conn->connect_error) {
    die("Gagal terhubung ke database: " . $conn->connect_error);
}
?>