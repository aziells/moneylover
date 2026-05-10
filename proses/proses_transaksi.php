<?php
header('Content-Type: application/json');
include '../inc/koneksi.php';

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}

$id_user = $data['id_user']; // ID petugas/kasir
$nama_pelanggan = $data['nama_pelanggan'] ?? 'Umum';
$items = $data['items'];
$metode_pembayaran = $data['metode_pembayaran'];
$tgl = date('Y-m-d');
$jam = date('H:i:s');

$successCount = 0;
$errors = [];

foreach ($items as $item) {
    $id_menu = $item['id'];
    $jumlah = $item['quantity'];
    $harga_total = $item['price'] * $jumlah;
    $status_pembayaran = 'success'; // Since this is POS, payment is immediate

    $query = "INSERT INTO memesan (id_user, id_menu, nama_pelanggan, jumlah, harga_total, tgl_pemesanan, jam_pemesanan, metode_pembayaran, status_pembayaran) 
              VALUES ('$id_user', '$id_menu', '$nama_pelanggan', '$jumlah', '$harga_total', '$tgl', '$jam', '$metode_pembayaran', '$status_pembayaran')";

    if (mysqli_query($koneksi, $query)) {
        // Kurangi stok di tabel menu
        $updateStock = "UPDATE menu SET stok = stok - $jumlah WHERE id_menu = '$id_menu'";
        mysqli_query($koneksi, $updateStock);
        
        $successCount++;
    } else {
        $errors[] = mysqli_error($koneksi);
    }
}

if ($successCount > 0) {
    echo json_encode(['status' => 'success', 'message' => "$successCount items saved"]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save transaction', 'errors' => $errors]);
}
?>
