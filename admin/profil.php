<?php
session_start();
include '../inc/koneksi.php';

// Proteksi halaman - Cek apakah yang masuk benar-benar Admin
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
    <title>Profil Admin - MoneyLover</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    /* Menyamakan background dengan dashboard */
    body { 
        background-color: #fdfae7; /* Krem Dashboard */
        color: #8B4513; /* Teks Cokelat */
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Navbar Atas agar ukurannya sama dengan Dashboard */
    .top-bar {
        background-color: #8B4513; /* Cokelat Dashboard */
        padding: 15px 30px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    /* Kontainer Konten */
    .content-container {
        padding: 40px 20px;
    }

    /* Kartu Profil (Disesuaikan agar ukurannya proposional) */
    .card-profile {
        background: white;
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        max-width: 800px; /* Menjaga agar tidak terlalu lebar tapi tetap lega */
        margin: 0 auto;
        overflow: hidden;
    }

    .card-header-brown {
        background-color: #8B4513;
        color: white;
        padding: 20px;
        text-align: center;
    }

    /* Tombol Kembali agar warnanya sama dengan tombol Dashboard */
    .btn-dashboard {
        background-color: #8B4513; /* Warna Orange-Cokelat tombol Dashboard Anda */
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: bold;
    }

    .btn-dashboard:hover {
        background-color: #9e5a28;
        color: white;
    }
</style>

<body>
    <div class="top-bar">
        <div class="fw-bold fs-4">
            <i class="fa-solid fa-wallet me-2 text-warning"></i> Money Lover
        </div>
        <div>
            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                <i class="fa-solid fa-user-check me-1"></i> Admin Mode
            </span>
        </div>
    </div>

    <div class="container content-container">
        <div class="card card-profile shadow-sm">
            <div class="card-header-brown">
                <i class="fa-solid fa-circle-user fa-4x mb-2"></i>
                <h3 class="m-0">Profil Administrator</h3>
            </div>
            
            <div class="card-body p-5">
                <div class="row mb-4 border-bottom pb-3">
                    <div class="col-sm-4 text-muted">Nama Lengkap</div>
                    <div class="col-sm-8 fw-bold fs-5"><?php echo $_SESSION['nama']; ?></div>
                </div>
                <div class="row mb-4 border-bottom pb-3">
                    <div class="col-sm-4 text-muted">ID Pengguna</div>
                    <div class="col-sm-8 fw-bold">#<?php echo $_SESSION['user_id']; ?></div>
                </div>
                <div class="row mb-5 border-bottom pb-3">
                    <div class="col-sm-4 text-muted">Status Akses</div>
                    <div class="col-sm-8 text-success fw-bold"><i class="fa-solid fa-check-circle"></i> Terverifikasi</div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="index.php" class="btn-dashboard">
                        <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Dashboard
                    </a>
                    <a href="../proses/proses_logout.php" class="text-danger text-decoration-none fw-bold">
                        <i class="fa-solid fa-sign-out-alt"></i> Keluar
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>