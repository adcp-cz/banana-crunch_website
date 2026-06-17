<?php
session_start();
require 'database/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

// Tentukan direktori upload
$upload_dir = 'assets/images/users/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto'])) {
    $file = $_FILES['foto'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];

    // Validasi file
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png'];

    if (in_array($file_ext, $allowed)) {
        if ($file_error === 0) {
            if ($file_size < 2000000) { // Max 2MB
                $new_file_name = time() . "_" . $user_id . "." . $file_ext;
                $dest = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $dest)) {
                    // Simpan ke database
                    $query = "INSERT INTO foto (user_id, nama_foto) VALUES ('$user_id', '$new_file_name')";
                    if (mysqli_query($koneksi, $query)) {
                        $_SESSION['msg'] = "Foto berhasil ditambahkan!";
                    } else {
                        $_SESSION['error'] = "Gagal menyimpan ke database.";
                    }
                } else {
                    $_SESSION['error'] = "Gagal mengunggah file.";
                }
            } else {
                $_SESSION['error'] = "Ukuran file terlalu besar (Maks 2MB).";
            }
        } else {
            $_SESSION['error'] = "Terjadi kesalahan saat mengunggah.";
        }
    } else {
        $_SESSION['error'] = "Format file tidak didukung (Hanya JPG, JPEG, PNG).";
    }

    // Redirect kembali ke profil
    $redirect = ($_SESSION['role'] == 'admin') ? 'admin/dashboard.php' : 'user/profil_user.php';
    header("Location: $redirect");
    exit();
}

// Fitur Hapus Foto
if ($action == 'hapus' && isset($_GET['id'])) {
    $foto_id = (int)$_GET['id'];

    // Cari nama file
    $query_find = "SELECT nama_foto FROM foto WHERE id = $foto_id AND user_id = $user_id";
    $result_find = mysqli_query($koneksi, $query_find);
    
    if (mysqli_num_rows($result_find) > 0) {
        $data_foto = mysqli_fetch_assoc($result_find);
        $file_path = $upload_dir . $data_foto['nama_foto'];

        // Hapus file fisik
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Hapus dari database
        $query_del = "DELETE FROM foto WHERE id = $foto_id AND user_id = $user_id";
        if (mysqli_query($koneksi, $query_del)) {
            $_SESSION['msg'] = "Foto berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Gagal menghapus dari database.";
        }
    }

    $redirect = ($_SESSION['role'] == 'admin') ? 'admin/dashboard.php' : 'user/profil_user.php';
    header("Location: $redirect");
    exit();
}
?>