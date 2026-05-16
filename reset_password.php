<?php
require_once 'config/database.php';

echo "<h1>Reset Password Semua User</h1>";

$new_password = 'password123';
$hashed = password_hash($new_password, PASSWORD_DEFAULT);

echo "Password baru: <strong>{$new_password}</strong><br>";
echo "Hash baru: <code>{$hashed}</code><br><br>";

// Update super_admin
$stmt = $pdo->prepare("UPDATE super_admin SET password = ?");
$stmt->execute([$hashed]);
echo "✅ Super admin password updated<br>";

// Update admin_prodi
$stmt = $pdo->prepare("UPDATE admin_prodi SET password = ?");
$stmt->execute([$hashed]);
echo "✅ Admin prodi password updated<br>";

// Update dosen
$stmt = $pdo->prepare("UPDATE dosen SET password = ?");
$stmt->execute([$hashed]);
echo "✅ Dosen password updated<br>";

// Update mahasiswa
$stmt = $pdo->prepare("UPDATE mahasiswa SET password = ?");
$stmt->execute([$hashed]);
echo "✅ Mahasiswa password updated<br>";

echo "<hr>";
echo "<h3>Semua password telah direset ke: <code>password123</code></h3>";
echo "<a href='index.php?page=login'>Kembali ke Halaman Login</a>";
?>