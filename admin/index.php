<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Lover - Dashboard Admin</title>
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
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-circle-user fs-4 me-2"></i>
                            <span><?php echo $_SESSION['nama']; ?></span>
                        </button>
                        
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                           <li>
                                <a class="dropdown-item py-2" href="profil.php" data-bs-toggle="modal" data-bs-target="#modalProfil">
                                    <i class="fa-solid fa-user me-3 text-secondary"></i> Profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="../proses/proses_logout.php">
                                    <i class="fa-solid fa-right-from-bracket me-3"></i> Logout
                                </a>
                            </li>
                        </ul>
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
                    <a href="index.php" class="sidebar-link active">
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
                    <a href="laporan.php" class="sidebar-link">
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
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </h1>
                <div class="text-muted">
                    <i class="fas fa-calendar"></i>
                    <span><?php echo date('d F Y'); ?></span>
                </div>
            </div>

            <!-- Statistik -->
            <div class="row">
                <div class="col col-3">
                    <div class="card stat-card border-primary">
                        <div class="card-body">
                            <div class="stat-content">
                                <div class="stat-info">
                                    <h6>Pendapatan Hari Ini</h6>
                                    <h4 id="stat-pendapatan">Rp 0</h4>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col col-3">
                    <div class="card stat-card border-success">
                        <div class="card-body">
                            <div class="stat-content">
                                <div class="stat-info">
                                    <h6>Transaksi Hari Ini</h6>
                                    <h4 id="stat-transaksi">0</h4>
                                </div>
                                <div class="stat-icon bg-success">
                                    <i class="fas fa-receipt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col col-3">
                    <div class="card stat-card border-warning">
                        <div class="card-body">
                            <div class="stat-content">
                                <div class="stat-info">
                                    <h6>Barang Terjual</h6>
                                    <h4 id="stat-barang">0</h4>
                                </div>
                                <div class="stat-icon bg-warning">
                                    <i class="fas fa-box-open"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col col-3">
                    <div class="card stat-card border-info">
                        <div class="card-body">
                            <div class="stat-content">
                                <div class="stat-info">
                                    <h6>Rata-rata Transaksi</h6>
                                    <h4 id="stat-rata">Rp 0</h4>
                                </div>
                                <div class="stat-icon bg-info">
                                    <i class="fas fa-calculator"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i>
                        <span>Aksi Cepat</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="barang.php" class="btn btn-primary">
                            <i class="fas fa-box"></i>
                            <span>Kelola Barang</span>
                        </a>
                        <a href="laporan.php" class="btn btn-success">
                            <i class="fas fa-chart-bar"></i>
                            <span>Lihat Laporan</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Transaksi Terbaru -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i>
                        <span>Transaksi Terbaru</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal/Waktu</th>
                                    <th>Kasir</th>
                                    <th>Total</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="laporan.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye"></i>
                            <span>Lihat Semua Transaksi</span>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Custom JavaScript -->
    <script src="../assets/js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchDashboardData();
            // Refresh every 30 seconds
            setInterval(fetchDashboardData, 30000);
        });

        function fetchDashboardData() {
            fetch('get_dashboard_data.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        updateStats(data.stats);
                        updateRecentTransactions(data.recent_transactions);
                    }
                })
                .catch(error => console.error('Error fetching dashboard data:', error));
        }

        function updateStats(stats) {
            document.getElementById('stat-pendapatan').textContent = 'Rp ' + parseInt(stats.pendapatan_hari_ini).toLocaleString('id-ID');
            document.getElementById('stat-transaksi').textContent = stats.transaksi_hari_ini;
            document.getElementById('stat-barang').textContent = stats.barang_terjual;
            document.getElementById('stat-rata').textContent = 'Rp ' + parseInt(stats.rata_rata).toLocaleString('id-ID');
        }

        function updateRecentTransactions(transactions) {
            const tableBody = document.querySelector('.table tbody');
            tableBody.innerHTML = '';

            if (transactions.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Belum ada transaksi hari ini</td></tr>';
                return;
            }

            transactions.forEach((trx, index) => {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${trx.waktu}</td>
                        <td>${trx.kasir}</td>
                        <td>Rp ${parseInt(trx.total).toLocaleString('id-ID')}</td>
                        <td>${trx.metode}</td>
                        <td><span class="badge bg-success" style="background-color: #198754; color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.8em;">${trx.status}</span></td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        }
    </script>
</body>
</html>
