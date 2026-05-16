<?php
session_start();
include '../../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);
$ujian_id = $input['ujian_id'];
$mk_induk_id = $input['mk_induk_id'];

// Ambil semua soal dari MK Induk
$query = "SELECT id FROM soal WHERE mk_induk_id = $mk_induk_id";
$result = mysqli_query($conn, $query);
$semua_soal = [];
while ($row = mysqli_fetch_assoc($result)) {
    $semua_soal[] = $row['id'];
}

// Acak dan ambil 5 soal baru
shuffle($semua_soal);
$soal_baru = array_slice($semua_soal, 0, 5);

// Hapus jawaban yang sudah ada
mysqli_query($conn, "DELETE FROM jawaban WHERE ujian_id = $ujian_id");

// Update soal yang dikeluarkan
$soal_baru_json = json_encode($soal_baru);
mysqli_query($conn, "UPDATE ujian SET soal_yang_dikeluarkan = '$soal_baru_json' WHERE id = $ujian_id");

echo json_encode(['status' => 'success', 'soal_baru' => $soal_baru]);
?>