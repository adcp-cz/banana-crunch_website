<?php
session_start();
// Hubungkan ke database
require 'database/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tangkap data dari form
    $name = mysqli_real_escape_string($koneksi, $_POST['name']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $phone = mysqli_real_escape_string($koneksi, $_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Validasi: Pastikan konfirmasi password cocok
    if ($password !== $confirm_password) {
        echo "<script>
                alert('Konfirmasi kata sandi tidak cocok!');
                window.location.href = 'register.php';
              </script>";
        exit;
    }

    // 2. Validasi: Cek apakah email sudah pernah didaftarkan
    $cek_email = "SELECT email FROM users WHERE email = '$email'";
    $hasil_cek = mysqli_query($koneksi, $cek_email);
    
    if (mysqli_num_rows($hasil_cek) > 0) {
        echo "<script>
                alert('Alamat email sudah terdaftar! Silakan gunakan email lain atau langsung Masuk.');
                window.location.href = 'register.php';
              </script>";
        exit;
    }

    // 3. Keamanan: Enkripsi password menggunakan BCRYPT
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // 4. Simpan ke database (Secara default role-nya adalah 'user' dan is_active = 1)
    $query = "INSERT INTO users (name, email, phone, password, role, is_active) 
              VALUES ('$name', '$email', '$phone', '$hashed_password', 'user', 1)";

    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil, arahkan ke halaman login
        echo "<script>
                alert('Pendaftaran berhasil! Silakan masuk menggunakan akun baru Anda.');
                window.location.href = 'login.php';
              </script>";
    } else {
        // Jika ada error pada database
        echo "<script>
                alert('Terjadi kesalahan sistem: " . mysqli_error($koneksi) . "');
                window.location.href = 'register.php';
              </script>";
    }
} else {
    // Jika file diakses langsung tanpa lewat form
    header("Location: register.php");
    exit;
}
?>