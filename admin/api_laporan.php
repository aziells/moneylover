<?php
header('Content-Type: application/json');
include '../inc/koneksi.php';

// Query untuk mengambil data transaksi
// Kita GROUP BY waktu dan user untuk menyatukan item-item dalam satu transaksi yang sama
$query = "
    SELECT 
        m.tgl_pemesanan,
        m.jam_pemesanan,
        m.id_user,
        m.nama_pelanggan,
        m.metode_pembayaran,
        u.nama AS nama_kasir,
        GROUP_CONCAT(mn.nama_menu SEPARATOR ', ') as items,
        SUM(m.harga_total) as total_transaksi,
        COUNT(m.id_pemesanan) as jumlah_item
    FROM memesan m
    LEFT JOIN user u ON m.id_user = u.id_user
    LEFT JOIN menu mn ON m.id_menu = mn.id_menu
    GROUP BY m.tgl_pemesanan, m.jam_pemesanan, m.id_user, m.nama_pelanggan
    ORDER BY m.tgl_pemesanan DESC, m.jam_pemesanan DESC
";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    echo json_encode(['error' => mysqli_error($koneksi)]);
    exit;
}

$data = [];
$i = 1;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Generate No. Transaksi dari timestamp
        $timestamp = strtotime($row['tgl_pemesanan'] . ' ' . $row['jam_pemesanan']);
        $noTransaksi = 'TRX-' . date('YmdHis', $timestamp);
        
        $data[] = [
            'id' => $i++, // ID urut untuk frontend
            'tanggal' => $row['tgl_pemesanan'],
            'jam' => $row['jam_pemesanan'],
            'noTransaksi' => $noTransaksi,
            'kasir' => $row['nama_kasir'] ?: 'Admin/System', // Fallback jika kasir dihapus/null
            'pelanggan' => $row['nama_pelanggan'],
            'item' => $row['items'], // String gabungan nama menu
            'total' => (int)$row['total_transaksi'],
            'metode' => $row['metode_pembayaran'] ?: 'Tunai', // Default Tunai jika kosong
            'jumlah_item' => (int)$row['jumlah_item']
        ];
    }
}

echo json_encode($data);
