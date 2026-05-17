<?php
// admin/backup/index.php
// ======================================================
// BACKUP & RESTORE DATABASE
// ======================================================

session_start();
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$backup_dir = __DIR__ . '/../../backup/';
$success = '';
$error = '';

// Buat folder backup jika belum ada
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0777, true);
}

// ======================================================
// FUNGSI BACKUP DATABASE
// ======================================================
function backupDatabase($conn, $backup_dir) {
    $tables = array();
    $result = mysqli_query($conn, "SHOW TABLES");
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }
    
    $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $filepath = $backup_dir . $filename;
    $file = fopen($filepath, 'w');
    
    if (!$file) return false;
    
    fwrite($file, "-- ======================================================\n");
    fwrite($file, "-- BACKUP DATABASE UJIAN ONLINE\n");
    fwrite($file, "-- Tanggal: " . date('Y-m-d H:i:s') . "\n");
    fwrite($file, "-- ======================================================\n\n");
    fwrite($file, "SET FOREIGN_KEY_CHECKS=0;\n\n");
    
    foreach ($tables as $table) {
        fwrite($file, "DROP TABLE IF EXISTS `$table`;\n");
        $create = mysqli_query($conn, "SHOW CREATE TABLE $table");
        $row = mysqli_fetch_row($create);
        fwrite($file, $row[1] . ";\n\n");
        
        $data = mysqli_query($conn, "SELECT * FROM $table");
        $num_fields = mysqli_num_fields($data);
        
        if (mysqli_num_rows($data) > 0) {
            fwrite($file, "INSERT INTO `$table` VALUES ");
            $row_count = 0;
            while ($row_data = mysqli_fetch_row($data)) {
                $row_count++;
                $values = array();
                for ($i = 0; $i < $num_fields; $i++) {
                    $value = $row_data[$i];
                    if (is_null($value)) {
                        $values[] = "NULL";
                    } else {
                        $values[] = "'" . mysqli_real_escape_string($conn, $value) . "'";
                    }
                }
                fwrite($file, "(" . implode(",", $values) . ")");
                if ($row_count < mysqli_num_rows($data)) {
                    fwrite($file, ",\n");
                } else {
                    fwrite($file, ";\n\n");
                }
            }
        } else {
            fwrite($file, "-- Tabel $table kosong\n\n");
        }
    }
    
    fwrite($file, "SET FOREIGN_KEY_CHECKS=1;\n");
    fclose($file);
    return $filename;
}

// ======================================================
// PROSES BACKUP
// ======================================================
if (isset($_POST['backup'])) {
    $result = backupDatabase($conn, $backup_dir);
    if ($result) {
        $success = "✅ Backup berhasil! File: {$result}";
    } else {
        $error = "❌ Backup gagal! Tidak dapat menulis file.";
    }
}

// ======================================================
// PROSES RESTORE
// ======================================================
if (isset($_POST['restore']) && isset($_POST['backup_file'])) {
    $backup_file = $_POST['backup_file'];
    $filepath = $backup_dir . $backup_file;
    
    if (file_exists($filepath) && filesize($filepath) > 100) {
        $sql_content = file_get_contents($filepath);
        $queries = explode(";\n", $sql_content);
        $error_count = 0;
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query) && 
                $query != "SET FOREIGN_KEY_CHECKS=0" && 
                $query != "SET FOREIGN_KEY_CHECKS=1") {
                if (!mysqli_query($conn, $query)) {
                    $error_count++;
                }
            }
        }
        
        if ($error_count == 0) {
            $success = "✅ Restore berhasil! Database dikembalikan dari file: {$backup_file}";
        } else {
            $error = "⚠️ Restore selesai dengan {$error_count} error.";
        }
    } else {
        $error = "❌ File backup tidak valid atau kosong!";
    }
}

// ======================================================
// PROSES DOWNLOAD & HAPUS
// ======================================================
if (isset($_GET['download'])) {
    $file = $backup_dir . $_GET['download'];
    if (file_exists($file)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit();
    }
}

if (isset($_GET['delete'])) {
    $file = $backup_dir . $_GET['delete'];
    if (file_exists($file)) {
        unlink($file);
        $success = "🗑️ File backup berhasil dihapus!";
    }
}

// Ambil daftar file backup
$backup_files = glob($backup_dir . '*.sql');
$valid_backups = array();
foreach ($backup_files as $file) {
    if (filesize($file) > 1024) {
        $valid_backups[] = $file;
    }
}
rsort($valid_backups);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Backup & Restore Database</h1>
    <p class="page-subtitle">Backup data atau restore dari file backup sebelumnya</p>
</div>

<?php if($success): ?>
    <div class="alert success"><?= $success ?></div>
<?php endif; ?>

<?php if($error): ?>
    <div class="alert error"><?= $error ?></div>
<?php endif; ?>

<div class="dashboard-grid">
    <!-- Card Backup -->
    <div class="card-modern">
        <i class="fas fa-database" style="font-size: 48px; color: #4f46e5;"></i>
        <h3 style="margin: 16px 0 8px 0;">Backup Database</h3>
        <p style="color: #64748b;">Membuat file backup SQL dari seluruh database</p>
        <form method="POST">
            <button type="submit" name="backup" class="btn-primary" style="width: 100%;" onclick="return confirm('Yakin ingin melakukan backup database?')">
                <i class="fas fa-cloud-upload-alt"></i> Backup Sekarang
            </button>
        </form>
    </div>
    
    <!-- Card Restore -->
    <div class="card-modern">
        <i class="fas fa-undo-alt" style="font-size: 48px; color: #4f46e5;"></i>
        <h3 style="margin: 16px 0 8px 0;">Restore Database</h3>
        <p style="color: #64748b;">Pulihkan database dari file backup</p>
        <form method="POST">
            <select name="backup_file" class="form-control" style="margin-bottom: 12px;" required>
                <option value="">-- Pilih File Backup --</option>
                <?php foreach($valid_backups as $file): ?>
                    <option value="<?= basename($file) ?>"><?= basename($file) ?> (<?= round(filesize($file)/1024, 2) ?> KB)</option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="restore" class="btn-warning" style="width: 100%; background: #f59e0b; color: white; border: none; padding: 10px 20px; border-radius: 12px; cursor: pointer;" onclick="return confirm('PERINGATAN! Restore akan MENIMPA data saat ini. Lanjutkan?')">
                <i class="fas fa-cloud-download-alt"></i> Restore Pilihan
            </button>
        </form>
    </div>
</div>

<!-- Daftar File Backup -->
<div class="card-modern" style="margin-top: 24px;">
    <h3><i class="fas fa-file-archive"></i> Daftar File Backup (Valid)</h3>
    <div style="overflow-x: auto;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Nama File</th>
                    <th>Ukuran</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($valid_backups) > 0): ?>
                    <?php foreach($valid_backups as $file): ?>
                    <tr>
                        <td><?= basename($file) ?></td>
                        <td><?= round(filesize($file)/1024, 2) ?> KB</td>
                        <td><?= date('d/m/Y H:i:s', filemtime($file)) ?></td>
                        <td>
                            <a href="?download=<?= basename($file) ?>" class="btn-primary" style="padding: 4px 10px; font-size: 12px;">Download</a>
                            <a href="?delete=<?= basename($file) ?>" class="btn-danger" style="padding: 4px 10px; font-size: 12px; background: #dc2626; color: white; border-radius: 8px; text-decoration: none; display: inline-block;" onclick="return confirm('Yakin hapus file backup ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">Belum ada file backup yang valid. Silakan backup terlebih dahulu.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="alert info" style="margin-top: 16px;">
    <i class="fas fa-info-circle"></i> 
    <strong>Catatan:</strong> 
    <ul style="margin-left: 20px; margin-top: 8px;">
        <li>Backup menggunakan PHP native (tidak perlu mysqldump)</li>
        <li>File backup disimpan di folder <strong>/backup/</strong></li>
        <li>Restore akan MENIMPA data saat ini, pastikan Anda sudah backup sebelum restore</li>
        <li>Hanya file backup dengan ukuran di atas 1KB yang ditampilkan</li>
    </ul>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>