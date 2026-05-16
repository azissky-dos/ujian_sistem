<?php
// config/config.php

// CEK APAKAH SUDAH DIDEKLARASIKAN SEBELUMNYA
if (!defined('BASE_URL')) {

    // Deteksi environment
    $is_local = false;
    $is_railway = false;

    if (isset($_SERVER['SERVER_NAME'])) {
        if ($_SERVER['SERVER_NAME'] == 'localhost' || 
            $_SERVER['SERVER_NAME'] == '127.0.0.1' ||
            strpos($_SERVER['SERVER_NAME'], '.test') !== false) {
            $is_local = true;
        }
    }

    if (getenv('RAILWAY_ENVIRONMENT') !== false || 
        (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'railway.app') !== false)) {
        $is_railway = true;
    }

    // Tentukan base URL
    $base_url = '';
    if ($is_local) {
        $base_url = '/Ujian_System';  // Sesuaikan dengan folder local Anda
    } else {
        $base_url = '';
    }

    // Tentukan base path
    $base_path = __DIR__ . '/..';

    // Definisikan konstanta (hanya SEKALI)
    define('BASE_URL', $base_url);
    define('BASE_PATH', $base_path);
    define('IS_LOCAL', $is_local);
    define('IS_RAILWAY', $is_railway);
}
?>