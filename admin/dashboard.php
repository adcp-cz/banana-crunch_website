<?php
session_start();
require '../database/koneksi.php';

// 1. PROTEKSI HALAMAN ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

// 2. KUMPULKAN DATA STATISTIK UTAMA
// Total Penjualan (Hanya pesanan yang tidak batal)
$q_revenue = mysqli_query($koneksi, "SELECT SUM(total_price) as total FROM orders WHERE status != 'BATAL'");
$revenue = mysqli_fetch_assoc($q_revenue)['total'] ?? 0;

// Total Pesanan
$q_orders = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM orders");
$total_orders = mysqli_fetch_assoc($q_orders)['total'] ?? 0;

// Total User (Pelanggan)
$q_users = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM users WHERE role = 'user'");
$total_users = mysqli_fetch_assoc($q_users)['total'] ?? 0;

// Total Produk Aktif
$q_products = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM products WHERE is_active = 1");
$total_products = mysqli_fetch_assoc($q_products)['total'] ?? 0;

// Fungsi Format Rupiah Singkat untuk UI (Contoh: 12.500.000 jadi 12.5M)
function formatRupiahSingkat($angka) {
    if ($angka >= 1000000) {
        return 'Rp ' . round($angka / 1000000, 1) . 'M';
    } elseif ($angka >= 1000) {
        return 'Rp ' . round($angka / 1000, 1) . 'k';
    }
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// 3. AMBIL 5 PESANAN TERBARU
$query_recent_orders = "SELECT o.id, o.total_price, o.status, u.name as customer_name 
                        FROM orders o 
                        JOIN users u ON o.user_id = u.id 
                        ORDER BY o.created_at DESC LIMIT 5";
$recent_orders = mysqli_query($koneksi, $query_recent_orders);

// 4. SIAPKAN DATA GRAFIK 7 HARI TERAKHIR
$label_hari = [];
$data_penjualan = [];

for ($i = 6; $i >= 0; $i--) {
    $tanggal_target = date('Y-m-d', strtotime("-$i days"));
    $nama_hari = date('D', strtotime($tanggal_target)); 
    
    // Format bahasa Indonesia
    $hari_indo = ['Mon'=>'Sen', 'Tue'=>'Sel', 'Wed'=>'Rab', 'Thu'=>'Kam', 'Fri'=>'Jum', 'Sat'=>'Sab', 'Sun'=>'Min'];
    $label_hari[] = $hari_indo[$nama_hari];

    $q_harian = mysqli_query($koneksi, "SELECT SUM(total_price) as harian FROM orders WHERE DATE(created_at) = '$tanggal_target' AND status != 'BATAL'");
    $hasil_harian = mysqli_fetch_assoc($q_harian)['harian'] ?? 0;
    
    // Dibagi sejuta agar serasi dengan grafik "Dalam Jutaan"
    $data_penjualan[] = round($hasil_harian / 1000000, 2); 
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jajan Pisang | Admin Panel Management</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">

    <style>
        /* --- DASHBOARD SPECIFIC STYLES --- */
        /* --- DASHBOARD STATS CARD --- */
        .stat-card {
            background-color: var(--card-bg); border-radius: var(--radius-lg); padding: 24px;
            box-shadow: var(--shadow-soft); border: none; display: flex; justify-content: space-between;
            align-items: center; position: relative; overflow: hidden; height: 100%;
        }
        .stat-title { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 8px; }
        .stat-value { font-size: 1.6rem; font-weight: 700; color: var(--text-dark); margin-bottom: 6px; }
        .stat-trend { font-size: 0.8rem; font-weight: 500; }
        .trend-up { color: #22C55E; }
        .trend-neutral { color: var(--text-muted); }

        .stat-icon-box {
            width: 48px; height: 48px; border-radius: var(--radius-md); display: flex;
            align-items: center; justify-content: center; font-size: 1.25rem;
        }
        .icon-box-yellow { background-color: rgba(255, 214, 0, 0.2); color: var(--secondary); }
        .icon-box-khaki { background-color: rgba(138, 111, 0, 0.1); color: var(--secondary); }
        .icon-box-cyan { background-color: rgba(0, 229, 255, 0.15); color: #00B8D9; }

        /* --- VISUALIZATION & DATA CARD --- */
        .dashboard-panel-card {
            background-color: var(--card-bg); border-radius: var(--radius-lg); padding: 30px;
            box-shadow: var(--shadow-soft); border: none; height: 100%;
        }
        .panel-card-title { font-weight: 700; font-size: 1.2rem; color: var(--text-dark); margin: 0; }
        .filter-dropdown-btn {
            background: #FFFFFF; border: 1px solid #EFEFEF; border-radius: var(--radius-md);
            padding: 6px 14px; font-size: 0.85rem; font-weight: 500; color: var(--text-dark);
        }

        /* --- RECENT ORDERS LIST --- */
        .order-list-wrapper { display: flex; flex-direction: column; gap: 20px; margin-top: 25px; }
        .order-item-row { display: flex; align-items: center; justify-content: space-between; }
        .order-customer-profile { display: flex; align-items: center; gap: 15px; }
        .customer-initial-avatar {
            width: 44px; height: 44px; border-radius: var(--radius-md); background-color: #F2F2F7;
            display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.9rem; color: var(--text-dark);
        }
        .order-code { font-size: 0.9rem; font-weight: 600; margin: 0; color: var(--text-dark); }
        .customer-name { font-size: 0.8rem; color: var(--text-muted); }
        .order-price-info { text-align: right; }
        .order-amount { font-size: 0.9rem; font-weight: 600; margin: 0; }
        
        .status-pill {
            display: inline-block; font-size: 0.65rem; font-weight: 700; padding: 4px 10px;
            border-radius: var(--radius-md); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;
        }
        .status-pill.selesai { background-color: rgba(34, 197, 94, 0.15); color: #22C55E; }
        .status-pill.proses { background-color: rgba(245, 158, 11, 0.15); color: #F59E0B; }
        .status-pill.baru { background-color: rgba(142, 142, 147, 0.15); color: #8E8E93; }

        /* Responsive Breakpoint */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); transition: var(--transition); }
            .main-content { margin-left: 0; padding: 24px; }
        }
    </style>
</head>
<body>
    <?php include '../navbar_login.php'; ?>

    <?php include '../components/admin-sidebar.php'; ?>

    <main class="main-content">
        
        <div class="dash-header">
            <h1 class="dash-title">Dashboard</h1>
            <div class="notification-bell">
                <i class="far fa-bell fs-5"></i>
                <span class="bell-badge"></span>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div>
                        <div class="stat-title">Total Penjualan</div>
                        <div class="stat-value"><?= formatRupiahSingkat($revenue) ?></div>
                        <div class="stat-trend trend-up"><i class="fas fa-arrow-up me-1"></i> Tervalidasi</div>
                    </div>
                    <div class="stat-icon-box icon-box-yellow"><i class="fas fa-wallet"></i></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div>
                        <div class="stat-title">Total Pesanan</div>
                        <div class="stat-value"><?= number_format($total_orders, 0, ',', '.') ?></div>
                        <div class="stat-trend trend-up"><i class="fas fa-arrow-up me-1"></i> Seluruh waktu</div>
                    </div>
                    <div class="stat-icon-box icon-box-khaki"><i class="fas fa-truck"></i></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div>
                        <div class="stat-title">Total User</div>
                        <div class="stat-value"><?= number_format($total_users, 0, ',', '.') ?></div>
                        <div class="stat-trend trend-neutral">&mdash; Stabil bulan ini</div>
                    </div>
                    <div class="stat-icon-box icon-box-cyan"><i class="fas fa-user"></i></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div>
                        <div class="stat-title">Total Produk</div>
                        <div class="stat-value"><?= number_format($total_products, 0, ',', '.') ?></div>
                        <div class="stat-trend trend-up"><i class="fas fa-plus me-1"></i> Katalog aktif</div>
                    </div>
                    <div class="stat-icon-box icon-box-yellow"><i class="fas fa-box"></i></div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-xl-8 col-lg-7">
                <div class="dashboard-panel-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="panel-card-title">Statistik Penjualan</h2>
                        <button class="filter-dropdown-btn">7 Hari Terakhir <i class="fas fa-chevron-down ms-1 fs-xs text-muted"></i></button>
                    </div>
                    <div style="position: relative; height:320px; width:100%;">
                        <canvas id="salesAnalyticsChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-5">
                <div class="dashboard-panel-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="panel-card-title">Pesanan Terbaru</h2>
                        <a href="orders.php" class="small font-weight-medium text-decoration-none" style="color: var(--secondary);">Lihat Semua</a>
                    </div>
                    
                    <div class="order-list-wrapper">
                        <?php if (mysqli_num_rows($recent_orders) > 0): ?>
                            <?php while ($ord = mysqli_fetch_assoc($recent_orders)): ?>
                                <?php 
                                    // Pembuatan inisial
                                    $nama = explode(" ", $ord['customer_name']);
                                    $inisial = strtoupper(substr($nama[0], 0, 1) . (isset($nama[1]) ? substr($nama[1], 0, 1) : ''));
                                    
                                    // Pemetaan warna badge dengan class CSS khusus yang kamu buat
                                    $badge_class = '';
                                    if ($ord['status'] == 'SELESAI') $badge_class = 'selesai';
                                    elseif ($ord['status'] == 'PROSES' || $ord['status'] == 'DIKIRIM') $badge_class = 'proses';
                                    else $badge_class = 'baru'; // Untuk status batal atau lainnya
                                ?>
                                <div class="order-item-row">
                                    <div class="order-customer-profile">
                                        <div class="customer-initial-avatar"><?= $inisial ?></div>
                                        <div>
                                            <h4 class="order-code">#ORD-<?= str_pad($ord['id'], 3, '0', STR_PAD_LEFT) ?></h4>
                                            <span class="customer-name"><?= htmlspecialchars($ord['customer_name']) ?></span>
                                        </div>
                                    </div>
                                    <div class="order-price-info">
                                        <h4 class="order-amount"><?= formatRupiahSingkat($ord['total_price']) ?></h4>
                                        <span class="status-pill <?= $badge_class ?>"><?= $ord['status'] ?></span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">Belum ada transaksi masuk.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('salesAnalyticsChart').getContext('2d');
        
        // PHP DINAMIS: Inject array dari PHP ke Javascript
        const labelsHari = <?= json_encode($label_hari) ?>;
        const dataPenjualan = <?= json_encode($data_penjualan) ?>;

        const chartGradient = ctx.createLinearGradient(0, 0, 0, 300);
        chartGradient.addColorStop(0, 'rgba(138, 111, 0, 0.15)');
        chartGradient.addColorStop(1, 'rgba(138, 111, 0, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labelsHari,
                datasets: [{
                    label: 'Penjualan (Juta)',
                    data: dataPenjualan,
                    borderColor: '#8A6F00', 
                    borderWidth: 3,
                    pointBackgroundColor: '#8A6F00',
                    pointHoverRadius: 6,
                    tension: 0.4, 
                    fill: true,
                    backgroundColor: chartGradient
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false } 
                },
                scales: {
                    x: {
                        grid: { display: false }, 
                        ticks: { color: '#8E8E93', font: { family: 'Open Sans' } }
                    },
                    y: {
                        min: 0,
                        // Menghapus 'max: 4.5' agar chart bisa terus membesar jika pendapatan tembus lebih dari 4.5 Juta
                        ticks: {
                            color: '#8E8E93',
                            font: { family: 'Open Sans' }
                        },
                        grid: { color: '#F2F2F7' }
                    }
                }
            }
        });
    </script>
</body>
</html>

