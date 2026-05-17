<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
$ujian_id = isset($input['ujian_id']) ? (int)$input['ujian_id'] : 0;
mysqli_query($conn, "UPDATE ujian SET jumlah_pindah_tab = jumlah_pindah_tab + 1 WHERE id = $ujian_id");
$result = mysqli_query($conn, "SELECT jumlah_pindah_tab FROM ujian WHERE id = $ujian_id");
$row = mysqli_fetch_assoc($result);
echo json_encode(['status' => 'success', 'count' => $row ? (int)$row['jumlah_pindah_tab'] : 0]);
?>