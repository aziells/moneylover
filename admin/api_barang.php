<?php
header('Content-Type: application/json');
include '../inc/koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Fetch all items
        $query = "SELECT * FROM menu ORDER BY id_menu DESC";
        $result = mysqli_query($koneksi, $query);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $data]);
        break;

    case 'POST':
        // Tambah/Edit via POST (using FormData because of images)
        $id = isset($_POST['id_menu']) ? $_POST['id_menu'] : null;
        $nama = mysqli_real_escape_string($koneksi, $_POST['nama_menu'] ?? '');
        $harga = (int) ($_POST['harga_jual'] ?? 0);
        $stok = (int) ($_POST['stok'] ?? 0);
        $status = mysqli_real_escape_string($koneksi, $_POST['status'] ?? 'tersedia');
        
        $gambar_name = null;
        
        // Handle image upload
        if (isset($_FILES['gambar']) && ($_FILES['gambar']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $uploadErr = (int) ($_FILES['gambar']['error'] ?? UPLOAD_ERR_NO_FILE);
            if ($uploadErr !== UPLOAD_ERR_OK) {
                echo json_encode(['status' => 'error', 'message' => 'Upload gagal (kode ' . $uploadErr . ').']);
                exit();
            }

            $target_dir = "../assets/images/items/";
            if (!is_dir($target_dir)) {
                if (!@mkdir($target_dir, 0777, true)) {
                    echo json_encode(['status' => 'error', 'message' => 'Folder upload tidak bisa dibuat: ' . $target_dir]);
                    exit();
                }
            }

            if (!is_writable($target_dir)) {
                echo json_encode(['status' => 'error', 'message' => 'Folder upload tidak bisa ditulis: ' . $target_dir]);
                exit();
            }

            $file_ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
            if (!in_array($file_ext, $allowed_ext, true)) {
                echo json_encode(['status' => 'error', 'message' => 'Format gambar tidak didukung.']);
                exit();
            }

            $gambar_name = time() . '_' . uniqid() . '.' . $file_ext;
            $target_file = $target_dir . $gambar_name;

            if (!is_uploaded_file($_FILES['gambar']['tmp_name'])) {
                echo json_encode(['status' => 'error', 'message' => 'Upload sementara tidak valid.']);
                exit();
            }

            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                // If it's an update, maybe delete the old image?
                if ($id) {
                    $safeId = (int) $id;
                    $old_query = "SELECT gambar FROM menu WHERE id_menu = $safeId";
                    $old_res = mysqli_query($koneksi, $old_query);
                    $old_data = $old_res ? mysqli_fetch_assoc($old_res) : null;
                    if ($old_data && $old_data['gambar'] && file_exists($target_dir . $old_data['gambar'])) {
                        @unlink($target_dir . $old_data['gambar']);
                    }
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengupload gambar (tidak bisa memindahkan file).']);
                exit();
            }
        }

        if ($id) {
            // Update
            $safeId = (int) $id;
            $sql = "UPDATE menu SET nama_menu = '$nama', harga_jual = $harga, stok = $stok, status = '$status'";
            if ($gambar_name) {
                $sql .= ", gambar = '$gambar_name'";
            }
            $sql .= " WHERE id_menu = $safeId";
            $message = "Barang berhasil diperbarui";
        } else {
            // Insert
            $sql = "INSERT INTO menu (nama_menu, harga_jual, stok, status, gambar) VALUES ('$nama', $harga, $stok, '$status', '$gambar_name')";
            $message = "Barang berhasil ditambahkan";
        }

        if (mysqli_query($koneksi, $sql)) {
            // Jika insert baru, update kode_barang dengan id yang baru didapat
            if (!$id) {
                $new_id = mysqli_insert_id($koneksi);
                mysqli_query($koneksi, "UPDATE menu SET kode_barang = '$new_id' WHERE id_menu = $new_id");
            }
            echo json_encode(['status' => 'success', 'message' => $message]);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($koneksi)]);
        }
        break;

    case 'DELETE':
        // Delete item
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id_menu'];
        
        // Delete image first
        $old_query = "SELECT gambar FROM menu WHERE id_menu = $id";
        $old_res = mysqli_query($koneksi, $old_query);
        $old_data = mysqli_fetch_assoc($old_res);
        if ($old_data && $old_data['gambar']) {
            $target_dir = "../assets/images/items/";
            if (file_exists($target_dir . $old_data['gambar'])) {
                unlink($target_dir . $old_data['gambar']);
            }
        }
        
        $sql = "DELETE FROM menu WHERE id_menu = $id";
        if (mysqli_query($koneksi, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Barang berhasil dihapus']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($koneksi)]);
        }
        break;

    case 'PUT':
        // We use POST with FormData for updates that include files because PHP doesn't natively parse multipart/form-data for PUT easily.
        // But if someone sends PUT without file, we can handle it here if needed.
        // For simplicity, our frontend will use POST for both Add and Update when files are involved.
        echo json_encode(['status' => 'error', 'message' => 'Silakan gunakan POST untuk update dengan gambar']);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Metode tidak didukung']);
        break;
}
?>
