<?php
// Pengaturan koneksi ke database MySQL
$host = "localhost";
$user = "root";
$pass = "";
$db   = "moneylover";

$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>