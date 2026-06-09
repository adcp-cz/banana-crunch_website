<?php
session_start();
// Arahkan require ke file koneksi.php yang benar
require '../database/koneksi.php'; 

// Cek apakah ada request POST dari form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Tangkap data dari form
    $name = mysqli_real_escape_string($koneksi, $_POST['name']);
    $description = mysqli_real_escape_string($koneksi, $_POST['description']);
    $category = mysqli_real_escape_string($koneksi, $_POST['category']);
    $price = $_POST['price'];
    $weight = $_POST['weight'];
    $stock = $_POST['stock'];
    
    // Buat slug (URL Friendly) dari nama produk (contoh: Keripik Manis -> keripik-manis)
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    
    // Ambil ID Admin yang sedang login (Sebagai contoh kita pakai angka 1 untuk admin default)
    // Nanti jika fitur login sudah jalan, ganti jadi: $created_by = $_SESSION['user_id'];
    $created_by = 1; 

    // --- PROSES UPLOAD GAMBAR ---
    $image_name = "no-image.png"; // Default jika gagal
    
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $img_tmp = $_FILES['image']['tmp_name'];
        // Generate nama unik agar file tidak tertimpa
        $image_name = time() . '_' . $_FILES['image']['name']; 
        
        // Tentukan folder tujuan (Pastikan folder assets/images/products/ ini ada di komputermu!)
        $target_dir = "../assets/images/products/";
        
        // Pindahkan file dari memori sementara ke folder tujuan
        move_uploaded_file($img_tmp, $target_dir . $image_name);
    }

    // --- PROSES INSERT KE DATABASE ---
    $query = "INSERT INTO products 
              (name, slug, description, price, stock, weight, image, category, is_active, created_by) 
              VALUES 
              ('$name', '$slug', '$description', '$price', '$stock', '$weight', '$image_name', '$category', 1, '$created_by')";

    if (mysqli_query($koneksi, $query)) {
        // Jika sukses, munculkan alert dan kembali ke halaman produk
        echo "<script>
                alert('Produk berhasil ditambahkan!');
                window.location.href='products.php';
              </script>";
    } else {
        // Jika gagal
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }
}
?>