<?php
// includes/cek_login.php
// ======================================================
// CEK APAKAH USER SUDAH LOGIN
// ======================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Refresh session timeout
$_SESSION['LAST_ACTIVITY'] = time();
?>