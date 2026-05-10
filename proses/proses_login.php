<?php
session_start();

require_once('../koneksi.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Username dan password harus diisi!";
        header("Location: ../login.php");
        exit();
    }
    
    // Cari user berdasarkan username
    $query = "SELECT * FROM `user` WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
       
        if ($password == $user['password']) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['level'] = $user['role']; 
            
        
            if ($user['role'] == 'admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../kasir/index.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Password salah!";
            header("Location: ../login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Username tidak ditemukan!";
        header("Location: ../login.php");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>