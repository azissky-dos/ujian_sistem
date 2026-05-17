<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$ujian_id = isset($_GET['ujian_id']) ? (int)$_GET['ujian_id'] : 0;
$result = mysqli_query($conn, "SELECT jumlah_pindah_tab FROM ujian WHERE id = $ujian_id");
$row = mysqli_fetch_assoc($result);
echo json_encode(['count' => $row ? (int)$row['jumlah_pindah_tab'] : 0]);
?>