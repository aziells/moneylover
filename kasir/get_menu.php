<?php
session_start();
require_once '../inc/koneksi.php';

// Cek apakah user sudah login dan merupakan kasir
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'kasir') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Set header untuk JSON response
header('Content-Type: application/json');

try {
    // Ambil semua barang yang tersedia (stok > 0 dan status bukan non-aktif)
    $query = "SELECT * FROM barang WHERE stok > 0 AND status != 'non-aktif' ORDER BY kategori, nama_barang";
    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($koneksi)]);
        exit();
    }

    // Kelompokkan barang berdasarkan kategori
    $menuByCategory = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $kategori = $row['kategori'];

        if (!isset($menuByCategory[$kategori])) {
            $menuByCategory[$kategori] = [
                'name' => $kategori,
                'items' => []
            ];
        }

        $menuByCategory[$kategori]['items'][] = [
            'id' => (int) $row['id_barang'],
            'kode' => $row['kode_barang'],
            'name' => $row['nama_barang'],
            'price' => (int) $row['harga'],
            'stok' => (int) $row['stok'],
            'gambar' => $row['gambar']
        ];
    }

    // Konversi ke array
    $categories = array_values($menuByCategory);

    echo json_encode([
        'success' => true,
        'data' => $categories
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

mysqli_close($koneksi);
?>