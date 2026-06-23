<?php 
session_start();
// Hubungkan ke database koneksi.php
require 'database/koneksi.php';

// Ambil semua produk yang berstatus aktif (is_active = 1) untuk difilter secara langsung (client-side)
$query = "SELECT * FROM products WHERE is_active = 1 ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jajan Pisang | Artisanal Banana Delights</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* --- DESIGN SYSTEM & VARIABLES --- */
        :root {
            --primary: #FACC15; 
            --primary-dark: #CA8A04;
            --primary-glow: rgba(250, 204, 21, 0.4);
            --secondary: #1E293B; 
            --bg-light: #F8FAFC; 
            --card-bg: rgba(255, 255, 255, 1);
            --text-dark: rgb(68, 68, 68);
            --text-muted: #64748B;
            --radius-card: 12px; 
            --radius-btn: 50px;
            --shadow-soft: 0 4px 20px rgba(0,0,0,0.05);
            --transition: all 0.3s ease;
        }

        /* --- FIX CEKUNGAN PUTIH ATAS --- */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            font-family: 'Open Sans', sans-serif;
            background-color: #ffffff; 
            color: var(--text-dark);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        a { text-decoration: none; }

        .section-pill {
            background-color: rgba(250, 204, 21, 0.15);
            color: var(--primary-dark);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 50px;
            display: inline-block;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 0;
        }

        .section-subtitle {
            font-size: 1.1rem; /* Sedikit lebih besar dari teks biasa */
            color: var(--text-muted); /* Menggunakan warna abu-abu redup agar rapi */
            margin-top: -30px; /* Menarik teks agak ke atas agar lebih dekat dengan judul utama */
            margin-bottom: 60px; /* Memberi jarak dengan deretan kotak toko di bawahnya */
            font-weight: 400;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        /* --- HERO SECTION --- */
        .hero-section {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            
            /* Ganti 'assets/images/gambar-hero-anda.jpg' dengan lokasi file gambar Anda */
            background: url('assets/images/background.jpg') no-repeat center center/cover;
            
            background-attachment: fixed; /* Parallax effect */
            overflow: hidden;
            margin-top: 0;
            padding-top: 0;
        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: rgba(24, 24, 24, 0.7); 
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            padding: 0 20px;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin: 15px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .hero-desc {
            font-size: 1.1rem;
            font-weight: 300;
            margin-bottom: 30px;
        }
        .btn-hero-outline {
            border: 1px solid white;
            color: white;
            padding: 10px 30px;
            border-radius: 50px;
            font-size: 0.9rem;
            letter-spacing: 1px;
            transition: var(--transition);
            background: transparent;
        }
        .btn-hero-outline:hover {
            background: white;
            color: var(--text-dark);
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--text-dark);
        }

        .tentang-text {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.8;
            text-align: justify;
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid rgba(186, 199, 0, 0.08);
            border-radius: var(--radius-card);
            padding: 0;
            overflow: hidden;
            height: 100%;
            transition: var(--transition);
        }
        .feature-card:hover {
            box-shadow: var(--shadow-soft);
            transform: translateY(-5px);
        }
        .feature-img-box {
            height: 400px;
            background: var(--bg-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }
        .feature-content {
            padding: 25px 20px;
            text-align: left;
        }

        /* --- TOKO SECTION CARDS --- */
        .store-card-box {
            background: white;
            border: 1px solid rgba(177, 165, 0, 0.08);
            border-radius: 12px;
            padding: 25px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            text-decoration: none;
            height: 100%;
            transition: all 0.4s ease; 
            position: relative;
            overflow: hidden;
        }

        /* Liquid Wave Effect */
        .store-card-box::before,
        .store-card-box::after {
            content: "";
            position: absolute;
            bottom: -200%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: var(--primary);
            opacity: 0.85;
            border-radius: 38%;
            z-index: 1;
            transition: bottom 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .store-card-box::after {
            opacity: 0.4;
            border-radius: 42%;
            transition-duration: 0.8s;
        }

        @keyframes liquid-wave {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .store-card-box:hover::before,
        .store-card-box:active::before {
            bottom: -15%;
            animation: liquid-wave 5s linear infinite;
        }

        .store-card-box:hover::after,
        .store-card-box:active::after {
            bottom: -5%;
            animation: liquid-wave 4s linear infinite;
        }

        .store-logo-img {
            height: 60px;
            width: auto;
            max-width: 120px;
            object-fit: contain;
            margin-bottom: 15px;
            transition: transform 0.4s ease;
            position: relative;
            z-index: 2;
        }
        .store-name {
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--text-dark);
            margin-bottom: 5px;
            transition: color 0.4s ease;
            position: relative;
            z-index: 2;
        }
        .store-loc {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 0;
            transition: color 0.4s ease;
            position: relative;
            z-index: 2;
        }
        .store-card-box:hover,
        .store-card-box:active {
            border-color: var(--primary);
            transform: translateY(-5px); 
            box-shadow: 0 10px 25px rgba(250, 204, 21, 0.3);
        }
        .store-card-box:hover .store-name,
        .store-card-box:active .store-name,
        .store-card-box:hover .store-loc,
        .store-card-box:active .store-loc {
            color: var(--text-dark);
        }
        .store-card-box:hover .store-logo-img,
        .store-card-box:active .store-logo-img {
            transform: scale(1.05);
        }

        .grid-box {
            background: white;
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            height: 80px;
            transition: var(--transition);
            color: var(--text-dark);
            font-weight: 500;
            font-size: 0.9rem;
        }
        .grid-box:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 15px rgba(250, 204, 21, 0.1);
        }
        .grid-box i {
            font-size: 1.5rem;
        }

        .filter-pills {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .feature-photo {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ini memastikan gambar tidak gepeng meskipun ukuran aslinya berbeda */
    transition: transform 0.4s ease;
}

/* Opsional: Membuat gambar sedikit membesar saat disentuh mouse (hover) */
.feature-card:hover .feature-photo {
    transform: scale(1.05);
}
        .filter-btn {
            background: #f1f5f9;
            color: var(--text-muted);
            border: none;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--transition);
        }
        .filter-btn.active, .filter-btn:hover {
            background: var(--primary);
            color: var(--text-dark);
        }
        
        .product-card { 
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: var(--radius-card);
            padding: 0;
            overflow: hidden;
            background: white;
            transition: var(--transition);
        }
        .product-card:hover {
            box-shadow: var(--shadow-soft);
        }
        .product-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .product-info {
            padding: 15px;
            text-align: center;
        }

        .bantuan-list {
            list-style: none;
            padding: 0;
            max-width: 600px;
            margin: 0 auto;
            text-align: left;
        }
        .bantuan-list li {
            padding: 12px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            color: var(--text-muted);
            font-size: 0.95rem;
            position: relative;
            padding-left: 20px;
        }
        .bantuan-list li::before {
            content: '';
            color: var(--primary);
            position: absolute;
            left: 0;
            font-size: 1.2rem;
            line-height: 1;
        }

        footer {
            border-top: 1px solid rgba(0,0,0,0.08);
            padding: 60px 0 20px;
            font-size: 0.9rem;
        }
        .footer-heading {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 15px;
            font-size: 1rem;
        }
        .footer-links a {
            color: var(--text-muted);
            display: block;
            margin-bottom: 10px;
            transition: var(--transition);
        }
        .footer-links a:hover {
            color: var(--primary-dark);
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.5rem; }
            .section-title { font-size: 1.75rem; }
            .tentang-text { text-align: left; }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/components/navbar.php'; ?>

    <header class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container hero-content" data-aos="fade-up">
            <div class="section-pill text-white" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.5);">Selamat Datang di Jajan Pisang</div>
            <h1 class="hero-title">CITA RASA PISANG<br>TRADISIONAL & MODERN</h1>
            <p class="hero-desc">Pusat Oleh-Oleh dan Camilan Pisang Nusantara Khas Banyumas</p>
            <a href="#tentang" class="btn-hero-outline">SELENGKAPNYA</a>
        </div>
    </header>

    <section id="tentang" class="py-5 mt-4">
        <div class="container text-center">
            <div class="section-pill" data-aos="fade-up">ABOUT</div>
            <h2 class="section-title mb-5" data-aos="fade-up">Tentang <span style="color: var(--primary-dark);">Jajan Pisang</span></h2>
            
            <div class="row text-start justify-content-center" data-aos="fade-up" data-aos-delay="100">
                <div class="col-md-5">
                    <p class="tentang-text">Jajan Pisang hadir membawa semangat untuk melestarikan cita rasa olahan pisang nusantara dengan sentuhan modern. Kami berkomitmen memberikan kualitas terbaik, diproses secara higienis dari kebun hingga ke meja Anda, memastikan setiap gigitan menghadirkan memori manis.</p>
                </div>
                <div class="col-md-5">
                    <p class="tentang-text">Karena Indonesia kaya akan hasil alam, terutama varietas pisang lokal, kami memberdayakan petani sekitar untuk menghadirkan camilan bebas pengawet. Temukan kelezatan asli dari Jajan Pisang yang cocok menemani setiap momen spesial keluarga Anda.</p>
                </div>
            </div>
            <div class="mt-4" data-aos="fade-up" data-aos-delay="200">
                <a href="about.php" class="btn btn-outline-dark rounded-pill px-4">Lihat Selengkapnya</a>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container text-center">
    <div class="section-pill" data-aos="fade-up">WHY US</div>
    <h2 class="section-title mb-5" data-aos="fade-up">Mengapa di <span style="color: var(--primary-dark);">Jajan Pisang?</span></h2>
    <p class="section-subtitle" data-aos="fade-up">Kami selalu memilih bahan baku unggulan, diproses oleh tangan ahli dengan standar mutu yang ketat, guna memberikan pengalaman ngemil pisang terbaik untuk Anda.</p>
    
    <div class="row g-4 justify-content-center text-start">
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="feature-card">
                <div class="feature-img-box">
                    <!-- Ganti src dengan lokasi gambar Anda -->
                    <img src="assets/images/mengapa-1.jpg" alt="Bahan Segar Berkualitas" class="feature-photo">
                </div>
                <div class="feature-content">
                    <h4 class="fw-bold fs-6">Bahan Segar Berkualitas</h4>
                    <p class="text-muted fs-6 mb-0" style="font-size: 0.85rem !important;">Pisang dipanen langsung dari petani lokal setiap pagi untuk menjamin kesegaran maksimal pada produk kami.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
            <div class="feature-card">
                <div class="feature-img-box">
                    <!-- Ganti src dengan lokasi gambar Anda -->
                    <img src="assets/images/mengapa-2.jpg" alt="Dukungan UMKM Lokal" class="feature-photo">
                </div>
                <div class="feature-content">
                    <h4 class="fw-bold fs-6">Dukungan UMKM Lokal</h4>
                    <p class="text-muted fs-6 mb-0" style="font-size: 0.85rem !important;">100% mendukung UMKM dan petani lokal Indonesia. Memberdayakan ekonomi daerah dengan kemitraan yang sehat.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
            <div class="feature-card">
                <div class="feature-img-box">
                    <!-- Ganti src dengan lokasi gambar Anda -->
                    <img src="assets/images/mengapa-3.jpg" alt="Alami Tanpa Pengawet" class="feature-photo">
                </div>
                <div class="feature-content">
                    <h4 class="fw-bold fs-6">Alami Tanpa Pengawet</h4>
                    <p class="text-muted fs-6 mb-0" style="font-size: 0.85rem !important;">Kelezatan alami dan sehat. Kami tidak menggunakan bahan pengawet buatan dalam memproses semua olahan pisang.</p>
                </div>
            </div>
        </div>
    </div>
</div>
    </section>

    <section class="py-5">
        <div class="container text-center">
            <div class="section-pill" data-aos="fade-up">MITRA</div>
            <h2 class="section-title mb-5" data-aos="fade-up">Mitra Petani & Pemasok</h2>
            
            <div class="row g-3 justify-content-center" data-aos="fade-up" data-aos-delay="100">
                <div class="col-md-3 col-sm-6"><div class="grid-box"><i class="fas fa-tractor text-success"></i> Kelompok Tani Banyumas</div></div>
                <div class="col-md-3 col-sm-6"><div class="grid-box"><i class="fas fa-store text-warning"></i> Koperasi Desa Merah Putih</div></div>
                <div class="col-md-3 col-sm-6"><div class="grid-box"><i class="fas fa-seedling text-success"></i> Kebun Pisang Makmur</div></div>
                <div class="col-md-3 col-sm-6"><div class="grid-box"><i class="fas fa-box text-secondary"></i> Supplier Sinar Mas</div></div>
                <div class="col-md-3 col-sm-6"><div class="grid-box"><i class="fas fa-leaf text-success"></i> Tani Jaya Purwokerto</div></div>
                <div class="col-md-3 col-sm-6"><div class="grid-box"><i class="fas fa-truck text-primary"></i> Ekspedisi Maju Bersama</div></div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container text-center">
            <div class="section-pill" data-aos="fade-up">STORE</div>
            <h2 class="section-title mb-5" data-aos="fade-up">Toko <span style="color: var(--primary-dark);">Jajan Pisang</span></h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Toko jajan pisang tersedia di "Offline Store" dan "Marketplace".</p>
            
            <div class="row g-4 justify-content-center" data-aos="fade-up" data-aos-delay="100">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="#" class="store-card-box">
                        <img src="assets/images/logo-jajanpisang.png" alt="Toko Fisik" class="store-logo-img">
                        <h5 class="store-name">Toko Fisik</h5>
                        <p class="store-loc">Purwokerto</p>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="#" class="store-card-box">
                        <img src="assets/images/logo-tokopedia.png" alt="Tokopedia" class="store-logo-img">
                        <h5 class="store-name">Jajan Pisang Tokopedia</h5>
                        <p class="store-loc">Online</p>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="#" class="store-card-box">
                        <img src="assets/images/logo-shopee.png" alt="Shopee" class="store-logo-img">
                        <h5 class="store-name">Jajan Pisang Shopee</h5>
                        <p class="store-loc">Online</p>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="#" class="store-card-box">
                        <img src="assets/images/logo-gofood.png" alt="GoFood" class="store-logo-img">
                        <h5 class="store-name">GoFood / GrabFood</h5>
                        <p class="store-loc">Online</p>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="produk" class="py-5">
        <div class="container text-center">
            <div class="section-pill" data-aos="fade-up">PRODUCT</div>
            <h2 class="section-title mb-5" data-aos="fade-up">Produk <span style="color: var(--primary-dark);">Jajan Pisang</span></h2>
            <p class="section-subtitle mb-4" data-aos="fade-up" data-aos-delay="100">Temukan berbagai macam camilan pisang nusantara di jajan pisang</p>
            
            <div class="filter-pills" data-aos="fade-up">
                <button class="filter-btn active" data-filter="all">Semua</button>
                <button class="filter-btn" data-filter="Original">Original</button>
                <button class="filter-btn" data-filter="Pedas">Pedas</button>
                <button class="filter-btn" data-filter="Manis">Manis</button>
                <button class="filter-btn" data-filter="Gurih">Gurih</button>
                <button class="filter-btn" data-filter="Paket">Paket</button>
            </div>
            
            <div class="row g-4 mt-2">
                <?php 
                if (mysqli_num_rows($result) > 0) {
                    $delay = 100;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $image_path = 'assets/images/products/' . htmlspecialchars($row['image']);
                ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 product-item" data-category="<?= htmlspecialchars($row['category']) ?>" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                    <a href="product-detail.php?id=<?= $row['id'] ?>" class="text-decoration-none">
                        <div class="product-card">
                            <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="product-img" onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=No+Image'">
                            <div class="product-info">
                                <h6 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($row['name']) ?></h6>
                                <p class="text-muted mb-0 fw-medium" style="font-size: 0.9rem;">Rp <?= number_format($row['price'], 0, ',', '.') ?></p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php 
                        $delay += 50; 
                    }
                } else { 
                ?>
                <div class="col-12 text-center py-5">
                    <div class="text-muted fs-5"><i class="fas fa-box-open me-2"></i> Belum ada produk aktif di dalam database.</div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container text-center">
            <div class="section-pill" data-aos="fade-up">BANTUAN</div>
            <h2 class="section-title mb-4" data-aos="fade-up">Bantuan <span style="color: var(--primary-dark);">Jajan Pisang</span></h2>
            <p class="text-muted mb-5" data-aos="fade-up">Punya pertanyaan? Mungkin Anda menemukan jawabannya di sini.</p>
            
            <div data-aos="fade-up" data-aos-delay="100">
                <ul class="bantuan-list">
                    <li>Bagaimana cara memesan produk Jajan Pisang secara online?</li>
                    <li>Berapa lama estimasi pengiriman untuk pesanan ke luar kota?</li>
                    <li>Apakah produk Jajan Pisang aman dikonsumsi anak-anak?</li>
                    <li>Apakah tersedia opsi kemasan hampers atau hadiah?</li>
                    <li>Di mana saja letak toko agen resmi Jajan Pisang?</li>
                </ul>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <h2 class="fw-bold mb-3" style="color: var(--primary-dark);">Jajan Pisang</h2>
                    <div class="text-muted fs-6 footer-links">
                        <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> Purwokerto, Jawa Tengah, Indonesia</p>
                        <p class="mb-2"><i class="fas fa-envelope me-2"></i> info@jajanpisang.com</p>
                        <p class="mb-2"><i class="fas fa-phone me-2"></i> +62 812 3456 7890</p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 footer-links">
                    <h5 class="footer-heading">Navigasi</h5>
                    <a href="index.php">Beranda</a>
                    <a href="about.php">Tentang Kami</a>
                    <a href="products.php">Produk</a>
                    <a href="contact.php">Kontak</a>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 footer-links">
                    <h5 class="footer-heading">Kategori</h5>
                    <a href="#">Original</a>
                    <a href="#">Pedas</a>
                    <a href="#">Manis</a>
                    <a href="#">Gurih</a>
                    <a href="#">Paket</a>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 footer-links">
                    <h5 class="footer-heading">Bantuan</h5>
                    <a href="#">Cara Pemesanan</a>
                    <a href="#">Konfirmasi Pembayaran</a>
                    <a href="#">Syarat & Ketentuan</a>
                    <a href="#">Kebijakan Privasi</a>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 footer-links text-lg-end text-md-start">
                    <h5 class="footer-heading">Sosial Media</h5>
                    <a href="#"><i class="fab fa-instagram me-2"></i> @jajanpisang</a>
                    <a href="#"><i class="fab fa-facebook me-2"></i> Jajan Pisang ID</a>
                    <a href="#"><i class="fab fa-tiktok me-2"></i> @jajanpisang.id</a>
                </div>
            </div>
            <div class="text-center text-muted mt-4 pt-4" style="border-top: 1px solid rgba(0,0,0,0.05); font-size: 0.85rem;">
                &copy; 2026 Jajan Pisang UMKM Artisanal Banana Delights. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        AOS.init({
            once: true,
            offset: 50,
        });

        // Hapus transisi navbar lama dari script karena kita buat static/transparan mengikuti hero
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar'); 
            if(navbar) {
                if (window.scrollY > 50) {
                    navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                    navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.05)';
                } else {
                    navbar.style.background = 'transparent';
                    navbar.style.boxShadow = 'none';
                }
            }
        });

        // Product Filtering Logic (Client-Side)
        document.addEventListener('DOMContentLoaded', () => {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const productItems = document.querySelectorAll('.product-item');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    
                    // Remove active class from all buttons
                    filterBtns.forEach(b => b.classList.remove('active'));
                    // Add active class to clicked button
                    btn.classList.add('active');

                    const filterValue = btn.getAttribute('data-filter');

                    productItems.forEach(item => {
                        if (filterValue === 'all') {
                            item.style.display = 'block';
                        } else {
                            if (item.getAttribute('data-category') === filterValue) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>

