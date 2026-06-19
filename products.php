<?php
session_start();
// Hubungkan ke database
require 'database/koneksi.php';

// Menangkap filter kategori jika ada yang diklik (menggunakan metode GET)
$kategori_dipilih = isset($_GET['category']) ? mysqli_real_escape_string($koneksi, $_GET['category']) : '';

// Menyusun query MySQL berdasarkan filter kategori
if (!empty($kategori_dipilih)) {
    $query = "SELECT * FROM products WHERE is_active = 1 AND category = '$kategori_dipilih' ORDER BY id DESC";
} else {
    $query = "SELECT * FROM products WHERE is_active = 1 ORDER BY id DESC";
}

$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk | PisangKraf</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

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
            --shadow-soft: 0 10px 40px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 15px 50px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        a { text-decoration: none; }

    

        /* --- HEADER PAGE --- */
        .page-header-banner {
            padding: 140px 0 60px;
            background: linear-gradient(to bottom, #FFFDEB, var(--bg-light));
            text-center: center;
        }

        /* --- FILTER BUTTONS --- */
        .filter-btn {
            background-color: #FFFFFF;
            color: var(--text-dark);
            border: 1px solid #E5E7EB;
            padding: 10px 24px;
            border-radius: var(--radius-btn);
            font-weight: 500;
            font-size: 0.9rem;
            transition: var(--transition);
            display: inline-block;
            margin: 5px;
        }
        .filter-btn:hover, .filter-btn.active {
            background-color: var(--primary);
            border-color: var(--primary);
            color: var(--text-dark);
            box-shadow: 0 4px 15px rgba(255, 214, 0, 0.2);
        }

        /* --- PRODUCT CARDS --- */
        .premium-card {
            background: var(--card-bg);
            border-radius: var(--radius-card);
            border: none;
            padding: 20px;
            box-shadow: var(--shadow-soft);
            transition: var(--transition);
            height: 100%;
        }
        .premium-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }
        .product-img-wrapper {
            position: relative;
            border-radius: calc(var(--radius-card) - 5px);
            overflow: hidden;
            margin-bottom: 20px;
        }
        .product-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: var(--transition);
        }
        .premium-card:hover .product-img { transform: scale(1.05); }
        
        .price-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--card-bg);
            padding: 6px 15px;
            border-radius: var(--radius-btn);
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            z-index: 2;
            color: var(--secondary);
        }
        .category-tag {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-muted);
            font-weight: 600;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 5px;
        }

        .btn-outline-custom {
            border: 1.5px solid var(--text-dark);
            color: var(--text-dark);
            font-weight: 600;
            padding: 10px 24px;
            border-radius: var(--radius-btn);
            background: transparent;
            transition: var(--transition);
            display: block;
            text-align: center;
            font-size: 0.9rem;
        }
        .btn-outline-custom:hover {
            background-color: var(--text-dark);
            color: #fff;
        }

        /* --- FOOTER --- */
        footer {
            background-color: #EFEFEF;
            padding: 60px 0 30px;
            margin-top: 80px;
        }
        .footer-logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 15px;
        }
        .footer-links a {
            color: var(--text-muted);
            display: block;
            margin-bottom: 10px;
            transition: var(--transition);
        }
        .footer-links a:hover { color: var(--secondary); }

        /* --- RESPONSIVE MEDIA QUERIES --- */
        @media (max-width: 991.98px) {
            .page-header-banner { padding: 100px 0 40px; }
            .page-header-banner h1 { font-size: 2.2rem; }
        }

        @media (max-width: 767.98px) {
            .filter-container { 
                display: flex; 
                overflow-x: auto; 
                padding: 10px 0; 
                margin-bottom: 20px !important;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }
            .filter-container::-webkit-scrollbar { display: none; }
            .filter-btn { flex: 0 0 auto; white-space: nowrap; padding: 8px 18px; font-size: 0.85rem; }
            
            .page-header-banner { padding: 80px 0 30px; }
            .page-header-banner h1 { font-size: 1.8rem; }
            .page-header-banner p { font-size: 0.9rem; }
        }
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

    

    <header class="page-header-banner text-center" data-aos="fade-down">
        <div class="container">
            <h1 class="fw-bold mb-2">Semua Produk Kami</h1>
            <p class="text-muted">Jelajahi kelezatan berbagai varian keripik pisang pilihan terbaik kami.</p>
        </div>
    </header>

    <section class="py-4">
        <div class="container">
            
            <div class="text-center mb-5 filter-container" data-aos="fade-up">
                <a href="products.php" class="filter-btn <?= empty($kategori_dipilih) ? 'active' : '' ?>">Semua</a>
                <a href="products.php?category=Original" class="filter-btn <?= ($kategori_dipilih == 'Original') ? 'active' : '' ?>">Original</a>
                <a href="products.php?category=Pedas" class="filter-btn <?= ($kategori_dipilih == 'Pedas') ? 'active' : '' ?>">Pedas</a>
                <a href="products.php?category=Manis" class="filter-btn <?= ($kategori_dipilih == 'Manis') ? 'active' : '' ?>">Manis</a>
                <a href="products.php?category=Gurih" class="filter-btn <?= ($kategori_dipilih == 'Gurih') ? 'active' : '' ?>">Gurih</a>
                <a href="products.php?category=Paket" class="filter-btn <?= ($kategori_dipilih == 'Paket') ? 'active' : '' ?>">Paket</a>
            </div>

            <div class="row g-4">
                <?php 
                if (mysqli_num_rows($result) > 0) {
                    $delay = 100;
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Jalur gambar produk disesuaikan
                        $image_path = 'assets/images/products/' . htmlspecialchars($row['image']);
                ?>
                <div class="col-xl-3 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                    <div class="premium-card d-flex flex-column">
                        <div class="product-img-wrapper">
                            <span class="price-badge">Rp <?= number_format($row['price'], 0, ',', '.') ?></span>
                            <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="product-img" onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=No+Image'">
                        </div>
                        <div class="mt-auto">
                            <span class="category-tag"><?= htmlspecialchars($row['category']) ?></span>
                            <h4 class="fw-bold fs-5 mb-3 text-truncate" title="<?= htmlspecialchars($row['name']) ?>"><?= htmlspecialchars($row['name']) ?></h4>
                            
                            <a href="product-detail.php?id=<?= $row['id'] ?>" class="btn-outline-custom w-100">Detail Produk</a>
                        </div>
                    </div>
                </div>
                <?php 
                        $delay += 50; // Menaikkan delay AOS secara bertahap agar animasinya cantik
                    }
                } else { 
                ?>
                <div class="col-12 text-center py-5" data-aos="fade-up">
                    <div class="text-muted fs-5 py-4">
                        <i class="fas fa-box-open fa-2x mb-3 d-block text-secondary"></i>
                        Maaf, produk untuk kategori "<b><?= htmlspecialchars($kategori_dipilih) ?></b>" belum tersedia saat ini.
                    </div>
                </div>
                <?php } ?>
            </div>

        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-md-5 mb-4 mb-md-0">
                    <div class="footer-logo">PisangKraf</div>
                    <p class="text-muted fs-6 pe-md-5">Menghadirkan kelezatan olahan pisang nusantara dengan sentuhan modern dan kualitas premium.</p>
                </div>
                <div class="col-md-2 footer-links">
                    <h6 class="fw-bold mb-3">Tautan</h6>
                    <a href="index.php">Home</a>
                    <a href="products.php">Products</a>
                </div>
                <div class="col-md-3 footer-links">
                    <h6 class="fw-bold mb-3">Bantuan</h6>
                    <a href="#">Cara Pesan</a>
                    <a href="#">Kebijakan Pengiriman</a>
                </div>
            </div>
            <hr class="mt-5 mb-3" style="border-color: #ddd;">
            <div class="text-center text-muted" style="font-size: 0.85rem;">
                &copy; 2026 PisangKraf UMKM Artisanal Banana Delights. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
            offset: 40,
        });
    </script>
</body>
</html>