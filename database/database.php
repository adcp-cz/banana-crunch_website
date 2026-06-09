<?php

/**
 * ============================================================
 *  database.php — Koneksi Database MySQLi
 *  Project  : UMKM Keripik Pisang
 *  Author   : Developer
 *  Version  : 1.0.0
 * ============================================================
 *  Cara pakai:
 *    require_once 'config/database.php';
 *    $result = mysqli_query($conn, "SELECT ...");
 * ============================================================
 */

// ------------------------------------------------------------
// 1. KONFIGURASI — sesuaikan nilai di bawah ini
// ------------------------------------------------------------
define('DB_HOST',     'localhost');
define('DB_USER',     'root');
define('DB_PASS',     '');
define('DB_NAME',     'db_keripik_pisang');
define('DB_PORT',     3306);
define('DB_CHARSET',  'utf8mb4');


// ------------------------------------------------------------
// 2. FUNGSI KONEKSI
//    Mengembalikan object $conn yang siap digunakan.
//    Langsung exit jika koneksi gagal (fail-fast).
// ------------------------------------------------------------
function db_connect(): mysqli
{
    // Buat koneksi MySQLi
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

    // Cek error koneksi
    if ($conn->connect_errno) {
        // Mode PRODUCTION  → tampilkan pesan umum, log detail ke file
        // Mode DEVELOPMENT → tampilkan pesan lengkap (ganti nilai di bawah)
        $is_dev = defined('APP_ENV') && APP_ENV === 'development';

        if ($is_dev) {
            $msg = sprintf(
                '[DB ERROR] Koneksi gagal — Errno: %d | Error: %s',
                $conn->connect_errno,
                $conn->connect_error
            );
        } else {
            // Catat error ke error_log server, jangan tampilkan ke user
            error_log(sprintf(
                '[DB ERROR] Koneksi gagal — Errno: %d | Error: %s | Host: %s | DB: %s',
                $conn->connect_errno,
                $conn->connect_error,
                DB_HOST,
                DB_NAME
            ));
            $msg = 'Sistem sedang mengalami gangguan. Silakan coba beberapa saat lagi.';
        }

        // Hentikan eksekusi agar halaman tidak lanjut tanpa koneksi
        http_response_code(503);
        exit($msg);
    }

    // Paksa charset utf8mb4 agar karakter khusus & emoji aman
    if (!$conn->set_charset(DB_CHARSET)) {
        error_log('[DB ERROR] Gagal set charset ' . DB_CHARSET . ': ' . $conn->error);
    }

    return $conn;
}


// ------------------------------------------------------------
// 3. BUAT INSTANCE KONEKSI (global $conn)
//    Langsung tersedia setelah file ini di-include/require.
// ------------------------------------------------------------
$conn = db_connect();


// ------------------------------------------------------------
// 4. FUNGSI UTILITAS — opsional, membantu query lebih aman
// ------------------------------------------------------------

/**
 * Escape string untuk mencegah SQL Injection.
 * Gunakan ini HANYA jika belum pakai prepared statement.
 *
 * Contoh:
 *   $name = db_escape($conn, $_POST['name']);
 *   $sql  = "SELECT * FROM users WHERE name = '$name'";
 */
function db_escape(mysqli $conn, string $value): string
{
    return $conn->real_escape_string(trim($value));
}

/**
 * Jalankan query & kembalikan mysqli_result.
 * Script langsung berhenti jika query gagal (fail-fast).
 *
 * Contoh:
 *   $result = db_query($conn, "SELECT * FROM products WHERE is_active = 1");
 *   while ($row = $result->fetch_assoc()) { ... }
 */
function db_query(mysqli $conn, string $sql): mysqli_result|bool
{
    $result = $conn->query($sql);

    if ($result === false) {
        $is_dev = defined('APP_ENV') && APP_ENV === 'development';
        $msg    = $is_dev
            ? '[DB QUERY ERROR] ' . $conn->error . ' | SQL: ' . $sql
            : 'Terjadi kesalahan saat memproses data.';

        error_log('[DB QUERY ERROR] ' . $conn->error . ' | SQL: ' . $sql);
        http_response_code(500);
        exit($msg);
    }

    return $result;
}

/**
 * Hitung total baris dari suatu tabel / kondisi.
 *
 * Contoh:
 *   $total = db_count($conn, "SELECT COUNT(*) FROM products WHERE is_active = 1");
 *   echo $total; // 5
 */
function db_count(mysqli $conn, string $sql): int
{
    $result = db_query($conn, $sql);
    $row    = $result->fetch_row();
    return (int) ($row[0] ?? 0);
}

/**
 * Tutup koneksi secara eksplisit (opsional — PHP otomatis tutup di akhir script).
 *
 * Contoh:
 *   db_close($conn);
 */
function db_close(mysqli $conn): void
{
    $conn->close();
}