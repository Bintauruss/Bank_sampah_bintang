<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

function cek_akses($allowed_roles = []) {
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        echo "<script>
            alert('Akses ditolak! Anda tidak memiliki izin untuk membuka halaman ini.');
            window.location='transaksi.php';
        </script>";
        exit;
    }
}
?>
