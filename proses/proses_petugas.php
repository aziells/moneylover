<?php
header('Content-Type: application/json');
include '../inc/koneksi.php';

$aksi = $_GET['aksi'] ?? $_POST['aksi'] ?? '';

switch ($aksi) {
    case 'tampil':
        getData();
        break;
    case 'detail':
        getDetail();
        break;
    case 'tambah':
        tambahData();
        break;
    case 'edit':
        editData();
        break;
    case 'hapus':
        hapusData();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Aksi tidak valid']);
        break;
}

function getData() {
    global $koneksi;
    $query = "SELECT id_user, nama, username, email, role FROM `user` ORDER BY id_user DESC";
    $result = mysqli_query($koneksi, $query);
    if ($result === false) {
        echo json_encode(['success' => false, 'message' => mysqli_error($koneksi)]);
        return;
    }
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $data]);
}

function getDetail() {
    global $koneksi;
    $id = $_GET['id'] ?? '';
    $id = mysqli_real_escape_string($koneksi, $id);
    
    $query = "SELECT * FROM `user` WHERE id_user = '$id'";
    $result = mysqli_query($koneksi, $query);
    if ($result === false) {
        echo json_encode(['success' => false, 'message' => mysqli_error($koneksi)]);
        return;
    }

    $data = mysqli_fetch_assoc($result);
    
    if ($data) {
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
    }
}

function tambahData() {
    global $koneksi;
    
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama'] ?? '');
    $username = mysqli_real_escape_string($koneksi, $_POST['username'] ?? '');
    $email = mysqli_real_escape_string($koneksi, $_POST['email'] ?? '');
    $password = mysqli_real_escape_string($koneksi, $_POST['password'] ?? ''); // Disimpan plain text sesuai pola login saat ini
    $role = mysqli_real_escape_string($koneksi, $_POST['role'] ?? '');

    if ($nama === '' || $username === '' || $email === '' || $password === '' || $role === '') {
        echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
        return;
    }
    
    // Cek username/email duplicate
    $check = mysqli_query($koneksi, "SELECT * FROM `user` WHERE username='$username' OR email='$email'");
    if ($check === false) {
        echo json_encode(['success' => false, 'message' => mysqli_error($koneksi)]);
        return;
    }
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(['success' => false, 'message' => 'Username atau Email sudah terdaftar']);
        return;
    }
    
    $query = "INSERT INTO `user` (nama, username, email, password, role) VALUES ('$nama', '$username', '$email', '$password', '$role')";
    
    if (mysqli_query($koneksi, $query)) {
        echo json_encode(['success' => true, 'message' => 'Berhasil menambah petugas']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal: ' . mysqli_error($koneksi)]);
    }
}

function editData() {
    global $koneksi;
    
    $id = mysqli_real_escape_string($koneksi, $_POST['id'] ?? '');
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama'] ?? '');
    $username = mysqli_real_escape_string($koneksi, $_POST['username'] ?? '');
    $email = mysqli_real_escape_string($koneksi, $_POST['email'] ?? '');
    $role = mysqli_real_escape_string($koneksi, $_POST['role'] ?? '');
    $password = mysqli_real_escape_string($koneksi, $_POST['password'] ?? '');

    if ($id === '' || $nama === '' || $username === '' || $email === '' || $role === '') {
        echo json_encode(['success' => false, 'message' => 'Field wajib tidak boleh kosong']);
        return;
    }
    
    // Cek duplicate selain user ini
    $check = mysqli_query($koneksi, "SELECT * FROM `user` WHERE (username='$username' OR email='$email') AND id_user != '$id'");
    if ($check === false) {
        echo json_encode(['success' => false, 'message' => mysqli_error($koneksi)]);
        return;
    }
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(['success' => false, 'message' => 'Username atau Email sudah digunakan user lain']);
        return;
    }
    
    if (!empty($password)) {
        $query = "UPDATE `user` SET nama='$nama', username='$username', email='$email', password='$password', role='$role' WHERE id_user='$id'";
    } else {
        $query = "UPDATE `user` SET nama='$nama', username='$username', email='$email', role='$role' WHERE id_user='$id'";
    }
    
    if (mysqli_query($koneksi, $query)) {
        echo json_encode(['success' => true, 'message' => 'Berhasil mengubah data petugas']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal: ' . mysqli_error($koneksi)]);
    }
}

function hapusData() {
    global $koneksi;
    $id = mysqli_real_escape_string($koneksi, $_POST['id'] ?? '');
    if ($id === '') {
        echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
        return;
    }
    
    $query = "DELETE FROM `user` WHERE id_user='$id'";
    
    if (mysqli_query($koneksi, $query)) {
        echo json_encode(['success' => true, 'message' => 'Berhasil menghapus petugas']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal: ' . mysqli_error($koneksi)]);
    }
}
?>
