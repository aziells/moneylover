<?php
// 1. Panggil library Midtrans
require_once dirname(__DIR__) . '/midtrans-php/Midtrans.php';
// 2. Konfigurasi (Ganti dengan Server Key dari Dashboard Midtrans kamu)
\Midtrans\Config::$serverKey = 'Mid-server-5Om0Gp96cflRl75DK6YqEwGl';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;

// 3. Ambil data total harga dari modal kasir kamu
$total = isset($_GET['total']) ? $_GET['total'] : 0;

$order_id = 'MLC-' . time() . '-' . rand(100, 999);

$params = array(
    'payment_type' => 'qris',
    'transaction_details' => array(
        'order_id' => $order_id,
        'gross_amount' => $total,
    ),
);

try {
    // Minta QRIS ke Midtrans
    $response = \Midtrans\CoreApi::charge($params);
    
    // Ambil URL gambar QRIS dari respon Midtrans
    if (isset($response->actions[0]->url)) {
        echo json_encode([
            'status' => 'success',
            'qr_url' => $response->actions[0]->url,
            'order_id' => $order_id
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'QRIS URL tidak ditemukan dalam response'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}