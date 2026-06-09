<?php
session_start();
// Hubungkan ke database
require '../database/koneksi.php';

// Mengecek apakah ada parameter 'id' yang dikirim melalui URL (metode GET)
if (isset($_GET['id'])) {
    // Tangkap ID dan pastikan formatnya adalah angka (integer) untuk mencegah SQL Injection
    $id = (int)$_GET['id'];

    // --- LANGKAH 1: CARI NAMA FILE GAMBAR ---
    // Sebelum menghapus data, kita harus tahu nama file gambarnya agar bisa dihapus dari folder
    $query_gambar = "SELECT image FROM products WHERE id = $id";
    $result_gambar = mysqli_query($koneksi, $query_gambar);

    if (mysqli_num_rows($result_gambar) > 0) {
        $row = mysqli_fetch_assoc($result_gambar);
        $nama_gambar = $row['image'];

        // --- LANGKAH 2: HAPUS FILE FISIK GAMBAR ---
        // Jika nama gambar ada dan bukan gambar bawaan (no-image.png), maka hapus filenya
        if ($nama_gambar != 'no-image.png' && $nama_gambar != '') {
            $path_gambar = "../assets/images/products/" . $nama_gambar;
            
            // Cek apakah file benar-benar ada di folder
            if (file_exists($path_gambar)) {
                unlink($path_gambar); // Fungsi unlink() digunakan untuk menghapus file di PHP
            }
        }

        // --- LANGKAH 3: HAPUS DATA DARI DATABASE ---
        $query_hapus = "DELETE FROM products WHERE id = $id";
        
        if (mysqli_query($koneksi, $query_hapus)) {
            // Jika berhasil dihapus, tampilkan alert dan kembali ke halaman produk
            echo "<script>
                    alert('Produk berhasil dihapus!');
                    window.location.href = 'products.php';
                  </script>";
        } else {
            // Jika query database gagal
            echo "<script>
                    alert('Gagal menghapus produk dari database: " . mysqli_error($koneksi) . "');
                    window.location.href = 'products.php';
                  </script>";
        }
    } else {
        // Jika ID produk tidak ditemukan di database
        echo "<script>
                alert('Data produk tidak ditemukan!');
                window.location.href = 'products.php';
              </script>";
    }
} else {
    // Jika seseorang mencoba mengakses file ini secara langsung tanpa mengirim ID
    header("Location: products.php");
    exit();
}
?>