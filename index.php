<?php 
session_start();
// Hubungkan ke database koneksi.php
require 'database/koneksi.php';

// Ambil semua produk yang berstatus aktif (is_active = 1)
$query = "SELECT * FROM products WHERE is_active = 1 ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PisangKraf | Artisanal Banana Delights</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* --- DESIGN SYSTEM & VARIABLES --- */
        :root {
            --primary: #FACC15; /* Vibrant Yellow */
            --primary-dark: #CA8A04;
            --primary-glow: rgba(250, 204, 21, 0.4);
            --secondary: #1E293B; /* Slate 800 */
            --bg-light: #F8FAFC; /* Slate 50 */
            --card-bg: rgba(255, 255, 255, 0.85);
            --text-dark: #0F172A;
            --text-muted: #64748B;
            --radius-card: 24px;
            --radius-btn: 50px;
            --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.08);
            --shadow-hover: 0 20px 40px -10px rgba(250, 204, 21, 0.2);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        a { text-decoration: none; }

        /* --- HERO SECTION --- */
        .hero-section {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: url('https://images.unsplash.com/photo-1603833665858-e61d17a86224?q=80&w=2000&auto=format&fit=crop') no-repeat center center/cover;
            overflow: hidden;
        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(105deg, rgba(248,250,252,1) 0%, rgba(248,250,252,0.95) 45%, rgba(248,250,252,0.4) 100%);
            backdrop-filter: blur(4px);
            z-index: 1;
        }
        
        /* Floating shapes for dynamic feel */
        .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            z-index: 1;
            animation: float 8s ease-in-out infinite;
        }
        .shape-1 {
            width: 300px; height: 300px;
            background: var(--primary);
            top: -10%; left: -5%;
            opacity: 0.3;
        }
        .shape-2 {
            width: 400px; height: 400px;
            background: var(--primary-dark);
            bottom: -10%; right: -10%;
            opacity: 0.2;
            animation-delay: -4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding-top: 100px;
        }
        .badge-artisanal {
            background: linear-gradient(135deg, rgba(250,204,21,0.2), rgba(250,204,21,0.05));
            border: 1px solid rgba(250,204,21,0.4);
            backdrop-filter: blur(10px);
            color: var(--primary-dark);
            font-weight: 600;
            padding: 8px 20px;
            border-radius: var(--radius-btn);
            display: inline-block;
            margin-bottom: 24px;
            font-size: 0.9rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 24px;
            color: var(--text-dark);
            background: linear-gradient(to right, var(--text-dark), #475569);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-title span {
            color: var(--primary-dark);
            background: none;
            -webkit-text-fill-color: var(--primary-dark);
            position: relative;
        }
        .hero-title span::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 30%;
            background: var(--primary-glow);
            bottom: 5px;
            left: 0;
            z-index: -1;
            transform: rotate(-2deg);
        }
        .hero-desc {
            font-size: 1.15rem;
            color: var(--text-muted);
            margin-bottom: 40px;
            max-width: 540px;
            line-height: 1.7;
        }
        
        /* --- BUTTONS --- */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), #EAB308);
            color: var(--text-dark);
            font-weight: 600;
            padding: 14px 36px;
            border-radius: var(--radius-btn);
            border: none;
            transition: var(--transition);
            box-shadow: 0 10px 20px -5px var(--primary-glow);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }
        .btn-primary-custom::before {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: all 0.6s;
        }
        .btn-primary-custom:hover::before {
            left: 100%;
        }
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px -5px rgba(234, 179, 8, 0.4);
            color: var(--text-dark);
        }
        .btn-outline-custom {
            border: 2px solid var(--text-dark);
            color: var(--text-dark);
            font-weight: 600;
            padding: 12px 32px;
            border-radius: var(--radius-btn);
            background: transparent;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-outline-custom:hover {
            background-color: var(--text-dark);
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.3);
        }

        /* --- CARDS & COMPONENTS --- */
        .premium-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.4);
            border-radius: var(--radius-card);
            padding: 35px 30px;
            box-shadow: var(--shadow-soft);
            transition: var(--transition);
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .premium-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 5px;
            background: linear-gradient(90deg, var(--primary), transparent);
            opacity: 0;
            transition: var(--transition);
        }
        .premium-card:hover::before { opacity: 1; }
        .premium-card:hover {
            transform: translateY(-12px);
            box-shadow: var(--shadow-hover);
        }
        
        /* Why Us Section */
        .icon-box {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 25px;
            transition: var(--transition);
            background: #fff;
            box-shadow: 0 8px 20px -5px rgba(0,0,0,0.05);
        }
        .premium-card:hover .icon-box {
            transform: scale(1.1) rotate(5deg);
        }
        .icon-box.yellow { color: var(--primary-dark); border: 1px solid rgba(250,204,21,0.2); }
        .icon-box.green { color: #10B981; border: 1px solid rgba(16,185,129,0.2); }
        .icon-box.blue { color: #3B82F6; border: 1px solid rgba(59,130,246,0.2); }

        /* Products Section */
        .product-card { 
            padding: 15px; 
            background: #fff;
        }
        .product-img-wrapper {
            position: relative;
            border-radius: calc(var(--radius-card) - 10px);
            overflow: hidden;
            margin-bottom: 25px;
        }
        .product-img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .premium-card:hover .product-img { transform: scale(1.08); }
        
        .product-img-wrapper::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.4) 0%, transparent 50%);
            opacity: 0;
            transition: var(--transition);
        }
        .premium-card:hover .product-img-wrapper::after { opacity: 1; }

        .price-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(5px);
            padding: 8px 16px;
            border-radius: var(--radius-btn);
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            z-index: 2;
            color: var(--text-dark);
            border: 1px solid rgba(255,255,255,0.5);
        }

        /* Testimonial Section */
        .testimonial-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #EAB308);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--text-dark);
            margin-right: 18px;
            box-shadow: 0 4px 10px var(--primary-glow);
        }
        .quote-icon {
            color: rgba(250, 204, 21, 0.15);
            font-size: 4rem;
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 0;
        }
        .testimonial-content { position: relative; z-index: 1; }

        /* Newsletter Section */
        .newsletter-section {
            background: linear-gradient(135deg, var(--text-dark), #1E293B);
            padding: 100px 0;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .newsletter-section::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: var(--primary);
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.1;
            top: -50%; left: -10%;
        }
        .newsletter-form {
            max-width: 550px;
            margin: 0 auto;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-btn);
            padding: 8px;
            display: flex;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            transition: var(--transition);
        }
        .newsletter-form:focus-within {
            border-color: rgba(250, 204, 21, 0.5);
            background: rgba(255,255,255,0.15);
        }
        .newsletter-input {
            border: none;
            background: transparent;
            color: #fff;
            padding: 15px 25px;
            border-radius: var(--radius-btn);
            flex-grow: 1;
            outline: none;
            font-size: 1rem;
        }
        .newsletter-input::placeholder { color: rgba(255,255,255,0.6); }
        .newsletter-btn {
            background: var(--primary);
            color: var(--text-dark);
            border: none;
            padding: 0 35px;
            border-radius: var(--radius-btn);
            font-weight: 600;
            transition: var(--transition);
        }
        .newsletter-btn:hover { 
            background: #fff; 
            transform: scale(1.02);
        }

        /* Footer */
        footer {
            background-color: #fff;
            padding: 80px 0 30px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }
        .footer-logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 20px;
            background: linear-gradient(to right, var(--text-dark), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .footer-links a {
            color: var(--text-muted);
            display: inline-block;
            margin-bottom: 12px;
            transition: var(--transition);
            position: relative;
        }
        .footer-links a::after {
            content: '';
            position: absolute;
            width: 0; height: 2px;
            bottom: -2px; left: 0;
            background: var(--primary);
            transition: var(--transition);
        }
        .footer-links a:hover { color: var(--primary-dark); }
        .footer-links a:hover::after { width: 100%; }

        .social-links a {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: var(--bg-light);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            transition: var(--transition);
        }
        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        /* Section Titles */
        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 15px;
            color: var(--text-dark);
        }

        /* --- RESPONSIVE MEDIA QUERIES --- */
        @media (max-width: 991.98px) {
            .hero-title { font-size: 3rem; }
            .hero-section { background-position: center; text-align: center; }
            .hero-overlay { background: linear-gradient(to bottom, rgba(248,250,252,0.9) 0%, rgba(248,250,252,0.95) 100%); }
            .hero-desc { margin-left: auto; margin-right: auto; }
            .hero-content { display: flex; flex-direction: column; align-items: center; }
            .shape { display: none; }
        }

        @media (max-width: 767.98px) {
            .hero-title { font-size: 2.5rem; }
            .section-title { font-size: 2rem; }
            .newsletter-form { flex-direction: column; background: transparent; border: none; box-shadow: none; padding: 0; }
            .newsletter-input { margin-bottom: 15px; width: 100%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); text-align: center; }
            .newsletter-btn { padding: 15px; width: 100%; }
        }

        @media (max-width: 575.98px) {
            .hero-title { font-size: 2rem; }
            .badge-artisanal { font-size: 0.8rem; padding: 6px 15px; }
            .product-img { height: 200px; }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/components/navbar.php'; ?>

    <style>
        /* --- NAVBAR --- */
        .navbar-custom {
            padding: 20px 0;
            transition: var(--transition);
            background: transparent;
        }
        .navbar-custom.scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            padding: 15px 0;
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
        .nav-link:hover { color: var(--secondary) !important; }
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

    

    <header class="hero-section">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <div class="row">
                <div class="col-lg-7 col-md-9" data-aos="fade-up" data-aos-duration="1000">
                    <div class="badge-artisanal"><i class="fas fa-star text-warning me-2"></i>Jajan Pisang</div>
                    <h1 class="hero-title">Cita Rasa Pisang Tradisional,<br><span>Gaya Modern.</span></h1>
                    <p class="hero-desc">Jajan Pisang menghadirkan berbagai olahan pisang berkualitas premium dengan cita rasa khas Indonesia yang tak tertandingi.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="products.php" class="btn-primary-custom">Belanja Sekarang</a>
                        <a href="#produk" class="btn-outline-custom px-4 py-2">Lihat Produk</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="py-5 mt-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Kenapa Kami?</h2>
                <p class="text-muted fs-5">Kami berkomitmen memberikan kualitas terbaik, dari kebun hingga ke meja Anda.</p>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="premium-card">
                        <div class="icon-box yellow"><i class="fas fa-leaf"></i></div>
                        <h4 class="fw-bold fs-5">Bahan Segar</h4>
                        <p class="text-muted fs-6 mb-0">Pisang dipanen langsung dari petani lokal setiap pagi untuk menjamin kesegaran maksimal.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="premium-card">
                        <div class="icon-box green"><i class="fas fa-heart"></i></div>
                        <h4 class="fw-bold fs-5">Produk Lokal</h4>
                        <p class="text-muted fs-6 mb-0">100% mendukung UMKM dan petani lokal Indonesia. Dari kita, untuk kita.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="premium-card">
                        <div class="icon-box blue"><i class="fas fa-shield-alt"></i></div>
                        <h4 class="fw-bold fs-5">Tanpa Pengawet</h4>
                        <p class="text-muted fs-6 mb-0">Alami dan sehat. Kami tidak menggunakan bahan pengawet buatan dalam setiap produk.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="produk" class="py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4" data-aos="fade-up">
                <div>
                    <h2 class="section-title mb-1">Produk Terpopuler</h2>
                    <p class="text-muted fs-5 mb-0">Pilihan favorit pelanggan kami langsung dari kebun.</p>
                </div>
                <a href="products.php" class="text-dark fw-medium">Lihat Semua <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            
            <div class="row g-4">
                <?php 
                // Cek ketersediaan produk di database
                if (mysqli_num_rows($result) > 0) {
                    $delay = 100; // Efek delay AOS dinamis
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Menyesuaikan jalur gambar produk dengan folder admin upload
                        $image_path = 'assets/images/products/' . htmlspecialchars($row['image']);
                ?>
                <div class="col-xl-3 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                    <div class="premium-card product-card d-flex flex-column">
                        <div class="product-img-wrapper">
                            <span class="price-badge">Rp <?= number_format($row['price'], 0, ',', '.') ?></span>
                            <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="product-img" onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=No+Image'">
                        </div>
                        <h4 class="fw-bold fs-5 mb-3"><?= htmlspecialchars($row['name']) ?></h4>
                        <a href="product-detail.php?id=<?= $row['id'] ?>" class="btn-outline-custom w-100 mt-auto text-center" style="display: block;">Detail Produk</a>
                    </div>
                </div>
                <?php 
                        $delay += 100; 
                    }
                } else { 
                ?>
                <div class="col-12 text-center py-5">
                    <div class="text-muted fs-5"><i class="fas fa-box-open me-2"></i> Belum ada produk aktif di dalam database MySQL.</div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Kata Mereka</h2>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="premium-card position-relative">
                        <i class="fas fa-quote-right quote-icon"></i>
                        <div class="testimonial-content">
                            <div class="d-flex align-items-center mb-3">
                                <div class="testimonial-avatar">SR</div>
                                <div>
                                    <h5 class="fw-bold mb-0 fs-6">Siti Rahma</h5>
                                    <span class="text-muted" style="font-size: 0.8rem;">Pelanggan Setia</span>
                                </div>
                            </div>
                            <p class="text-muted fst-italic mb-0">"Bolu pisangnya beneran lembut banget dan manisnya pas! Selalu jadi andalan buat acara keluarga."</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="premium-card position-relative">
                        <i class="fas fa-quote-right quote-icon"></i>
                        <div class="testimonial-content">
                            <div class="d-flex align-items-center mb-3">
                                <div class="testimonial-avatar">BS</div>
                                <div>
                                    <h5 class="fw-bold mb-0 fs-6">Budi Santoso</h5>
                                    <span class="text-muted" style="font-size: 0.8rem;">Pecinta Cemilan</span>
                                </div>
                            </div>
                            <p class="text-muted fst-italic mb-0">"Pisang goreng madunya juara! Renyah di luar dan karamel madunya itu lho, bikin nagih. Packagingnya rapi."</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="premium-card position-relative">
                        <i class="fas fa-quote-right quote-icon"></i>
                        <div class="testimonial-content">
                            <div class="d-flex align-items-center mb-3">
                                <div class="testimonial-avatar">AW</div>
                                <div>
                                    <h5 class="fw-bold mb-0 fs-6">Ayu Wandira</h5>
                                    <span class="text-muted" style="font-size: 0.8rem;">Ibu Rumah Tangga</span>
                                </div>
                            </div>
                            <p class="text-muted fst-italic mb-0">"Anak-anak suka banget sama Pisang Nuggetnya. Bahannya kerasa premium dan nggak pake pengawet sama sekali."</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="newsletter-section text-center" data-aos="zoom-in">
        <div class="container position-relative z-3">
            <h2 class="section-title text-white mb-3">Dapatkan Promo Menarik!</h2>
            <p class="mb-4 fs-5 text-white-50">Daftar buletin kami untuk info produk terbaru dan diskon khusus.</p>
            <form class="newsletter-form">
                <input type="email" class="newsletter-input" placeholder="Masukkan email Anda" required>
                <button type="submit" class="newsletter-btn">Daftar</button>
            </form>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-md-5 mb-4 mb-md-0">
                    <div class="footer-logo">PisangKraf</div>
                    <p class="text-muted fs-6 pe-md-5">Menghadirkan kelezatan olahan pisang nusantara dengan sentuhan modern dan kualitas premium.</p>
                    <div class="social-links d-flex gap-2 mt-4">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="col-md-2 footer-links">
                    <h6 class="fw-bold mb-3">Tautan</h6>
                    <a href="index.php">Home</a>
                    <a href="products.php">Products</a>
                    <a href="about.php">About Us</a>
                    <a href="contact.php">Contact</a>
                </div>
                <div class="col-md-3 footer-links">
                    <h6 class="fw-bold mb-3">Bantuan</h6>
                    <a href="#">Cara Pesan</a>
                    <a href="#">Kebijakan Pengiriman</a>
                    <a href="#">Syarat & Ketentuan</a>
                    <a href="#">Kebijakan Privasi</a>
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
            offset: 50,
        });

        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>