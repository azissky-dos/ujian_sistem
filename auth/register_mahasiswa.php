<?php
include __DIR__ . '/../config/config.php';
include BASE_PATH . '/config/database.php';
include BASE_PATH . '/includes/fungsi.php';

// Ambil daftar kelas
$kelas_list = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas");

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nim = mysqli_real_escape_string($conn, $_POST['nim']);
    $kelas_id = $_POST['kelas_id'];
    $mk_induk_ids = isset($_POST['mk_induk_ids']) ? $_POST['mk_induk_ids'] : [];
    
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username sudah digunakan!";
    } elseif (empty($mk_induk_ids)) {
        $error = "Silakan pilih minimal satu mata kuliah!";
    } else {
        // Insert user
        $query_user = "INSERT INTO users (username, password, role, nama_lengkap, email, nim_nip) 
                       VALUES ('$username', '$password', 'mahasiswa', '$nama', '$email', '$nim')";
        mysqli_query($conn, $query_user);
        $user_id = mysqli_insert_id($conn);
        
        // Insert enrollment ke kelas
        $query_enroll = "INSERT INTO enrollments (mahasiswa_id, kelas_id, status) 
                         VALUES ($user_id, $kelas_id, 'active')";
        mysqli_query($conn, $query_enroll);
        $enrollment_id = mysqli_insert_id($conn);
        
        // Insert enrollment ke mata kuliah induk yang dipilih
        foreach ($mk_induk_ids as $mk_induk_id) {
            // Cari mk_id (per kelas) yang sesuai dengan mk_induk_id dan kelas_id
            $mk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM mata_kuliah WHERE mk_induk_id = $mk_induk_id AND kelas_id = $kelas_id"));
            if ($mk) {
                mysqli_query($conn, "INSERT INTO enrollment_mk (enrollment_id, mk_id, mk_induk_id) 
                                     VALUES ($enrollment_id, {$mk['id']}, $mk_induk_id)");
            }
        }
        
        $success = "Registrasi berhasil! Silakan login.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Mahasiswa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .checkbox-item:hover {
            background: #e0e7ff;
        }
        .checkbox-item input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #4f46e5;
        }
        .checkbox-item label {
            margin: 0;
            cursor: pointer;
            font-weight: normal;
            color: #1e293b;
        }
        .select-all {
            margin-bottom: 10px;
            padding: 8px;
            background: #f1f5f9;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="auth-card" style="max-width: 650px;">
        <div class="auth-header">
            <i class="fas fa-user-plus"></i>
            <h2>Registrasi Mahasiswa</h2>
            <p>Daftar untuk mengikuti ujian</p>
        </div>
        <?php if(isset($error)) echo "<div class='alert error'>$error</div>"; ?>
        <?php if(isset($success)) echo "<div class='alert success'>$success</div>"; ?>
        
        <form method="POST" id="registerForm">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label>NIM</label>
                <input type="text" name="nim" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Pilih Kelas</label>
                <select name="kelas_id" id="kelas_id" class="form-control" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php while($k = mysqli_fetch_assoc($kelas_list)): ?>
                        <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Pilih Mata Kuliah (centang yang akan diikuti)</label>
                <div id="mk_checklist">
                    <div class="alert info" style="text-align:center; background:#f1f5f9;">
                        <i class="fas fa-info-circle"></i> Silakan pilih kelas terlebih dahulu
                    </div>
                </div>
            </div>
            
            <button type="submit" name="register" class="btn-primary btn-block">Daftar</button>
        </form>
        <div class="auth-footer">
            <p>Sudah punya akun? <a href="<?= BASE_URL ?>/auth/login.php">Login</a></p>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#kelas_id').change(function() {
        var kelas_id = $(this).val();
        if(kelas_id) {
            $.ajax({
                url: '<?= BASE_URL ?>/auth/get_mk_by_kelas.php',
                type: 'POST',
                data: {kelas_id: kelas_id},
                dataType: 'json',
                success: function(data) {
                    var html = '';
                    if(data.length > 0) {
                        html += '<div class="select-all">';
                        html += '<input type="checkbox" id="select_all_mk">';
                        html += '<label for="select_all_mk" style="margin:0; font-weight:500;">Pilih Semua Mata Kuliah</label>';
                        html += '</div>';
                        html += '<div class="checkbox-group" id="checkbox_group">';
                        for(var i = 0; i < data.length; i++) {
                            html += '<div class="checkbox-item">';
                            html += '<input type="checkbox" name="mk_induk_ids[]" value="' + data[i].id + '" id="mk_' + data[i].id + '">';
                            html += '<label for="mk_' + data[i].id + '"><strong>' + data[i].kode_mk + '</strong> - ' + data[i].nama_mk + '</label>';
                            html += '</div>';
                        }
                        html += '</div>';
                    } else {
                        html = '<div class="alert error" style="text-align:center;">Belum ada mata kuliah di kelas ini. Hubungi admin.</div>';
                    }
                    $('#mk_checklist').html(html);
                    
                    $('#select_all_mk').change(function() {
                        $('input[name="mk_induk_ids[]"]').prop('checked', $(this).prop('checked'));
                    });
                }
            });
        } else {
            $('#mk_checklist').html('<div class="alert info" style="text-align:center; background:#f1f5f9;"><i class="fas fa-info-circle"></i> Silakan pilih kelas terlebih dahulu</div>');
        }
    });
});
</script>
</body>
</html>