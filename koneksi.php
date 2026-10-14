<?php
// ================================
// KONEKSI DATABASE
// ================================
$host = "localhost";
$user = "root";
$pass = "";
$db   = "bank_sampah_bintang";   // <-- GANTI sesuai nama database PERSIS di phpMyAdmin

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
