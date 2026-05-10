<?php
header('Content-Type: application/json');
include '../inc/koneksi.php';

session_start();

// Simple auth check
if (!isset($_SESSION['user_id'])) {
    // For now, if not logged in, just return empty or error.
    // Ideally should verify role too, but basic session check is fine for this step.
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Fetch all transactions
// Grouping logic:
// The 'memesan' table stores individual items.
// We use 'nama_pelanggan' (which contains the unique Receipt ID e.g. "Pelanggan MLC12345") 
// and 'tgl_pemesanan' + 'jam_pemesanan' to group them into a single transaction.

$query = "SELECT m.*, mn.nama_menu, u.nama as nama_kasir 
          FROM memesan m 
          LEFT JOIN menu mn ON m.id_menu = mn.id_menu 
          LEFT JOIN user u ON m.id_user = u.id_user 
          ORDER BY m.tgl_pemesanan DESC, m.jam_pemesanan DESC, m.id_pemesanan DESC";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($koneksi)]);
    exit;
}

$grouped = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Unique key for grouping: we use nama_pelanggan because it's unique per transaction
    $groupKey = $row['nama_pelanggan']; 
    
    // Extract ID like MLCxxxxx from "Pelanggan MLCxxxxx"
    $matches = [];
    $txId = $row['nama_pelanggan']; // Default
    if (preg_match('/MLC\d+/', $row['nama_pelanggan'], $matches)) {
        $txId = $matches[0];
    }
    
    if (!isset($grouped[$groupKey])) {
        // Initialize transaction object
        $grouped[$groupKey] = [
            'id' => $txId,
            'date' => $row['tgl_pemesanan'], // YYYY-MM-DD
            'time' => $row['jam_pemesanan'], // HH:MM:SS
            'items' => [],
            'total' => 0,
            'paymentMethod' => $row['metode_pembayaran'],
            'status' => ($row['status_pembayaran'] == 'success') ? 'completed' : $row['status_pembayaran'],
            // Since we don't store actual cash paid/change in DB, we infer them for history view
            // to prevent UI errors.
            'cashPaid' => 0, 
            'change' => 0,   
            'cashier' => $row['nama_kasir'] ?? 'Unknown'
        ];
    }
    
    // Determine item name (if menu deleted, use backup or generic)
    $itemName = $row['nama_menu'] ?? 'Item Terhapus';
    
    // Add item to this transaction
    $grouped[$groupKey]['items'][] = [
        'name' => $itemName,
        'quantity' => (int)$row['jumlah'],
        'price' => (float)$row['harga_total'] / (int)$row['jumlah'] // Calculate unit price
    ];
    
    // Accumulate total
    $grouped[$groupKey]['total'] += (float)$row['harga_total'];
    
    // Update cashPaid to at least match total for history display consistency
    // (Since we assume past transactions were successful)
    $grouped[$groupKey]['cashPaid'] = $grouped[$groupKey]['total'];
}

// Convert associative array to indexed array
$history = array_values($grouped);

echo json_encode(['status' => 'success', 'data' => $history]);
?>
