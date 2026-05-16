<?php
session_start();
echo "Session ID: " . session_id() . "<br>";
echo "Session data: <pre>";
print_r($_SESSION);
echo "</pre>";
?>