<?php
    session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Refresh session timeout (perpanjang session)
$_SESSION['LAST_ACTIVITY'] = time();
?>