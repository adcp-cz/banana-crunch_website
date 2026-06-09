<?php
session_start();
// Hubungkan ke database
require 'database/koneksi.php';

// 1. WAJIB LOGIN: Cek apakah user sudah masuk ke akunnya
if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Silakan masuk (login) terlebih dahulu untuk menambahkan produk ke keranjang!');
            window.location.href = 'login.php';
          </script>";
    exit();
}

// Pastikan data dikirim dari form menggunakan POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Tangkap data dari session dan form
    $user_id    = $_SESSION['user_id'];
    $product_id = (int) $_POST['product_id'];
    $quantity   = (int) $_POST['quantity'];

    // 2. CEK STOK: Pastikan produk valid dan stoknya masih cukup
    $query_stok = "SELECT stock, name FROM products WHERE id = $product_id AND is_active = 1";
    $result_stok = mysqli_query($koneksi, $query_stok);
    
    if (mysqli_num_rows($result_stok) == 0) {
        echo "<script>
                alert('Produk tidak ditemukan atau tidak aktif!');
                window.location.href = 'products.php';
              </script>";
        exit();
    }
    
    $data_produk = mysqli_fetch_assoc($result_stok);
    $stok_tersedia = $data_produk['stock'];

    if ($quantity > $stok_tersedia) {
        echo "<script>
                alert('Maaf, jumlah yang diminta melebihi stok yang tersedia!');
                window.location.href = 'product-detail.php?id=$product_id';
              </script>";
        exit();
    }

    // 3. CEK KERANJANG: Apakah produk ini sudah pernah dimasukkan ke keranjang oleh user ini?
    $query_cek_keranjang = "SELECT id, qty FROM cart WHERE user_id = $user_id AND product_id = $product_id";
    $result_cek = mysqli_query($koneksi, $query_cek_keranjang);

    if (mysqli_num_rows($result_cek) > 0) {
        // JIKA SUDAH ADA: Update jumlahnya (qty lama + qty baru)
        $data_keranjang = mysqli_fetch_assoc($result_cek);
        $qty_baru = $data_keranjang['qty'] + $quantity;

        // Proteksi: Pastikan total kuantitas di keranjang tidak melebihi stok asli
        if ($qty_baru > $stok_tersedia) {
            $qty_baru = $stok_tersedia; 
        }

        $cart_id = $data_keranjang['id'];
        $query_eksekusi = "UPDATE cart SET qty = $qty_baru WHERE id = $cart_id";
        
    } else {
        // JIKA BELUM ADA: Masukkan sebagai baris baru di tabel cart
        $query_eksekusi = "INSERT INTO cart (user_id, product_id, qty) VALUES ($user_id, $product_id, $quantity)";
    }

    // 4. JALANKAN QUERY & BERIKAN NOTIFIKASI
    if (mysqli_query($koneksi, $query_eksekusi)) {
        echo "<script>
                alert('Berhasil! " . htmlspecialchars($data_produk['name']) . " telah ditambahkan ke keranjang Anda.');
                window.location.href = 'product-detail.php?id=$product_id';
              </script>";
    } else {
        echo "<script>
                alert('Sistem mengalami kendala: " . mysqli_error($koneksi) . "');
                window.location.href = 'product-detail.php?id=$product_id';
              </script>";
    }

} else {
    // Jika file diakses langsung dari URL tanpa menekan tombol form
    header("Location: products.php");
    exit();
}
?>