<?php
session_start();
require '../database/koneksi.php';

// 1. PROTEKSI HALAMAN
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. AMBIL DATA PENGGUNA UNTUK SIDEBAR
$query_user = "SELECT name FROM users WHERE id = $user_id";
$result_user = mysqli_query($koneksi, $query_user);
$user_data = mysqli_fetch_assoc($result_user);

$nama_lengkap = $user_data['name'];
$kata = explode(" ", $nama_lengkap);
$inisial = strtoupper(substr($kata[0], 0, 1) . (isset($kata[1]) ? substr($kata[1], 0, 1) : ''));

// 3. AMBIL DATA SELURUH PESANAN USER INI
// Mengambil data berdasarkan order_code/created_at terbaru
$query_orders = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
$result_orders = mysqli_query($koneksi, $query_orders);

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
    <title>Pesanan Saya | Naori Coffee</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/user.css">

    <style>
        /* --- ORDER CARDS --- */
        .order-card { background-color: var(--card-bg); border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); border: 1px solid var(--border-color); margin-bottom: 25px; overflow: hidden; }
        .order-header { background-color: #F9FAFB; padding: 15px 25px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
        .order-header-info span { display: block; font-size: 0.85rem; color: var(--text-muted); }
        .order-header-info strong { font-size: 1rem; color: var(--text-dark); }
        
        .order-body { padding: 25px; }
        
        .product-item { display: flex; align-items: center; justify-content: space-between; padding: 15px 0; border-bottom: 1px dashed #E5E7EB; }
        .product-item:last-child { border-bottom: none; padding-bottom: 0; }
        
        .product-info { display: flex; align-items: center; gap: 15px; flex: 2; }
        .product-img { width: 70px; height: 70px; border-radius: 10px; object-fit: cover; border: 1px solid #E5E7EB; }
        .product-name { font-weight: 600; color: var(--text-dark); margin-bottom: 5px; }
        .product-meta { font-size: 0.85rem; color: var(--text-muted); }
        
        .product-price-total { flex: 1; text-align: right; font-weight: 600; color: var(--text-dark); }

        .order-footer { padding: 15px 25px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
        .total-label { color: var(--text-muted); font-size: 0.9rem; margin-right: 10px; }
        .total-amount { font-size: 1.25rem; font-weight: 700; color: var(--secondary); }

        /* Badges */
        .badge-status { padding: 6px 16px; border-radius: var(--radius-pill); font-size: 0.8rem; font-weight: 600; }
        .bg-pending { background-color: #F3F4F6; color: #4B5563; }
        .bg-proses { background-color: #FEF3C7; color: #D97706; }
        .bg-dikirim { background-color: #DBEAFE; color: #1D4ED8; }
        .bg-selesai { background-color: #DCFCE7; color: #15803D; }
        .bg-batal { background-color: #FEE2E2; color: #B91C1C; }

        .empty-state { text-align: center; padding: 60px 20px; background: #fff; border-radius: var(--radius-lg); border: 1px dashed #ccc; }
        .empty-state i { font-size: 4rem; color: #E5E7EB; margin-bottom: 20px; }
    </style>
</head>
<body>

    <?php include '../navbar_login.php'; ?>
    <?php include '../components/user-sidebar.php'; ?>

    <main class="main-content">
        <header class="page-header">
            <h1 class="page-title">Daftar Pesanan</h1>
            <p class="text-muted">Lacak status pengiriman dan lihat riwayat belanja Anda.</p>
        </header>

        <div class="order-list">
            <?php if (mysqli_num_rows($result_orders) > 0): ?>
                
                <?php while ($order = mysqli_fetch_assoc($result_orders)): ?>
                    <?php 
                        // Atur Warna Status Sesuai ENUM Database
                        $badge_class = '';
                        $status_text = '';
                        $status_db = strtolower($order['status']);

                        if ($status_db == 'pending') { $badge_class = 'bg-pending'; $status_text = 'Menunggu Pembayaran'; }
                        elseif ($status_db == 'paid' || $status_db == 'processing') { $badge_class = 'bg-proses'; $status_text = 'Sedang Diproses'; }
                        elseif ($status_db == 'shipped') { $badge_class = 'bg-dikirim'; $status_text = 'Sedang Dikirim'; }
                        elseif ($status_db == 'completed') { $badge_class = 'bg-selesai'; $status_text = 'Selesai'; }
                        elseif ($status_db == 'cancelled') { $badge_class = 'bg-batal'; $status_text = 'Dibatalkan'; }
                        else { $badge_class = 'bg-batal'; $status_text = $order['status']; }

                        $order_id = $order['id'];
                    ?>
                    
                    <div class="order-card">
                        <div class="order-header">
                            <div class="d-flex gap-4">
                                <div class="order-header-info">
                                    <span>Tanggal Pembelian</span>
                                    <strong><?= tanggal_indo($order['created_at']) ?></strong>
                                </div>
                                <div class="order-header-info">
                                    <span>Nomor Pesanan</span>
                                    <strong><?= htmlspecialchars($order['order_code']) ?></strong>
                                </div>
                            </div>
                            <div>
                                <span class="badge-status <?= $badge_class ?>"><i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> <?= $status_text ?></span>
                            </div>
                        </div>

                        <div class="order-body">
                            <?php 
                            // Query disesuaikan dengan kolom order_details
                            // Menggunakan LEFT JOIN agar jika produk dihapus dari tabel products, data riwayat pesanan tetap utuh
                            $query_details = "SELECT od.*, p.image FROM order_details od LEFT JOIN products p ON od.product_id = p.id WHERE od.order_id = $order_id";
                            $result_details = mysqli_query($koneksi, $query_details);
                            
                            while ($item = mysqli_fetch_assoc($result_details)): 
                            ?>
                                <div class="product-item">
                                    <div class="product-info">
                                        <img src="../assets/images/products/<?= htmlspecialchars($item['image'] ?? 'default.png') ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="product-img" onerror="this.src='https://via.placeholder.com/70?text=No+Img'">
                                        <div>
                                            <div class="product-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                            <div class="product-meta">Rp <?= number_format($item['product_price'], 0, ',', '.') ?> x <?= $item['qty'] ?> barang</div>
                                        </div>
                                    </div>
                                    <div class="product-price-total">
                                        Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <div class="order-footer">
                            <a href="../contact.php" class="btn btn-light rounded-pill border fw-medium" style="font-size: 0.85rem;"><i class="fas fa-headset me-2"></i>Bantuan</a>
                            <div>
                                <span class="total-label">Total Belanja:</span>
                                <span class="total-amount">Rp <?= number_format($order['grand_total'], 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>

            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h4 class="fw-bold mb-2">Belum ada pesanan</h4>
                    <p class="text-muted mb-4">Anda belum melakukan transaksi apapun. Yuk, mulai belanja!</p>
                    <a href="../products.php" class="btn btn-dark rounded-pill px-4 py-2">Lihat Katalog Produk</a>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
