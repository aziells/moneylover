<?php
session_start();
require_once '../inc/koneksi.php';

// Cek apakah user sudah login dan merupakan admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Set header untuk JSON response
header('Content-Type: application/json');

// Ambil aksi dari request
$aksi = $_POST['aksi'] ?? $_GET['aksi'] ?? '';

// Default ke tampil jika aksi kosong
if (empty($aksi)) {
    $aksi = 'tampil';
}

try {
    switch ($aksi) {
        case 'tampil':
            // Menampilkan semua data barang
            $query = "SELECT * FROM barang ORDER BY id_barang DESC";
            $result = mysqli_query($koneksi, $query);

            $barang = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $barang[] = [
                    'id' => $row['id_barang'],
                    'kode' => $row['kode_barang'],
                    'nama' => $row['nama_barang'],
                    'harga' => (float) $row['harga'],
                    'stok' => (int) $row['stok'],
                    'status' => $row['status'],
                    'gambar' => $row['gambar']
                ];
            }

            echo json_encode(['success' => true, 'data' => $barang]);
            break;

        case 'tambah':
            $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
            $harga = (float) $_POST['harga'];
            $stok = (int) $_POST['stok'];
            $status = mysqli_real_escape_string($koneksi, $_POST['status'] ?? 'tersedia');

            // Handle auto-generate kode if not provided
            if (empty($_POST['kode'])) {
                $checkLast = mysqli_query($koneksi, "SELECT MAX(id_barang) as max_id FROM barang");
                $lastId = mysqli_fetch_assoc($checkLast)['max_id'] ?? 0;
                $kode = "BRG-" . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $kode = mysqli_real_escape_string($koneksi, $_POST['kode']);
            }

            // Default kategori if not provided
            $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori'] ?? 'Umum');

            // Handle file upload
            $gambar = null;
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
                $filename = time() . '_' . $kode . '.' . $ext;
                $target = '../assets/img/barang/' . $filename;

                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                    $gambar = $filename;
                }
            }

            // Cek apakah kode barang sudah ada
            $check = mysqli_query($koneksi, "SELECT * FROM barang WHERE kode_barang = '$kode'");
            if (mysqli_num_rows($check) > 0) {
                echo json_encode(['success' => false, 'message' => 'Kode barang sudah digunakan']);
                exit();
            }

            $query = "INSERT INTO barang (kode_barang, nama_barang, harga, stok, status, gambar) 
                      VALUES ('$kode', '$kategori', '$nama', $harga, $stok, '$status', '$gambar')";

            if (mysqli_query($koneksi, $query)) {
                $id = mysqli_insert_id($koneksi);
                echo json_encode([
                    'success' => true,
                    'message' => 'Barang berhasil ditambahkan',
                    'data' => [
                        'id' => $id,
                        'kode' => $kode,
                        'nama' => $nama,
                        'harga' => $harga,
                        'stok' => $stok
                    ]
                ]);
            } else {
                $errorMsg = mysqli_error($koneksi);
                echo json_encode(['success' => false, 'message' => 'Gagal menambahkan barang: ' . $errorMsg]);
            }
            break;

        case 'edit':
            $id = (int) ($_POST['id'] ?? ($_POST['id_menu'] ?? 0));
            $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
            $harga = (float) $_POST['harga'];
            $stok = (int) $_POST['stok'];
            $status = mysqli_real_escape_string($koneksi, $_POST['status']);

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID barang tidak valid']);
                exit();
            }

            // Handle file upload
            $updateGambar = "";
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
                $filename = time() . '_' . $id . '.' . $ext;
                $target = '../assets/img/barang/' . $filename;

                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                    $updateGambar = ", gambar = '$filename'";

                    // Optional: delete old image
                    $old = mysqli_query($koneksi, "SELECT gambar FROM barang WHERE id_barang = $id");
                    $oldRow = mysqli_fetch_assoc($old);
                    if ($oldRow && $oldRow['gambar']) {
                        @unlink('../assets/img/barang/' . $oldRow['gambar']);
                    }
                }
            }

            $query = "UPDATE barang SET 
                      nama_barang = '$nama',
                      harga = $harga,
                      stok = $stok,
                      status = '$status'
                      $updateGambar
                      WHERE id_barang = $id";

            if (mysqli_query($koneksi, $query)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Barang berhasil diupdate',
                    'data' => [
                        'id' => $id,
                        'kode' => $kode,
                        'nama' => $nama,
                        'harga' => $harga,
                        'stok' => $stok
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal mengupdate barang: ' . mysqli_error($koneksi)]);
            }
            break;

        case 'hapus':
            if (empty($_POST['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID barang tidak ditemukan']);
                exit();
            }

            $id = (int) $_POST['id'];

            // Cek apakah barang ada
            $check = mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang = $id");
            if (mysqli_num_rows($check) == 0) {
                echo json_encode(['success' => false, 'message' => 'Barang tidak ditemukan']);
                exit();
            }

            $query = "DELETE FROM barang WHERE id_barang = $id";

            if (mysqli_query($koneksi, $query)) {
                echo json_encode(['success' => true, 'message' => 'Barang berhasil dihapus']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menghapus barang: ' . mysqli_error($koneksi)]);
            }
            break;

        case 'detail':
            if (empty($_GET['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID barang tidak ditemukan']);
                exit();
            }

            $id = (int) $_GET['id'];
            $query = "SELECT * FROM barang WHERE id_barang = $id";
            $result = mysqli_query($koneksi, $query);

            if ($row = mysqli_fetch_assoc($result)) {
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'id' => $row['id_barang'],
                        'kode' => $row['kode_barang'],
                        'nama' => $row['nama_barang'],
                        'harga' => (int) $row['harga'],
                        'stok' => (int) $row['stok']
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Barang tidak ditemukan']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Aksi tidak valid: ' . $aksi]);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

mysqli_close($koneksi);
?>