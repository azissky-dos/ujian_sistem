<?php
session_start();
include __DIR__ . '/../config/config.php';
include BASE_PATH . '/includes/cek_login.php';
include BASE_PATH . '/config/database.php';

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$master_mk_list = mysqli_query($conn, "SELECT id, kode_mk, nama_mk FROM mata_kuliah_induk ORDER BY kode_mk");
$kelas_list = mysqli_query($conn, "
    SELECT k.id, k.nama_kelas, u.nama_lengkap as dosen_nama, u.id as dosen_id
    FROM kelas k 
    LEFT JOIN users u ON k.dosen_id = u.id 
    ORDER BY k.nama_kelas
");

if (isset($_POST['simpan'])) {
    $master_mk_id = $_POST['master_mk_id'];
    $kelas_id = $_POST['kelas_id'];
    $dosen_id = $_POST['dosen_id'];
    $durasi_ujian = $_POST['durasi_ujian'];
    $is_latihan = isset($_POST['is_latihan']) ? 1 : 0;
    
    $master = mysqli_fetch_assoc(mysqli_query($conn, "SELECT kode_mk, nama_mk FROM mata_kuliah_induk WHERE id=$master_mk_id"));
    $kode_mk = $master['kode_mk'];
    $nama_mk = $master['nama_mk'];
    
    $query = "INSERT INTO mata_kuliah (kode_mk, nama_mk, kelas_id, dosen_id, durasi_ujian, is_latihan, mk_induk_id) 
              VALUES ('$kode_mk', '$nama_mk', $kelas_id, " . ($dosen_id ? $dosen_id : 'NULL') . ", $durasi_ujian, $is_latihan, $master_mk_id)";
    mysqli_query($conn, $query);
    header('Location: ' . BASE_URL . '/admin/matakuliah/index.php');
    exit();
}

include BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Tambah Mata Kuliah ke Kelas</h1>
    <a href="<?= BASE_URL ?>/admin/matakuliah/index.php" class="btn-outline">← Kembali</a>
</div>

<div class="card-modern" style="max-width:550px">
    <form method="POST">
        <div class="form-group">
            <label>Pilih Mata Kuliah (dari Master)</label>
            <select name="master_mk_id" class="form-control" required>
                <option value="">-- Pilih MK Induk --</option>
                <?php while($mm = mysqli_fetch_assoc($master_mk_list)): ?>
                    <option value="<?= $mm['id'] ?>"><?= htmlspecialchars($mm['kode_mk']) ?> - <?= htmlspecialchars($mm['nama_mk']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Pilih Kelas</label>
            <select name="kelas_id" id="kelas_id" class="form-control" required>
                <option value="">-- Pilih Kelas --</option>
                <?php while($k = mysqli_fetch_assoc($kelas_list)): ?>
                    <option value="<?= $k['id'] ?>" data-dosen-id="<?= $k['dosen_id'] ?>" data-dosen-nama="<?= htmlspecialchars($k['dosen_nama'] ?? '') ?>">
                        <?= htmlspecialchars($k['nama_kelas']) ?> <?= $k['dosen_nama'] ? '(Dosen: ' . htmlspecialchars($k['dosen_nama']) . ')' : '(Belum ada dosen)' ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Dosen Pengajar</label>
            <input type="text" id="dosen_nama_display" class="form-control" readonly value="-">
            <input type="hidden" name="dosen_id" id="dosen_id">
        </div>
        
        <div class="form-group">
            <label>Durasi Ujian (menit)</label>
            <input type="number" name="durasi_ujian" class="form-control" value="60" required>
        </div>
        
        <div class="form-group">
            <label><input type="checkbox" name="is_latihan"> Mode Latihan (tidak dinilai)</label>
        </div>
        
        <button type="submit" name="simpan" class="btn-primary">Simpan</button>
    </form>
</div>

<script>
document.getElementById('kelas_id').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var dosenId = selectedOption.getAttribute('data-dosen-id');
    var dosenNama = selectedOption.getAttribute('data-dosen-nama');
    document.getElementById('dosen_id').value = dosenId || '';
    document.getElementById('dosen_nama_display').value = dosenNama || '-';
});
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>