<?php
session_start();
include '../../config/database.php';

$ujian_id = $_GET['ujian_id'];
$result = mysqli_query($conn, "SELECT jumlah_pindah_tab FROM ujian WHERE id = $ujian_id");
$row = mysqli_fetch_assoc($result);

echo json_encode(['count' => $row['jumlah_pindah_tab']]);
?>