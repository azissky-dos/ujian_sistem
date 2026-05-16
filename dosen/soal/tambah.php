<?php
session_start();
include '../../includes/cek_login.php';
include '../../config/database.php';

if ($_SESSION['role'] != 'dosen') {
    die("Akses ditolak!");
}

$dosen_id = $_SESSION['user_id'];
$mk_induk_id_dari_url = isset($_GET['mk_induk_id']) ? (int)$_GET['mk_induk_id'] : 0;

// Ambil daftar MK Induk yang diajarkan dosen (melalui kelas yang diajar)
$mk_induk_list = mysqli_query($conn, "
    SELECT DISTINCT mki.id, mki.kode_mk, mki.nama_mk
    FROM mata_kuliah_induk mki
    JOIN mata_kuliah mk ON mk.mk_induk_id = mki.id
    JOIN kelas k ON mk.kelas_id = k.id
    WHERE k.dosen_id = $dosen_id
    ORDER BY mki.kode_mk
");

if (isset($_POST['simpan'])) {
    $mk_induk_id = $_POST['mk_induk_id'];
    $tipe_soal = $_POST['tipe_soal'];
    $teks_soal = mysqli_real_escape_string($conn, $_POST['teks_soal']);
    $kunci_jawaban = mysqli_real_escape_string($conn, $_POST['kunci_jawaban']);
    $bobot = $_POST['bobot'];
    
    // Untuk tipe soal PG, ambil nilai pilihan
    $pilihan_A = mysqli_real_escape_string($conn, $_POST['pilihan_A'] ?? '');
    $pilihan_B = mysqli_real_escape_string($conn, $_POST['pilihan_B'] ?? '');
    $pilihan_C = mysqli_real_escape_string($conn, $_POST['pilihan_C'] ?? '');
    $pilihan_D = mysqli_real_escape_string($conn, $_POST['pilihan_D'] ?? '');
    $pilihan_E = mysqli_real_escape_string($conn, $_POST['pilihan_E'] ?? '');
    
    $query = "INSERT INTO soal (mk_induk_id, tipe_soal, teks_soal, pilihan_A, pilihan_B, pilihan_C, pilihan_D, pilihan_E, kunci_jawaban, bobot) 
              VALUES ('$mk_induk_id', '$tipe_soal', '$teks_soal', '$pilihan_A', '$pilihan_B', '$pilihan_C', '$pilihan_D', '$pilihan_E', '$kunci_jawaban', '$bobot')";
    mysqli_query($conn, $query);
    $success = "Soal berhasil ditambahkan! Soal ini akan tersedia untuk semua kelas yang memiliki MK ini.";
}

include '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Buat Soal</h1>
    <p class="page-subtitle">Soal akan tersedia untuk SEMUA kelas yang memiliki mata kuliah yang sama</p>
</div>

<?php if(isset($success)): ?>
    <div class="alert success"><?= $success ?></div>
<?php endif; ?>

<div class="card-modern" style="max-width:700px">
    <form method="POST" id="formSoal">
        <div class="form-group">
            <label>Pilih Mata Kuliah (Master)</label>
            <select name="mk_induk_id" id="mk_induk_id" class="form-control" required>
                <option value="">-- Pilih Mata Kuliah --</option>
                <?php while($mk = mysqli_fetch_assoc($mk_induk_list)): ?>
                    <option value="<?= $mk['id'] ?>" <?= ($mk_induk_id_dari_url == $mk['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($mk['kode_mk']) ?> - <?= htmlspecialchars($mk['nama_mk']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <small style="color: #10b981;">✓ Soal akan otomatis tersedia untuk semua kelas yang mengajar MK ini</small>
        </div>
        
        <div class="form-group">
            <label>Tipe Soal</label>
            <select name="tipe_soal" id="tipe_soal" class="form-control" required>
                <option value="pg">Pilihan Ganda (PG)</option>
                <option value="essay_mutlak">Essay Mutlak (jawaban harus persis)</option>
                <option value="essay_argumen">Essay Argument (dinilai berdasarkan kemiripan)</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Teks Soal</label>
            <textarea name="teks_soal" class="form-control" rows="4" required placeholder="Tulis pertanyaan soal di sini..."></textarea>
        </div>
        
        <!-- ========== PILIHAN A-E (khusus PG) ========== -->
        <div id="pg_options" style="display: none; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; margin-bottom: 20px; background: #f8fafc;">
            <h4 style="margin-bottom: 15px;">📝 Opsi Pilihan Ganda</h4>
            <div class="form-group">
                <label>A.</label>
                <input type="text" name="pilihan_A" class="form-control" placeholder="Teks untuk pilihan A">
            </div>
            <div class="form-group">
                <label>B.</label>
                <input type="text" name="pilihan_B" class="form-control" placeholder="Teks untuk pilihan B">
            </div>
            <div class="form-group">
                <label>C.</label>
                <input type="text" name="pilihan_C" class="form-control" placeholder="Teks untuk pilihan C">
            </div>
            <div class="form-group">
                <label>D.</label>
                <input type="text" name="pilihan_D" class="form-control" placeholder="Teks untuk pilihan D">
            </div>
            <div class="form-group">
                <label>E.</label>
                <input type="text" name="pilihan_E" class="form-control" placeholder="Teks untuk pilihan E">
            </div>
        </div>
        
        <div class="form-group">
            <label>Kunci Jawaban</label>
            <textarea name="kunci_jawaban" class="form-control" rows="3" required 
                placeholder="Untuk PG: cukup A/B/C/D/E&#10;Untuk Essay: tulis kunci jawaban lengkap"></textarea>
        </div>
        
        <div class="form-group">
            <label>Bobot Nilai</label>
            <input type="number" name="bobot" class="form-control" value="10" required>
        </div>
        
        <button type="submit" name="simpan" class="btn-primary">Simpan Soal</button>
        <a href="list.php" class="btn-outline">Lihat Daftar Soal</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#tipe_soal').change(function() {
        if ($(this).val() == 'pg') {
            $('#pg_options').slideDown(300);
        } else {
            $('#pg_options').slideUp(300);
        }
    });
    
    $('#tipe_soal').trigger('change');
});
</script>

<?php include '../../includes/footer.php'; ?>