<?php
session_start();
// Hubungkan ke database
require '../database/koneksi.php';

// Pastikan request datang dari form metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Tangkap semua data dari form modal edit
    $id          = (int) $_POST['id'];
    $name        = mysqli_real_escape_string($koneksi, $_POST['name']);
    $description = mysqli_real_escape_string($koneksi, $_POST['description']);
    $category    = mysqli_real_escape_string($koneksi, $_POST['category']);
    $price       = (float) $_POST['price'];
    $weight      = (int) $_POST['weight'];
    $stock       = (int) $_POST['stock'];
    $gambar_lama = $_POST['gambar_lama']; // Nama file gambar sebelum diedit

    // 2. Logika Penggantian Foto Produk
    $image_name = $gambar_lama; // Secara default, kita asumsikan admin tidak mengganti foto

    // Cek apakah ada file foto BARI yang diunggah (error 0 berarti ada file)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $img_tmp = $_FILES['image']['tmp_name'];
        // Beri nama unik untuk gambar baru
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_dir = "../assets/images/products/";

        // Buat folder jika ternyata belum ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Pindahkan gambar baru ke folder target
        if (move_uploaded_file($img_tmp, $target_dir . $image_name)) {
            // HAPUS GAMBAR LAMA dari folder agar tidak menumpuk memenuhi penyimpanan
            // Syarat: Jangan hapus jika gambar lamanya adalah 'no-image.png'
            if ($gambar_lama != 'no-image.png' && $gambar_lama != '') {
                $path_lama = $target_dir . $gambar_lama;
                if (file_exists($path_lama)) {
                    unlink($path_lama); // Menghapus file fisik
                }
            }
        } else {
            // Jika gagal memindahkan gambar baru, kembalikan nama ke gambar lama untuk berjaga-jaga
            $image_name = $gambar_lama;
        }
    }

    // 3. Eksekusi Query UPDATE ke Database
    $query = "UPDATE products SET 
                name = '$name', 
                description = '$description', 
                category = '$category', 
                price = $price, 
                weight = $weight, 
                stock = $stock, 
                image = '$image_name' 
              WHERE id = $id";

    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil
        echo "<script>
                alert('Produk berhasil diperbarui!');
                window.location.href = 'products.php';
              </script>";
    } else {
        // Jika gagal database
        echo "<script>
                alert('Gagal memperbarui produk: " . mysqli_error($koneksi) . "');
                window.location.href = 'products.php';
              </script>";
    }
} else {
    // Jika file diakses langsung tanpa mengirim data
    header("Location: products.php");
    exit();
}
?>