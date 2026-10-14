<?php
session_start();

// Kalau belum login, tendang ke halaman login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}
?>
