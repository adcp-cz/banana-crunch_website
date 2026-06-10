<?php
session_start();
// Hubungkan ke database
require 'database/koneksi.php';

// 1. WAJIB LOGIN
if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Silakan masuk terlebih dahulu untuk melihat keranjang Anda.');
            window.location.href = 'login.php';
          </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. LOGIKA HAPUS ITEM 
if (isset($_GET['del'])) {
    $id_hapus = (int) $_GET['del'];
    $query_hapus = "DELETE FROM cart WHERE id = $id_hapus AND user_id = $user_id";
    mysqli_query($koneksi, $query_hapus);
    
    header("Location: cart.php"); 
    exit();
}

// 3. AMBIL DATA KERANJANG
$query = "SELECT c.id AS cart_id, c.qty, p.id AS product_id, p.name, p.price, p.image, p.stock 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = $user_id
          ORDER BY c.id DESC";

$result = mysqli_query($koneksi, $query);
$jumlah_item = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja | PisangKraf</title>
    
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

        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-light); color: var(--text-dark); }
        a { text-decoration: none; }

        .navbar-custom { padding: 15px 0; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
        .navbar-brand { font-weight: 700; font-size: 1.5rem; color: var(--secondary) !important; }
        .nav-link { font-weight: 500; color: var(--text-dark) !important; margin: 0 10px; transition: var(--transition); }
        .nav-link:hover { color: var(--secondary) !important; }
        .nav-icons a { color: var(--text-dark); margin-left: 20px; font-size: 1.2rem; transition: var(--transition); }
        .nav-icons a:hover { color: var(--secondary); }
        .cart-badge { background-color: var(--secondary); font-size: 0.6rem; transform: translate(-10px, -10px); }

        .cart-section { padding-top: 120px; padding-bottom: 60px; min-height: 80vh; }
        .cart-card { background-color: var(--card-bg); border-radius: var(--radius-card); border: none; padding: 25px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03); margin-bottom: 20px; }
        
        .cart-item { display: flex; align-items: center; padding: 20px 0; border-bottom: 1px solid #F3F4F6; }
        .cart-item:last-child { border-bottom: none; padding-bottom: 0; }
        
        /* Styling khusus untuk Checkbox kustom */
        .custom-checkbox { width: 22px; height: 22px; cursor: pointer; accent-color: var(--secondary); }
        
        .item-img { width: 90px; height: 90px; object-fit: cover; border-radius: 12px; margin: 0 20px; }
        .item-details { flex-grow: 1; }
        .item-title { font-weight: 600; font-size: 1.1rem; color: var(--text-dark); margin-bottom: 5px; display: inline-block;}
        .item-price { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px; }
        .item-actions { display: flex; align-items: center; gap: 15px; }
        
        .btn-delete-item { color: #EF4444; background: #FEE2E2; border: none; width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: var(--transition); }
        .btn-delete-item:hover { background: #EF4444; color: #fff; }

        .summary-card { background-color: var(--card-bg); border-radius: var(--radius-card); padding: 25px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05); border: 2px solid var(--primary); }
        .summary-title { font-weight: 700; font-size: 1.2rem; margin-bottom: 20px; border-bottom: 1px solid #E5E7EB; padding-bottom: 15px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 0.95rem; color: var(--text-muted); }
        .summary-total { display: flex; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px dashed #E5E7EB; font-weight: 700; font-size: 1.2rem; color: var(--secondary); }

        .btn-checkout { background-color: var(--primary); color: var(--text-dark); font-weight: 600; padding: 14px; border-radius: var(--radius-btn); border: none; width: 100%; transition: var(--transition); margin-top: 25px; box-shadow: 0 4px 15px rgba(255, 214, 0, 0.3); }
        .btn-checkout:hover { background-color: var(--secondary); color: #fff; transform: translateY(-2px); }
        .btn-checkout:disabled { background-color: #E5E7EB; color: #9CA3AF; cursor: not-allowed; box-shadow: none; transform: none; }

        .empty-cart-state { text-align: center; padding: 60px 20px; }
        .empty-cart-icon { font-size: 5rem; color: #E5E7EB; margin-bottom: 20px; }
    </style>
</head>
<body>

    <?php require_once __DIR__ . '/components/navbar.php';?>

    <style>
        /* --- NAVBAR --- */
        .navbar-custom {
            padding: 15px 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--secondary) !important;
        }
        .nav-link {
            font-weight: 500;
            color: var(--text-dark) !important;
            margin: 0 10px;
            transition: var(--transition);
        }
        .nav-link:hover, .nav-link.active { color: var(--secondary) !important; }
        .nav-icons a {
            color: var(--text-dark);
            margin-left: 20px;
            font-size: 1.2rem;
            transition: var(--transition);
        }
        .nav-icons a:hover { color: var(--secondary); }
        .cart-badge {
            background-color: var(--secondary);
            font-size: 0.6rem;
            transform: translate(-10px, -10px);
        }
    </style>

    <section class="cart-section">
        <div class="container">
            <h2 class="fw-bold mb-4">Keranjang Belanja</h2>

            <form action="checkout.php" method="POST" id="form-keranjang">
                <div class="row g-4">
                    
                    <div class="col-lg-8">
                        <div class="cart-card">
                            <?php 
                            if ($jumlah_item > 0) {
                                while ($item = mysqli_fetch_assoc($result)) {
                                    $subtotal = $item['price'] * $item['qty'];
                                    $image_path = 'assets/images/products/' . htmlspecialchars($item['image']);
                            ?>
                                <div class="cart-item">
                                    <div class="form-check">
                                        <input class="form-check-input custom-checkbox item-checkbox" 
                                               type="checkbox" 
                                               name="selected_items[]" 
                                               value="<?= $item['cart_id'] ?>" 
                                               data-subtotal="<?= $subtotal ?>" 
                                               checked>
                                    </div>

                                    <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="item-img" onerror="this.onerror=null; this.src='https://via.placeholder.com/100?text=No+Img'">
                                    
                                    <div class="item-details">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <a href="product-detail.php?id=<?= $item['product_id'] ?>" class="item-title"><?= htmlspecialchars($item['name']) ?></a>
                                                <div class="item-price">Rp <?= number_format($item['price'], 0, ',', '.') ?> <span class="text-muted">x <?= $item['qty'] ?></span></div>
                                                
                                                <?php if($item['qty'] > $item['stock']): ?>
                                                    <small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Stok tersisa hanya <?= $item['stock'] ?>.</small>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="fw-bold fs-6">Rp <?= number_format($subtotal, 0, ',', '.') ?></div>
                                        </div>

                                        <div class="item-actions mt-2">
                                            <div class="badge bg-light text-dark border">Qty: <?= $item['qty'] ?></div>
                                            <a href="cart.php?del=<?= $item['cart_id'] ?>" class="btn-delete-item" onclick="return confirm('Hapus produk ini dari keranjang?');" title="Hapus Item">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                }
                            } else {
                            ?>
                                <div class="empty-cart-state">
                                    <i class="fas fa-shopping-basket empty-cart-icon"></i>
                                    <h4 class="fw-bold mb-3">Keranjang Anda Kosong</h4>
                                    <p class="text-muted mb-4">Sepertinya Anda belum menambahkan camilan pisang favorit Anda.</p>
                                    <a href="products.php" class="btn btn-warning fw-bold px-4 rounded-pill">Mulai Belanja</a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <?php if ($jumlah_item > 0): ?>
                    <div class="col-lg-4">
                        <div class="summary-card sticky-top" style="top: 100px;">
                            <h4 class="summary-title">Ringkasan Pesanan</h4>
                            
                            <div class="summary-row">
                                <span id="label-total-barang">Total Harga (0 Barang)</span>
                                <span class="text-dark fw-medium" id="teks-total-harga">Rp 0</span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Biaya Pengiriman</span>
                                <span class="text-success fw-medium">Dihitung di Checkout</span>
                            </div>

                            <div class="summary-total">
                                <span>Total Belanja</span>
                                <span id="teks-total-belanja">Rp 0</span>
                            </div>

                            <button type="submit" class="btn btn-checkout text-center" id="btn-checkout">
                                Beli Sekarang <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </form>
        </div>
    </section>

    <footer>
        <div class="container text-center">
            <div class="footer-logo" style="font-size: 1.5rem; font-weight: 700; color: var(--secondary); margin-bottom: 10px;">PisangKraf</div>
            <p class="text-muted fs-6 mb-3">Kelezatan olahan pisang nusantara kualitas premium.</p>
            <div class="text-muted" style="font-size: 0.85rem;">&copy; 2026 PisangKraf. All rights reserved.</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil semua elemen checkbox yang ada di keranjang
            const checkboxes = document.querySelectorAll('.item-checkbox');
            const teksTotalHarga = document.getElementById('teks-total-harga');
            const teksTotalBelanja = document.getElementById('teks-total-belanja');
            const labelTotalBarang = document.getElementById('label-total-barang');
            const btnCheckout = document.getElementById('btn-checkout');

            // Fungsi untuk mengkalkulasi ulang total berdasarkan checkbox yang dicentang
            function kalkulasiTotal() {
                let totalSistem = 0;
                let jumlahBarangDicentang = 0;

                checkboxes.forEach(function(cb) {
                    if (cb.checked) {
                        // Ambil harga dari atribut data-subtotal
                        totalSistem += parseInt(cb.getAttribute('data-subtotal'));
                        jumlahBarangDicentang++;
                    }
                });

                // Format angka menjadi standar Rupiah (Contoh: 15000 -> 15.000)
                let formatRupiah = new Intl.NumberFormat('id-ID').format(totalSistem);

                // Update tulisan di layar
                teksTotalHarga.innerText = 'Rp ' + formatRupiah;
                teksTotalBelanja.innerText = 'Rp ' + formatRupiah;
                labelTotalBarang.innerText = 'Total Harga (' + jumlahBarangDicentang + ' Barang)';

                // Jika tidak ada barang yang dicentang, matikan tombol "Beli Sekarang"
                if (jumlahBarangDicentang === 0) {
                    btnCheckout.disabled = true;
                } else {
                    btnCheckout.disabled = false;
                }
            }

            // Pasang 'pendengar' kejadian ke semua checkbox. Jika di-klik, jalankan fungsi kalkulasiTotal
            checkboxes.forEach(function(cb) {
                cb.addEventListener('change', kalkulasiTotal);
            });

            // Jalankan kalkulasi satu kali saat halaman pertama kali dimuat
            kalkulasiTotal();
        });
    </script>
</body>
</html>