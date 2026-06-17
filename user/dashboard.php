<?php
session_start();
require '../database/koneksi.php'; // Pastikan path sudah pakai ../

// 1. PROTEKSI HALAMAN: Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. AMBIL DATA PENGGUNA
$query_user = "SELECT name, email FROM users WHERE id = $user_id";
$result_user = mysqli_query($koneksi, $query_user);
$user_data = mysqli_fetch_assoc($result_user);

$nama_lengkap = $user_data['name'];
// Membuat inisial nama
$kata = explode(" ", $nama_lengkap);
$inisial = strtoupper(substr($kata[0], 0, 1) . (isset($kata[1]) ? substr($kata[1], 0, 1) : ''));

// 3. AMBIL DATA STATISTIK (Disesuaikan dengan ENUM Database Baru)
// Pesanan Aktif (pending, paid, processing, shipped)
$query_aktif = "SELECT COUNT(*) as aktif FROM orders WHERE user_id = $user_id AND status IN ('pending', 'paid', 'processing', 'shipped')";
$pesanan_aktif = mysqli_fetch_assoc(mysqli_query($koneksi, $query_aktif))['aktif'];

// Total Pesanan Keseluruhan
$query_total = "SELECT COUNT(*) as total FROM orders WHERE user_id = $user_id";
$total_pesanan = mysqli_fetch_assoc(mysqli_query($koneksi, $query_total))['total'];

// Total Belanja (Menggunakan kolom grand_total dan mengabaikan yang dibatalkan)
$query_belanja = "SELECT SUM(grand_total) as belanja FROM orders WHERE user_id = $user_id AND status != 'cancelled'";
$total_belanja = mysqli_fetch_assoc(mysqli_query($koneksi, $query_belanja))['belanja'] ?? 0;

// 4. AMBIL RIWAYAT PESANAN TERBARU (Menggunakan order_details dan LEFT JOIN products)
$query_riwayat = "SELECT o.id as order_id, o.order_code, o.created_at, o.status, od.product_name, p.image as product_image 
                  FROM orders o 
                  JOIN order_details od ON o.id = od.order_id 
                  LEFT JOIN products p ON od.product_id = p.id 
                  WHERE o.user_id = $user_id 
                  GROUP BY o.id 
                  ORDER BY o.created_at DESC LIMIT 5";
$riwayat_result = mysqli_query($koneksi, $query_riwayat);

// Fungsi format tanggal Indonesia
function tanggal_indo($tanggal) {
    $bulan = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    $pecahkan = explode('-', date('Y-m-d', strtotime($tanggal)));
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna | PisangKraf</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/user.css">

    <style>
        /* --- STAT CARDS --- */
        .stat-card {
            background-color: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 25px;
            box-shadow: var(--shadow-soft);
            border: 1px solid #EFEFEF;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: var(--transition);
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .icon-active { background-color: rgba(255, 214, 0, 0.2); color: var(--secondary); }
        .icon-total { background-color: rgba(34, 197, 94, 0.1); color: #15803D; }
        .icon-wallet { background-color: rgba(239, 68, 68, 0.1); color: #B91C1C; }

        .stat-info h5 { font-size: 0.9rem; color: var(--text-muted); font-weight: 500; margin-bottom: 5px; }
        .stat-info h3 { font-size: 1.8rem; font-weight: 700; margin: 0; color: var(--text-dark); }

        /* --- ACTIVITY SECTION --- */
        .activity-section {
            background-color: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: var(--shadow-soft);
            border: 1px solid #EFEFEF;
            margin-top: 40px;
        }
        .activity-title {
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #EFEFEF;
        }

        .activity-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #F2F2F7;
        }
        .activity-item:last-child { border-bottom: none; padding-bottom: 0; }

        .item-product-info { display: flex; align-items: center; gap: 15px; flex: 2; }
        .item-img { width: 60px; height: 60px; border-radius: 10px; object-fit: cover; border: 1px solid #E5E7EB; }
        .item-name { font-weight: 600; color: var(--text-dark); margin-bottom: 4px; }
        .item-date { font-size: 0.8rem; color: var(--text-muted); }

        .item-status { flex: 1; text-align: center; }
        .status-badge {
            padding: 6px 14px;
            border-radius: var(--radius-pill);
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        
        /* CSS Badge Warna Baru (Disesuaikan dengan ENUM database) */
        .status-pending { background-color: #F3F4F6; color: #4B5563; }
        .status-proses { background-color: rgba(59, 130, 246, 0.15); color: #1D4ED8; }
        .status-dikirim { background-color: rgba(255, 214, 0, 0.2); color: var(--secondary); }
        .status-selesai { background-color: rgba(34, 197, 94, 0.15); color: #15803D; }
        .status-batal { background-color: rgba(239, 68, 68, 0.15); color: #B91C1C; }

        .item-action { flex: 1; text-align: right; }
        .btn-detail {
            border: 1px solid #E5E7EB; color: var(--text-dark); background: transparent;
            padding: 8px 20px; border-radius: var(--radius-pill); font-size: 0.85rem;
            font-weight: 500; transition: var(--transition); text-decoration: none; display: inline-block;
        }
        .btn-detail:hover { background-color: var(--text-dark); color: #fff; border-color: var(--text-dark); }
    </style>
</head>
<body>

    <?php include '../navbar_login.php'; ?>
    <?php include '../components/user-sidebar.php'; ?>

    <main class="main-content">
        
        <header class="welcome-header">
            <h1 class="welcome-title">Selamat Datang Kembali!</h1>
            <p class="text-muted">Pantau aktivitas belanja dan pesanan produk olahan pisang favorit Anda di sini.</p>
        </header>

        <div class="row g-4">
            <div class="col-xl-4 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon icon-active"><i class="fas fa-truck-loading"></i></div>
                    <div class="stat-info">
                        <h5>Pesanan Aktif</h5>
                        <h3><?= $pesanan_aktif ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon icon-total"><i class="fas fa-box-open"></i></div>
                    <div class="stat-info">
                        <h5>Total Pesanan</h5>
                        <h3><?= $total_pesanan ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon icon-wallet"><i class="fas fa-wallet"></i></div>
                    <div class="stat-info">
                        <h5>Total Belanja</h5>
                        <h3 style="font-size: 1.4rem;">Rp <?= number_format($total_belanja, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <section class="activity-section">
            <h3 class="activity-title">Riwayat Aktivitas Terbaru</h3>

            <div class="activity-list">
                <?php if (mysqli_num_rows($riwayat_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($riwayat_result)): ?>
                        <?php 
                            // Atur warna class badge berdasarkan status database ENUM baru
                            $status_db = strtolower($row['status']);
                            $badge_class = '';
                            $status_text = '';

                            if ($status_db == 'pending') { $badge_class = 'status-pending'; $status_text = 'Menunggu Pembayaran'; }
                            elseif ($status_db == 'paid' || $status_db == 'processing') { $badge_class = 'status-proses'; $status_text = 'Sedang Diproses'; }
                            elseif ($status_db == 'shipped') { $badge_class = 'status-dikirim'; $status_text = 'Sedang Dikirim'; }
                            elseif ($status_db == 'completed') { $badge_class = 'status-selesai'; $status_text = 'Selesai'; }
                            elseif ($status_db == 'cancelled') { $badge_class = 'status-batal'; $status_text = 'Dibatalkan'; }
                            else { $badge_class = 'status-batal'; $status_text = $row['status']; }
                        ?>
                        <div class="activity-item">
                            <div class="item-product-info">
                                <img src="../assets/images/products/<?= htmlspecialchars($row['product_image'] ?? 'default.png') ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" class="item-img" onerror="this.src='https://via.placeholder.com/60?text=No+Img'">
                                <div>
                                    <div class="item-name"><?= htmlspecialchars($row['product_name']) ?></div>
                                    <div class="item-date"><?= tanggal_indo($row['created_at']) ?> &bull; <?= htmlspecialchars($row['order_code']) ?></div>
                                </div>
                            </div>
                            <div class="item-status">
                                <span class="status-badge <?= $badge_class ?>"><?= $status_text ?></span>
                            </div>
                            <div class="item-action">
                                <a href="pesanan.php" class="btn-detail">Lihat Detail</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-receipt mb-3" style="font-size: 2rem; color: #E5E7EB;"></i>
                        <p>Belum ada aktivitas pesanan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>