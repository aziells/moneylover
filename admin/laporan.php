<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../inc/koneksi.php';


?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Lover - Laporan</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
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
                        <span><?php echo $_SESSION['nama']; ?></span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user"></i> Profil
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="../proses/proses_logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Menu</span>
                </h2>
            </div>
            
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="index.php" class="sidebar-link">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="barang.php" class="sidebar-link">
                        <i class="fas fa-box"></i>
                        <span>Barang</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="petugas.php" class="sidebar-link">
                        <i class="fas fa-users"></i>
                        <span>Petugas</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="laporan.php" class="sidebar-link active">
                        <i class="fas fa-chart-bar"></i>
                        <span>Laporan</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="content-header">
                <h1 class="page-title">
                    <i class="fas fa-chart-bar"></i>
                    <span>Laporan</span>
                </h1>
                <div class="btn-group">
                    <button class="btn btn-outline-primary btn-sm" id="printLaporan">
                        <i class="fas fa-print"></i>
                        <span>Cetak Laporan</span>
                    </button>
                </div>
            </div>


            <!-- Tabel Laporan -->
            <div class="card">
                <div class="card-header">
                    <div class="flex justify-between align-center">
                        <h3 class="card-title">
                            <i class="fas fa-table"></i>
                            <span>Detail Transaksi</span>
                        </h3>
                        <div class="text-muted" id="infoTransaksiTop">
                            Menampilkan -- transaksi
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Total Transaksi</th>
                                    <th>Kasir Bertugas</th>
                                    <th>Total Pendapatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <!-- Data akan dimuat via JavaScript -->
                            <tbody id="laporanTableBody">
                                <tr>
                                    <td colspan="8" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="flex justify-between align-center mt-3">
                        <div class="text-muted" id="infoTransaksiBottom">
                            <!-- Info pagination -->
                        </div>

                        <nav aria-label="Page navigation">
                            <ul class="pagination" id="pagination">
                                <!-- Pagination generated by JS -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Custom JavaScript -->
    <script src="../assets/js/script.js?v=<?php echo filemtime(__DIR__ . '/../assets/js/script.js'); ?>"></script>
    <script src="../assets/js/laporan.js?v=<?php echo filemtime(__DIR__ . '/../assets/js/laporan.js'); ?>"></script>
</body>
</html>
