<?php
session_start();
// Hubungkan ke database
require '../database/koneksi.php'; 

// Pastikan form dikirim melalui metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Tangkap semua input dari form dan bersihkan dari karakter berbahaya
    $name        = mysqli_real_escape_string($koneksi, $_POST['name']);
    $description = mysqli_real_escape_string($koneksi, $_POST['description']);
    $category    = mysqli_real_escape_string($koneksi, $_POST['category']);
    $price       = (float) $_POST['price'];
    $weight      = (int) $_POST['weight'];
    $stock       = (int) $_POST['stock'];
    
    // 2. Buat URL-friendly slug otomatis dari nama produk (Misal: "Pisang Manis" jadi "pisang-manis")
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    
    // 3. Ambil ID Admin yang sedang login (Atau gunakan angka 1 sebagai default jika session belum sempurna)
    $created_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; 

    // 4. Proses Upload Foto Produk
    $image_name = "no-image.png"; // Nama gambar default jika admin tidak mengunggah foto
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $img_tmp = $_FILES['image']['tmp_name'];
        // Beri nama unik menggunakan waktu saat ini agar nama file tidak bentrok
        $image_name = time() . '_' . basename($_FILES['image']['name']); 
        
        // Tentukan folder tujuan penyimpanan gambar
        $target_dir = "../assets/images/products/";
        
        // Buat foldernya otomatis jika belum ada di komputermu
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Pindahkan file gambar dari penyimpanan sementara ke folder tujuan
        move_uploaded_file($img_tmp, $target_dir . $image_name);
    }

    // 5. Query untuk memasukkan data ke tabel `products`
    $query = "INSERT INTO products 
              (name, slug, description, price, stock, weight, image, category, is_active, created_by) 
              VALUES 
              ('$name', '$slug', '$description', $price, $stock, $weight, '$image_name', '$category', 1, $created_by)";

    // 6. Eksekusi query dan berikan notifikasi
    if (mysqli_query($koneksi, $query)) {
        echo "<script>
                alert('Berhasil! Produk baru telah ditambahkan ke katalog.');
                window.location.href='products.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambahkan produk: " . mysqli_error($koneksi) . "');
                window.location.href='products.php';
              </script>";
    }
} else {
    // Jika file diakses langsung tanpa lewat form
    header("Location: products.php");
    exit();
}
?>