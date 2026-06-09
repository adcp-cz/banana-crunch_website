<?php
// Hubungkan ke database
require 'database/koneksi.php';

// Kita set password barunya jadi: admin123
$password_baru = "admin123";

// Biarkan PHP yang membuat kode hash-nya secara otomatis
$hash = password_hash($password_baru, PASSWORD_BCRYPT);

// Update password untuk admin@gmail.com (sesuai email di database kamu)
$query = "UPDATE users SET password = '$hash' WHERE email = 'admin@gmail.com'";

if(mysqli_query($koneksi, $query)){
    echo "<h1>Hore! Password Berhasil Diubah!</h1>";
    echo "Silakan login dengan data berikut:<br><br>";
    echo "Email: <b>admin@gmail.com</b><br>";
    echo "Password: <b>admin123</b><br><br>";
    echo "<a href='login.php'>Klik di sini untuk Login</a>";
} else {
    echo "Gagal mengubah password: " . mysqli_error($koneksi);
}
?>