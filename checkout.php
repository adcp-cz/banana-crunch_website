<?php
session_start();
require 'database/koneksi.php';

// 1. WAJIB LOGIN
if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Silakan masuk terlebih dahulu untuk melakukan checkout.');
            window.location.href = 'login.php';
          </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// ===================================================
// FASE 1: MENAMPILKAN BARANG DARI CART
// ===================================================
$items_to_buy = [];
$total_belanja = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_items'])) {
    $selected_cart_ids = $_POST['selected_items'];
    
    foreach ($selected_cart_ids as $cart_id) {
        $cart_id = (int)$cart_id;
        $query = "SELECT c.id AS cart_id, c.qty, p.id AS product_id, p.name, p.price, p.image, p.stock 
                  FROM cart c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.id = $cart_id AND c.user_id = $user_id";
        $result = mysqli_query($koneksi, $query);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $items_to_buy[] = $row;
            $total_belanja += ($row['price'] * $row['qty']);
        }
    }
    
    $_SESSION['checkout_items'] = $items_to_buy;
    $_SESSION['total_belanja'] = $total_belanja;

} elseif (isset($_GET['action']) && $_GET['action'] == 'proses') {
    
    // ===================================================
    // FASE 2: MEMPROSES PEMBELIAN AKHIR (SESUAI DATABASE)
    // ===================================================
    if (!isset($_SESSION['checkout_items']) || empty($_SESSION['checkout_items'])) {
        header("Location: cart.php");
        exit();
    }

    $items_to_buy = $_SESSION['checkout_items'];
    $total_belanja = $_SESSION['total_belanja'];
    $shipping_cost = 0; // Set ke 0 karena di UI "Gratis Ongkir"
    $grand_total = $total_belanja + $shipping_cost;
    
    // Tangkap data dari form
    $shipping_name    = mysqli_real_escape_string($koneksi, $_POST['nama_penerima']);
    $shipping_phone   = mysqli_real_escape_string($koneksi, $_POST['telepon']);
    $shipping_address = mysqli_real_escape_string($koneksi, $_POST['alamat_lengkap']);
    $payment_method   = mysqli_real_escape_string($koneksi, $_POST['payment_method']);
    
    // Generate Order Code Otomatis
    $order_code = 'ORD-' . time();

    // Mulai Database Transaction
    mysqli_begin_transaction($koneksi);

    try {
        // A. INSERT KE TABEL orders
        // Karena kolom lain di database seperti city, province bernilai "Yes NULL", kita bisa abaikan dulu jika belum ada di form
        $query_order = "INSERT INTO orders (
                            order_code, user_id, total_price, shipping_cost, grand_total, 
                            shipping_name, shipping_phone, shipping_address, payment_method, status, created_at
                        ) VALUES (
                            '$order_code', $user_id, $total_belanja, $shipping_cost, $grand_total, 
                            '$shipping_name', '$shipping_phone', '$shipping_address', '$payment_method', 'pending', NOW()
                        )";
        
        if (!mysqli_query($koneksi, $query_order)) {
            throw new Exception("Gagal membuat pesanan: " . mysqli_error($koneksi));
        }
        
        $order_id = mysqli_insert_id($koneksi); 

        // B. INSERT KE TABEL order_details
        foreach ($items_to_buy as $item) {
            $product_id    = $item['product_id'];
            $qty_beli      = $item['qty'];
            $product_price = $item['price'];
            $cart_id       = $item['cart_id'];
            
            // Siapkan data spesifik untuk tabel order_details kamu
            $product_name  = mysqli_real_escape_string($koneksi, $item['name']);
            $subtotal      = $qty_beli * $product_price;

            // 1. Cek Stok
            $res_cek = mysqli_query($koneksi, "SELECT stock FROM products WHERE id = $product_id FOR UPDATE");
            $data_stok = mysqli_fetch_assoc($res_cek);
            
            if ($data_stok['stock'] < $qty_beli) {
                throw new Exception("Maaf, stok " . $item['name'] . " tidak mencukupi.");
            }

            // 2. Simpan Rincian Pesanan (MENYESUAIKAN KOLOM DATABASE BARU)
            $query_detail = "INSERT INTO order_details (
                                order_id, product_id, product_name, product_price, qty, subtotal, created_at
                             ) VALUES (
                                $order_id, $product_id, '$product_name', $product_price, $qty_beli, $subtotal, NOW()
                             )";
                             
            if (!mysqli_query($koneksi, $query_detail)) {
                throw new Exception("Gagal menyimpan rincian barang: " . mysqli_error($koneksi));
            }

            // 3. Potong Stok
            $query_update_stok = "UPDATE products SET stock = stock - $qty_beli WHERE id = $product_id";
            if (!mysqli_query($koneksi, $query_update_stok)) {
                throw new Exception("Gagal memotong stok gudang.");
            }

            // 4. Hapus dari Keranjang
            $query_hapus_cart = "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id";
            mysqli_query($koneksi, $query_hapus_cart);
        }

        // Commit transaksi jika sukses semua
        mysqli_commit($koneksi);
        
        unset($_SESSION['checkout_items']);
        unset($_SESSION['total_belanja']);

        echo "<script>
                alert('Checkout Berhasil! Pesanan Anda segera diproses.');
                window.location.href = 'user/pesanan.php'; 
              </script>";
        exit();

    } catch (Exception $e) {
        // Batalkan transaksi jika terjadi error
        mysqli_rollback($koneksi);
        $pesan_error = addslashes($e->getMessage());
        
        echo "<script>
                alert('Sistem mendeteksi kendala: " . $pesan_error . "');
                window.location.href = 'cart.php';
              </script>";
        exit();
    }
} else {
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Pembayaran | Naori Coffee</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #FFD600;
            --secondary: #8A6F00;
            --bg-light: #FAFAFA;
            --card-bg: #FFFFFF;
            --text-dark: #1F1F1F;
            --text-muted: #6c757d;
            --radius-card: 20px;
            --radius-btn: 30px;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            padding-top: 110px;
        }

        .checkout-card { background: var(--card-bg); border-radius: var(--radius-card); padding: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.03); border: none; margin-bottom: 25px; }
        .form-label { font-weight: 500; font-size: 0.9rem; margin-bottom: 8px; }
        .form-control, .form-select { border-radius: 12px; padding: 12px 18px; border: 1px solid #E5E7EB; font-size: 0.95rem; }
        .form-control:focus, .form-select:focus { box-shadow: 0 0 0 4px rgba(255, 214, 0, 0.15); border-color: var(--primary); }
        .product-list-item { display: flex; align-items: center; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #F3F4F6; }
        .product-list-item:last-child { border-bottom: none; padding-bottom: 0; }
        .btn-confirm { background-color: var(--primary); color: var(--text-dark); font-weight: 600; padding: 15px; border-radius: var(--radius-btn); border: none; width: 100%; transition: var(--transition); box-shadow: 0 4px 15px rgba(255, 214, 0, 0.3); font-size: 1rem; }
        .btn-confirm:hover { background-color: var(--secondary); color: #fff; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(138, 111, 0, 0.25); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top" style="padding: 15px 0; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
        <div class="container">
            <a class="navbar-brand fw-bold text-warning" href="index.php" style="color: var(--secondary) !important; font-size: 1.5rem;">Naori Coffee</a>
            <span class="navbar-text fw-medium text-dark"><i class="fas fa-lock text-success me-2"></i> Secure Checkout</span>
        </div>
    </nav>

    <div class="container mb-5">
        <h2 class="fw-bold mb-4">Informasi Pengiriman</h2>
        
        <form action="checkout.php?action=proses" method="POST">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="checkout-card">
                        <h5 class="fw-bold mb-4 text-secondary"><i class="fas fa-map-marker-alt me-2"></i>Alamat Tujuan</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap Penerima</label>
                            <input type="text" class="form-control" name="nama_penerima" placeholder="Contoh: Andhika Purnama" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon / WhatsApp</label>
                            <input type="tel" class="form-control" name="telepon" placeholder="Contoh: 08123588XXXX" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap Rumah</label>
                            <textarea class="form-control" name="alamat_lengkap" rows="4" placeholder="Tuliskan nama jalan, nomor rumah, RT/RW, Kecamatan, Kota dan Provinsi" style="resize:none;" required></textarea>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Metode Pembayaran</label>
                            <select class="form-select" name="payment_method" required>
                                <option value="COD">Bayar di Tempat / COD</option>
                                <option value="Transfer Bank">Transfer Bank (Verifikasi Otomatis)</option>
                                <option value="E-Wallet">E-Wallet (Dana / OVO / ShopeePay)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="checkout-card">
                        <h5 class="fw-bold mb-3 text-secondary"><i class="fas fa-shopping-basket me-2"></i>Rincian Item</h5>
                        
                        <div class="mb-4">
                            <?php foreach ($items_to_buy as $item): ?>
                                <div class="product-list-item">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/products/<?= htmlspecialchars($item['image']) ?>" style="width:55px; height:55px; object-fit:cover; border-radius:10px; margin-right:15px;" onerror="this.src='https://via.placeholder.com/55?text=No+Img'">
                                        <div>
                                            <h6 class="fw-bold mb-1" style="font-size:0.9rem;"><?= htmlspecialchars($item['name']) ?></h6>
                                            <small class="text-muted">Rp <?= number_format($item['price'], 0, ',', '.') ?> x <?= $item['qty'] ?></small>
                                        </div>
                                    </div>
                                    <div class="fw-bold" style="font-size:0.95rem;">Rp <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <h5 class="fw-bold mb-3 text-secondary">Total Pembayaran</h5>
                        <div class="d-flex justify-content-between mb-2 text-muted" style="font-size: 0.95rem;">
                            <span>Subtotal Produk</span>
                            <span>Rp <?= number_format($total_belanja, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-muted" style="font-size: 0.95rem;">
                            <span>Biaya Pengiriman</span>
                            <span class="text-success fw-semibold">Gratis Ongkir</span>
                        </div>
                        <hr style="border-top: 1px dashed #ccc;">
                        <div class="d-flex justify-content-between mb-4 fw-bold text-danger fs-5">
                            <span>Total Tagihan</span>
                            <span>Rp <?= number_format($total_belanja, 0, ',', '.') ?></span>
                        </div>
                        
                        <button type="submit" class="btn-confirm">Konfirmasi & Selesaikan Pembelian</button>
                        <a href="cart.php" class="btn btn-light w-100 rounded-pill mt-2 text-muted border fw-medium py-2" style="font-size: 0.9rem;">Kembali ke Keranjang</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>