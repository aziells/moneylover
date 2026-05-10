<?php
session_start();
include '../koneksi.php';

// Fetch menu data
$queryMenu = "SELECT * FROM menu WHERE status = 'tersedia'";
$resultMenu = mysqli_query($koneksi, $queryMenu);
$menuItems = [];
if ($resultMenu) {
    while ($row = mysqli_fetch_assoc($resultMenu)) {
        $menuItems[] = $row;
    }
}

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'kasir') {
    header("Location: ../login.php");
    exit();
}
// $_SESSION['user_id'] = 1;
// $_SESSION['level'] = 'kasir';
// $_SESSION['nama'] = 'Test Kasir';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoneyLover - Kasir Coffee Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Specific Cashier Overrides */
        .coffee-header {
            background: linear-gradient(135deg, var(--primary-brown), var(--dark-brown));
            padding: var(--spacing-md) 0;
            border-bottom: 3px solid var(--golden-brown);
            margin-bottom: var(--spacing-md);
        }
        
        .logo {
            font-size: 2rem;
            font-weight: 800;
            color: var(--white);
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        /* Layout Specifics */
        .menu-container, .cart-container {
            height: calc(100vh - 220px);
            overflow-y: auto;
            background-color: var(--white);
            border: 2px solid var(--tan);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            display: flex;
            flex-direction: column;
        }

        .menu-header, .cart-header {
            background: linear-gradient(135deg, var(--cream), #fff9f0);
            padding: var(--spacing-md);
            border-bottom: 1px solid var(--tan);
        }

        .menu-header h4, .cart-header h4 {
            margin: 0;
            color: var(--primary-brown);
            font-weight: 700;
            font-size: var(--font-lg);
        }
        
        .menu-section {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: var(--spacing-md);
            padding: var(--spacing-md);
            overflow-y: auto;
        }
        
        .menu-card {
            background: var(--white);
            border: 1px solid var(--tan);
            border-radius: var(--radius-md);
            height: auto;
            min-height: 150px;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .menu-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--secondary-brown);
        }
        
        .menu-img {
            height: 100px;
            background-color: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark-gray);
        }
        
        .menu-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .menu-info {
            padding: var(--spacing-sm);
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .menu-item-name {
            font-weight: 600;
            color: var(--dark-brown);
            margin-bottom: var(--spacing-xs);
            line-height: 1.2;
        }
        
        .menu-item-price {
            color: var(--primary-brown);
            font-weight: 700;
            margin-bottom: var(--spacing-sm);
        }

        .btn-add {
            width: 100%;
            padding: 5px;
            font-size: var(--font-sm);
            background-color: var(--primary-brown);
            color: var(--white);
            border: none;
            border-radius: var(--radius-sm);
            transition: background 0.2s;
        }

        .btn-add:hover {
            background-color: var(--secondary-brown);
        }
        
        /* Cart Styles */
        .cart-items-container {
            flex-grow: 1;
            padding: var(--spacing-md);
            overflow-y: auto;
        }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-sm) 0;
            border-bottom: 1px dashed var(--tan);
        }
        
        .cart-item-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-quantity {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 1px solid var(--tan);
            background-color: var(--cream);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-quantity:hover {
            background-color: var(--tan);
        }

        .cart-total {
            background-color: var(--light-gray);
            padding: var(--spacing-md);
            border-top: 2px solid var(--tan);
        }

        /* Tabs Custom */
        .nav-tabs .nav-link {
            color: var(--dark-brown);
            font-weight: 600;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--primary-brown);
            border-color: var(--tan) var(--tan) var(--white);
            background-color: var(--white);
        }

        /* History Container */
        .history-container {
            background-color: var(--white);
            border: 2px solid var(--tan);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: var(--spacing-md);
            min-height: calc(100vh - 220px);
        }

        .table-history {
            width: 100%;
            border-collapse: collapse;
        }

        /* History Table override */
        .table-history th {
            background: linear-gradient(135deg, var(--primary-brown), var(--secondary-brown));
            color: var(--white);
            padding: 10px;
        }
        .table-history td {
            padding: 10px;
            border-bottom: 1px solid var(--medium-gray);
        }

        .header-actions {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-actions .nav-tabs {
            border-bottom: 0;
        }

        .header-actions .nav-tabs .nav-link {
            background: rgba(255,255,255,0.9);
            border: 1px solid var(--tan);
            color: var(--primary-brown);
            border-radius: 20px;
            padding: 5px 12px;
            font-weight: 600;
        }

        .header-actions .nav-tabs .nav-link.active {
            background: var(--white);
            border-color: var(--tan);
            color: var(--primary-brown);
        }

        .logout-btn {
            position: static;
            background: rgba(255,255,255,0.9);
            color: var(--primary-brown);
            padding: 5px 15px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s;
            white-space: nowrap;
        }
        .logout-btn:hover {
            transform: translateY(-2px);
            background: var(--white);
            color: var(--secondary-brown);
        }
        
        /* Buttons & Badges */
        .btn-view {
            background-color: var(--light-brown);
            color: var(--dark-brown);
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        
        .btn-view:hover {
            background-color: var(--secondary-brown);
            color: var(--white);
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-completed {
            background-color: #e8f5e9;
            color: var(--green-success);
        }
        
        .status-pending {
            background-color: #fff3e0;
            color: #ef6c00;
        }
        
        .status-cancelled {
            background-color: #ffebee;
            color: #c62828;
        }
        
        .btn-action {
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: none;
            width: 100%;
            margin-top: 10px;
        }
        
        .btn-bayar {
            background: linear-gradient(to right, var(--secondary-brown), var(--primary-brown));
            color: var(--white);
        }
        
        .btn-batal {
            background-color: var(--light-gray);
            color: var(--dark-brown);
            border: 1px solid var(--tan);
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }
        
        /* Modal & Receipt */
        .modal-header-custom {
            background: linear-gradient(to right, var(--primary-brown), var(--secondary-brown));
            color: var(--white);
        }
        
        .payment-option {
            border: 2px solid var(--tan);
            border-radius: var(--radius-md);
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
            background-color: var(--white);
        }
        
        .payment-option:hover {
            border-color: var(--secondary-brown);
            transform: translateY(-3px);
            box-shadow: var(--shadow-sm);
        }
        
        .payment-option.selected {
            border-color: var(--primary-brown);
            background-color: var(--cream);
        }
        
        .payment-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: var(--secondary-brown);
        }
        
        .receipt-container {
            font-family: 'Courier New', Courier, monospace;
            max-width: 400px;
            margin: 0 auto;
            background-color: var(--white);
            padding: 20px;
            border-radius: 5px;
            border: 1px solid var(--medium-gray);
        }

        .receipt-header {
            text-align: center;
            font-weight: bold;
            font-size: 1.4rem;
            margin-bottom: 15px;
            color: var(--primary-brown);
        }
        
        .receipt-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding-bottom: 5px;
            border-bottom: 1px dashed var(--medium-gray);
        }
        
        .receipt-line.total {
            font-weight: bold;
            border-top: 2px dashed var(--dark-brown);
            padding-top: 10px;
            margin-top: 10px;
            border-bottom: none;
        }
        
        .thank-you {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
            padding-top: 15px;
            border-top: 2px dashed #ccc;
        }
        
        .qr-code-placeholder {
            width: 250px;
            min-height: 250px;
            background-color: var(--white);
            border: 2px solid var(--tan);
            border-radius: 10px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: var(--dark-gray);
            transition: all 0.3s ease;
        }

        /* Animasi untuk notifikasi pembayaran berhasil */
        @keyframes successPulse {
            0% { transform: scale(0.8); opacity: 0; }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes checkmark {
            0% { stroke-dashoffset: 100; }
            100% { stroke-dashoffset: 0; }
        }

        .payment-success-container {
            text-align: center;
            padding: 20px;
            animation: successPulse 0.5s ease-out;
        }

        .payment-success-icon {
            font-size: 100px;
            color: #28a745;
            animation: successPulse 0.6s ease-out;
        }

        .payment-success-title {
            color: #28a745;
            margin-top: 15px;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .payment-success-subtitle {
            color: #666;
            margin-top: 10px;
        }

        .payment-success-order {
            background: #e8f5e9;
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 10px;
            font-size: 0.9rem;
            color: #2e7d32;
        }

        .empty-cart, .empty-history {
            text-align: center;
            padding: 40px 20px;
            color: var(--dark-gray);
        }
        
        .empty-cart i, .empty-history i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--tan);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="coffee-header position-relative">
        <div class="container d-flex justify-content-center">
            <div class="logo">MoneyLover</div>
        </div>
        <div class="header-actions">
            <ul class="nav nav-tabs" id="mainTabs">
                <li class="nav-item">
                    <a class="nav-link active" id="kasir-tab" data-bs-toggle="tab" href="#kasir">
                        <i class="fas fa-cash-register"></i> Kasir
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="riwayat-tab" data-bs-toggle="tab" href="#riwayat">
                        <i class="fas fa-history"></i> Riwayat
                    </a>
                </li>
            </ul>
            <a href="../proses/proses_logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    
    <div class="container-fluid px-4 mt-3">
        <!-- Tab Content -->
        <div class="tab-content" id="mainTabContent">
            <!-- Kasir Tab -->
            <div class="tab-pane fade show active" id="kasir" role="tabpanel" aria-labelledby="kasir-tab">
                <!-- Petugas Info -->
                <div class="card mb-3">
                    <div class="card-body py-2 px-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0 fs-6 text-primary-brown"><i class="fas fa-user-tie"></i> Petugas: <strong id="petugas-nama"><?php echo $_SESSION['nama']; ?></strong></h5>
                                <p class="mb-0 text-muted small"><i class="fas fa-store"></i> MoneyLover - Jln. Cinta No. 23</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="mb-0 text-muted small"><i class="fas fa-calendar-day"></i> <span id="current-date">26/11/2025 14:07</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Menu Section (Left) -->
                    <div class="col-lg-8">
                        <div class="menu-container">
                            <div class="menu-header">
                                <h4><i class="fas fa-coffee"></i> Daftar Menu</h4>
                            </div>
                            
                            <div class="menu-section" id="menu-content">
                                <?php if (empty($menuItems)): ?>
                                    <p class="text-center w-100">Belum ada menu tersedia.</p>
                                <?php else: ?>
                                    <?php foreach ($menuItems as $item): ?>
                                        <div class="menu-card">
                                            <div class="menu-img">
                                                <?php if (!empty($item['gambar'])): ?>
                                                    <img src="../assets/images/items/<?php echo $item['gambar']; ?>" alt="<?php echo htmlspecialchars($item['nama_menu']); ?>" onerror="this.onerror=null;this.parentNode.innerHTML='<i class=&quot;fas fa-utensils fa-3x&quot;></i>';">
                                                <?php else: ?>
                                                    <i class="fas fa-utensils fa-3x"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="menu-info">
                                                <div class="menu-item-name"><?php echo htmlspecialchars($item['nama_menu']); ?></div>
                                                <div class="small text-muted mb-1">Stok: <?php echo $item['stok']; ?></div>
                                                <div class="menu-item-price">Rp <?php echo number_format($item['harga_jual'], 0, ',', '.'); ?></div>
                                                
                                                <button class="btn-add" onclick="addToCart(<?php echo $item['id_menu']; ?>)" <?php echo ($item['stok'] <= 0) ? 'disabled style="background-color: #ccc; cursor: not-allowed;"' : ''; ?>>
                                                    <?php echo ($item['stok'] > 0) ? '+ Tambah' : 'Habis'; ?>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cart Section (Right) -->
                    <div class="col-lg-4">
                        <div class="cart-container">
                            <div class="cart-header">
                                <h4><i class="fas fa-shopping-cart"></i> Keranjang</h4>
                            </div>
                            
                            <div class="cart-items-container" id="cart-items">

                                <div class="empty-cart">
                                    <i class="fas fa-coffee"></i>
                                    <p>Keranjang kosong</p>
                                    <p class="small">Pilih menu dari daftar menu</p>
                                </div>
                            </div>
                            
                            <div class="cart-total">
                                <div class="row">
                                    <div class="col-6">
                                        <h5>Total:</h5>
                                    </div>
                                    <div class="col-6 text-end">
                                        <h5 id="total-all">Rp 0</h5>
                                    </div>
                                </div>
                                
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <button class="btn-action btn-batal" id="btn-batal">Batal Transaksi</button>
                                        <button class="btn-action btn-bayar" id="btn-bayar">Bayar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Riwayat Pemesanan Tab -->
            <div class="tab-pane fade" id="riwayat" role="tabpanel" aria-labelledby="riwayat-tab">
                <!-- Petugas Info -->
                <div class="card mb-3">
                    <div class="card-body py-2 px-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0 fs-6 text-primary-brown"><i class="fas fa-user-tie"></i> Petugas: <strong id="history-petugas-nama"><?php echo $_SESSION['nama']; ?></strong></h5>
                                <p class="mb-0 text-muted small"><i class="fas fa-history"></i> Riwayat Transaksi Pemesanan</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="mb-0 text-muted small"><i class="fas fa-calendar-day"></i> <span id="history-current-date">26/11/2025 14:07</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filter Section -->
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label for="filter-date" class="form-label small">Tanggal</label>
                                <input type="date" class="form-control form-control-sm" id="filter-date" value="2025-11-26">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="filter-status" class="form-label small">Status</label>
                                <select class="form-select form-select-sm" id="filter-status">
                                    <option value="">Semua Status</option>
                                    <option value="completed">Selesai</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="filter-payment" class="form-label small">Metode Bayar</label>
                                <select class="form-select form-select-sm" id="filter-payment">
                                    <option value="">Semua Metode</option>
                                    <option value="cash">Cash</option>
                                    <option value="qr">QR Code</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2 d-flex align-items-end">
                                <button class="btn btn-secondary btn-sm w-100" id="btn-reset-filter">
                                    <i class="fas fa-redo"></i> Reset Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- History Table -->
                <div class="history-container">
                    <div class="history-table-container">
                        <table class="table-history">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="history-table-body">
                                
                            </tbody>
                        </table>
                        
                        <!-- Empty state -->
                        <div id="empty-history" class="empty-history" style="display: none;">
                            <i class="fas fa-receipt"></i>
                            <p>Belum ada riwayat transaksi</p>
                            <p class="small">Transaksi yang berhasil akan muncul di sini</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Pilih Metode Pembayaran -->
    <div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-labelledby="paymentMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="paymentMethodModalLabel">Pilih Metode Pembayaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="payment-option" id="cash-option">
                                <div class="payment-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <h5>Cash</h5>
                                <p>Bayar dengan uang tunai</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                        <div class="payment-option" id="qr-option" onclick="panggilQRIS()" style="cursor:pointer;">
                            <div class="payment-icon">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <h5>QR Code</h5>
                            <p>Scan untuk pembayaran digital</p>
                        </div>
                    </div>

                   <div id="qr-form" style="display: none;">
                        <div class="qr-code-container text-center">
                            <h5>Scan QR Code untuk Pembayaran</h5>
                            <div class="qr-code-placeholder" style="min-height: 250px; display: flex; align-items: center; justify-content: center; border: 1px dashed #ccc; margin-bottom: 15px;">
                                
                                <img id="tampilan-qris" src="" alt="QRIS" style="max-width: 100%; display: none;">
                                
                                <div id="loading-qris">
                                    <i class="fas fa-spinner fa-spin" style="font-size: 3rem;"></i>
                                    <div>Menyiapkan QRIS...</div>
                                </div>

                            </div>
                            <p>Total: <strong id="qr-total-display">Rp 0</strong></p>
                            <p class="small text-muted">Scan dengan aplikasi pembayaran digital Anda</p>
                        </div>
                    </div>
                    <!-- Form untuk Cash -->
                    <div id="cash-form" style="display: none;">
                        <div class="mb-3">
                            <label for="total-bayar" class="form-label">Total Bayar</label>
                            <input type="text" class="form-control" id="total-bayar" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="uang-dibayar" class="form-label">Uang Dibayar (Cash)</label>
                            <input type="number" class="form-control" id="uang-dibayar" placeholder="Masukkan jumlah uang" min="0">
                        </div>
                        <div class="mb-3">
                            <label for="kembalian" class="form-label">Kembalian</label>
                            <input type="text" class="form-control" id="kembalian" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="btn-confirm-payment" style="display: none;">Konfirmasi Pembayaran</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Struk Pembayaran -->
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="receiptModalLabel">Struk Pembayaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="receipt-container">
                        <div class="receipt-header">MoneyLover Coffee</div>
                        <div class="receipt-line">Money Lover Coffee</div>
                        <div class="receipt-line">Jln. Cinta No. 23</div>
                        
                        <div class="receipt-line mt-3">Struk No: <span id="receipt-number">MLC00125</span></div>
                        <div class="receipt-line">Tanggal: <span id="receipt-date">26/11/2025 14:07</span></div>
                        <div class="receipt-line">Kasir: <span id="receipt-kasir"><?php echo $_SESSION['nama']; ?></span></div>
                        <div class="receipt-line">Metode: <span id="receipt-metode">Cash</span></div>
                        
                        <div id="receipt-items" class="mt-3">
                            <!-- Receipt items will be loaded here -->
                        </div>
                        
                        <div class="mt-3">
                            <div class="receipt-line">Subtotal: <span id="receipt-subtotal">Rp 0</span></div>
                            <div class="receipt-line total">Total Bayar: <span id="receipt-total">Rp 0</span></div>
                            <div class="receipt-line" id="receipt-bayar-line">Bayar (Tunai): <span id="receipt-bayar">Rp 0</span></div>
                            <div class="receipt-line kembali">Kembali: <span id="receipt-kembali">Rp 0</span></div>
                        </div>
                        
                        <div class="thank-you">*** Terima Kasih ***</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btn-print-receipt"><i class="fas fa-print"></i> Cetak Struk</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Detail Riwayat -->
    <div class="modal fade" id="historyDetailModal" tabindex="-1" aria-labelledby="historyDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="historyDetailModalLabel">Detail Transaksi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="history-detail-content">
                        <!-- Detail content will be loaded here via JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btn-reprint-history"><i class="fas fa-print"></i> Cetak Ulang</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Data Menu (Injected from PHP)
        const menuItems = <?php echo json_encode($menuItems); ?>;

        // Data keranjang dan riwayat
        let cart = [];
        let transactionHistory = [];
        let selectedPaymentMethod = null;
        let currentTransactionId = null;
        
        // Get petugas name from PHP session
        const petugasNama = "<?php echo $_SESSION['nama']; ?>";
        
        // Initialize history from Database
        function initializeHistory() {
            fetchHistory();
        }

        // Fetch history from backend
        function fetchHistory() {
            fetch('get_riwayat.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        transactionHistory = data.data;
                        // Apply current filters if any
                        filterHistory();
                    } else {
                        console.error('Failed to fetch history:', data.message);
                    }
                })
                .catch(error => console.error('Error fetching history:', error));
        }

        // Save history to localStorage - Removed (Deprecated)
        function saveHistoryToLocalStorage() {
            // No-op
        }
        
        // Update tanggal
        function updateDateTime() {
            const now = new Date();
            const dateStr = now.toLocaleDateString('en-GB') + ' ' + now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
            document.getElementById('current-date').textContent = dateStr;
            document.getElementById('history-current-date').textContent = dateStr;
            document.getElementById('receipt-date').textContent = dateStr;
        }
        
        // Generate receipt number
        function generateReceiptNumber() {
            const prefix = "MLC";
            const randomNum = Math.floor(Math.random() * 10000);
            return prefix + randomNum.toString().padStart(5, '0');
        }
        
        // Generate receipt number
        function generateReceiptNumber() {
            const prefix = "MLC";
            const randomNum = Math.floor(Math.random() * 10000);
            return prefix + randomNum.toString().padStart(5, '0');
        }

        // Render menu removed (handled by PHP)
        
        // Tambah ke keranjang
        function addToCart(itemId) {
            // Find item in menuItems (array from DB)
            const foundItem = menuItems.find(item => item.id_menu == itemId);
            
            if (!foundItem) return;
            
            // Map DB columns
            const id = foundItem.id_menu;
            const name = foundItem.nama_menu;
            const price = parseFloat(foundItem.harga_jual);
            const maxStock = parseInt(foundItem.stok);

            if (maxStock <= 0) {
                alert('Stok barang habis!');
                return;
            }
            
            // Cek apakah produk sudah ada di keranjang
            const existingItem = cart.find(item => item.id == itemId);
            
            if (existingItem) {
                if (existingItem.quantity + 1 > maxStock) {
                    alert(`Stok tidak mencukupi! Sisa stok: ${maxStock}`);
                    return;
                }
                existingItem.quantity++;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    quantity: 1,
                    maxStock: maxStock
                });
            }
            
            renderCart();
        }
        
        // Hapus dari keranjang
        function removeFromCart(itemId) {
            cart = cart.filter(item => item.id != itemId);
            renderCart();
        }
        
        // Update jumlah item
        function updateQuantity(itemId, newQuantity) {
            const item = cart.find(item => item.id == itemId);
            
            if (item) {
                if (newQuantity <= 0) {
                    removeFromCart(itemId);
                } else {
                    if (newQuantity > item.maxStock) {
                        alert(`Stok tidak mencukupi! Sisa stok: ${item.maxStock}`);
                    } else {
                        item.quantity = newQuantity;
                    }
                }
            }
            
            renderCart();
        }
        
        // Render keranjang
        function renderCart() {
            const cartItems = document.getElementById('cart-items');
            
            if (cart.length === 0) {
                cartItems.innerHTML = `
                    <div class="empty-cart">
                        <i class="fas fa-coffee"></i>
                        <p>Keranjang kosong</p>
                        <p class="small">Pilih menu dari daftar di sebelah kiri</p>
                    </div>
                `;
                document.getElementById('total-all').textContent = 'Rp 0';
                return;
            }
            
            let cartHTML = '';
            let totalAll = 0;
            
            cart.forEach(item => {
                const total = item.price * item.quantity;
                totalAll += total;
                
                cartHTML += `
                    <div class="cart-item">
                        <div class="cart-item-name">
                            <div><strong>${item.name}</strong></div>
                            <div class="small">Rp ${item.price.toLocaleString()}</div>
                        </div>
                        <div class="cart-item-controls">
                            <button class="btn-quantity" onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                            <span class="cart-item-quantity">${item.quantity}</span>
                            <button class="btn-quantity" onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                        </div>
                        <div class="cart-item-price">
                            Rp ${total.toLocaleString()}
                        </div>
                    </div>
                `;
            });
            
            cartItems.innerHTML = cartHTML;
            document.getElementById('total-all').textContent = `Rp ${totalAll.toLocaleString()}`;
        }
        
        // Render history
        function renderHistory(filteredHistory = null) {
            const historyBody = document.getElementById('history-table-body');
            const emptyHistory = document.getElementById('empty-history');
            const dataToRender = filteredHistory || transactionHistory;
            
            if (dataToRender.length === 0) {
                historyBody.innerHTML = '';
                emptyHistory.style.display = 'block';
                return;
            }
            
            emptyHistory.style.display = 'none';
            let historyHTML = '';
            
            dataToRender.forEach((transaction, index) => {
                // Count total items
                const totalItems = transaction.items.reduce((sum, item) => sum + item.quantity, 0);
                
                // Determine status badge
                let statusBadge = '';
                if (transaction.status === 'completed') {
                    statusBadge = '<span class="status-badge status-completed">Selesai</span>';
                } else if (transaction.status === 'pending') {
                    statusBadge = '<span class="status-badge status-pending">Pending</span>';
                } else {
                    statusBadge = '<span class="status-badge status-cancelled">Dibatalkan</span>';
                }
                
                // Determine payment method text
                const paymentText = transaction.paymentMethod === 'cash' ? 'Cash' : 'QR Code';
                
                historyHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td><strong>${transaction.id}</strong></td>
                        <td>${transaction.date}</td>
                        <td>${transaction.time}</td>
                        <td>${totalItems} item(s)</td>
                        <td>Rp ${transaction.total.toLocaleString()}</td>
                        <td>${paymentText}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <button class="btn-view" onclick="viewHistoryDetail('${transaction.id}')">
                                <i class="fas fa-eye"></i> Lihat
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            historyBody.innerHTML = historyHTML;
        }
        
        // Filter history
        function filterHistory() {
            const dateFilter = document.getElementById('filter-date').value;
            const statusFilter = document.getElementById('filter-status').value;
            const paymentFilter = document.getElementById('filter-payment').value;
            
            let filtered = transactionHistory;
            
            // Filter by date
            if (dateFilter) {
                filtered = filtered.filter(transaction => transaction.date === dateFilter);
            }
            
            // Filter by status
            if (statusFilter) {
                filtered = filtered.filter(transaction => transaction.status === statusFilter);
            }
            
            // Filter by payment method
            if (paymentFilter) {
                filtered = filtered.filter(transaction => transaction.paymentMethod === paymentFilter);
            }
            
            renderHistory(filtered);
        }
        
        // View history detail
        function viewHistoryDetail(transactionId) {
            const transaction = transactionHistory.find(t => t.id === transactionId);
            if (!transaction) return;
            
            const detailContent = document.getElementById('history-detail-content');
            
            // Build detail HTML
            let detailHTML = `
                <div class="receipt-container">
                    <div class="receipt-header">MoneyLover Coffee</div>
                    <div class="receipt-line">Money Lover Coffee</div>
                    <div class="receipt-line">Jln. Cinta No. 23</div>
                    
                    <div class="receipt-line mt-3">Struk No: <span>${transaction.id}</span></div>
                    <div class="receipt-line">Tanggal: <span>${transaction.date} ${transaction.time}</span></div>
                    <div class="receipt-line">Kasir: <span>${transaction.cashier}</span></div>
                    <div class="receipt-line">Metode: <span>${transaction.paymentMethod === 'cash' ? 'Cash' : 'QR Code'}</span></div>
                    
                    <div class="mt-3">
            `;
            
            // Add items
            transaction.items.forEach(item => {
                const total = item.price * item.quantity;
                detailHTML += `
                    <div class="receipt-line">${item.name} ${item.quantity}x <span>Rp ${total.toLocaleString()}</span></div>
                `;
            });
            
            // Add totals
            detailHTML += `
                        <div class="receipt-line">Subtotal: <span>Rp ${transaction.total.toLocaleString()}</span></div>
                        <div class="receipt-line total">Total Bayar: <span>Rp ${transaction.total.toLocaleString()}</span></div>
            `;
            
            // Add payment details
            if (transaction.paymentMethod === 'cash') {
                detailHTML += `
                        <div class="receipt-line">Bayar (Tunai): <span>Rp ${transaction.cashPaid.toLocaleString()}</span></div>
                        <div class="receipt-line kembali">Kembali: <span>Rp ${transaction.change.toLocaleString()}</span></div>
                `;
            } else {
                detailHTML += `
                        <div class="receipt-line">Bayar (QR Code): <span>Rp ${transaction.total.toLocaleString()}</span></div>
                `;
            }
            
            detailHTML += `
                    </div>
                    
                    <div class="thank-you">*** Terima Kasih ***</div>
                </div>
            `;
            
            detailContent.innerHTML = detailHTML;
            
            // Store current transaction ID for reprint
            currentTransactionId = transactionId;
            
            // Show modal
            const historyDetailModal = new bootstrap.Modal(document.getElementById('historyDetailModal'));
            historyDetailModal.show();
        }
        
        // Setup pembayaran
        function setupPayment() {
            const totalBayar = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            if (totalBayar === 0) {
                alert('Keranjang kosong! Tambahkan menu terlebih dahulu.');
                return;
            }
            
            // Reset payment selection
            selectedPaymentMethod = null;
            document.getElementById('cash-option').classList.remove('selected');
            document.getElementById('qr-option').classList.remove('selected');
            document.getElementById('cash-form').style.display = 'none';
            document.getElementById('qr-form').style.display = 'none';
            document.getElementById('btn-confirm-payment').style.display = 'none';
            
            // Show payment method modal
            const paymentMethodModal = new bootstrap.Modal(document.getElementById('paymentMethodModal'));
            paymentMethodModal.show();
            
            // Setup payment options
            document.getElementById('cash-option').addEventListener('click', function() {
                selectPaymentMethod('cash');
            });
            
            document.getElementById('qr-option').addEventListener('click', function() {
                selectPaymentMethod('qr');
            });
        }
        
        // Select payment method
        function selectPaymentMethod(method) {
            selectedPaymentMethod = method;
            
            // Update UI
            document.getElementById('cash-option').classList.remove('selected');
            document.getElementById('qr-option').classList.remove('selected');
            document.getElementById('cash-form').style.display = 'none';
            document.getElementById('qr-form').style.display = 'none';
            
            if (method === 'cash') {
                document.getElementById('cash-option').classList.add('selected');
                document.getElementById('cash-form').style.display = 'block';
                
                const totalBayar = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                document.getElementById('total-bayar').value = `Rp ${totalBayar.toLocaleString()}`;
                document.getElementById('uang-dibayar').value = '';
                document.getElementById('kembalian').value = 'Rp 0';
                
                // Hitung kembalian saat input uang berubah
                document.getElementById('uang-dibayar').addEventListener('input', function() {
                    const uangDibayar = parseFloat(this.value) || 0;
                    const kembalian = uangDibayar - totalBayar;
                    
                    if (kembalian >= 0) {
                        document.getElementById('kembalian').value = `Rp ${kembalian.toLocaleString()}`;
                    } else {
                        document.getElementById('kembalian').value = 'Rp 0';
                    }
                });
                
                document.getElementById('btn-confirm-payment').style.display = 'block';
            } else if (method === 'qr') {
                document.getElementById('qr-option').classList.add('selected');
                document.getElementById('qr-form').style.display = 'block';
                
                const totalBayar = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                document.getElementById('qr-total').textContent = `Rp ${totalBayar.toLocaleString()}`;
                
                // Simulate QR payment (auto confirm after 3 seconds)
                document.getElementById('btn-confirm-payment').style.display = 'block';
            }
        }
        
        // Konfirmasi pembayaran
        function confirmPayment() {
            if (!selectedPaymentMethod) {
                alert('Pilih metode pembayaran terlebih dahulu!');
                return;
            }
            
            const totalBayar = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            let uangDibayar = 0;
            let kembalian = 0;
            
            if (selectedPaymentMethod === 'cash') {
                uangDibayar = parseFloat(document.getElementById('uang-dibayar').value) || 0;
                kembalian = uangDibayar - totalBayar;
                
                if (uangDibayar < totalBayar) {
                    alert('Uang yang dibayarkan kurang!');
                    return;
                }
            } else if (selectedPaymentMethod === 'qr') {
                // For QR payment, assume exact payment
                uangDibayar = totalBayar;
                kembalian = 0;
            }
            
            // Generate transaction ID
            const transactionId = generateReceiptNumber();
            
            // Create transaction record
            const transaction = {
                id: transactionId,
                date: new Date().toISOString().split('T')[0],
                time: new Date().toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }),
                items: cart.map(item => ({
                    name: item.name,
                    quantity: item.quantity,
                    price: item.price
                })),
                total: totalBayar,
                paymentMethod: selectedPaymentMethod,
                status: 'completed',
                cashPaid: uangDibayar,
                change: kembalian,
                cashier: petugasNama
            };
            
            // Create transaction data object
            const transactionData = {
                id_user: <?php echo $_SESSION['user_id']; ?>,
                nama_pelanggan: 'Pelanggan ' + generateReceiptNumber(), // Simple placeholder
                metode_pembayaran: selectedPaymentMethod,
                items: cart.map(item => ({
                    id: item.id,
                    quantity: item.quantity,
                    price: item.price
                }))
            };

            // Send to backend
            fetch('../proses/proses_transaksi.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(transactionData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update history from server
                    fetchHistory();


                    // Generate receipt locally for display
                    generateReceipt(totalBayar, uangDibayar, kembalian, transactionId);
                    
                    // Clear cart and show receipt modal
                    cart = [];
                    renderCart();
                    
                    // Tutup modal pembayaran
                    const paymentMethodModal = bootstrap.Modal.getInstance(document.getElementById('paymentMethodModal'));
                    paymentMethodModal.hide();
                    
                    // Tampilkan modal struk
                    const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
                    receiptModal.show();
                    
                    // Refresh history if needed (optional, as we don't pull history from DB yet in this view)
                } else {
                    alert('Gagal menyimpan transaksi: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem.');
            });
            
            // Tampilkan modal struk (Moved to fetch success callback)
            // const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
            // receiptModal.show();
            
            // Clear cart
            cart = [];
            renderCart();
        }
        
        // Generate receipt
        function generateReceipt(totalBayar, uangDibayar, kembalian, transactionId = null) {
            // Update receipt number
            const receiptId = transactionId || generateReceiptNumber();
            document.getElementById('receipt-number').textContent = receiptId;
            
            // Update payment method
            const methodText = selectedPaymentMethod === 'cash' ? 'Cash' : 'QR Code';
            document.getElementById('receipt-metode').textContent = methodText;
            
            // Update receipt items
            const receiptItems = document.getElementById('receipt-items');
            receiptItems.innerHTML = '';
            
            cart.forEach(item => {
                const total = item.price * item.quantity;
                const itemElement = document.createElement('div');
                itemElement.className = 'receipt-line';
                itemElement.innerHTML = `${item.name} ${item.quantity}x <span>Rp ${total.toLocaleString()}</span>`;
                receiptItems.appendChild(itemElement);
            });
            
            // Update totals
            document.getElementById('receipt-subtotal').textContent = `Rp ${totalBayar.toLocaleString()}`;
            document.getElementById('receipt-total').textContent = `Rp ${totalBayar.toLocaleString()}`;
            document.getElementById('receipt-bayar').textContent = `Rp ${uangDibayar.toLocaleString()}`;
            document.getElementById('receipt-kembali').textContent = `Rp ${kembalian.toLocaleString()}`;
            
            // Update payment line text
            const bayarLine = document.getElementById('receipt-bayar-line');
            if (selectedPaymentMethod === 'cash') {
                bayarLine.innerHTML = 'Bayar (Tunai): <span id="receipt-bayar">Rp ' + uangDibayar.toLocaleString() + '</span>';
            } else {
                bayarLine.innerHTML = 'Bayar (QR Code): <span id="receipt-bayar">Rp ' + uangDibayar.toLocaleString() + '</span>';
            }
        }
        
        // Print receipt
        function printReceipt() {
            // Get content
            const receiptContent = document.querySelector('#receiptModal .receipt-container').cloneNode(true);
            
            // Open window
            const printWindow = window.open('', 'PrintReceipt', 'height=600,width=400');
            
            printWindow.document.write('<html><head><title>Struk Pembayaran</title>');
            printWindow.document.write('<style>');
            printWindow.document.write('body { font-family: "Courier New", monospace; padding: 10px; }');
            printWindow.document.write('.receipt-container { width: 100%; max-width: 300px; margin: 0 auto; }');
            printWindow.document.write('.receipt-header { text-align: center; font-weight: bold; margin-bottom: 20px; font-size: 16px; }');
            printWindow.document.write('.receipt-line { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 14px; }');
            printWindow.document.write('.receipt-line span { text-align: right; }');
            printWindow.document.write('.total { border-top: 1px dashed black; padding-top: 10px; margin-top: 10px; font-weight: bold; }');
            printWindow.document.write('.thank-you { text-align: center; margin-top: 20px; border-top: 1px dashed black; padding-top: 10px; font-style: italic; }');
            printWindow.document.write('</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(receiptContent.outerHTML);
            printWindow.document.write('<script>window.onload = function() { window.print(); window.close(); }<\/script>');
            printWindow.document.write('</body></html>');
            
            printWindow.document.close();
        }
        
        // Batal transaksi
        function cancelTransaction() {
            if (cart.length === 0) return;
            
            if (confirm('Apakah Anda yakin ingin membatalkan transaksi ini?')) {
                cart = [];
                renderCart();
            }
        }
        
        // Inisialisasi
        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            // fetchMenu(); // Removed, handled by PHP
            renderCart();
            initializeHistory();
            
            // Set default date for filter
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('filter-date').value = today;
            
            // Event listeners for kasir
            document.getElementById('btn-bayar').addEventListener('click', setupPayment);
            document.getElementById('btn-confirm-payment').addEventListener('click', confirmPayment);
            document.getElementById('btn-batal').addEventListener('click', cancelTransaction);
            document.getElementById('btn-print-receipt').addEventListener('click', printReceipt);
            
            // Event listeners for history
            document.getElementById('filter-date').addEventListener('change', filterHistory);
            document.getElementById('filter-status').addEventListener('change', filterHistory);
            document.getElementById('filter-payment').addEventListener('change', filterHistory);
            document.getElementById('btn-reset-filter').addEventListener('click', function() {
                document.getElementById('filter-date').value = today;
                document.getElementById('filter-status').value = '';
                document.getElementById('filter-payment').value = '';
                filterHistory();
            });
            
            document.getElementById('btn-reprint-history').addEventListener('click', function() {
                if (currentTransactionId) {
                    alert(`Mencetak ulang struk untuk transaksi ${currentTransactionId}`);
                }
            });
            
            // Update waktu setiap menit
            setInterval(updateDateTime, 60000);
            
            // Bootstrap tab switching
            const tabEls = document.querySelectorAll('a[data-bs-toggle="tab"]');
            tabEls.forEach(tabEl => {
                tabEl.addEventListener('shown.bs.tab', function (event) {
                    // Update date when switching to history tab
                    if (event.target.id === 'riwayat-tab') {
                        updateDateTime();
                        filterHistory();
                    }
                });
            });
        });
       // Variabel untuk menyimpan interval polling
       let paymentCheckInterval = null;
       let currentOrderId = null;
       
       function panggilQRIS() {
    // 1. Ambil total bayar dari keranjang
    const hargaFinal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    if (hargaFinal <= 0) {
        alert('Keranjang kosong! Tambahkan menu terlebih dahulu.');
        return;
    }
    
    // 2. Set payment method ke qr
    selectedPaymentMethod = 'qr';
    document.getElementById('cash-option').classList.remove('selected');
    document.getElementById('qr-option').classList.add('selected');
    document.getElementById('cash-form').style.display = 'none';
    
    // 3. Tampilan Awal: Munculkan form, sembunyikan gambar, munculkan loading
    document.getElementById('qr-form').style.display = 'block';
    document.getElementById('loading-qris').style.display = 'block';
    document.getElementById('tampilan-qris').style.display = 'none';
    document.getElementById('qr-total-display').innerText = 'Rp ' + hargaFinal.toLocaleString();

    // 4. Panggil PHP kita
    fetch('get_qris.php?total=' + hargaFinal)
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                document.getElementById('loading-qris').style.display = 'none';
                
                let imgQR = document.getElementById('tampilan-qris');
                imgQR.src = data.qr_url;
                imgQR.style.display = 'block';
                
                // Simpan order_id untuk polling
                currentOrderId = data.order_id;
                
                // Sembunyikan tombol konfirmasi (akan muncul otomatis saat pembayaran sukses)
                document.getElementById('btn-confirm-payment').style.display = 'none';
                
                // Mulai polling status pembayaran
                startPaymentCheck(data.order_id, hargaFinal);
                
                console.log("QRIS Berhasil dimuat: " + data.qr_url);
                console.log("Order ID: " + data.order_id);
            } else {
                alert('Gagal mendapatkan QRIS: ' + data.message);
                document.getElementById('qr-form').style.display = 'none';
                document.getElementById('loading-qris').style.display = 'none';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Koneksi bermasalah');
            document.getElementById('loading-qris').style.display = 'none';
        });
}

// Fungsi untuk memulai polling status pembayaran
function startPaymentCheck(orderId, totalBayar) {
    // Hentikan polling sebelumnya jika ada
    if (paymentCheckInterval) {
        clearInterval(paymentCheckInterval);
    }
    
    // Polling setiap 3 detik
    paymentCheckInterval = setInterval(() => {
        checkPaymentStatus(orderId, totalBayar);
    }, 3000);
    
    // Juga cek sekali langsung
    setTimeout(() => {
        checkPaymentStatus(orderId, totalBayar);
    }, 1000);
}

// Fungsi untuk cek status pembayaran
function checkPaymentStatus(orderId, totalBayar) {
    fetch('check_payment.php?order_id=' + orderId)
        .then(response => response.json())
        .then(data => {
            console.log("Status pembayaran:", data);
            
            if (data.status === 'success' && data.is_paid) {
                // Hentikan polling
                stopPaymentCheck();
                
                // Tampilkan notifikasi sukses
                showPaymentSuccess(totalBayar, orderId);
            }
        })
        .catch(err => {
            console.error("Error checking payment status:", err);
        });
}

// Fungsi untuk menghentikan polling
function stopPaymentCheck() {
    if (paymentCheckInterval) {
        clearInterval(paymentCheckInterval);
        paymentCheckInterval = null;
    }
}

// Fungsi untuk menampilkan notifikasi pembayaran berhasil
function showPaymentSuccess(totalBayar, orderId) {
    // Update tampilan QRIS menjadi sukses dengan animasi
    const qrContainer = document.querySelector('.qr-code-placeholder');
    qrContainer.style.minHeight = '280px';
    qrContainer.style.border = '3px solid #28a745';
    qrContainer.style.background = 'linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%)';
    
    qrContainer.innerHTML = `
        <div class="payment-success-container">
            <i class="fas fa-check-circle payment-success-icon"></i>
            <h4 class="payment-success-title">Pembayaran Berhasil!</h4>
            <p class="payment-success-subtitle">
                Total: <strong>Rp ${totalBayar.toLocaleString()}</strong>
            </p>
            <div class="payment-success-order">
                <i class="fas fa-receipt"></i> ${orderId}
            </div>
        </div>
    `;
    
    // Tampilkan tombol konfirmasi
    document.getElementById('btn-confirm-payment').style.display = 'block';
    document.getElementById('btn-confirm-payment').textContent = 'Selesaikan Transaksi';
    document.getElementById('btn-confirm-payment').classList.remove('btn-success');
    document.getElementById('btn-confirm-payment').classList.add('btn-success');
}
    </script>
</body>
</html>