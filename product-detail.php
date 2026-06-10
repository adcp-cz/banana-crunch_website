<?php
session_start();
// Hubungkan ke database
require 'database/koneksi.php';

// 1. Cek apakah ada parameter 'id' di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Jika tidak ada ID, kembalikan user ke halaman produk
    header("Location: products.php");
    exit;
}

// 2. Ambil ID dan pastikan formatnya angka (integer) untuk mencegah error/hack
$id = (int)$_GET['id'];

// 3. Query ke database untuk mencari produk spesifik berdasarkan ID tersebut
$query = "SELECT * FROM products WHERE id = $id AND is_active = 1";
$result = mysqli_query($koneksi, $query);

// 4. Cek apakah produk ditemukan di database
if (mysqli_num_rows($result) > 0) {
    // Ekstrak datanya ke dalam variabel $product
    $product = mysqli_fetch_assoc($result);
} else {
    // Jika ID diketik manual ngawur di URL dan tidak ada di database
    echo "<script>
            alert('Maaf, detail produk tidak ditemukan atau produk sudah tidak aktif!');
            window.location.href = 'products.php';
          </script>";
    exit;
}

// Tentukan Path Gambar
$image_path = 'assets/images/products/' . htmlspecialchars($product['image']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> | PisangKraf</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- DESIGN SYSTEM REUSE --- */
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
        }

        a { text-decoration: none; }

        

        /* --- PRODUCT DETAIL LAYOUT --- */
        .detail-section {
            padding-top: 120px;
            padding-bottom: 60px;
            min-height: 80vh;
        }

        .img-showcase {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: var(--radius-card);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .category-badge {
            background-color: rgba(255, 214, 0, 0.2);
            color: var(--secondary);
            font-weight: 600;
            font-size: 0.85rem;
            padding: 6px 16px;
            border-radius: var(--radius-btn);
            display: inline-block;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .product-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .product-price {
            font-size: 1.8rem;
            color: var(--secondary);
            font-weight: 700;
            margin-bottom: 25px;
        }

        .product-desc {
            font-size: 1.05rem;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .meta-info-box {
            background-color: var(--card-bg);
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 30px;
            display: flex;
            gap: 30px;
        }
        .meta-item span { display: block; }
        .meta-label { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;}
        .meta-value { font-weight: 600; font-size: 1rem; color: var(--text-dark); }

        /* --- ADD TO CART FORM --- */
        .quantity-wrapper {
            display: flex;
            align-items: center;
            border: 1px solid #E5E7EB;
            border-radius: var(--radius-btn);
            overflow: hidden;
            width: 140px;
        }
        .qty-btn {
            background: #fff;
            border: none;
            padding: 10px 15px;
            font-weight: 600;
            color: var(--text-dark);
            transition: var(--transition);
        }
        .qty-btn:hover { background: #F3F4F6; }
        .qty-input {
            width: 50px;
            text-align: center;
            border: none;
            font-weight: 600;
            outline: none;
            pointer-events: none; /* Cegah ketik manual sementara untuk UI sederhana */
        }

        .btn-add-cart {
            background-color: var(--primary);
            color: var(--text-dark);
            font-weight: 600;
            padding: 14px 30px;
            border-radius: var(--radius-btn);
            border: none;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(255, 214, 0, 0.3);
            flex-grow: 1;
        }
        .btn-add-cart:hover {
            background-color: var(--secondary);
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-add-cart:disabled {
            background-color: #E5E7EB;
            color: #9CA3AF;
            box-shadow: none;
            cursor: not-allowed;
            transform: none;
        }

        /* --- FOOTER --- */
        footer { background-color: #EFEFEF; padding: 60px 0 30px; margin-top: 50px; }
        .footer-logo { font-size: 1.5rem; font-weight: 700; color: var(--secondary); margin-bottom: 15px; }
        .footer-links a { color: var(--text-muted); display: block; margin-bottom: 10px; transition: var(--transition); }
        .footer-links a:hover { color: var(--secondary); }
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

    <section class="detail-section">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php" class="text-muted">Home</a></li>
                    <li class="breadcrumb-item"><a href="products.php" class="text-muted">Katalog</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
                </ol>
            </nav>

            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-showcase" onerror="this.onerror=null; this.src='https://via.placeholder.com/600x600?text=No+Image'">
                </div>

                <div class="col-lg-6">
                    <div class="category-badge"><?= htmlspecialchars($product['category']) ?></div>
                    <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
                    
                    <div class="product-price">Rp <?= number_format($product['price'], 0, ',', '.') ?></div>
                    
                    <p class="product-desc"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

                    <div class="meta-info-box">
                        <div class="meta-item">
                            <span class="meta-label">Berat Bersih</span>
                            <span class="meta-value"><?= htmlspecialchars($product['weight']) ?> Gram</span>
                        </div>
                        <div class="meta-item" style="border-left: 1px solid #E5E7EB; padding-left: 20px;">
                            <span class="meta-label">Ketersediaan</span>
                            <?php if ($product['stock'] > 0): ?>
                                <span class="meta-value text-success"><i class="fas fa-check-circle me-1"></i> Tersedia (<?= $product['stock'] ?>)</span>
                            <?php else: ?>
                                <span class="meta-value text-danger"><i class="fas fa-times-circle me-1"></i> Habis</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <form action="proses_keranjang.php" method="POST" class="d-flex gap-3">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        
                        <div class="quantity-wrapper">
                            <button type="button" class="qty-btn" id="btn-minus"><i class="fas fa-minus"></i></button>
                            <input type="number" id="qty-input" name="quantity" class="qty-input" value="1" min="1" max="<?= $product['stock'] ?>" readonly>
                            <button type="button" class="qty-btn" id="btn-plus"><i class="fas fa-plus"></i></button>
                        </div>

                        <button type="submit" class="btn-add-cart" <?= ($product['stock'] <= 0) ? 'disabled' : '' ?>>
                            <i class="fas fa-shopping-cart me-2"></i> Tambah ke Keranjang
                        </button>
                    </form>
                    <?php if ($product['stock'] <= 0): ?>
                        <small class="text-danger mt-2 d-block">Maaf, produk ini sedang tidak dapat dipesan.</small>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container text-center">
            <div class="footer-logo">PisangKraf</div>
            <p class="text-muted fs-6 mb-4">Kelezatan olahan pisang nusantara kualitas premium.</p>
            <div class="text-muted" style="font-size: 0.85rem;">&copy; 2026 PisangKraf. All rights reserved.</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const btnMinus = document.getElementById('btn-minus');
        const btnPlus = document.getElementById('btn-plus');
        const qtyInput = document.getElementById('qty-input');
        const maxStock = parseInt(qtyInput.getAttribute('max'));

        btnPlus.addEventListener('click', () => {
            let currentVal = parseInt(qtyInput.value);
            if (currentVal < maxStock) {
                qtyInput.value = currentVal + 1;
            }
        });

        btnMinus.addEventListener('click', () => {
            let currentVal = parseInt(qtyInput.value);
            if (currentVal > 1) {
                qtyInput.value = currentVal - 1;
            }
        });
    </script>
</body>
</html>