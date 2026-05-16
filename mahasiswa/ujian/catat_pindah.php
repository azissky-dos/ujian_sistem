<?php
session_start();
include '../../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);
$ujian_id = $input['ujian_id'];

// Increment jumlah_pindah_tab
mysqli_query($conn, "UPDATE ujian SET jumlah_pindah_tab = jumlah_pindah_tab + 1 WHERE id = $ujian_id");

// Ambil nilai terbaru
$result = mysqli_query($conn, "SELECT jumlah_pindah_tab FROM ujian WHERE id = $ujian_id");
$row = mysqli_fetch_assoc($result);

echo json_encode(['status' => 'success', 'count' => $row['jumlah_pindah_tab']]);
?>