<?php
require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . '/config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['kelas_id']) || empty($_POST['kelas_id'])) {
    echo json_encode([]);
    exit();
}

$kelas_id = intval($_POST['kelas_id']);

$query = "SELECT DISTINCT mki.id, mki.kode_mk, mki.nama_mk
          FROM mata_kuliah_induk mki
          JOIN mata_kuliah mk ON mk.mk_induk_id = mki.id
          WHERE mk.kelas_id = $kelas_id
          ORDER BY mki.kode_mk";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(['error' => mysqli_error($conn)]);
    exit();
}

$mk_list = [];
while($row = mysqli_fetch_assoc($result)) {
    $mk_list[] = [
        'id' => $row['id'],
        'kode_mk' => $row['kode_mk'],
        'nama_mk' => $row['nama_mk']
    ];
}

echo json_encode($mk_list);
?>