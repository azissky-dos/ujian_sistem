<?php
include 'auth/login.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah file database ada di folder lokal atau cloud
if (file_exists('config/database.php')) {
    include 'config/database.php';
} else {
    include '../config/database.php';
}
?>