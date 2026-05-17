<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
$ujian_id = isset($input['ujian_id']) ? (int)$input['ujian_id'] : 0;
$mk_induk_id = isset($input['mk_induk_id']) ? (int)$input['mk_induk_id'] : 0;

$query = "SELECT id FROM soal WHERE mk_induk_id = $mk_induk_id";
$result = mysqli_query($conn, $query);
$semua_soal = [];
while ($row = mysqli_fetch_assoc($result)) {
    $semua_soal[] = $row['id'];
}
shuffle($semua_soal);
$soal_baru = array_slice($semua_soal, 0, 5);
mysqli_query($conn, "DELETE FROM jawaban WHERE ujian_id = $ujian_id");
mysqli_query($conn, "UPDATE ujian SET soal_yang_dikeluarkan = '" . json_encode($soal_baru) . "' WHERE id = $ujian_id");
echo json_encode(['status' => 'success', 'soal_baru' => $soal_baru]);
?>