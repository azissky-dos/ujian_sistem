<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 7200,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Tentukan base path
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/Ujian_System';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Ujian Online</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/Ujian_System/assets/css/style.css">
</head>
<body>
<div class="app-wrapper">
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php include $base_path . '/includes/navbar.php'; ?>
    <?php endif; ?>
    <main class="main-content <?= isset($_SESSION['user_id']) ? 'with-sidebar' : 'full-width' ?>">
        <div class="content-container">