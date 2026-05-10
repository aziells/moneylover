<?php
header('Content-Type: application/json');
include '../inc/koneksi.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Filters
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

$whereClause = "WHERE m.status_pembayaran = 'success'";
if ($bulan) {
    $whereClause .= " AND MONTH(m.tgl_pemesanan) = '$bulan'";
}
if ($tahun) {
    $whereClause .= " AND YEAR(m.tgl_pemesanan) = '$tahun'";
}

// Query: Group by Date
// One row = One Day
$query = "
    SELECT 
        m.tgl_pemesanan as tanggal,
        COUNT(DISTINCT m.nama_pelanggan) as jumlah_transaksi,
        SUM(m.harga_total) as total_pendapatan,
        GROUP_CONCAT(DISTINCT u.nama SEPARATOR ', ') as list_kasir
    FROM memesan m
    LEFT JOIN user u ON m.id_user = u.id_user
    $whereClause
    GROUP BY m.tgl_pemesanan
    ORDER BY m.tgl_pemesanan DESC
";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($koneksi)]);
    exit;
}

$reportData = [];
$subtotal_transaksi = 0;
$subtotal_pendapatan = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $reportData[] = [
        'tanggal' => $row['tanggal'], // YYYY-MM-DD
        'formatted_date' => date('d F Y', strtotime($row['tanggal'])),
        'jumlah_transaksi' => (int)$row['jumlah_transaksi'],
        'total_pendapatan' => (float)$row['total_pendapatan'],
        'kasir' => $row['list_kasir']
    ];
    
    $subtotal_transaksi += (int)$row['jumlah_transaksi'];
    $subtotal_pendapatan += (float)$row['total_pendapatan'];
}

$response = [
    'status' => 'success',
    'data' => $reportData,
    'summary' => [
        'total_transaksi' => $subtotal_transaksi,
        'total_pendapatan' => $subtotal_pendapatan,
        'rata_rata' => ($subtotal_transaksi > 0) ? ($subtotal_pendapatan / $subtotal_transaksi) : 0
    ]
];

echo json_encode($response);
?>
