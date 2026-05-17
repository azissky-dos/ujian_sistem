<?php
session_start();

// Tentukan BASE_PATH secara manual (AMAN)
$base_path = dirname(__DIR__, 2);  // naik 2 level dari admin/xxx/ ke root
require_once $base_path . '/config/config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

// Cek role
if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

// Koneksi database
require_once BASE_PATH . '/config/database.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$filter_kelas = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;
$filter_mk = isset($_GET['mk_induk_id']) ? (int)$_GET['mk_induk_id'] : 0;

$query = "SELECT 
            mhs.nim_nip as nim,
            mhs.nama_lengkap as nama_mahasiswa,
            k.nama_kelas,
            mki.kode_mk,
            mki.nama_mk as nama_mk_induk,
            u.nilai_akhir,
            u.jumlah_pindah_tab,
            u.selesai_ujian
          FROM ujian u
          JOIN enrollments e ON u.enrollment_id = e.id
          JOIN users mhs ON e.mahasiswa_id = mhs.id
          JOIN mata_kuliah mk ON u.mk_id = mk.id
          JOIN mata_kuliah_induk mki ON mk.mk_induk_id = mki.id
          JOIN kelas k ON mk.kelas_id = k.id
          WHERE u.status = 'selesai'";

if (!empty($search)) {
    $query .= " AND (mhs.nim_nip LIKE '%$search%' 
                    OR mhs.nama_lengkap LIKE '%$search%' 
                    OR mki.kode_mk LIKE '%$search%'
                    OR mki.nama_mk LIKE '%$search%'
                    OR k.nama_kelas LIKE '%$search%')";
}
if ($filter_kelas > 0) {
    $query .= " AND k.id = $filter_kelas";
}
if ($filter_mk > 0) {
    $query .= " AND mki.id = $filter_mk";
}
$query .= " ORDER BY u.selesai_ujian DESC";

$result = mysqli_query($conn, $query);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_nilai_" . date('Y-m-d') . ".xls");

echo "<table border='1'>";
echo "<tr><th>No</th><th>NIM</th><th>Nama Mahasiswa</th><th>Kelas</th><th>Mata Kuliah</th><th>Nilai</th><th>Pindah Tab</th><th>Tanggal Ujian</th></tr>";

$no = 1;
while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $no++ . "</td>";
    echo "<td>" . $row['nim'] . "</td>";
    echo "<td>" . $row['nama_mahasiswa'] . "</td>";
    echo "<td>" . $row['nama_kelas'] . "</td>";
    echo "<td>" . $row['kode_mk'] . " - " . $row['nama_mk_induk'] . "</td>";
    echo "<td>" . round($row['nilai_akhir'], 2) . "</td>";
    echo "<td>" . $row['jumlah_pindah_tab'] . "x</td>";
    echo "<td>" . date('d/m/Y H:i', strtotime($row['selesai_ujian'])) . "</td>";
    echo "</tr>";
}

echo "</table>";
?>