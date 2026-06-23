<?php
$host = "localhost";
$user = "root"; // Sesuaikan jika kamu pakai password di XAMPP/Laragon
$pass = ""; 
$db   = "db_keripik_pisang";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}