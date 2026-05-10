<?php
header('Content-Type: application/json');
include '../inc/koneksi.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$today = date('Y-m-d');

// 1. Get Stats (Today)
$stats = [
    'pendapatan_hari_ini' => 0,
    'transaksi_hari_ini' => 0,
    'barang_terjual' => 0,
    'rata_rata' => 0
];

// Query for Today's Stats
// Note: We count unique 'nama_pelanggan' as unique transactions because 'id_pemesanan' is per item
$queryStats = "
    SELECT 
        SUM(harga_total) as revenue,
        COUNT(DISTINCT nama_pelanggan) as trx_count, 
        SUM(jumlah) as items_sold
    FROM memesan 
    WHERE tgl_pemesanan = '$today' AND status_pembayaran = 'success'
";
$resultStats = mysqli_query($koneksi, $queryStats);
if ($rowStats = mysqli_fetch_assoc($resultStats)) {
    $stats['pendapatan_hari_ini'] = (float)$rowStats['revenue'];
    $stats['transaksi_hari_ini'] = (int)$rowStats['trx_count'];
    $stats['barang_terjual'] = (int)$rowStats['items_sold'];
    
    if ($stats['transaksi_hari_ini'] > 0) {
        $stats['rata_rata'] = $stats['pendapatan_hari_ini'] / $stats['transaksi_hari_ini'];
    }
}

// 2. Get Recent Transactions (Limit 10)
// Grouping logic matched with Kasir
$queryTrx = "SELECT m.*, mn.nama_menu, u.nama as nama_kasir 
             FROM memesan m 
             LEFT JOIN menu mn ON m.id_menu = mn.id_menu 
             LEFT JOIN user u ON m.id_user = u.id_user 
             ORDER BY m.tgl_pemesanan DESC, m.jam_pemesanan DESC, m.id_pemesanan DESC";

$resultTrx = mysqli_query($koneksi, $queryTrx);
$grouped = [];
$limit = 10;
$count = 0;

while ($row = mysqli_fetch_assoc($resultTrx)) {
    $groupKey = $row['nama_pelanggan'];
    
    if (!isset($grouped[$groupKey])) {
        if ($count >= $limit) continue; // Stop creating new groups if limit reached
        
        $count++;
        
        // Extract ID
        $matches = [];
        $txId = $row['nama_pelanggan'];
        if (preg_match('/MLC\d+/', $row['nama_pelanggan'], $matches)) {
            $txId = $matches[0];
        }
        
        $grouped[$groupKey] = [
            'id' => $txId,
            'waktu' => $row['tgl_pemesanan'] . ' ' . $row['jam_pemesanan'],
            'kasir' => $row['nama_kasir'] ?: 'Admin/System',
            'total' => 0,
            'metode' => $row['metode_pembayaran'] ?: 'Tunai',
            'status' => $row['status_pembayaran'] == 'success' ? 'Selesai' : $row['status_pembayaran']
        ];
    }
    
    // Add to total regardless of limit (to ensure total is correct for the visible groups)
    if (isset($grouped[$groupKey])) {
        $grouped[$groupKey]['total'] += (float)$row['harga_total'];
    }
}

$dashboardData = [
    'status' => 'success',
    'stats' => $stats,
    'recent_transactions' => array_values($grouped)
];

echo json_encode($dashboardData);
?>
