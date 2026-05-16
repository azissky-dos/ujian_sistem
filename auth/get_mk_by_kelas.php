<?php
include '../config/database.php';

$kelas_id = $_POST['kelas_id'];

// Ambil MK Induk yang tersedia di kelas tersebut
$query = "SELECT DISTINCT mki.id, mki.kode_mk, mki.nama_mk
          FROM mata_kuliah_induk mki
          JOIN mata_kuliah mk ON mk.mk_induk_id = mki.id
          WHERE mk.kelas_id = $kelas_id
          ORDER BY mki.kode_mk";
$result = mysqli_query($conn, $query);

$mk_list = [];
while($row = mysqli_fetch_assoc($result)) {
    $mk_list[] = $row;
}

header('Content-Type: application/json');
echo json_encode($mk_list);
?>