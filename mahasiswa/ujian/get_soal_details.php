<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);
$soal_ids = isset($input['soal_ids']) ? $input['soal_ids'] : [];

$result = [];
foreach ($soal_ids as $id) {
    $id = (int)$id;
    $query = "SELECT id, tipe_soal, teks_soal, pilihan_A, pilihan_B, pilihan_C, pilihan_D, pilihan_E FROM soal WHERE id = $id";
    $row = mysqli_fetch_assoc(mysqli_query($conn, $query));
    if ($row) {
        $result[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($result);
?>