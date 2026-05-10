<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../inc/koneksi.php';

$tanggal = $_GET['tanggal'] ?? '';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    $tanggal = '';
}

$rows = [];
$totalHari = 0;
$queryError = '';

if ($tanggal !== '') {
    $tanggalEsc = mysqli_real_escape_string($koneksi, $tanggal);

    $query = "
        SELECT 
            m.tgl_pemesanan,
            m.jam_pemesanan,
            m.id_user,
            m.nama_pelanggan,
            MAX(m.metode_pembayaran) AS metode_pembayaran,
            MAX(u.nama) AS nama_kasir,
            GROUP_CONCAT(mn.nama_menu SEPARATOR ', ') AS items,
            SUM(m.harga_total) AS total_transaksi,
            SUM(m.jumlah) AS jumlah_item
        FROM memesan m
        LEFT JOIN user u ON m.id_user = u.id_user
        LEFT JOIN menu mn ON m.id_menu = mn.id_menu
        WHERE m.tgl_pemesanan = '$tanggalEsc' AND m.status_pembayaran = 'success'
        GROUP BY m.tgl_pemesanan, m.jam_pemesanan, m.id_user, m.nama_pelanggan
        ORDER BY m.jam_pemesanan DESC
    ";

    $result = mysqli_query($koneksi, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
            $totalHari += (int)$row['total_transaksi'];
        }
    } else {
        $queryError = mysqli_error($koneksi);
    }
}

function rupiah($angka) {
    $n = (int)$angka;
    return 'Rp ' . number_format($n, 0, ',', '.');
}

function h($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Lover - Detail Laporan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container navbar-container">
            <a href="index.php" class="navbar-brand">
                <i class="fas fa-wallet"></i>
                <span>Money Lover</span>
            </a>

            <button class="navbar-toggler">
                <i class="fas fa-bars"></i>
            </button>

            <ul class="navbar-menu">
                <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo h($_SESSION['nama']); ?></span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="../proses/proses_logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="app-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Menu</span>
                </h2>
            </div>

            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="index.php" class="sidebar-link"><i class="fas fa-home"></i><span>Dashboard</span></a>
                </li>
                <li class="sidebar-item">
                    <a href="barang.php" class="sidebar-link"><i class="fas fa-box"></i><span>Barang</span></a>
                </li>
                <li class="sidebar-item">
                    <a href="petugas.php" class="sidebar-link"><i class="fas fa-users"></i><span>Petugas</span></a>
                </li>
                <li class="sidebar-item">
                    <a href="laporan.php" class="sidebar-link active"><i class="fas fa-chart-bar"></i><span>Laporan</span></a>
                </li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="content-header">
                <h1 class="page-title">
                    <i class="fas fa-receipt"></i>
                    <span>Detail Laporan</span>
                </h1>
                <div class="btn-group">
                    <a class="btn btn-outline-primary btn-sm" href="laporan.php">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="flex justify-between align-center">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-day"></i>
                            <span><?php echo $tanggal ? h(date('d F Y', strtotime($tanggal))) : 'Tanggal tidak valid'; ?></span>
                        </h3>
                        <div class="text-muted">
                            Total: <?php echo rupiah($totalHari); ?>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <?php if ($tanggal === ''): ?>
                        <div class="text-muted">Tanggal tidak valid. Buka dari halaman laporan.</div>
                    <?php elseif ($queryError !== ''): ?>
                        <div class="text-muted">Query error: <?php echo h($queryError); ?></div>
                    <?php elseif (count($rows) === 0): ?>
                        <div class="text-muted">Tidak ada transaksi untuk tanggal ini.</div>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>No. Transaksi</th>
                                        <th>Kasir</th>
                                        <th>Pelanggan</th>
                                        <th>Item</th>
                                        <th>Jumlah Item</th>
                                        <th>Metode</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $r):
                                        $timestamp = strtotime($r['tgl_pemesanan'] . ' ' . $r['jam_pemesanan']);
                                        $noTransaksi = 'TRX-' . date('YmdHis', $timestamp);
                                    ?>
                                        <tr>
                                            <td><?php echo h($r['jam_pemesanan']); ?></td>
                                            <td><?php echo h($noTransaksi); ?></td>
                                            <td><?php echo h($r['nama_kasir'] ?: 'Admin/System'); ?></td>
                                            <td><?php echo h($r['nama_pelanggan']); ?></td>
                                            <td><?php echo h($r['items']); ?></td>
                                            <td><?php echo (int)$r['jumlah_item']; ?></td>
                                            <td><?php echo h($r['metode_pembayaran'] ?: 'Tunai'); ?></td>
                                            <td class="fw-bold"><?php echo rupiah($r['total_transaksi']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>
