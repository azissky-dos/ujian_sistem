<?php
// config/config.php
if (!defined('BASE_URL')) {
    
    // Deteksi environment
    $is_local = false;
    $is_railway = false;
    
    if (isset($_SERVER['SERVER_NAME'])) {
        if ($_SERVER['SERVER_NAME'] == 'localhost' || 
            $_SERVER['SERVER_NAME'] == '127.0.0.1') {
            $is_local = true;
        }
    }
    
    if (getenv('RAILWAY_ENVIRONMENT') !== false) {
        $is_railway = true;
    }
    
    // Tentukan BASE_URL
    $base_url = '';
    if ($is_local) {
        // Sesuaikan dengan nama folder project Anda di local
        $base_url = '/Ujian_System';
    }
    // Untuk Railway, biarkan kosong
    
    // Tentukan BASE_PATH untuk include file (absolute path)
    if ($is_local) {
        $base_path = $_SERVER['DOCUMENT_ROOT'] . '/Ujian_System';
    } else {
        $base_path = __DIR__ . '/..';
    }
    
    define('BASE_URL', $base_url);
    define('BASE_PATH', $base_path);
    define('IS_LOCAL', $is_local);
    define('IS_RAILWAY', $is_railway);
}
?>