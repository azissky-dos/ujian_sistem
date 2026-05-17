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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Registrasi Mahasiswa - Ujian Online</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .auth-card {
            background: white;
            border-radius: 32px;
            padding: 40px;
            width: 100%;
            max-width: 650px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .auth-header i {
            font-size: 48px;
            color: #4f46e5;
            margin-bottom: 16px;
        }

        .auth-header h2 {
            font-size: 24px;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .auth-header p {
            color: #64748b;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1e293b;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
        }

        select.form-control {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234f46e5'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(99,102,241,0.4);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert.error {
            background: #fee2e2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }

        .alert.success {
            background: #dcfce7;
            color: #16a34a;
            border-left: 4px solid #16a34a;
        }

        .alert.info {
            background: #e0e7ff;
            color: #4338ca;
            border-left: 4px solid #4338ca;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            max-height: 300px;
            overflow-y: auto;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            background: white;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #e2e8f0;
        }

        .checkbox-item:hover {
            background: #e0e7ff;
            border-color: #6366f1;
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
            font-size: 13px;
            flex: 1;
        }

        .checkbox-item label strong {
            color: #4f46e5;
        }

        .select-all {
            margin-bottom: 12px;
            padding: 10px 12px;
            background: #f1f5f9;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #e2e8f0;
        }

        .select-all input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #4f46e5;
        }

        .select-all label {
            margin: 0;
            cursor: pointer;
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .auth-footer p {
            color: #64748b;
            font-size: 14px;
        }

        .auth-footer a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        /* Required field indicator */
        .required:after {
            content: " *";
            color: #dc2626;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .auth-card {
                padding: 24px;
            }
            
            .checkbox-group {
                grid-template-columns: 1fr;
            }
            
            .auth-header h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-user-plus"></i>
            <h2>Registrasi Mahasiswa</h2>
            <p>Daftar untuk mengikuti ujian online</p>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($success)): ?>
            <div class="alert success">
                <i class="fas fa-check-circle"></i> <?= $success ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" id="registerForm">
            <div class="form-group">
                <label class="required">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>
            
            <div class="form-group">
                <label class="required">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            
            <div class="form-group">
                <label class="required">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" placeholder="Masukkan nama lengkap" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="contoh: email@domain.com">
            </div>
            
            <div class="form-group">
                <label class="required">NIM</label>
                <input type="text" name="nim" class="form-control" placeholder="Masukkan NIM" required>
            </div>
            
            <div class="form-group">
                <label class="required">Pilih Kelas</label>
                <select name="kelas_id" id="kelas_id" class="form-control" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php while($k = mysqli_fetch_assoc($kelas_list)): ?>
                        <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="required">Pilih Mata Kuliah</label>
                <div id="mk_checklist">
                    <div class="alert info">
                        <i class="fas fa-info-circle"></i> Silakan pilih kelas terlebih dahulu
                    </div>
                </div>
                <small style="color: #64748b; font-size: 12px; margin-top: 8px; display: block;">
                    <i class="fas fa-check-circle"></i> Centang mata kuliah yang akan diikuti
                </small>
            </div>
            
            <button type="submit" name="register" class="btn-primary">
                <i class="fas fa-user-check"></i> Daftar Sekarang
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#kelas_id').change(function() {
        var kelas_id = $(this).val();
        if(kelas_id) {
            $('#mk_checklist').html('<div class="alert info"><i class="fas fa-spinner fa-spin"></i> Memuat data mata kuliah...</div>');
            
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
                        html += '<label for="select_all_mk">📋 Pilih Semua Mata Kuliah</label>';
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
                        html = '<div class="alert error"><i class="fas fa-exclamation-triangle"></i> Belum ada mata kuliah di kelas ini. Hubungi admin.</div>';
                    }
                    $('#mk_checklist').html(html);
                    
                    $('#select_all_mk').change(function() {
                        $('input[name="mk_induk_ids[]"]').prop('checked', $(this).prop('checked'));
                    });
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    $('#mk_checklist').html('<div class="alert error"><i class="fas fa-exclamation-circle"></i> Gagal memuat data mata kuliah. Silakan coba lagi.</div>');
                }
            });
        } else {
            $('#mk_checklist').html('<div class="alert info"><i class="fas fa-info-circle"></i> Silakan pilih kelas terlebih dahulu</div>');
        }
    });
});
</script>
</body>
</html>