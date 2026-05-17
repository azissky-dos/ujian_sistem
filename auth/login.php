<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['LAST_ACTIVITY'] = time();
        
        if ($user['role'] == 'admin') {
            header('Location: admin/dashboard.php');
        } elseif ($user['role'] == 'dosen') {
            header('Location: ' . BASE_URL . '/dosen/dashboard.php');
        } elseif ($user['role'] == 'mahasiswa') {
            header('Location: ' . BASE_URL . '/mahasiswa/dashboard.php');
        }
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}

// Cek jika sudah login
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: ' . BASE_URL . '/admin/dashboard.php');
    } elseif ($_SESSION['role'] == 'dosen') {
        header('Location: ' . BASE_URL . '/dosen/dashboard.php');
    } elseif ($_SESSION['role'] == 'mahasiswa') {
        header('Location: ' . BASE_URL . '/mahasiswa/dashboard.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ujian Online</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .auth-container { display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .auth-card { background: white; border-radius: 32px; padding: 40px; width: 100%; max-width: 420px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
        .auth-header { text-align: center; margin-bottom: 32px; }
        .auth-header i { font-size: 48px; color: #4f46e5; margin-bottom: 16px; }
        .auth-header h2 { font-size: 24px; color: #0f172a; margin-bottom: 8px; }
        .auth-header p { color: #64748b; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #1e293b; font-size: 14px; }
        .form-group label i { margin-right: 8px; color: #4f46e5; }
        .form-control { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 14px; font-size: 14px; transition: all 0.3s ease; font-family: 'Inter', sans-serif; }
        .form-control:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
        .btn-primary { background: linear-gradient(135deg, #6366f1, #4f46e5); border: none; padding: 12px 24px; border-radius: 12px; color: white; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 16px; width: 100%; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 20px -5px rgba(99,102,241,0.4); }
        .btn-primary i { margin-right: 8px; }
        .alert { padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; }
        .alert.error { background: #fee2e2; color: #dc2626; border-left: 4px solid #dc2626; }
        .alert.error i { margin-right: 8px; }
        .auth-footer { text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .auth-footer p { color: #64748b; font-size: 14px; }
        .auth-footer a { color: #4f46e5; text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }
        .password-wrapper { position: relative; }
        .toggle-password { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #64748b; }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-graduation-cap"></i>
            <h2>Login Ujian Online</h2>
            <p>Silakan masuk dengan akun Anda</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                    <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                </div>
            </div>
            
            <button type="submit" name="login" class="btn-primary">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Belum punya akun? <a href="register_mahasiswa.php">Registrasi Mahasiswa</a></p>
        </div>
    </div>
</div>

<script>
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    if (togglePassword && password) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
</script>
</body>
</html>