<?php
// Panggil koneksi database
require 'database/koneksi.php';

// Kita atur password baru yang mudah diingat dulu: 12345
$password_baru = "12345";

// Biarkan PHP yang membuatkan kode hash-nya secara otomatis agar 100% cocok
$hash_password = password_hash($password_baru, PASSWORD_BCRYPT);

// Masukkan ke database untuk admin (ID = 1)
$query = "UPDATE users SET password = '$hash_password' WHERE id = 1";

if (mysqli_query($koneksi, $query)) {
    echo "<h1>BERHASIL!</h1>";
    echo "<p>Kata sandi untuk Admin telah di-reset menjadi: <b>12345</b></p>";
    echo "<a href='login.php'>Klik di sini untuk kembali ke halaman Login</a>";
} else {
    echo "Gagal mereset: " . mysqli_error($koneksi);
}
?>