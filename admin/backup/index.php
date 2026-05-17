<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

require_once BASE_PATH . '/config/database.php';

$backup_dir = BASE_PATH . '/backup/';
$success = '';
$error = '';

if (!is_dir($backup_dir)) mkdir($backup_dir, 0777, true);

function backupDatabase($conn, $backup_dir) {
    $tables = [];
    $result = mysqli_query($conn, "SHOW TABLES");
    while ($row = mysqli_fetch_row($result)) $tables[] = $row[0];
    
    $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $filepath = $backup_dir . $filename;
    $file = fopen($filepath, 'w');
    if (!$file) return false;
    
    fwrite($file, "-- Backup Database\nSET FOREIGN_KEY_CHECKS=0;\n\n");
    
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
                $values = [];
                for ($i = 0; $i < $num_fields; $i++) {
                    $value = $row_data[$i];
                    $values[] = is_null($value) ? "NULL" : "'" . mysqli_real_escape_string($conn, $value) . "'";
                }
                fwrite($file, "(" . implode(",", $values) . ")");
                fwrite($file, $row_count < mysqli_num_rows($data) ? ",\n" : ";\n\n");
            }
        }
    }
    
    fwrite($file, "SET FOREIGN_KEY_CHECKS=1;\n");
    fclose($file);
    return $filename;
}

if (isset($_POST['backup'])) {
    $result = backupDatabase($conn, $backup_dir);
    $result ? $success = "✅ Backup berhasil! File: {$result}" : $error = "❌ Backup gagal!";
}

if (isset($_POST['restore']) && isset($_POST['backup_file'])) {
    $filepath = $backup_dir . $_POST['backup_file'];
    if (file_exists($filepath) && filesize($filepath) > 100) {
        $queries = explode(";\n", file_get_contents($filepath));
        $error_count = 0;
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query) && $query != "SET FOREIGN_KEY_CHECKS=0" && $query != "SET FOREIGN_KEY_CHECKS=1") {
                if (!mysqli_query($conn, $query)) $error_count++;
            }
        }
        $error_count == 0 ? $success = "✅ Restore berhasil!" : $error = "⚠️ Restore selesai dengan {$error_count} error.";
    } else {
        $error = "❌ File backup tidak valid!";
    }
}

if (isset($_GET['download'])) {
    $file = $backup_dir . $_GET['download'];
    if (file_exists($file)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        readfile($file);
        exit();
    }
}

if (isset($_GET['delete'])) {
    $file = $backup_dir . $_GET['delete'];
    if (file_exists($file)) unlink($file);
    header('Location: ' . BASE_URL . '/admin/backup/index.php');
    exit();
}

$backup_files = glob($backup_dir . '*.sql');
$valid_backups = array_filter($backup_files, function($f) { return filesize($f) > 1024; });
rsort($valid_backups);

require_once BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Backup & Restore</h1>
</div>

<?php if($success): ?>
    <div class="alert success"><?= $success ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="alert error"><?= $error ?></div>
<?php endif; ?>

<div class="dashboard-grid">
    <div class="card-modern">
        <form method="POST">
            <button type="submit" name="backup" class="btn-primary" style="width:100%" onclick="return confirm('Yakin backup database?')">Backup Sekarang</button>
        </form>
    </div>
    <div class="card-modern">
        <form method="POST">
            <select name="backup_file" class="form-control" style="margin-bottom:12px" required>
                <option value="">-- Pilih File --</option>
                <?php foreach($valid_backups as $file): ?>
                    <option value="<?= basename($file) ?>"><?= basename($file) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="restore" class="btn-warning" style="width:100%; background:#f59e0b; color:white; padding:10px; border-radius:12px;" onclick="return confirm('Restore akan menimpa data! Lanjutkan?')">Restore</button>
        </form>
    </div>
</div>

<div class="card-modern">
    <h3>Daftar Backup</h3>
    <table class="table-modern">
        <thead><tr><th>Nama File</th><th>Ukuran</th><th>Tanggal</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach($valid_backups as $file): ?>
            <tr>
                <td><?= basename($file) ?></td>
                <td><?= round(filesize($file)/1024, 2) ?> KB</td>
                <td><?= date('d/m/Y H:i:s', filemtime($file)) ?></td>
                <td>
                    <a href="?download=<?= basename($file) ?>" class="btn-primary" style="padding:4px 10px;">Download</a>
                    <a href="?delete=<?= basename($file) ?>" class="btn-danger" style="padding:4px 10px;" onclick="return confirm('Yakin hapus?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>