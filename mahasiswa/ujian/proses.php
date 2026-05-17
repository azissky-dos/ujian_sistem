<?php
session_start();
require_once __DIR__ . '/../../config/database.php'];
require_once __DIR__ . '/../../includes/fungsi.php';

// Set header JSON
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
    exit();
}

$ujian_id = isset($input['ujian_id']) ? (int)$input['ujian_id'] : 0;
$jawaban = isset($input['jawaban']) ? $input['jawaban'] : [];

if ($ujian_id == 0 || empty($jawaban)) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit();
}

$total_skor = 0;
$total_bobot = 0;

foreach ($jawaban as $jwb) {
    $soal_id = (int)$jwb['soal_id'];
    $jawaban_mhs = mysqli_real_escape_string($conn, $jwb['jawaban']);
    
    $result = mysqli_query($conn, "SELECT * FROM soal WHERE id = $soal_id");
    $soal = mysqli_fetch_assoc($result);
    
    if ($soal) {
        $kunci = $soal['kunci_jawaban'];
        $bobot = (int)$soal['bobot'];
        $tipe = $soal['tipe_soal'];
        $total_bobot += $bobot;
        
        if ($tipe == 'pg') {
            $skor = ($jawaban_mhs == $kunci) ? $bobot : 0;
        } elseif ($tipe == 'essay_mutlak') {
            $skor = (trim(strtolower($jawaban_mhs)) == trim(strtolower($kunci))) ? $bobot : 0;
        } else {
            $similarity = similarity($jawaban_mhs, $kunci);
            $skor = ($similarity / 100) * $bobot;
        }
        
        $total_skor += $skor;
        
        mysqli_query($conn, "INSERT INTO jawaban (ujian_id, soal_id, jawaban_mahasiswa, skor) 
                             VALUES ($ujian_id, $soal_id, '$jawaban_mhs', $skor)");
    }
}

$nilai_akhir = ($total_bobot > 0) ? ($total_skor / $total_bobot) * 100 : 0;
mysqli_query($conn, "UPDATE ujian SET status='selesai', selesai_ujian=NOW(), nilai_akhir=$nilai_akhir WHERE id=$ujian_id");

echo json_encode(['status' => 'success', 'nilai' => round($nilai_akhir, 2)]);
?>