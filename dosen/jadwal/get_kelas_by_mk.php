<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'dosen') {
    die("Akses ditolak!");
}

$dosen_id = $_SESSION['user_id'];
$mk_induk_id = $_POST['mk_induk_id'];

// Ambil daftar kelas yang mengajar MK ini dan diajar oleh dosen tersebut
$query = "
    SELECT DISTINCT k.id, k.nama_kelas, u.nama_lengkap as dosen_nama
    FROM kelas k
    JOIN mata_kuliah mk ON mk.kelas_id = k.id
    JOIN users u ON k.dosen_id = u.id
    WHERE mk.mk_induk_id = $mk_induk_id AND k.dosen_id = $dosen_id
    ORDER BY k.nama_kelas
";

$result = mysqli_query($conn, $query);
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>