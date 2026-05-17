<?php
session_start();
include __DIR__ . '/../config/config.php';

session_destroy();
header('Location: login.php');
exit();
?>