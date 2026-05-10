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
    <title>Money Lover - Kelola Petugas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .password-hint {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="container navbar-container">
            <a href="index.php" class="navbar-brand">
                <i class="fas fa-wallet"></i>
                <span>Money Lover</span>
            </a>
            <ul class="navbar-menu">
                <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo $_SESSION['nama']; ?></span>
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
                <h2 class="sidebar-title"><i class="fas fa-tachometer-alt"></i> <span>Menu</span></h2>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item"><a href="index.php" class="sidebar-link"><i class="fas fa-home"></i>
                        <span>Dashboard</span></a></li>
                <li class="sidebar-item"><a href="barang.php" class="sidebar-link"><i class="fas fa-box"></i>
                        <span>Barang</span></a></li>
                         <li class="sidebar-item"><a href="petugas.php" class="sidebar-link active"><i class="fas fa-users"></i>
                        <span>Petugas</span></a></li>
                <li class="sidebar-item"><a href="laporan.php" class="sidebar-link"><i class="fas fa-chart-bar"></i>
                        <span>Laporan</span></a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="content-header">
                <h1 class="page-title"><i class="fas fa-users"></i> <span>Kelola Petugas</span></h1>
                <button class="btn btn-primary" id="btnTambahPetugas">
                    <i class="fas fa-plus"></i> Tambah Petugas
                </button>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="petugasTableBody">
                            <!-- Data petugas akan dimuat di sini -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Tambah/Edit Petugas -->
    <div id="modalPetugas" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">
                    <i class="fas fa-user-plus"></i>
                    <span>Tambah Petugas</span>
                </h3>
                <button type="button" class="modal-close" id="closeModal">&times;</button>
            </div>
            <form id="formPetugas">
                <input type="hidden" id="petugasId" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama" required placeholder="Masukkan nama lengkap">
                    </div>
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required placeholder="Masukkan username">
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="Masukkan email">
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password">
                        <p class="password-hint" id="passwordHint">Kosongkan jika tidak ingin mengubah password (saat edit)</p>
                    </div>
                    <div class="form-group">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="kasir">Kasir</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnBatal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpan">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/script.js?v=<?php echo filemtime(__DIR__ . '/../assets/js/script.js'); ?>"></script>
    <script src="../assets/js/petugas.js?v=<?php echo filemtime(__DIR__ . '/../assets/js/petugas.js'); ?>"></script>
</body>

</html>