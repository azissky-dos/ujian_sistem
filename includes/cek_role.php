<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}
$allowed_roles = func_get_args();
if (!in_array($_SESSION['role'], $allowed_roles)) {
    echo "<script>alert('Akses ditolak!'); window.location='../auth/login.php';</script>";
    exit();
}
?>