<?php
// Mulai session untuk menyimpan data login
session_start();

// Panggil koneksi database
require 'database/koneksi.php'; 

// Cek apakah form dikirim menggunakan method POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Tangkap input email dan password
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // Cari user berdasarkan email di database
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($koneksi, $query);

    // Jika email ditemukan
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Cek apakah akun aktif
        if ($row['is_active'] == 0) {
            echo "<script>
                    alert('Akun Anda telah dinonaktifkan. Silakan hubungi admin.');
                    window.location.href='login.php';
                  </script>";
            exit;
        }

        // Verifikasi password yang diinput dengan password hash (bcrypt) di database
        if (password_verify($password, $row['password'])) {
            
            // Jika password benar, set Session
            $_SESSION['user_id'] = $row['id'];
            
            // PERBAIKAN: Ubah 'user_role' menjadi 'role' agar sinkron dengan file dashboard
            $_SESSION['role'] = $row['role']; 
            $_SESSION['user_name'] = $row['name'];

            // Cek Role: Arahkan ke dashboard yang sesuai
            if ($row['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: user/dashboard.php"); 
            }
            exit;
            
        } else {
            // Password salah
            echo "<script>
                    alert('Kata sandi yang Anda masukkan salah!');
                    window.location.href='login.php';
                  </script>";
        }
    } else {
        // Email tidak ditemukan
        echo "<script>
                alert('Alamat email tidak terdaftar di sistem kami!');
                window.location.href='login.php';
              </script>";
    }
}
?>