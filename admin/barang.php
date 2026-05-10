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
    <title>Money Lover - Manajemen Barang</title>
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
                    <a href="barang.php" class="sidebar-link active">
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
                    <i class="fas fa-box"></i>
                    <span>Manajemen Barang</span>
                </h1>
                <div class="btn-group">
                    <button class="btn btn-outline-primary btn-sm" id="refreshData">
                        <i class="fas fa-sync-alt"></i>
                        <span>Refresh Data</span>
                    </button>
                </div>
            </div>

            <!-- Aksi Cepat -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i>
                        <span>Aksi Cepat</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <button class="btn btn-primary" onclick="openModal('tambahBarangModal')">
                            <i class="fas fa-plus-circle"></i>
                            <span>Insert Data</span>
                        </button>
                        <div class="flex align-center gap-sm">
                            <i class="fas fa-sort-amount-down" style="color: var(--forest-green);"></i>
                            <select class="form-select form-select-sm" id="stokSortOrder" style="width: auto;">
                                <option value="desc" selected>Stok Terbanyak</option>
                                <option value="asc">Stok Terkecil</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Daftar Barang -->
            <div class="card">
                <div class="card-header">
                    <div class="flex justify-between align-center">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i>
                            <span>Daftar Barang</span>
                        </h3>
                        <div class="flex align-center">
                            <span class="mr-2">Show</span>
                            <select class="form-select form-select-sm" id="showEntries" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25" selected>25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="ml-2">entries</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-striped" id="barangTable">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">Gambar</th>
                                    <th width="15%">Kode Barang</th>
                                    <th width="25%">Nama Barang</th>
                                    <th width="15%">Harga</th>
                                    <th width="10%">Stok</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="barangTableBody">

                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between align-center mt-3">
                        <div class="text-muted" id="tableInfo">
                            Showing 1 to 10 of 42 entries
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination" id="pagination">
                                <!-- Pagination akan diisi oleh JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Tambah Barang -->
    <div class="modal" id="tambahBarangModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-plus-circle"></i>
                    <span>Tambah Barang Baru</span>
                </h3>
                <button class="modal-close">&times;</button>
            </div>
            <form id="formTambahBarang">
                <div class="modal-body">
                    <input type="hidden" id="barangId" name="id_menu">
                    <div class="form-group">
                        <label for="namaBarang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="namaBarang" name="nama_menu" required
                            placeholder="Contoh: latte">
                    </div>
                    <div class="form-group">
                        <label for="hargaBarang" class="form-label">Harga (Rp)</label>
                        <input type="number" class="form-control" id="hargaBarang" name="harga_jual" min="0" required
                            placeholder="Contoh: 25000">
                    </div>
                    <div class="form-group">
                        <label for="stokBarang" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stokBarang" name="stok" min="0" required
                            placeholder="Contoh: 50">
                    </div>
                    <div class="form-group">
                        <label for="statusBarang" class="form-label">Status</label>
                        <select class="form-select" id="statusBarang" name="status" required>
                            <option value="tersedia">Tersedia</option>
                            <option value="habis">Habis</option>
                            <option value="non-aktif">Non-aktif</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="gambarBarang" class="form-label">Gambar Barang</label>
                        <input type="file" class="form-control" id="gambarBarang" name="gambar" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('tambahBarangModal')">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom JavaScript -->
    <script src="../assets/js/script.js?v=<?php echo filemtime(__DIR__ . '/../assets/js/script.js'); ?>"></script>
    <script src="../assets/js/barang.js?v=<?php echo filemtime(__DIR__ . '/../assets/js/barang.js'); ?>"></script>
</body>

</html>