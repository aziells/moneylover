<?php
header('Content-Type: application/json');

// Panggil library Midtrans
require_once dirname(__DIR__) . '/midtrans-php/Midtrans.php';

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'Mid-server-5Om0Gp96cflRl75DK6YqEwGl';
\Midtrans\Config::$isProduction = false;

// Ambil order_id dari parameter
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';

if (empty($order_id)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Order ID tidak ditemukan'
    ]);
    exit;
}

try {
    // Cek status transaksi ke Midtrans
    $status = \Midtrans\Transaction::status($order_id);
    
    // Status yang menandakan pembayaran berhasil:
    // - settlement: Pembayaran sudah masuk
    // - capture: Pembayaran berhasil (untuk kartu kredit)
    $transaction_status = $status->transaction_status ?? 'unknown';
    $fraud_status = $status->fraud_status ?? 'accept';
    
    $is_success = false;
    $message = '';
    
    if ($transaction_status == 'capture') {
        if ($fraud_status == 'accept') {
            $is_success = true;
            $message = 'Pembayaran berhasil!';
        }
    } else if ($transaction_status == 'settlement') {
        $is_success = true;
        $message = 'Pembayaran berhasil!';
    } else if ($transaction_status == 'pending') {
        $message = 'Menunggu pembayaran...';
    } else if ($transaction_status == 'deny' || $transaction_status == 'expire' || $transaction_status == 'cancel') {
        $message = 'Pembayaran gagal atau dibatalkan';
    } else {
        $message = 'Status: ' . $transaction_status;
    }
    
    echo json_encode([
        'status' => 'success',
        'transaction_status' => $transaction_status,
        'is_paid' => $is_success,
        'message' => $message,
        'order_id' => $order_id
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
