<?php
// File: dosen/jadwal/index.php
session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'dosen') {
    die("Akses ditolak!");
}

$dosen_id = $_SESSION['user_id'];
$success = '';
$error = '';
$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$edit_data = null;

// Ambil data jadwal yang akan diedit
if ($edit_id > 0) {
    $edit_query = mysqli_query($conn, "
        SELECT j.*, mki.kode_mk, mki.nama_mk, k.nama_kelas
        FROM jadwal_ujian j
        JOIN mata_kuliah_induk mki ON j.mk_induk_id = mki.id
        JOIN kelas k ON j.kelas_id = k.id
        WHERE j.id = $edit_id AND k.dosen_id = $dosen_id
    ");
    $edit_data = mysqli_fetch_assoc($edit_query);
}

// Handle tambah/update jadwal
if (isset($_POST['simpan'])) {
    $mk_induk_id = $_POST['mk_induk_id'];
    $kelas_id = $_POST['kelas_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'] . ' ' . $_POST['waktu_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'] . ' ' . $_POST['waktu_selesai'];
    $durasi_menit = $_POST['durasi_menit'];
    
    if ($edit_id > 0) {
        // Update jadwal
        $query = "UPDATE jadwal_ujian SET 
                  tanggal_mulai = '$tanggal_mulai', 
                  tanggal_selesai = '$tanggal_selesai', 
                  durasi_menit = $durasi_menit 
                  WHERE id = $edit_id";
        mysqli_query($conn, $query);
        $success = "Jadwal ujian berhasil diupdate!";
    } else {
        // Insert baru
        $cek = mysqli_query($conn, "SELECT id FROM jadwal_ujian WHERE mk_induk_id = $mk_induk_id AND kelas_id = $kelas_id");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Jadwal untuk MK dan kelas ini sudah ada! Silakan edit jika ingin mengubah.";
        } else {
            $query = "INSERT INTO jadwal_ujian (mk_induk_id, kelas_id, tanggal_mulai, tanggal_selesai, durasi_menit) 
                      VALUES ($mk_induk_id, $kelas_id, '$tanggal_mulai', '$tanggal_selesai', $durasi_menit)";
            mysqli_query($conn, $query);
            $success = "Jadwal ujian berhasil ditambahkan!";
        }
    }
    
    // Redirect setelah sukses untuk menghilangkan parameter edit di URL
    if (empty($error)) {
        header('Location: index.php');
        exit();
    }
}

// Handle hapus jadwal
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM jadwal_ujian WHERE id = $id");
    $success = "Jadwal ujian berhasil dihapus!";
}

// Ambil daftar MK Induk yang diajarkan dosen
$mk_list = mysqli_query($conn, "
    SELECT DISTINCT mki.id, mki.kode_mk, mki.nama_mk
    FROM mata_kuliah_induk mki
    JOIN mata_kuliah mk ON mk.mk_induk_id = mki.id
    JOIN kelas k ON mk.kelas_id = k.id
    WHERE k.dosen_id = $dosen_id
    ORDER BY mki.kode_mk
");

// Ambil semua jadwal yang sudah ada
$jadwal_list = mysqli_query($conn, "
    SELECT j.*, mki.kode_mk, mki.nama_mk, k.nama_kelas
    FROM jadwal_ujian j
    JOIN mata_kuliah_induk mki ON j.mk_induk_id = mki.id
    JOIN kelas k ON j.kelas_id = k.id
    WHERE k.dosen_id = $dosen_id
    ORDER BY j.tanggal_mulai DESC
");

include '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Jadwal Ujian</h1>
    <p class="page-subtitle">Atur jadwal ujian untuk setiap mata kuliah dan kelas</p>
</div>

<?php if($success): ?>
    <div class="alert success"><?= $success ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="alert error"><?= $error ?></div>
<?php endif; ?>

<div class="dashboard-grid">
    <!-- Form Kiri: Tambah/Edit Jadwal -->
    <div class="card-modern">
        <h3><i class="fas fa-calendar-plus"></i> <?= $edit_id > 0 ? 'Edit Jadwal Ujian' : 'Atur Jadwal Ujian' ?></h3>
        <?php if($edit_id > 0 && $edit_data): ?>
            <div class="alert info" style="margin-bottom: 16px;">
                <i class="fas fa-info-circle"></i> Sedang mengedit jadwal untuk: 
                <strong><?= htmlspecialchars($edit_data['kode_mk']) ?> - <?= htmlspecialchars($edit_data['nama_mk']) ?></strong> 
                di kelas <strong><?= htmlspecialchars($edit_data['nama_kelas']) ?></strong>
            </div>
        <?php endif; ?>
        
        <form method="POST" id="formJadwal">
            <?php if($edit_id > 0 && $edit_data): ?>
                <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label>Pilih Mata Kuliah</label>
                <select name="mk_induk_id" id="mk_induk_id" class="form-control" <?= $edit_id > 0 ? 'disabled' : 'required' ?>>
                    <option value="">-- Pilih MK --</option>
                    <?php while($mk = mysqli_fetch_assoc($mk_list)): ?>
                        <option value="<?= $mk['id'] ?>" 
                            <?= ($edit_data && $edit_data['mk_induk_id'] == $mk['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mk['kode_mk']) ?> - <?= htmlspecialchars($mk['nama_mk']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <?php if($edit_id > 0): ?>
                    <small style="color: #64748b;">MK tidak dapat diubah saat mengedit</small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Pilih Kelas</label>
                <select name="kelas_id" id="kelas_id" class="form-control" <?= $edit_id > 0 ? 'disabled' : 'required' ?>>
                    <?php if($edit_data): ?>
                        <option value="<?= $edit_data['kelas_id'] ?>" selected>
                            <?= htmlspecialchars($edit_data['nama_kelas']) ?>
                        </option>
                    <?php else: ?>
                        <option value="">-- Pilih MK Terlebih Dahulu --</option>
                    <?php endif; ?>
                </select>
                <?php if($edit_id > 0): ?>
                    <small style="color: #64748b;">Kelas tidak dapat diubah saat mengedit</small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" 
                       value="<?= $edit_data ? date('Y-m-d', strtotime($edit_data['tanggal_mulai'])) : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label>Waktu Mulai</label>
                <input type="time" name="waktu_mulai" class="form-control" 
                       value="<?= $edit_data ? date('H:i', strtotime($edit_data['tanggal_mulai'])) : '08:00' ?>" required>
            </div>
            
            <div class="form-group">
                <label>Tanggal Selesai (Batas Akhir)</label>
                <input type="date" name="tanggal_selesai" class="form-control" 
                       value="<?= $edit_data ? date('Y-m-d', strtotime($edit_data['tanggal_selesai'])) : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label>Waktu Selesai</label>
                <input type="time" name="waktu_selesai" class="form-control" 
                       value="<?= $edit_data ? date('H:i', strtotime($edit_data['tanggal_selesai'])) : '17:00' ?>" required>
            </div>
            
            <div class="form-group">
                <label>Durasi Ujian (menit)</label>
                <input type="number" name="durasi_menit" class="form-control" 
                       value="<?= $edit_data ? $edit_data['durasi_menit'] : '60' ?>" required>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" name="simpan" class="btn-primary"><?= $edit_id > 0 ? 'Update Jadwal' : 'Simpan Jadwal' ?></button>
                <?php if($edit_id > 0): ?>
                    <a href="index.php" class="btn-outline">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Tabel Kanan: Daftar Jadwal -->
    <div class="card-modern">
        <h3><i class="fas fa-list"></i> Daftar Jadwal Ujian</h3>
        <table class="table-modern">
            <thead>
                <tr>
                    <th>MK</th>
                    <th>Kelas</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($jadwal_list) > 0): ?>
                    <?php while($j = mysqli_fetch_assoc($jadwal_list)): 
                        $now = new DateTime();
                        $mulai = new DateTime($j['tanggal_mulai']);
                        $selesai = new DateTime($j['tanggal_selesai']);
                        
                        if ($now < $mulai) {
                            $status = '<span class="badge badge-warning">⏳ Akan Datang</span>';
                        } elseif ($now > $selesai) {
                            $status = '<span class="badge badge-danger">❌ Berakhir</span>';
                        } else {
                            $status = '<span class="badge badge-success">✅ Berlangsung</span>';
                        }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($j['kode_mk']) ?> </td>
                        <td><?= htmlspecialchars($j['nama_kelas']) ?> </td>
                        <td><?= date('d/m/Y H:i', strtotime($j['tanggal_mulai'])) ?> </td>
                        <td><?= date('d/m/Y H:i', strtotime($j['tanggal_selesai'])) ?> </td>
                        <td><?= $j['durasi_menit'] ?> menit </td>
                        <td><?= $status ?> </td>
                        <td>
                            <a href="?edit=<?= $j['id'] ?>" class="btn-primary" style="padding:4px 8px;font-size:12px;">Edit</a>
                            <a href="?hapus=<?= $j['id'] ?>" class="btn-danger" style="padding:4px 8px;font-size:12px;" onclick="return confirm('Yakin hapus jadwal ini?')">Hapus</a>
                         ﹏
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center">Belum ada jadwal ujian ﹏
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Hanya jalankan AJAX jika bukan mode edit
    <?php if($edit_id == 0): ?>
    $('#mk_induk_id').change(function() {
        var mk_induk_id = $(this).val();
        if (mk_induk_id) {
            $.ajax({
                url: 'get_kelas_by_mk.php',
                type: 'POST',
                data: {mk_induk_id: mk_induk_id},
                dataType: 'json',
                success: function(data) {
                    var html = '<option value="">-- Pilih Kelas --</option>';
                    if (data.length > 0) {
                        for (var i = 0; i < data.length; i++) {
                            html += '<option value="' + data[i].id + '">' + data[i].nama_kelas + ' (Dosen: ' + data[i].dosen_nama + ')</option>';
                        }
                        $('#kelas_id').prop('disabled', false);
                    } else {
                        html = '<option value="">-- Tidak ada kelas --</option>';
                        $('#kelas_id').prop('disabled', true);
                    }
                    $('#kelas_id').html(html);
                }
            });
        } else {
            $('#kelas_id').html('<option value="">-- Pilih MK Terlebih Dahulu --</option>');
            $('#kelas_id').prop('disabled', true);
        }
    });
    <?php endif; ?>
});
</script>

<?php include '../../includes/footer.php'; ?>