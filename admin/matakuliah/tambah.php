<?php
// admin/matakuliah/tambah.php
// ======================================================
// TAMBAH MATA KULIAH KE KELAS
// ======================================================

session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

// Ambil daftar master MK
$master_mk_list = mysqli_query($conn, "SELECT id, kode_mk, nama_mk FROM mata_kuliah_induk ORDER BY kode_mk");

// Ambil daftar kelas dengan informasi dosen
$kelas_list = mysqli_query($conn, "
    SELECT k.id, k.nama_kelas, u.nama_lengkap as dosen_nama, u.id as dosen_id
    FROM kelas k 
    LEFT JOIN users u ON k.dosen_id = u.id 
    ORDER BY k.nama_kelas
");

if (isset($_POST['simpan'])) {
    $master_mk_id = (int)$_POST['master_mk_id'];
    $kelas_id = (int)$_POST['kelas_id'];
    $dosen_id = !empty($_POST['dosen_id']) ? (int)$_POST['dosen_id'] : 'NULL';
    $durasi_ujian = (int)$_POST['durasi_ujian'];
    $is_latihan = isset($_POST['is_latihan']) ? 1 : 0;
    
    // Ambil data dari master MK
    $master = mysqli_fetch_assoc(mysqli_query($conn, "SELECT kode_mk, nama_mk FROM mata_kuliah_induk WHERE id=$master_mk_id"));
    $kode_mk = mysqli_real_escape_string($conn, $master['kode_mk']);
    $nama_mk = mysqli_real_escape_string($conn, $master['nama_mk']);
    
    $query = "INSERT INTO mata_kuliah (kode_mk, nama_mk, kelas_id, dosen_id, durasi_ujian, is_latihan, mk_induk_id) 
              VALUES ('$kode_mk', '$nama_mk', $kelas_id, $dosen_id, $durasi_ujian, $is_latihan, $master_mk_id)";
    
    if (mysqli_query($conn, $query)) {
        header('Location: index.php');
        exit();
    } else {
        $error = "Gagal menambahkan: " . mysqli_error($conn);
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Tambah Mata Kuliah ke Kelas</h1>
    <a href="index.php" class="btn-outline">← Kembali</a>
</div>

<?php if(isset($error)): ?>
    <div class="alert error"><?= $error ?></div>
<?php endif; ?>

<div class="card-modern" style="max-width:550px; margin:0 auto;">
    <form method="POST">
        <div class="form-group">
            <label>Pilih Mata Kuliah (dari Master)</label>
            <select name="master_mk_id" class="form-control" required>
                <option value="">-- Pilih MK Induk --</option>
                <?php while($mm = mysqli_fetch_assoc($master_mk_list)): ?>
                    <option value="<?= $mm['id'] ?>">
                        <?= htmlspecialchars($mm['kode_mk']) ?> - <?= htmlspecialchars($mm['nama_mk']) ?>
                    </option>
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
            <small style="color: #64748b;">Dosen akan otomatis terisi berdasarkan kelas yang dipilih</small>
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
            <label>
                <input type="checkbox" name="is_latihan"> Mode Latihan (tidak dinilai)
            </label>
        </div>
        
        <button type="submit" name="simpan" class="btn-primary" style="width:100%">Simpan</button>
    </form>
</div>

<script>
// Saat kelas dipilih, otomatis isi dosen
document.getElementById('kelas_id').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var dosenId = selectedOption.getAttribute('data-dosen-id');
    var dosenNama = selectedOption.getAttribute('data-dosen-nama');
    
    document.getElementById('dosen_id').value = dosenId || '';
    document.getElementById('dosen_nama_display').value = dosenNama || '-';
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>