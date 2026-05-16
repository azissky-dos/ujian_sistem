<?php
session_start();

// Ganti baris include '../config/database.php'; dengan ini:
if (getenv('MYSQLHOST') || isset($_ENV['MYSQLHOST'])) {
    // Jalur mutlak khusus di server Railway (Linux)
    include $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
} else {
    // Jalur mutlak khusus di XAMPP laptop Bapak (Windows)
    include $_SERVER['DOCUMENT_ROOT'] . '/Ujian_System/config/database.php';
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama'] = $user['nama_lengkap'];
        
        if ($user['role'] == 'admin') header('Location: ../admin/dashboard.php');
        elseif ($user['role'] == 'dosen') header('Location: ../dosen/dashboard.php');
        else header('Location: ../mahasiswa/dashboard.php');
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Ujian Online</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-graduation-cap"></i>
            <h2>Aplikasi Ujian Online</h2>
            <p>Login untuk melanjutkan</p>
        </div>
        <?php if(isset($error)) echo "<div class='alert error'>$error</div>"; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" name="login" class="btn-primary btn-block">Login</button>
        </form>
        <div class="auth-footer">
            <p>Belum punya akun? <a href="register_mahasiswa.php">Register Mahasiswa</a></p>
            <p style="margin-top: 8px; font-size: 12px; color: #94a3b8;">Lupa password? Hubungi dosen atau admin.</p>
        </div>
    </div>
</div>
</body>
</html>