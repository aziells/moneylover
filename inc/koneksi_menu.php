<?php

$koneksi = mysqli_connect("localhost", "root", "", "moneylover");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
