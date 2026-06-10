<?php
session_start();
require '../database/koneksi.php';

// 1. Proteksi Halaman (Hanya Admin yang boleh mengakses)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// 2. Pastikan data dikirim melalui metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    
    $order_id = (int)$_POST['order_id'];
    // Ubah ke huruf kecil semua agar cocok dengan ENUM di database
    $status_baru = mysqli_real_escape_string($koneksi, strtolower($_POST['status']));

    // 3. Validasi Keamanan: Pastikan status yang dikirim sesuai dengan daftar ENUM database
    $valid_statuses = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'];
    
    if (!in_array($status_baru, $valid_statuses)) {
        echo "<script>
                alert('Error: Status yang dipilih tidak dikenali oleh sistem!');
                window.location.href = 'orders.php';
              </script>";
        exit();
    }

    // 4. Susun Query Dinamis (Update Status + Update Waktu Otomatis)
    $update_query = "UPDATE orders SET status = '$status_baru'";
    
    // Logika Pintar: Isi kolom waktu (datetime) secara otomatis berdasarkan status yang dipilih
    if ($status_baru == 'paid') {
        $update_query .= ", paid_at = NOW()";
    } elseif ($status_baru == 'shipped') {
        $update_query .= ", shipped_at = NOW()";
    } elseif ($status_baru == 'completed') {
        $update_query .= ", delivered_at = NOW()";
    }

    $update_query .= " WHERE id = $order_id";

    // 5. Eksekusi Query
    if (mysqli_query($koneksi, $update_query)) {
        echo "<script>
                alert('Status pesanan berhasil diperbarui!');
                window.location.href = 'orders.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal memperbarui status: " . mysqli_error($koneksi) . "');
                window.location.href = 'orders.php';
              </script>";
    }
} else {
    // Jika file ini diakses langsung dari URL tanpa menekan tombol "Simpan" di form
    header("Location: orders.php");
    exit();
}
?>