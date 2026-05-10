<?php
include "../config/koneksi.php";

if (isset($_POST['submit'])) {
    $nama   = $_POST['nama_menu'];
    $harga  = $_POST['harga_jual'];
    $status = $_POST['status'];

    // id_menu tidak perlu dimasukkan karena Auto Increment di database
    $query = "INSERT INTO menu (nama_menu, harga_jual, status) VALUES ('$nama', '$harga', '$status')";
    $hasil = mysqli_query($koneksi, $query);

    if ($hasil) {
        echo "<script>alert('Data Berhasil Disimpan!'); window.location='../admin/barang.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "'); window.location='../admin/barang.php';</script>";
    }
}
?>