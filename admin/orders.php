<?php
session_start();
// Hubungkan ke database
require '../database/koneksi.php';

// 1. PROTEKSI HALAMAN ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

// Ambil Nama Admin untuk Sidebar
$query_admin = mysqli_query($koneksi, "SELECT name FROM users WHERE id = $admin_id");
$admin_data = mysqli_fetch_assoc($query_admin);
$admin_name = $admin_data['name'] ?? 'Admin';

// Ambil data pesanan beserta nama pelanggan dari tabel users menggunakan JOIN
$query = "SELECT o.*, u.name AS customer_name 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          ORDER BY o.created_at DESC";
$result = mysqli_query($koneksi, $query);

$orders = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pesanan | Admin Panel</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- DESIGN SYSTEM REUSE --- */
        :root {
            --primary: #FFD600;
            --secondary: #8A6F00;
            --bg-main: #FAFAFA;
            --bg-sidebar: #FFFFFF;
            --card-bg: #FFFFFF;
            --text-dark: #1F1F1F;
            --text-muted: #8E8E93;
            --radius-lg: 20px;
            --radius-md: 12px;
            --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.03);
            --sidebar-width: 280px;
            --transition: all 0.3s ease;
        }

        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-main); color: var(--text-dark); overflow-x: hidden; }

        /* --- SIDEBAR STYLING --- */
        .sidebar {
            width: var(--sidebar-width);
            height: calc(100vh - 80px);
            position: fixed;
            top: 80px;
            left: 0;
            background-color: var(--bg-sidebar);
            border-right: 1px solid #EFEFEF;
            padding: 30px 24px;
            display: flex;
            flex-direction: column;
            z-index: 100;
            overflow-y: auto;
        }

        .admin-profile { display: flex; align-items: center; gap: 15px; margin-bottom: 40px; }
        .admin-avatar { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); }
        .admin-info h6 { font-size: 1rem; font-weight: 700; margin: 0; color: var(--text-dark); }
        .admin-info small { font-size: 0.8rem; color: var(--text-muted); display: block; }

        .sidebar-menu { list-style: none; padding: 0; margin: 0; flex-grow: 1; }
        .menu-link { display: flex; align-items: center; gap: 15px; padding: 14px 20px; color: var(--text-dark); font-weight: 500; font-size: 0.95rem; border-radius: var(--radius-md); text-decoration: none; transition: var(--transition); margin-bottom: 8px; }
        .menu-link i { font-size: 1.1rem; width: 20px; text-align: center; color: var(--text-dark); }
        .menu-link.active { background-color: var(--primary); font-weight: 600; }
        .menu-link:hover:not(.active) { background-color: #F5F5F7; }

        .logout-link { color: #DC3545; font-weight: 600; display: flex; align-items: center; gap: 12px; padding: 12px 20px; text-decoration: none; transition: var(--transition); border-radius: var(--radius-md); margin-top: auto; }
        .logout-link:hover { background-color: #FFF5F5; }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: var(--sidebar-width); padding: 40px; margin-top: 80px; min-height: calc(100vh - 80px); }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .table-card { background-color: var(--card-bg); border-radius: var(--radius-lg); padding: 25px; box-shadow: var(--shadow-soft); border: 1px solid #EFEFEF; }
        
        .custom-table thead th { background-color: #F9FAFB; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; padding: 15px 20px; border-bottom: none; letter-spacing: 0.5px; }
        .custom-table tbody td { padding: 18px 20px; vertical-align: middle; border-bottom: 1px solid #F2F2F7; font-size: 0.9rem; }

        /* --- BADGES STATUS --- */
        .status-badge { padding: 6px 14px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; }
        .bg-pending { background-color: #F3F4F6; color: #4B5563; }
        .bg-proses { background-color: #FEF3C7; color: #D97706; }
        .bg-dikirim { background-color: #DBEAFE; color: #1D4ED8; }
        .bg-selesai { background-color: #DCFCE7; color: #15803D; }
        .bg-batal { background-color: #FEE2E2; color: #B91C1C; }

        .btn-action { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #E5E7EB; background: #fff; color: var(--text-dark); transition: var(--transition); text-decoration: none; }
        .btn-action:hover { background-color: var(--primary); border-color: var(--primary); color: var(--text-dark); }

        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); transition: var(--transition); }
            .main-content { margin-left: 0; padding: 24px; }
        }
    </style>
</head>
<body>

    <?php include '../navbar_login.php'; ?>

    <aside class="sidebar">
        <div>
            <div class="admin-profile">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=200&auto=format&fit=crop" class="admin-avatar" alt="Admin">
                <div class="admin-info">
                    <h6><?= htmlspecialchars($admin_name) ?></h6>
                    <small>Naori Coffee Mgmt</small>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="menu-link"><i class="fas fa-th-large"></i> Ringkasan</a></li>
                <li><a href="products.php" class="menu-link"><i class="fas fa-box"></i> Produk</a></li>
                <li><a href="orders.php" class="menu-link active"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
                <li><a href="users.php" class="menu-link"><i class="fas fa-users"></i> Pengguna</a></li>
            </ul>
        </div>
        <a href="../login.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Keluar</a>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div>
                <h2 class="fw-bold mb-1">Daftar Pesanan</h2>
                <p class="text-muted small mb-0">Pantau dan kelola status transaksi pelanggan Anda.</p>
            </div>
        </header>

        <div class="table-card">
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>Nomor Order</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Total Tagihan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orders) > 0): ?>
                            <?php foreach ($orders as $row): ?>
                                <?php 
                                    // Tentukan warna badge berdasarkan status ENUM Database
                                    $status_db = strtolower($row['status']);
                                    $badge_class = '';
                                    $status_text = '';

                                    if ($status_db == 'pending') { $badge_class = 'bg-pending'; $status_text = 'Pending'; }
                                    elseif ($status_db == 'paid') { $badge_class = 'bg-proses'; $status_text = 'Dibayar'; }
                                    elseif ($status_db == 'processing') { $badge_class = 'bg-proses'; $status_text = 'Diproses'; }
                                    elseif ($status_db == 'shipped') { $badge_class = 'bg-dikirim'; $status_text = 'Dikirim'; }
                                    elseif ($status_db == 'completed') { $badge_class = 'bg-selesai'; $status_text = 'Selesai'; }
                                    elseif ($status_db == 'cancelled') { $badge_class = 'bg-batal'; $status_text = 'Dibatalkan'; }
                                    else { $badge_class = 'bg-batal'; $status_text = $row['status']; }
                                ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['order_code']) ?></td>
                                    <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <span class="fw-semibold d-block"><?= htmlspecialchars($row['customer_name']) ?></span>
                                        <small class="text-muted"><?= htmlspecialchars($row['shipping_phone']) ?></small>
                                    </td>
                                    <td class="fw-bold">Rp <?= number_format($row['grand_total'], 0, ',', '.') ?></td>
                                    <td><span class="status-badge <?= $badge_class ?>"><?= $status_text ?></span></td>
                                    <td>
                                        <button class="btn-action" data-bs-toggle="modal" data-bs-target="#modalStatus<?= $row['id'] ?>" title="Ubah Status">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Belum ada pesanan yang masuk.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php foreach ($orders as $row): ?>
        <div class="modal fade" id="modalStatus<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 20px; border: none; padding: 10px;">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="fw-bold">Update Status Pesanan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="proses_status.php" method="POST">
                        <div class="modal-body pt-3">
                            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                            
                            <p class="text-muted mb-4" style="font-size: 0.95rem;">Ubah status untuk Order <strong><?= htmlspecialchars($row['order_code']) ?></strong> atas nama <strong><?= htmlspecialchars($row['customer_name']) ?></strong>.</p>

                            <div class="mb-3">
                                <label class="form-label fw-medium mb-2">Pilih Status Baru</label>
                                <select class="form-select bg-light" name="status" style="border-radius: 12px; padding: 12px; border-color: #E5E7EB;" required>
                                    <option value="pending" <?= (strtolower($row['status']) == 'pending') ? 'selected' : '' ?>>Menunggu Pembayaran (Pending)</option>
                                    <option value="paid" <?= (strtolower($row['status']) == 'paid') ? 'selected' : '' ?>>Sudah Dibayar (Paid)</option>
                                    <option value="processing" <?= (strtolower($row['status']) == 'processing') ? 'selected' : '' ?>>Sedang Disiapkan (Processing)</option>
                                    <option value="shipped" <?= (strtolower($row['status']) == 'shipped') ? 'selected' : '' ?>>Dalam Perjalanan (Shipped)</option>
                                    <option value="completed" <?= (strtolower($row['status']) == 'completed') ? 'selected' : '' ?>>Selesai Diterima (Completed)</option>
                                    <option value="cancelled" <?= (strtolower($row['status']) == 'cancelled') ? 'selected' : '' ?>>Batalkan Pesanan (Cancelled)</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-dark rounded-pill px-4">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>