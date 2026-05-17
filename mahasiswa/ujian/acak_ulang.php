<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);
$ujian_id = isset($input['ujian_id']) ? (int)$input['ujian_id'] : 0;
$mk_induk_id = isset($input['mk_induk_id']) ? (int)$input['mk_induk_id'] : 0;

// Ambil semua soal dari MK Induk
$query = "SELECT id FROM soal WHERE mk_induk_id = $mk_induk_id";
$result = mysqli_query($conn, $query);
$semua_soal = [];
while ($row = mysqli_fetch_assoc($result)) {
    $semua_soal[] = (int)$row['id'];
}

if (count($semua_soal) < 5) {
    echo json_encode(['status' => 'error', 'message' => 'Soal tidak mencukupi']);
    exit();
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